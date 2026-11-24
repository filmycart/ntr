<?php 
  $sql_select = "select * from `slider`";
  $data = mysqli_query($conn,$sql_select);

  if((!empty($pgSlAction)) && (!empty($sid)) && ($pgSlAction == 'delete')){
      $sql_select_img = "select * from `slider` where `id`='$sid'";
      $data_img = mysqli_query($conn,$sql_select_img);
      $row = mysqli_fetch_assoc($data_img);
      $delete_img = $row['image'];

      $delimagePath = 'image/' . $delete_img;

      if (!empty($delete_img) && is_file($delimagePath)) {
        unlink($delimagePath);
      }

      $sql_delete = "delete from `slider` where `id`='$sid'";
      mysqli_query($conn,$sql_delete);

      header('location:admin.php?pg-url=sliders&msg=3');
  }
?>
  <!-- DataTables -->
  <link rel="stylesheet" href="plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
  <link rel="stylesheet" href="plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
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
                if ($pgMsg == 1) {
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
                } elseif ($pgMsg == 2) {
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
                } elseif ($pgMsg == 3) {
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
              <!-- /.modal -->
              <div class="modal fade" id="add-slider-modal">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title"><span id="add-slider-modal-title">Add Slider</span></h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>                        
                    <div class="modal-body">
                      <form name="frmSlider" id="frmSlider" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="sliderAction" id="sliderAction" value="create">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                  <label>Title</label>
                                  <input type="text" class="form-control" id="sliderTitle" name="sliderTitle">
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                  <label for="exampleInputFile">Image</label>
                                  <div class="input-group">
                                    <div class="custom-file">
                                      <input type="file" class="custom-file-input" id="sliderImage" name="sliderImage" accept="image/*"
                                        >
                                      <label class="custom-file-label" for="sliderImage">Choose image</label>
                                    </div>
                                  </div> 
                                </div>                                
                            </div>                            
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea rows="5" class="form-control" name="sliderDesc" id="sliderDesc"></textarea>
                                </div>
                            </div>
                        </div>                                                                                 
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label>Status</label>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="sliderStatus"
                                        id="sliderStatusActive"
                                        value="1">
                                    <label class="form-check-label">
                                        Active
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="sliderStatus"
                                        id="sliderStatusActive"
                                        value="2" checked>
                                    <label class="form-check-label">
                                        In-Active
                                    </label>
                                </div>
                            </div>
                          </div>
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                  <div class="input-group">
                                    <button type="submit" id="btnSubmit" name="btnSubmit" class="btn btn-primary">Submit</button>
                                  </div> 
                                </div>
                            </div>
                        </div>
                      </form>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->              
              <div class="modal fade" id="view-slider-modal">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title"><span id="view-slider-modal-title"></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>                        
                      <div class="modal-body">
                          <div class="row">
                              <div class="col-md-6 col-12">
                                  <div class="form-group">
                                      <label>ID</label>
                                      <div id="viewSliderId"></div>
                                  </div>
                              </div>
                              <div class="col-md-6 col-12">
                                  <div class="form-group">
                                      <label>Title</label>
                                      <p id="viewSliderTitle"></p>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6 col-12">
                                  <div class="form-group">
                                      <label>Description</label>
                                      <p id="viewSliderDesc"></p>
                                  </div>
                              </div>
                              <div class="col-md-6 col-12">
                                  <div class="form-group">
                                      <label>Status</label>                                      
                                      <p id="viewSliderStatus"></p>
                                  </div>
                              </div>
                          </div>

                          <div class="row">
                            <div class="col-md-12 col-12">
                              <div class="form-group">
                                <span id="viewSliderImageLink"></span>
                              </div>
                            </div>
                          </div>
                      </div>
                    </div>
                    <!-- /.modal-content -->
                  </div>
                  <!-- /.modal-dialog -->
              </div>
              <!-- /.modal -->
              <div class="modal fade" id="edit-slider-modal">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title"><span id="edit-slider-modal-title"></span></h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>                        
                    <div class="modal-body">
                      <form name="frmEditSlider" id="frmEditSlider" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="sliderAction" id="sliderAction" value="update">
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label>ID</label>
                                    <input type="text" class="form-control" name="editSliderId" id="editSliderId" disabled>
                                </div>
                            </div>
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                  <label>Title</label>
                                  <input type="text" class="form-control" id="editSliderTitle" name="editSliderTitle">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea rows="5" class="form-control" name="editSliderDesc" id="editSliderDesc"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                  <label for="exampleInputFile">Image</label>
                                  <div class="input-group"> 
                                    <div class="custom-file">
                                      <input type="file" class="custom-file-input" id="editSliderImage" name="editSliderImage" accept="image/*"
                                        >
                                      <label class="custom-file-label" for="editSliderImage">Choose image</label>
                                    </div>
                                  </div>
                                </div>                                
                            </div>
                            <div class="col-md-6 col-6">
                                <div class="form-group">
                                  <span id="editSliderImageLink"></span>
                                </div>
                            </div>                             
                        </div>
                        <div class="row">
                            <div class="col-md-6 col-12">
                                <div class="form-group">
                                    <label>Status</label>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="editSliderStatus"
                                            id="editSliderStatusActive"
                                            value="1">
                                        <label class="form-check-label">
                                            Active
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="radio"
                                            name="editSliderStatus"
                                            id="editSStatusActive"
                                            value="2">
                                        <label class="form-check-label">
                                            In-Active
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                  <div class="input-group">
                                    <button type="submit" id="btnUpdSubmit" name="btnUpdSubmit" class="btn btn-primary">Submit</button>
                                  </div> 
                                </div>
                            </div>
                        </div>
                      </form>
                    </div>
                  </div>
                  <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
              </div>
              <div class="row justify-content-end">
                <div class="col-md-2 col-12">
                  <div class="form-group">
                    <button type="button" class="btn btn-block btn-info btn-sm" data-toggle="modal" data-target="#add-slider-modal" title="Create Slider" onclick="addSlider('add')">Create Slider</button>
                  </div>
                </div>
              </div>
            <table id="sliderList" class="table table-bordered table-hover">
              <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Title</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Image</th>
                    <th class="text-center">Action</th>
                </tr>
              </thead>                
                <?php
                  $rows = mysqli_fetch_all($data, MYSQLI_ASSOC);
                  if (!empty($rows)) {
                      foreach ($rows as $row) {
                ?>                      
                   <tr>
                    <td class="text-center">
                      <div style="width:30px;">
                        <?=$row['id']?>
                      </div>
                    </td> 
                    <td class="text-center">
                      <div style="width:100px;">
                        <?=$row['title']?>
                      </div>
                    </td>  
                    <td class="text-center">
                      <div style="width:200px;">
                        <?=$row['desc']?>
                      </div>
                    </td>
                    <td class="text-center">
                         <img src="image/<?php echo $row['image']; ?>" style="height: 100%; width: 100%; object-fit: cover;">
                    </td>                  
                    <td class="text-center">
                        <div style="width:150px;">
                          <!-- View -->
                          <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#view-slider-modal" title="View" onclick="viewSlider('<?=$row['id']?>','view')">
                            <i class="fas fa-eye"></i>
                          </a>
                          <!-- Edit -->
                          <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#edit-slider-modal" title="Edit" onclick="editSlider('<?=$row['id']?>','edit')">
                            <i class="fas fa-edit"></i>
                          </a>
                          <!-- Delete -->
                          <a href="admin.php?pg-url=sliders&slAction=delete&sid=<?php echo $row['id']; ?>"
                             class="btn btn-danger btn-sm"
                             title="Delete"
                             onclick="return confirm('Are you sure you want to delete this slider?');">
                              <i class="fas fa-trash-alt"></i>
                          </a>
                        </div>  
                    </td>
                  </tr>
                <?php 
                    } 
                  } else {
                ?>
                    <tr>
                      <td colspan="5" align="center">
                        No Data Available   
                      </td>
                    </tr>  
                <?php    
                  }
                ?>
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
  <!-- jQuery -->
  <script src="plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- jquery-validation -->
  <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
  <script src="plugins/jquery-validation/additional-methods.min.js"></script>
  <!-- DataTables  & Plugins -->
  <script src="plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
  <script src="plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
  <script src="plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
  <script src="plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
  <script src="plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
  <script src="plugins/jszip/jszip.min.js"></script>
  <script src="plugins/pdfmake/pdfmake.min.js"></script>
  <script src="plugins/pdfmake/vfs_fonts.js"></script>
  <script src="plugins/datatables-buttons/js/buttons.html5.min.js"></script>
  <script src="plugins/datatables-buttons/js/buttons.print.min.js"></script>
  <script src="plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
  <!-- AdminLTE App -->
  <!-- <script src="dist/js/adminlte.min.js"></script> -->
  <!-- AdminLTE for demo purposes -->
  <script src="dist/js/demo.js"></script>
  <!-- Page specific script -->
  <script>
    $(function () {
      $("#example1").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');

      /*$('#sliderList').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false,
        "responsive": true
      });*/

      $("#sliderList").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    function viewSlider(sId, sAction) {
        $.ajax({
            url: 'slider-src.php',
            type: 'GET',
            dataType: 'json',
            data: {
                id: sId,
                action: sAction
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#viewSliderId').html(response.data.id);
                    $('#viewSliderTitle').html(response.data.title);
                    $('#viewSliderDesc').html(response.data.desc);
                      
                    var viewStatus = '';  
                    if(response.data.status == 1){
                      viewStatus = 'Active'
                    }else if(response.data.status == 2){
                      viewStatus = 'In-Active'
                    }

                    $('#viewSliderStatus').html(viewStatus);
                    $('#view-slider-modal-title').html("View "+response.data.title);

                    // Set current slider image
                    var sliderImage = response.data.image;

                    var imageLinkHtml = '';

                    if (sliderImage) {
                        var imagePath = window.location.origin + '/ntr/admin/image/' + sliderImage;
                        imageLinkHtml = '<label for="exampleInputFile">Image</label><div><a href="'+imagePath+'" target="_blank">'+sliderImage+'</a></div>';

                        $('#viewSliderImageLink').html(imageLinkHtml);
                    } else {
                        imageLinkHtml = '';
                        $('#viewSliderImageLink').html(imageLinkHtml);
                    }

                    //Reset file input
                    $('#sliderImage').val('');

                    $('.custom-file-label').text('Choose image');

                } else {
                    alert(response.message || 'Slider not found');
                }
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                alert('Something went wrong');
            }
        });
    }

    function editSlider(sId, sAction) {
        $.ajax({
            url: 'slider-src.php',
            type: 'GET',
            dataType: 'json',
            data: {
                id: sId,
                action: sAction
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#editSliderId').val(response.data.id);
                    $('#editSliderTitle').val(response.data.title);
                    $('#editSliderDesc').val(response.data.desc);

                    $('#edit-slider-modal-title').html(" "+response.data.title);

                    console.log("response",response);

                    // Set status radio button
                    var sliderStatus = response.data.status;

                    $('input[name="editSliderStatus"]')
                        .prop('checked', false);

                    $('input[name="editSliderStatus"][value="' + sliderStatus + '"]')
                        .prop('checked', true);

                    // Set current slider image
                    var sliderImage = response.data.image;

                    var imageLinkHtml = '';

                    if (sliderImage) {
                        var imagePath = window.location.origin + '/ntr/admin/image/' + sliderImage;
                        imageLinkHtml = '<label for="exampleInputFile">Current Image</label><div><a href="'+imagePath+'" target="_blank">'+sliderImage+'</a></div>';

                        $('#editSliderImageLink').html(imageLinkHtml);
                    } else {
                        imageLinkHtml = '';
                        $('#editSliderImageLink').html(imageLinkHtml);
                    }

                    //Reset file input
                    $('#editSliderImageLink').val('');

                    $('.custom-file-label').text('Choose image');

                } else {
                    alert(response.message || 'Slider not found');
                }
            },
            error: function (xhr) {
                alert('Something went wrong');
            }
        });
    }

    function addSlider(sAction) {

        var formData = new FormData(form);
        var submitBtn = $('#btnAddSubmit');

        //Disabled inputs are excluded from FormData
        formData.set('sliderTitle', $('#sliderTitle').val());
        formData.set('sliderDesc', $('#sliderDesc').val());
        formData.set('sliderStatus', $('#sliderStatus').val());
        formData.set('sliderFile', $('#sliderFile').val());

        $.ajax({
          url: 'slider-src.php',
          type: 'POST',
          data: formData,
          dataType: 'json',
          processData: false,
          contentType: false,
          beforeSend: function () {
              submitBtn.prop('disabled', true)
                       .text('Updating...');
          },
          success: function (response) {
              if (response.status === 'success') {
                  $('#add-slider-modal').modal('hide');

                  window.location.href =
                      'admin.php?pg-url=sliders&msg=1';

              } else {
                  alert(response.message || 'Update failed');
              }
          },
          complete: function () {
              submitBtn.prop('disabled', false)
                       .text('Submit');
          }
      });
    }

    $(document).ready(function () {
        // Initialize validation once
        $('#frmSlider').validate({
            rules: {
                sliderTitle: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                sliderDesc: {
                    required: true,
                    minlength: 10,
                    maxlength: 500
                },
                sliderImage: {
                    required: true,
                    extension: "jpg|jpeg|png|webp"
                }
            },
            messages: {
                sliderTitle: {
                    required: "Please enter slider title",
                    minlength: "Minimum 3 characters required",
                    maxlength: "Maximum 100 characters allowed"
                },
                sliderDesc: {
                    required: "Please enter slider description",
                    minlength: "Minimum 10 characters required",
                    maxlength: "Maximum 500 characters allowed"
                },
                sliderImage: {
                    required: "Please select an image",
                    extension: "Only JPG, JPEG, PNG and WEBP images allowed"
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.attr('type') === 'file') {
                    error.insertAfter(
                        element.closest('.input-group')
                    );
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form, event) {
                if (event) {
                    event.preventDefault();
                }

                var formData = new FormData(form);
                var submitBtn = $('#btnSubmit');

                //Disabled inputs are excluded from FormData
                formData.set('sliderId', $('#sliderId').val());

                $.ajax({
                    url: 'slider-src.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        submitBtn.prop('disabled', true)
                                 .text('Updating...');
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#edit-slider-modal').modal('hide');

                            window.location.href =
                                'admin.php?pg-url=sliders&msg=2';

                        } else {
                            alert(response.message || 'Update failed');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false)
                                 .text('Submit');
                    }
                });
                return false;
            }
        });

        // Initialize validation once
        $('#frmEditSlider').validate({
            rules: {
                editSliderTitle: {
                    required: true,
                    minlength: 3,
                    maxlength: 100
                },
                editSliderDesc: {
                    required: true,
                    minlength: 10,
                    maxlength: 500
                },
                editSliderImage: {
                    //required: true,
                    extension: "jpg|jpeg|png|webp"
                }
            },
            messages: {
                editSliderTitle: {
                    required: "Please enter slider title",
                    minlength: "Minimum 3 characters required",
                    maxlength: "Maximum 100 characters allowed"
                },
                editSliderDesc: {
                    required: "Please enter slider description",
                    minlength: "Minimum 10 characters required",
                    maxlength: "Maximum 500 characters allowed"
                },
                editSliderImage: {
                    //required: "Please select an image",
                    extension: "Only JPG, JPEG, PNG and WEBP images allowed"
                }
            },
            errorElement: 'span',
            errorPlacement: function (error, element) {
                error.addClass('invalid-feedback');
                if (element.attr('type') === 'file') {
                    error.insertAfter(
                        element.closest('.input-group')
                    );
                } else {
                    error.insertAfter(element);
                }
            },
            highlight: function (element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function (element) {
                $(element).removeClass('is-invalid');
            },
            submitHandler: function (form, event) {
                if (event) {
                    event.preventDefault();
                }

                var formData = new FormData(form);
                var submitBtn = $('#btnSubmit');

                //Disabled inputs are excluded from FormData
                formData.set('sliderId', $('#editSliderId').val());

                $.ajax({
                    url: 'slider-src.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        submitBtn.prop('disabled', true)
                                 .text('Updating...');
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#edit-slider-modal').modal('hide');

                            window.location.href =
                                'admin.php?pg-url=sliders&msg=2';

                        } else {
                            alert(response.message || 'Update failed');
                        }
                    },
                    complete: function () {
                        submitBtn.prop('disabled', false)
                                 .text('Submit');
                    }
                });
                return false;
            }
        });

        // Display selected image filename
        $('#sliderImage').on('change', function () {
            var filename = this.files.length
                ? this.files[0].name
                : 'Choose image';

            $(this).next('.custom-file-label').text(filename);
        });
    });
  </script>

