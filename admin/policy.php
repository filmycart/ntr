<?php 
  $sql_select_story = "select * from `about`";
  $data_story = mysqli_query($conn,$sql_select_story);

  $sql_select_mission = "select * from `about`";
  $data_mission = mysqli_query($conn,$sql_select_mission);

  $sql_select_thought = "select * from `about`";
  $data_thought = mysqli_query($conn,$sql_select_thought);
?>
  <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">About Us</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body">
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>About</th>
                    <th>Image</th>
                    <th rowspan="3">Action</th>
                  </tr>
                  </thead>                  
                  <?php while ($row = mysqli_fetch_assoc($data_story)) { ?>
                   <tr>
                    <td><?php echo $row['story_detail']; ?></td>
                    <td align="center">
                        <div style="width: 200px; height: 150px;"><img src="image/<?php echo $row['story_image']; ?>" style="height: 100%; width: 100%; object-fit: cover;">
                        </div>
                    </td>
                    <td valign="center">
                      <!--<a href="edit-story.php?e_id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a>-->                      
                      <a href="edit-story.php?e_id=<?php echo $row['id']; ?>"
                         class="btn btn-primary btn-sm" title="Edit">
                          <i class="fas fa-edit"></i>
                      </a>
                      <!-- View -->
                      <!-- <a href="view-story.php?id=<?php echo $row['id']; ?>"
                         class="btn btn-info btn-sm" title="View">
                          <i class="fas fa-eye"></i>
                      </a> -->
                    </td>
                  </tr>
                  <?php } ?>
                </table>
                <div>&nbsp;</div>
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Mission</th>
                    <th>Image</th>
                    <th rowspan="3">Action</th>
                  </tr>
                  </thead>                  
                  <?php while ($row = mysqli_fetch_assoc($data_mission)) { ?>
                   <tr>
                    <td><?php echo $row['mission_detail']; ?></td>
                    <td align="center">
                         <div style="width: 200px; height: 150px;"><img src="image/<?php echo $row['mission_image']; ?>" style="height: 100%; width: 100%; object-fit: cover;"></td></div>
                    </td>
                    <td valign="center">
                      <!-- <a href="edit-mission.php?e_id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a> -->
                      <a href="edit-mission.php?e_id=<?php echo $row['id']; ?>"
                         class="btn btn-primary btn-sm" title="Edit">
                          <i class="fas fa-edit"></i>
                      </a>
                    </td>
                  </tr>

                  <?php } ?>
                </table>
                <br>
                <table id="example2" class="table table-bordered table-hover">
                  <thead>
                  <tr>
                    <th>Vision</th>
                    <th>Name</th>
                    <th rowspan="3">Action</th>
                  </tr>
                  </thead>                  
                  <?php while ($row = mysqli_fetch_assoc($data_thought)) { ?>
                   <tr>
                    <td>"<?php echo $row['best_thought']; ?>"</td>
                    <td>- <?php echo $row['thought_by']; ?></td>
                    <td valign="center">
                      <!-- <a href="edit-thought.php?e_id=<?php echo $row['id']; ?>" class="btn btn-primary">Edit</a> -->
                      <a href="edit-thought.php?e_id=<?php echo $row['id']; ?>"
                         class="btn btn-primary btn-sm" title="Edit">
                          <i class="fas fa-edit"></i>
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
