<?php
	require_once __DIR__ . '/catalog-config.php';

	$table = catalogTable($_GET['table'] ?? '');

	$columns = catalogColumns($conn, $table);
	$foreign = catalogForeignKeys($conn, $table);

	if (empty($_SESSION['catalog_csrf'])) {
	    $_SESSION['catalog_csrf'] = bin2hex(random_bytes(24));
	}

	$fields = array_values(
	    array_filter($columns, 'catalogEditable')
	);

	$labels = [];
	$choices = [];

	// Get foreign key dropdown values
	foreach ($foreign as $col => $fk) {
	    $ref = $fk['REFERENCED_TABLE_NAME'];
	    $refCol = $fk['REFERENCED_COLUMN_NAME'];

	    if (!preg_match('/^[a-zA-Z0-9_]+$/', $ref)) {
	        continue;
	    }

	    $refCols = array_column(
	        catalogColumns($conn, $ref),
	        'Field'
	    );

	    $label = in_array('name', $refCols, true)
	        ? 'name'
	        : (in_array('title', $refCols, true) ? 'title' : $refCol);

	    $choices[$col] = [];

	    $rs = $conn->query(
	        'SELECT
	            `' . $refCol . '` AS id,
	            `' . $label . '` AS label
	        FROM `' . $ref . '`
	        ORDER BY `' . $refCol . '` DESC
	        LIMIT 1000'
	    );

	    while ($row = $rs->fetch_assoc()) {
	        $choices[$col][] = $row;
	    }
	}

	$orderBy = ($table === 'variant_attribute_values')
			    ? 'variant_id'
			    : 'id';

	// Get catalog records
	$rows = $conn->query(
	    'SELECT *
	     FROM `' . $table . '`
	     ORDER BY `' . $orderBy . '` DESC
	     LIMIT 1000'
	)->fetch_all(MYSQLI_ASSOC);

	/**
	 * Escape HTML output.
	 */
	function h($value) {
	    return htmlspecialchars(
	        (string) ($value ?? ''),
	        ENT_QUOTES,
	        'UTF-8'
	    );
	}
?>
<!-- Catalog Listing -->
<section class="content">
    <div class="container-fluid">
        <div class="card card-primary">

            <!-- Card Header -->
            <div class="card-header">
                <h3 class="card-title">
                    <?= h(CATALOG_TABLES[$table]) ?>
                </h3>

                <button
                    type="button"
                    class="btn btn-light btn-sm float-right"
                    id="catalogAdd"
                >
                    Add New
                </button>
            </div>

            <!-- Card Body -->
            <div class="card-body table-responsive">

                <table
                    id="catalogTable"
                    class="table table-bordered table-striped table-hover"
                >
                    <thead>
                        <tr>
                            <?php foreach ($columns as $column): ?>
                                <th>
                                    <?= h(
                                        ucwords(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $column['Field']
                                            )
                                        )
                                    ) ?>
                                </th>
                            <?php endforeach; ?>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                    <?php $key = $column['Field']; ?>

                                    <td>
                                        <?php if (
                                            catalogImageColumn($key)
                                            && !empty($row[$key])
                                        ): ?>

                                            <img
                                                src="<?= h($row[$key]) ?>"
                                                alt="Product image"
                                                style="max-width:65px; max-height:65px;"
                                            >

                                        <?php else: ?>

                                            <?= h(
                                                mb_strimwidth(
                                                    (string) ($row[$key] ?? ''),
                                                    0,
                                                    100,
                                                    '...'
                                                )
                                            ) ?>

                                        <?php endif; ?>
                                    </td>

                                <?php endforeach; ?>

                                <!-- Action Buttons -->
                                <td class="text-nowrap">

                                    <button
                                        type="button"
                                        class="btn btn-info btn-sm catalogView"
                                        data-id="<?= h($row['id']) ?>"
                                    >
                                        View
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-warning btn-sm catalogEdit"
                                        data-id="<?= h($row['id']) ?>"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm catalogDelete"
                                        data-id="<?= h($row['id']) ?>"
                                    >
                                        Delete
                                    </button>

                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</section>

<!-- Catalog Modal -->
<div
    class="modal fade"
    id="catalogModal"
    tabindex="-1"
>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">

                <h4 class="modal-title" id="catalogTitle">
                    Add
                </h4>

                <button
                    type="button"
                    class="close"
                    data-dismiss="modal"
                >
                    &times;
                </button>

            </div>

            <!-- Modal Form -->
            <form
                id="catalogForm"
                enctype="multipart/form-data"
            >
                <div class="modal-body">
                    <div class="row">

                        <?php foreach ($fields as $column): ?>

                            <?php
                                $name = $column['Field'];
                                $type = strtolower($column['Type']);

                                $required = $column['Null'] === 'NO'
                                    && $column['Default'] === null;

                                $isImage = catalogImageColumn($name);
                            ?>

                            <div class="col-md-6">
                                <div class="form-group">

                                    <label for="field_<?= h($name) ?>">
                                        <?= h(
                                            ucwords(
                                                str_replace('_', ' ', $name)
                                            )
                                        ) ?>
                                    </label>

                                    <?php if (isset($choices[$name])): ?>

                                        <!-- Foreign Key Dropdown -->
                                        <select
                                            class="form-control catalogField"
                                            id="field_<?= h($name) ?>"
                                            name="<?= h($name) ?>"
                                            <?= $required ? 'required' : '' ?>
                                        >
                                            <option value="">Select</option>

                                            <?php foreach ($choices[$name] as $option): ?>
                                                <option value="<?= h($option['id']) ?>">
                                                    <?= h($option['label']) ?>
                                                    (#<?= h($option['id']) ?>)
                                                </option>
                                            <?php endforeach; ?>

                                        </select>

                                    <?php elseif ($isImage): ?>

                                        <!-- Image Upload -->
                                        <input
                                            type="file"
                                            class="form-control-file catalogField"
                                            id="field_<?= h($name) ?>"
                                            name="<?= h($name) ?>"
                                            accept="image/jpeg,image/png,image/webp,image/gif"
                                        >

                                        <small class="text-muted">
                                            JPG, PNG, WEBP or GIF (max 5 MB).
                                            Leave blank to retain existing image.
                                        </small>

                                    <?php elseif (str_contains($type, 'text')): ?>

                                        <!-- Textarea -->
                                        <textarea
                                            class="form-control catalogField"
                                            id="field_<?= h($name) ?>"
                                            name="<?= h($name) ?>"
                                            <?= $required ? 'required' : '' ?>
                                        ></textarea>

                                    <?php elseif (
                                        preg_match('/^enum\((.*)\)$/i', $type, $matches)
                                    ): ?>

                                        <!-- ENUM Dropdown -->
                                        <select
                                            class="form-control catalogField"
                                            id="field_<?= h($name) ?>"
                                            name="<?= h($name) ?>"
                                        >
                                            <?php foreach (
                                                str_getcsv($matches[1], ',', "'")
                                                as $option
                                            ): ?>

                                                <option value="<?= h($option) ?>">
                                                    <?= h($option) ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>

                                    <?php else: ?>

                                        <?php
                                            $input = preg_match(
                                                '/int|decimal|float|double/',
                                                $type
                                            )
                                                ? 'number'
                                                : (
                                                    preg_match('/date|time/', $type)
                                                        ? 'text'
                                                        : 'text'
                                                );
                                        ?>

                                        <!-- Standard Input -->
                                        <input
                                            type="<?= $input ?>"
                                            class="form-control catalogField"
                                            id="field_<?= h($name) ?>"
                                            name="<?= h($name) ?>"
                                            <?= $input === 'number' ? 'step="any"' : '' ?>
                                            <?= $required ? 'required' : '' ?>
                                        >

                                    <?php endif; ?>

                                </div>
                            </div>

                        <?php endforeach; ?>

                    </div>

                    <!-- Error Message -->
                    <div
                        id="catalogError"
                        class="alert alert-danger d-none"
                    ></div>

                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="catalogSave"
                    >
                        Save
                    </button>

                </div>
            </form>

        </div>
    </div>
</div>

<!-- Catalog JavaScript -->
<script>
(function () {
    'use strict';

    const table = <?= json_encode($table) ?>;
    const csrf = <?= json_encode($_SESSION['catalog_csrf']) ?>;

    /**
     * Send AJAX request to catalog API.
     */
    function request(action, data) {
        data = data || new FormData();

        data.append('table', table);
        data.append('action', action);

        if (action !== 'get') {
            data.append('csrf', csrf);
        }

        return $.ajax({
            url: 'catalog-api.php',
            method: action === 'get' ? 'GET' : 'POST',
            data: action === 'get'
                ? Object.fromEntries(data)
                : data,
            processData: action === 'get',
            contentType: action === 'get' ? undefined : false,
            dataType: 'json'
        });
    }

    /**
     * Display AJAX error.
     */
    function error(xhr) {
        const message = xhr.responseJSON?.message || 'Request failed';

        $('#catalogError')
            .removeClass('d-none')
            .text(message);
    }

    /**
     * Open catalog modal.
     *
     * Modes:
     * create - Add record
     * edit   - Edit record
     * view   - View record
     */
    function modal(mode, id) {
        const form = $('#catalogForm')[0];

        form.reset();

        $('#catalogError').addClass('d-none');

        $('#catalogSave').toggle(mode !== 'view');

        $('.catalogField').prop(
            'disabled',
            mode === 'view'
        );

        $('#catalogTitle').text(
            mode === 'create'
                ? 'Add Record'
                : mode === 'edit'
                    ? 'Edit Record'
                    : 'View Record'
        );

        $('#catalogForm')
            .data('mode', mode)
            .data('id', id || '');

        // Create Mode
        if (mode === 'create') {
            $('#catalogModal').modal('show');
            return;
        }

        // Edit / View Mode
        const formData = new FormData();

        formData.append('id', id);

        request('get', formData)
            .done(function (response) {

                Object.entries(response.data).forEach(function ([key, value]) {
                    const element = document.getElementById(
                        'field_' + key
                    );

                    if (element && element.type !== 'file') {
                        $(element).val(value);
                    }
                });

                $('#catalogModal').modal('show');
            })
            .fail(error);
    }

    /**
     * Initialize catalog events.
     */
    $(function () {

        // Initialize DataTable
        if ($.fn.DataTable) {
            $('#catalogTable').DataTable({
                pageLength: 10,
                scrollX: true
            });
        }

        // Add New Record
        $('#catalogAdd').on('click', function () {
            modal('create');
        });

        // View Record
        $(document).on('click', '.catalogView', function () {
            const id = $(this).data('id');

            modal('view', id);
        });

        // Edit Record
        $(document).on('click', '.catalogEdit', function () {
            const id = $(this).data('id');

            modal('edit', id);
        });

        // Delete Record
        $(document).on('click', '.catalogDelete', function () {
            const id = $(this).data('id');

            if (!confirm(
                'Delete record #' + id + '? This cannot be undone.'
            )) {
                return;
            }

            const formData = new FormData();

            formData.append('id', id);

            request('delete', formData)
                .done(function () {
                    location.reload();
                })
                .fail(function (xhr) {
                    alert(
                        xhr.responseJSON?.message || 'Delete failed'
                    );
                });
        });

        // Create / Update Record
        $('#catalogForm').on('submit', function (event) {
            event.preventDefault();

            const formData = new FormData(this);
            const mode = $(this).data('mode');

            if (mode === 'edit') {
                formData.append(
                    'id',
                    $(this).data('id')
                );
            }

            $('#catalogSave').prop('disabled', true);

            request(
                mode === 'create' ? 'create' : 'update',
                formData
            )
                .done(function () {
                    location.reload();
                })
                .fail(error)
                .always(function () {
                    $('#catalogSave').prop('disabled', false);
                });
        });

    });

})();
</script>
