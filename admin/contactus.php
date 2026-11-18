<?php
  $sql_select = "select * from `contact_us`";
  $data = mysqli_query($conn,$sql_select);

  $update_data = "update `contact_us` set `status`='0' where `status`='1'";
  mysqli_query($conn,$update_data);

  //  $update_sql = "update contact set status=1 where status=0";
  // mysqli_query($conn,$update_sql);
?>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Contact Us</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <table id="example2" class="table table-bordered table-hover">
              <thead>
              <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Message of Inquiry</th>
              </tr>
              </thead>
              
              <?php while ($row = mysqli_fetch_assoc($data)) { ?>

               <tr>
                <td><?php echo $row['name']; ?></td>
                <td><?php echo $row['email']; ?></td>
                <td><?php echo $row['msg']; ?></td>
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
