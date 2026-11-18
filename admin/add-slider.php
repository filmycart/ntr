<?php
    $sql_select = "select * from `slider` where `id`='$sid'";
    $data = mysqli_query($conn,$sql_select);
    $row = mysqli_fetch_assoc($data);

    if (isset($_POST['submit_slider'])){
      if (!empty($sid)) {
        $title = $_POST['title'];
        $details = $_POST['details'];
        $image = $_FILES['image']['name'];

        if ($image=="") {
            $image = $row['image'];
        } else {
            if (!empty($row['image']) && file_exists('image/' . $row['image'])) {
              unlink('image/' . $row['image']);
            }

            $image = rand(0,1000000).$_FILES['image']['name'];
            $path = 'image/'.$image;
            move_uploaded_file($_FILES['image']['tmp_name'],$path);
        }

        $sql_update = "update `slider` set `title`='$title',`details`='$details',`image`='$image' where `id`='$sid'";
        mysqli_query($conn,$sql_update);

        header('location:admin.php?pg-url=sliders&msg=2');
      } else {
        $title = $_POST['title'];
        $details = $_POST['details'];
        $image = rand(1,1000000).$_FILES['image']['name'];

        $path = 'image/'.$image;

        move_uploaded_file($_FILES['image']['tmp_name'], $path);

        $sql_insert = "insert into `slider`(`title`,`details`,`image`)values('$title','$details','$image')";
        
        mysqli_query($conn,$sql_insert);

        header('location:admin.php?pg-url=sliders&msg=1');
      }
    }
?>

  
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <!-- left column -->
      <div class="col-md-6">
        <!-- general form elements -->
        <div class="card card-primary">
          <div class="card-header">
            <h3 class="card-title"><?=$pgTitle?></h3>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" class="form-control" id="sId" name="sId" value="<?php echo @$row['id']; ?>">
            <div class="card-body">
              <div class="form-group">
                <label for="exampleInputEmail1">Title of New Slider</label>
                <input type="text" class="form-control" id="exampleInputEmail1" placeholder="Enter Title of New Slider" name="title" maxlength="20" required value="<?php echo @$row['title']; ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputPassword1">Details of New Slider</label>
                <input type="text" class="form-control" id="exampleInputPassword1" placeholder="Enter Some Details of New Slider" name="details" maxlength="50" required value="<?php echo @$row['details']; ?>">
              </div>
              <div class="form-group">
                <label for="exampleInputFile">Add Image of New Slider (With White Background)</label>
                <div class="input-group">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="exampleInputFile" name="image" required>
                    <label class="custom-file-label" for="exampleInputFile">Choose image</label>
                  </div>
                </div> 
              </div>
              <?php
                  if((isset($row['image'])) && (!empty($row['image']))){
              ?>
                    <label for="exampleInputFile">Current Image of Slider</label>
                    <div style="width: 200px; height: 200px;">
                        <img src="image/<?php echo $row['image']; ?>" style="height: 100%; width: 100%; object-fit: cover;">
                    </div>
              <?php
                }  
              ?>
            </div>
            <!-- /.card-body -->
       
            <div class="card-footer">
              <button type="submit" class="btn btn-primary" name="submit_slider">Submit</button>
            </div>
          </form>
        </div>
        <!-- /.card -->
            </form>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!--/.col (right) -->
    </div>
    <!-- /.row -->
  </div><!-- /.container-fluid -->
</section>
<!-- /.content -->