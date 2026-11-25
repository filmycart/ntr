<?php
  header('Content-Type: application/json');

  require_once 'connection.php';

  $pgid = "";
  if ((isset($_GET['id'])) && (!empty($_GET['id']))) {
      $pgid = $_GET['id'];
  } elseif ((isset($_POST['productId'])) && (!empty($_POST['productId']))) {
      $pgid = $_POST['productId'];
  }

  $pgAction = "";
  if ((isset($_GET['action'])) && (!empty($_GET['action']))) {
      $pgAction = $_GET['action'];
  } elseif ((isset($_POST['productAction'])) && (!empty($_POST['productAction']))) {
      $pgAction = $_POST['productAction'];
  }

  if ($pgAction === 'add') {

    header('Content-Type: application/json; charset=utf-8');

    $sql = "SELECT
        id,
        parent_id,
        name,
        slug,
        status
        FROM product_categories
        WHERE status = 1
        ORDER BY name ASC";

    $result = mysqli_query($conn, $sql);

    if ($result === false) {
        http_response_code(500);

        echo json_encode([
        'status'  => 'error',
        'message' => 'Unable to fetch categories'
        ]);

        exit;
    }

    $categories = mysqli_fetch_all(
        $result,
        MYSQLI_ASSOC
    );

    echo json_encode([
        'status'  => 'success',
        'message' => 'Categories fetched successfully',
        'data'    => $categories
    ]);
    exit;
  }

  if($pgAction === 'create') {
    $productName = $_POST['productName'];
    $productSlug = $_POST['productSlug'];
    $productType = $_POST['productType'];
    $productMaterial = $_POST['productMaterial'];
    $productWeight = $_POST['productWeight'];
    $productCategory = $_POST['productCategory'];
    $productDimension = $_POST['productDimension'];
    $productDesc = $_POST['productDesc'];

    $productStatus = (int) $_POST['productStatus'];

    $productCategoryStr = "";
    if(!empty($productCategory)){
        $productCategoryStr = implode(",",$productCategory);
    }

    $extension = strtolower(
      pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION)
    );    

    $productImage = bin2hex(random_bytes(4)) . '.' . $extension;

    $path = 'image/products/'.$productImage;

    move_uploaded_file($_FILES['productImage']['tmp_name'], $path);
    
    $createdBy = (int) $_SESSION['login_id'];

    $sql = "SELECT * FROM product WHERE slug = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $productSlug);

    // Display SQL query for debugging
    $debugSql = "SELECT * FROM product WHERE slug = '" .
        mysqli_real_escape_string($conn, $productSlug) . "'";

    // Execute query
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if(!empty($row)){
        echo json_encode([
            'status' => 'error',
            'message' => 'Product slug already exist'
        ]);
    } else {
        $sql_insert = "INSERT INTO `product`
        (`category_id`,`name`, `slug`, `type`, `description`, `material`, `weight`, `dimension`, `status`, `created_by`, `created_at`)
        VALUES
        ('$productCategoryStr','$productName', '$productSlug', '$productType','$productDesc','$productMaterial','$productWeight','$productDimension','$productStatus', $createdBy, NOW())";

        if (mysqli_query($conn, $sql_insert)) {
            // Get last inserted product ID
            $productId = mysqli_insert_id($conn);

            $uploadedImages = uploadProductImages(
                $conn,
                $productId
            );
        } else {
            echo "Error: " . mysqli_error($conn);
        }

        if ($sql_insert) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Product Created successfully',
                'data' => $sql_insert
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
    }    
  }elseif ($pgAction === 'edit' && $pgid) {

        $data = array();

        if ($pgAction === 'edit') {

            header('Content-Type: application/json; charset=utf-8');

            $catsql = "SELECT
                id,
                parent_id,
                name,
                slug,
                status
                FROM product_categories
                WHERE status = 1
                ORDER BY name ASC";

            $catresult = mysqli_query($conn, $catsql);

            if ($catresult === false) {
                http_response_code(500);

                echo json_encode([
                    'status'  => 'error',
                    'message' => 'Unable to fetch categories'
                ]);
                exit;
            }

            $categories = mysqli_fetch_all(
                $catresult,
                MYSQLI_ASSOC
            );

            $stmt = mysqli_prepare(
                    $conn,
                    "SELECT
                        p.*,
                        GROUP_CONCAT(
                            DISTINCT pc.name
                            ORDER BY pc.id
                            SEPARATOR ', '
                        ) AS category_names
                    FROM product AS p
                    LEFT JOIN product_categories AS pc
                        ON FIND_IN_SET(pc.id, p.category_id) > 0
                    WHERE p.id = ?
                    GROUP BY p.id"
                );

            mysqli_stmt_bind_param($stmt, "i", $pgid);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            $row = mysqli_fetch_assoc($result);

            // Get all product images
            $stmtImages = mysqli_prepare(
                $conn,
                "SELECT id, product_id, image, is_primary
                 FROM product_images
                 WHERE product_id = ?
                 ORDER BY is_primary DESC, id ASC"
            );

            mysqli_stmt_bind_param($stmtImages, "i", $pgid);
            mysqli_stmt_execute($stmtImages);

            $resultImages = mysqli_stmt_get_result($stmtImages);

            $images = [];

            while ($imageRow = mysqli_fetch_assoc($resultImages)) {
                $images[] = $imageRow;
            }

            mysqli_stmt_close($stmtImages);

            if(!empty($row)){
                $data['product'] = $row;
            }

            if(!empty($categories)){
                $data['categories'] = $categories;
            }

            if(!empty($images)){
                $data['images'] = $images;
            }

            if ($row) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Product fetched successfully',
                    'data' => $data
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product not found'
                ]);
            }
            mysqli_stmt_close($stmt);    
        }        
  } elseif ($pgAction === 'view' && $pgid) {
        $stmt = mysqli_prepare(
                $conn,
                "SELECT
                    p.*,
                    GROUP_CONCAT(
                        DISTINCT pi.image
                        ORDER BY pi.image
                        SEPARATOR ','
                    ) AS images,
                    GROUP_CONCAT(
                        DISTINCT pc.name
                        ORDER BY pc.id
                        SEPARATOR ', '
                    ) AS category_names
                FROM product AS p
                LEFT JOIN product_images AS pi
                    ON p.id = pi.product_id
                LEFT JOIN product_categories AS pc
                    ON FIND_IN_SET(pc.id, p.category_id) > 0
                WHERE p.id = ?
                GROUP BY p.id"
            );

        mysqli_stmt_bind_param($stmt, "i", $pgid);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Product fetched successfully',
                'data' => $row
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Product not found'
            ]);
        }
        mysqli_stmt_close($stmt);
  } elseif($pgAction === 'update' && $pgid) {
        $stmt = mysqli_prepare(
                $conn,
                "SELECT
                    p.*,
                    GROUP_CONCAT(
                        DISTINCT pi.image
                        ORDER BY pi.image
                        SEPARATOR ','
                    ) AS images,
                    GROUP_CONCAT(
                        DISTINCT pc.name
                        ORDER BY pc.id
                        SEPARATOR ', '
                    ) AS category_names
                FROM product AS p
                LEFT JOIN product_images AS pi
                    ON p.id = pi.product_id
                LEFT JOIN product_categories AS pc
                    ON FIND_IN_SET(pc.id, p.category_id) > 0
                WHERE p.id = ?
                GROUP BY p.id"
            );

        mysqli_stmt_bind_param($stmt, "i", $pgid);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        $productName = $_POST['productName'];
        $productSlug = $_POST['productSlug'];
        $productType = $_POST['productType'];
        $productMaterial = $_POST['productMaterial'];
        $productWeight = $_POST['productWeight'];
        $productDimension = $_POST['productDimension'];
        $productDesc = $_POST['productDesc'];
        $productStatus = $_POST['productStatus'];

        $productCategory = $_POST['productCategoryList'];        
        $productCategoryStr = "";
        if(!empty($productCategory)){
            $productCategoryStr = implode(",",$productCategory);
        }

        if((isset($_FILES['productImage']['name'])) && (!empty($_FILES['productImage']['name']))) {

            $imagePath = 'image/products/' . $row['images'];

            if (!empty($row['images']) && is_file($imagePath)) {
                unlink($imagePath);
            }

            $extension = strtolower(
                pathinfo($_FILES['productImage']['name'], PATHINFO_EXTENSION)
            );
            
            $productImage = bin2hex(random_bytes(4)) . '.' . $extension;
            $path = 'image/products/' . $_FILES['productImage']['name'];
            move_uploaded_file($_FILES['productImage']['tmp_name'],$path);
        } else {
            $productImage = $row['images'];
        }

        $updatedBy = (int) $_SESSION['login_id'];

        $sql = "SELECT * FROM product WHERE slug = ? AND id != ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "si", $productSlug, $pgid);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        if(!empty($row)){
            echo json_encode([
                'status' => 'error',
                'message' => 'Product slug already exist'
            ]);
        } else {
            $sql_update = "UPDATE `product` SET `name`='$productName',`slug`='$productSlug',`type`='$productType',`material`='$productMaterial',`weight`='$productWeight',`dimension`='$productDimension',`description`='$productDesc',`status`='$productStatus',`category_id`='$productCategoryStr',`updated_by` = ".$updatedBy.",`updated_at`= NOW() WHERE `id`='$pgid'";        
       
            mysqli_query($conn,$sql_update);

            $uploadedImages = uploadProductImages(
                $conn,
                $pgid
            );

            if ($sql_update) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Product Updated successfully.',
                    'data' => $row
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Product not found'
                ]);
            }
        }
  } else {
      echo json_encode([
          'status' => 'error',
          'message' => 'Invalid request'
      ]);
  }

  function uploadProductImages($conn, $productId) {

        //Delete product image records
        $stmt = mysqli_prepare(
        $conn,
            "DELETE FROM product_images WHERE product_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $productId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        if (empty($_FILES['productImages']['name'][0])) {
            return [];
        }

        $uploadDir = __DIR__ . '/image/products/';

        if (!is_dir($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                throw new RuntimeException('Cannot create upload directory');
            }
        }

        $allowedTypes = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp'
        ];

        $uploadedImages = [];
        $maxSize = 2 * 1024 * 1024;
        $finfo = new finfo(FILEINFO_MIME_TYPE);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO product_images
            (product_id, image, is_primary)
            VALUES (?, ?, ?)"
        );

        foreach ($_FILES['productImages']['name'] as $key => $name) {
            $tmpName = $_FILES['productImages']['tmp_name'][$key];
            $fileSize = $_FILES['productImages']['size'][$key];
            $error = $_FILES['productImages']['error'][$key];

            if ($error !== UPLOAD_ERR_OK) {
                continue;
            }

            if ($fileSize > $maxSize) {
                continue;
            }

            $mimeType = $finfo->file($tmpName);

            if (!isset($allowedTypes[$mimeType])) {
                continue;
            }

            $extension = $allowedTypes[$mimeType];

            $imageName = bin2hex(random_bytes(16))
                         . '.' . $extension;

            $imagePath = $uploadDir . $imageName;

            if (move_uploaded_file($tmpName, $imagePath)) {

                $isPrimary = 0;

                try {
                    mysqli_stmt_bind_param(
                        $stmt,
                        "isi",
                        $productId,
                        $imageName,
                        $isPrimary
                    );

                    if (!mysqli_stmt_execute($stmt)) {
                        throw new RuntimeException('Image insert failed');
                    }

                    $uploadedImages[] = $imageName;

                } catch (Throwable $e) {
                    unlink($imagePath);
                    throw $e;
                }
            }
        }

        mysqli_stmt_close($stmt);

        return $uploadedImages;
    }
?>