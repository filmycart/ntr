<?php 

  $sql_select = "select * from `slider`";
  $data = mysqli_query($conn,$sql_select);

  if((!empty($pgSlAction)) && (!empty($sid)) && ($pgSlAction == 'delete')){
      $sql_select_img = "select * from `slider` where `id`='$sid'";
      $data_img = mysqli_query($conn,$sql_select_img);
      $row = mysqli_fetch_assoc($data_img);
      $delete_img = $row['image'];

      unlink('image/'.$delete_img);

      $sql_delete = "delete from `slider` where `id`='$sid'";
      mysqli_query($conn,$sql_delete);

      header('location:admin.php?pg-url=sliders&msg=3');
  }
?>
  <!-- Main content -->
  <section class="content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Sliders</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <?php
                if ($pgMsg == '1') {
              ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="icon fas fa-check-circle"></i>
                      <strong>Success!</strong> Slider Created Successfully.

                      <button type="button" class="close"
                              data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
              <?php
                } elseif ($pgMsg == '2') {
              ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="icon fas fa-check-circle"></i>
                      <strong>Success!</strong> Slider Updated Successfully.

                      <button type="button" class="close"
                              data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
              <?php    
                } elseif ($pgMsg == '3') {
              ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                      <i class="icon fas fa-check-circle"></i>
                      <strong>Success!</strong> Slider Deleted Successfully.

                      <button type="button" class="close"
                              data-dismiss="alert" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                      </button>
                  </div>
              <?php
                }
              ?>
              <table id="example2" class="table table-bordered table-hover">
                <thead>
                <tr>
                  <th>Titel of Slider</th>
                  <th>Details of Slider</th>
                  <th>Image</th>
                  <th>Action</th>
                </tr>
                </thead>
                
                <?php while ($row = mysqli_fetch_assoc($data)) { ?>

                 <tr>
                  <td><?php echo $row['title']; ?></td>
                  <td><?php echo $row['details']; ?></td>
                  <td align="center">
                       <div style="width: 250px; height: 200px;"><img src="image/<?php echo $row['image']; ?>" style="height: 100%; width: 100%; object-fit: cover;"></td></div>
                  </td>                  
                  <td class="text-center">
                      <!-- View -->
                      <a href="admin.php?pg-url=sliders&slAction=view&sid=<?php echo $row['id']; ?>"
                         class="btn btn-info btn-sm"
                         title="View">
                          <i class="fas fa-eye"></i>
                      </a>
                      <!-- Edit -->
                      <a href="admin.php?pg-url=add-slider&slAction=edit&sid=<?php echo $row['id']; ?>"
                         class="btn btn-primary btn-sm"
                         title="Edit">
                          <i class="fas fa-edit"></i>
                      </a>
                      <!-- Delete -->
                      <a href="admin.php?pg-url=sliders&slAction=delete&sid=<?php echo $row['id']; ?>"
                         class="btn btn-danger btn-sm"
                         title="Delete"
                         onclick="return confirm('Are you sure you want to delete this slider?');">
                          <i class="fas fa-trash-alt"></i>
                      </a>
                  </td>
                </tr>
                <?php } ?>
              </table>
            </div>
            <!-- /.card-body -->
          </div>
          <!-- /.card -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
  </section>
  <!-- /.content -->


