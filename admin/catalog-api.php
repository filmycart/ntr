<?php
    header('Content-Type: application/json; charset=utf-8');

    require_once __DIR__ . '/connection.php';
    require_once __DIR__ . '/catalog-config.php';

    function reply($ok, $message, $data = null) {
        echo json_encode([
            'status'  => $ok ? 'success' : 'error',
            'message' => $message,
            'data'    => $data
        ]);
        exit;
    }

    try {
        // Check user authentication
        if (empty($_SESSION['login_id'])) {
            http_response_code(401);
            reply(false, 'Login required');
        }

        // Get table and column information
        $table = catalogTable((string) ($_REQUEST['table'] ?? ''));

        $columns = catalogColumns($conn, $table);
        $map = array_column($columns, null, 'Field');

        if (!isset($map['id'])) {
            throw new RuntimeException('This module requires an id column');
        }

        // Get action and record ID
        $action = $_REQUEST['action'] ?? '';

        $id = filter_var(
            $_REQUEST['id'] ?? null,
            FILTER_VALIDATE_INT
        );

        // Get single record
        if ($action === 'get') {
            if (!$id) {
                throw new RuntimeException('Invalid ID');
            }

            $s = $conn->prepare(
                'SELECT * FROM `' . $table . '` WHERE `id` = ? LIMIT 1'
            );

            $s->bind_param('i', $id);
            $s->execute();

            $row = $s->get_result()->fetch_assoc();

            if (!$row) {
                throw new RuntimeException('Record not found');
            }

            reply(true, 'Record loaded', $row);
        }

        // Validate request method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new RuntimeException('POST required');
        }

        // Validate CSRF token
        $token = $_POST['csrf'] ?? '';

        if (!hash_equals($_SESSION['catalog_csrf'] ?? '', $token)) {
            throw new RuntimeException('Invalid CSRF token');
        }

        // Delete record
        if ($action === 'delete') {
            if (!$id) {
                throw new RuntimeException('Invalid ID');
            }

            $s = $conn->prepare(
                'DELETE FROM `' . $table . '` WHERE `id` = ?'
            );

            $s->bind_param('i', $id);
            $s->execute();

            if (!$s->affected_rows) {
                throw new RuntimeException('Record not found');
            }

            reply(true, 'Record deleted');
        }

        // Validate create or update action
        if (!in_array($action, ['create', 'update'], true)) {
            throw new RuntimeException('Invalid action');
        }

        if ($action === 'update' && !$id) {
            throw new RuntimeException('Invalid ID');
        }

        // Prepare field values
        $values = [];

        foreach ($columns as $col) {
            if (!catalogEditable($col)) {
                continue;
            }

            $name = $col['Field'];
            // Handle image upload
            if (
                isset($_FILES[$name]) &&
                $_FILES[$name]['error'] !== UPLOAD_ERR_NO_FILE
            ) {
                $file = $_FILES[$name];

                if (
                    !catalogImageColumn($name) ||
                    $file['error'] !== UPLOAD_ERR_OK ||
                    $file['size'] > 5 * 1024 * 1024
                ) {
                    throw new RuntimeException(
                        'Invalid image upload: ' . $name
                    );
                }

                // Validate image MIME type
                $mime = (new finfo(FILEINFO_MIME_TYPE))
                    ->file($file['tmp_name']);

                $ext = [
                    'image/jpeg' => 'jpg',
                    'image/png'  => 'png',
                    'image/webp' => 'webp',
                    'image/gif'  => 'gif'
                ][$mime] ?? null;

                if (!$ext) {
                    throw new RuntimeException(
                        'Unsupported image format'
                    );
                }

                // Create upload directory
                $dir = __DIR__ . '/image/catalog';

                if (
                    !is_dir($dir) &&
                    !mkdir($dir, 0755, true)
                ) {
                    throw new RuntimeException(
                        'Upload directory unavailable'
                    );
                }

                // Generate unique image filename
                $filename = bin2hex(random_bytes(16)) . '.' . $ext;

                // Move uploaded image
                if (
                    !move_uploaded_file(
                        $file['tmp_name'],
                        $dir . '/' . $filename
                    )
                ) {
                    throw new RuntimeException('Upload failed');
                }

                $values[$name] = 'image/catalog/' . $filename;

            } elseif (array_key_exists($name, $_POST)) {

                // Handle regular form fields
                $value = $_POST[$name];

                if (is_array($value)) {
                    throw new RuntimeException('Invalid value');
                }

                $values[$name] = (
                    $value === '' &&
                    $col['Null'] === 'YES'
                ) ? null : $value;

            } elseif (
                $action === 'create' &&
                $col['Null'] === 'NO' &&
                $col['Default'] === null &&
                !preg_match('/^(created_at|updated_at)$/', $name)
            ) {

                throw new RuntimeException(
                    'Missing required field: ' . $name
                );
            }
        }

        // Check submitted fields
        if (!$values) {
            throw new RuntimeException('No fields supplied');
        }

        // Prepare SQL parameters
        $names = array_keys($values);
        $params = array_values($values);

        $types = str_repeat('s', count($params));

        // Create record
        if ($action === 'create') {

            $sql = 'INSERT INTO `' . $table . '` (' .
                implode(
                    ',',
                    array_map(
                        fn($n) => '`' . $n . '`',
                        $names
                    )
                ) .
                ') VALUES (' .
                implode(
                    ',',
                    array_fill(0, count($names), '?')
                ) .
                ')';

        } else {

            // Update record
            $sql = 'UPDATE `' . $table . '` SET ' .
                implode(
                    ',',
                    array_map(
                        fn($n) => '`' . $n . '`=?',
                        $names
                    )
                ) .
                ' WHERE `id` = ?';

            $params[] = $id;
            $types .= 'i';
        }

        // Execute prepared statement
        $s = $conn->prepare($sql);

        $s->bind_param($types, ...$params);
        $s->execute();

        // Return success response
        reply(
            true,
            $action === 'create'
                ? 'Record created'
                : 'Record updated',
            [
                'id' => $action === 'create'
                    ? $conn->insert_id
                    : $id
            ]
        );

    } catch (Throwable $e) {
        http_response_code(400);
        reply(false, $e->getMessage());
    }
