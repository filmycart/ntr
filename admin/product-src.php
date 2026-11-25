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
    $productStatus = $_POST['productStatus'];

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

            $sql_insert_image = "INSERT INTO `product_images`
            (`product_id`,`image`, `status`, `created_by`, `created_at`)
            VALUES
            ('$productId','$productImage', '$productStatus', $createdBy, NOW())";

            if (mysqli_query($conn, $sql_insert_image)) {
                // Get last inserted product ID
                $productImgId = mysqli_insert_id($conn);
            } else {
                echo "Error: " . mysqli_error($conn);
            }
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
        "SELECT id, title, `desc`, status, image
         FROM slider
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $pgid);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $sliderTitle = $_POST['editSliderTitle'];
    $sliderDesc = $_POST['editSliderDesc'];
    $sliderStatus = $_POST['editSliderStatus'];
    $sliderImage = $_FILES['editSliderImage']['name'];

    if ($sliderImage=="") {
        $sliderImage = $row['image'];
    } else {
        $imagePath = 'image/products/' . $row['image'];

        if (!empty($row['image']) && is_file($imagePath)) {
            unlink($imagePath);
        }

        $extension = strtolower(
          pathinfo($_FILES['editSliderImage']['name'], PATHINFO_EXTENSION)
        );
        
        $sliderImage = bin2hex(random_bytes(4)) . '.' . $extension;
        $path = 'image/' . $sliderImage;
        move_uploaded_file($_FILES['editSliderImage']['tmp_name'],$path);
    }

    $sql_update = "UPDATE `slider` SET `title`='$sliderTitle',`desc`='$sliderDesc',`image`='$sliderImage',`status`='$sliderStatus',`updated_by` = ".$_SESSION['login_id'].",`updated_at`= NOW() where `id`='$pgid'";
    mysqli_query($conn,$sql_update);

    echo json_encode([
        'status' => 'success',
        'message' => 'Product fetched successfully.',
        'data' => $row
    ]);
  } else {
      echo json_encode([
          'status' => 'error',
          'message' => 'Invalid request'
      ]);
  }
?>