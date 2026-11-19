<?php
  header('Content-Type: application/json');

  require_once 'connection.php';

  $pgid = "";
  if ((isset($_GET['id'])) && (!empty($_GET['id']))) {
      $pgid = $_GET['id'];
  } elseif ((isset($_POST['sliderId'])) && (!empty($_POST['sliderId']))) {
      $pgid = $_POST['sliderId'];
  }

  $pgAction = "";
  if ((isset($_GET['action'])) && (!empty($_GET['action']))) {
      $pgAction = $_GET['action'];
  } elseif ((isset($_POST['sliderAction'])) && (!empty($_POST['sliderAction']))) {
      $pgAction = $_POST['sliderAction'];
  }

  if($pgAction === 'create') {
    $sliderTitle = $_POST['sliderTitle'];
    $sliderDesc = $_POST['sliderDesc'];
    $sliderStatus = $_POST['sliderStatus'];

    $extension = strtolower(
      pathinfo($_FILES['sliderImage']['name'], PATHINFO_EXTENSION)
    );    

    $sliderImage = bin2hex(random_bytes(4)) . '.' . $extension;

    $path = 'image/'.$sliderImage;

    move_uploaded_file($_FILES['sliderImage']['tmp_name'], $path);

    $createdBy = (int) $_SESSION['login_id'];

    $sql_insert = "INSERT INTO `slider`
    (`title`, `desc`, `image`, `status`, `created_by`, `created_at`)
    VALUES
    ('$sliderTitle', '$sliderDesc', '$sliderImage', '$sliderStatus', $createdBy, NOW())";
    mysqli_query($conn,$sql_insert);

    if ($sql_insert) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Slider Created successfully',
            'data' => $sql_insert
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Slider not found'
        ]);
    }
  }elseif ($pgAction === 'edit' && $pgid) {
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

    if ($row) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Slider fetched successfully',
            'data' => $row
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Slider not found'
        ]);
    }
    mysqli_stmt_close($stmt);
  } elseif ($pgAction === 'view' && $pgid) {
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

    if ($row) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Slider fetched successfully',
            'data' => $row
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Slider not found'
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
        $imagePath = 'image/' . $row['image'];

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
        'message' => 'Slider fetched successfully.',
        'data' => $row
    ]);
  } else {
      echo json_encode([
          'status' => 'error',
          'message' => 'Invalid request'
      ]);
  }
?>