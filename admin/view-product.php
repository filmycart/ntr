<?php
  mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

  if (
      !empty($pgPrAction) &&
      !empty($pid) &&
      $pgPrAction === 'delete'
  ) {
      $pid = (int) $pid;
      $images = [];

      mysqli_begin_transaction($conn);

      try {
          // 1. Get all product images
          $stmt = mysqli_prepare(
              $conn,
              "SELECT image FROM product_images WHERE product_id = ?"
          );

          mysqli_stmt_bind_param($stmt, "i", $pid);
          mysqli_stmt_execute($stmt);

          $result = mysqli_stmt_get_result($stmt);

          while ($row = mysqli_fetch_assoc($result)) {
              $images[] = $row['image'];
          }

          mysqli_stmt_close($stmt);
          // 2. Delete product image records
          $stmt = mysqli_prepare(
              $conn,
              "DELETE FROM product_images WHERE product_id = ?"
          );

          mysqli_stmt_bind_param($stmt, "i", $pid);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);

          // 3. Delete product record
          $stmt = mysqli_prepare(
              $conn,
              "DELETE FROM product WHERE id = ?"
          );

          mysqli_stmt_bind_param($stmt, "i", $pid);
          mysqli_stmt_execute($stmt);
          mysqli_stmt_close($stmt);

          // 4. Commit database transaction
          mysqli_commit($conn);
      } catch (Throwable $e) {
          mysqli_rollback($conn);
          error_log($e->getMessage());
          exit('Unable to delete product');
      }

      // 5. Delete physical image files
      foreach ($images as $image) {
          if (empty($image)) {
              continue;
          }

          $delimagePath = __DIR__ . '/image/products/' . basename($image);

          if (is_file($delimagePath)) {
              if (!unlink($delimagePath)) {
                  error_log('Unable to delete image: ' . $delimagePath);
              }
          }
      }

      // 6. Redirect
      header('Location: admin.php?pg-url=products&msg=3');
      exit;
  }

  // Fetch all products
  $sql_select = "SELECT * FROM `product` ORDER BY id DESC";
  $data = mysqli_query($conn, $sql_select);
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
              <h3 class="card-title">Products</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
              <?php
                if ($pgMsg == 1) {
              ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="icon fas fa-check-circle"></i>
                        <strong>Success!</strong>Product Created Successfully.

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
                        <strong>Success!</strong>Product Updated Successfully.
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
                        <strong>Success!</strong>Product Deleted Successfully.
                        <button type="button" class="close"
                                data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
              <?php
                }
              ?>
              <!-- /.modal -->
              <div class="modal fade" id="add-product-modal">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title"><span id="add-product-modal-title">Add Product</span></h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>                        
                    <div class="modal-body">
                      <form name="frmProduct" id="frmProduct" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="productAction" id="productAction" value="create">
                        <div class="row">
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Name</label>
                                  <input type="text" class="form-control" id="productName" name="productName">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Slug</label>
                                  <input type="text" class="form-control" id="productSlug" name="productSlug">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Type</label>
                                  <input type="text" class="form-control" id="productType" name="productType">
                                </div>
                            </div>                                                    
                        </div>
                        <div class="row">    
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Material</label>
                                  <input type="text" class="form-control" id="productMaterial" name="productMaterial">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Weight</label>
                                  <input type="text" class="form-control" id="productWeight" name="productWeight">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Dimension</label>
                                  <input type="text" class="form-control" id="productDimension" name="productDimension">
                                </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-4 col-4">
                              <div class="form-group">
                                  <label>Category</label>
                                  <div id="productCategoryList">
                                      <span class="text-muted">
                                          Loading Category...
                                      </span>
                                  </div>
                                  <span id="productCategoryError" class="text-danger"></span>
                              </div>
                            </div>
                            <div class="col-md-4 col-4">
                              <div class="form-group">
                                <label for="exampleInputFile">Image</label>
                                  <div class="input-group">
                                    <div class="custom-file">
                                      <input type="file" class="custom-file-input" id="productImage" name="productImage" accept="image/*">
                                      <label class="custom-file-label" for="productImage">Choose Image</label>
                                    </div>
                                  </div> 
                                </div>
                            </div> 
                            <div class="col-md-4 col-4">
                              <div class="form-group">
                                  <label class="d-block">Status</label>
                                  <div class="form-check form-check-inline">
                                      <input
                                          class="form-check-input"
                                          type="radio"
                                          name="productStatus"
                                          id="productStatusActive"
                                          value="1">
                                      <label class="form-check-label" for="productStatusActive">
                                          Active
                                      </label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input
                                          class="form-check-input"
                                          type="radio"
                                          name="productStatus"
                                          id="productStatusInactive"
                                          value="2"
                                          checked>
                                      <label class="form-check-label" for="productStatusInactive">
                                          In-Active
                                      </label>
                                  </div>
                              </div>
                          </div>                                               
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea rows="5" class="form-control" name="productDesc" id="productDesc"></textarea>
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
              <div class="modal fade" id="view-product-modal">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h4 class="modal-title"><span id="view-product-modal-title"></span></h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>                        
                      <div class="modal-body">
                          <div class="row">
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>ID</label>
                                      <div id="viewProductId"></div>
                                  </div>
                              </div>
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Name</label>
                                      <p id="viewProductName"></p>
                                  </div>
                              </div>
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Slug</label>
                                      <p id="viewProductSlug"></p>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Type</label>
                                      <div id="viewProductType"></div>
                                  </div>
                              </div>
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Material</label>
                                      <p id="viewProductMaterial"></p>
                                  </div>
                              </div>
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Weight</label>
                                      <p id="viewProductWeight"></p>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Dimension</label>
                                      <div id="viewProductDimension"></div>
                                  </div>
                              </div>
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Image</label>
                                      <span id="viewProductImageLink"></span>
                                  </div>
                              </div>
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Category</label>
                                      <p id="viewProductCategory"></p>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Status</label>
                                      <p id="viewProductStatus"></p>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-4 col-4">
                                  <div class="form-group">
                                      <label>Description</label>
                                      <div id="viewProductDesc"></div>
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
              <div class="modal fade" id="edit-product-modal">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h4 class="modal-title"><span id="edit-product-modal-title"></span></h4>
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                      </button>
                    </div>                        
                    <div class="modal-body">
                      <form name="frmEditProduct" id="frmEditProduct" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="productAction" id="productAction" value="update">
                        <div class="row">
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                    <label>ID</label>
                                    <input type="text" class="form-control" name="editProductId" id="editProductId" disabled>
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Name</label>
                                  <input type="text" class="form-control" id="editProductName" name="editProductName">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                    <label>Slug</label>
                                    <input type="text" class="form-control" name="editProductSlug" id="editProductSlug">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Type</label>
                                  <input type="text" class="form-control" id="editProductType" name="editProductType">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Material</label>
                                  <input type="text" class="form-control" id="editProductMaterial" name="editProductMaterial">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Weight</label>
                                  <input type="text" class="form-control" id="editProductWeight" name="editProductWeight">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                  <label>Dimension</label>
                                  <input type="text" class="form-control" id="editProductDimension" name="editProductDimension">
                                </div>
                            </div>
                            <div class="col-md-4 col-4">
                              <div class="form-group">
                                  <label>Category</label>
                                  <div id="editProductCategoryList">
                                      <span class="text-muted">
                                          Loading Category...
                                      </span>
                                  </div>
                                  <span id="editProductCategoryError" class="text-danger"></span>
                              </div>
                            </div>
                            <div class="col-md-4 col-4">
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
                            <div class="col-md-4 col-4">
                                <div class="form-group">
                                   <span id="editViewProductImageLink"></span>
                                </div>
                            </div>                                                       
                        </div>
                        <div class="row">
                          <div class="col-md-4 col-4">
                              <div class="form-group">
                                  <label class="d-block">Status</label>
                                  <div class="form-check form-check-inline">
                                      <input
                                          class="form-check-input"
                                          type="radio"
                                          name="editProductStatus"
                                          id="editProductStatusActive"
                                          value="1">
                                      <label class="form-check-label" for="productStatusActive">
                                          Active
                                      </label>
                                  </div>
                                  <div class="form-check form-check-inline">
                                      <input
                                          class="form-check-input"
                                          type="radio"
                                          name="editProductStatus"
                                          id="editProductStatusInactive"
                                          value="2"
                                          checked>
                                      <label class="form-check-label" for="productStatusInactive">
                                          In-Active
                                      </label>
                                  </div>
                              </div>
                            </div>
                        </div>  
                        <div class="row">                            
                            <div class="col-md-12 col-12">
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea rows="5" class="form-control" name="editProductDesc" id="editProductDesc"></textarea>
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
                    <button type="button" class="btn btn-block btn-info btn-sm" data-toggle="modal" data-target="#add-product-modal" title="Create Product" onclick="addProduct('add')">Create Product</button>
                  </div>
                </div>
              </div>
            <table id="productList" class="table table-bordered table-hover">
              <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Name</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Slug</th>
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
                        <?=$row['name']?>
                      </div>
                    </td>  
                    <td class="text-center">
                      <div style="width:200px;">
                        <?=$row['description']?>
                      </div>
                    </td>
                    <td class="text-center">
                        <div style="width:200px;">
                          <?=$row['slug']?>
                        </div>
                    </td>                  
                    <td class="text-center">
                        <div style="width:150px;">
                          <!-- View -->
                          <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#view-product-modal" title="View" onclick="viewProduct('<?=$row['id']?>','view')">
                            <i class="fas fa-eye"></i>
                          </a>
                          <!-- Edit -->
                          <a href="#" class="btn btn-info btn-sm" data-toggle="modal" data-target="#edit-product-modal" title="Edit" onclick="editProduct('<?=$row['id']?>','edit')">
                            <i class="fas fa-edit"></i>
                          </a>
                          <!-- Delete -->
                          <a href="admin.php?pg-url=products&prAction=delete&pid=<?=$row['id'];?>"
                             class="btn btn-danger btn-sm"
                             title="Delete"
                             onclick="return confirm('Are you sure you want to delete this product?');">
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

      $("#productList").DataTable({
        "responsive": true, "lengthChange": false, "autoWidth": false,
        "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
      }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    });

    function viewProduct(pId, pAction) {
        $.ajax({
            url: 'product-src.php',
            type: 'GET',
            dataType: 'json',
            data: {
                id: pId,
                action: pAction
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#viewProductId').html(response.data.id);
                    $('#viewProductName').html(response.data.name);
                    $('#viewProductSlug').html(response.data.slug);
                    $('#viewProductType').html(response.data.type);
                    $('#viewProductMaterial').html(response.data.material);
                    $('#viewProductWeight').html(response.data.weight);
                    $('#viewProductDimension').html(response.data.dimension);
                    $('#viewProductDesc').html(response.data.description);
                    $('#viewProductCategory').html(response.data.category_names);
                      
                    var viewStatus = '';  
                    if(response.data.status == 1){
                      viewStatus = 'Active'
                    }else if(response.data.status == 2){
                      viewStatus = 'In-Active'
                    }

                    $('#viewProductStatus').html(viewStatus);
                    $('#view-product-modal-title').html(response.data.name);

                    // Set current product image
                    var productImage = response.data.image;

                    var imageLinkHtml = '';

                    if (productImage) {
                        var imagePath = window.location.origin + '/ntr/admin/image/products/' + productImage;
                        imageLinkHtml = '<label for="exampleInputFile">Image</label><div><a href="'+imagePath+'" target="_blank">'+productImage+'</a></div>';

                        $('#viewProductImageLink').html(imageLinkHtml);
                    } else {
                        imageLinkHtml = '';
                        $('#viewProductImageLink').html(imageLinkHtml);
                    }

                    //Reset file input
                    $('#productImage').val('');

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

    function editProduct(pId, pAction) {

      if (pAction === 'edit') {
        $.ajax({
            url: 'product-src.php',
            type: 'GET',
            dataType: 'json',
            data: {
                id: pId,
                action: pAction
            },
            beforeSend: function () {
                $('#editProductCategoryList').html(
                    '<span>Loading categories...</span>'
                );
            },
            success: function (response) {

                if (response.status !== 'success') {
                    alert(response.message || 'Product not found');
                    return;
                }

                const product = response.data.product;
                const categories = response.data.categories || [];

                // Set product details
                $('#editProductId').val(product.id);
                $('#editProductName').val(product.name);
                $('#editProductDesc').val(product.description);
                $('#editProductSlug').val(product.slug);
                $('#editProductType').val(product.type);
                $('#editProductMaterial').val(product.material);
                $('#editProductWeight').val(product.weight);
                $('#editProductDimension').val(product.dimension);
                $('#editProductCategory').text(product.category_names);

                // Set modal title
                $('#edit-product-modal-title').text(' ' + product.name);

                // Get selected category IDs
                const selectedCategories = String(product.category_id || '')
                    .split(',')
                    .map(id => id.trim())
                    .filter(Boolean);

                // Load category checkboxes
                const container = $('#editProductCategoryList');

                container.empty();
                if (categories.length === 0) {
                    container.text('No categories available');
                } else {
                    $.each(categories, function (index, category) {
                        const checkboxId =
                            'editProductCategory_' + category.id;

                        const wrapper = $('<div>', {
                            class: 'custom-control custom-checkbox mb-2'
                        });

                        const checkbox = $('<input>', {
                            type: 'checkbox',
                            class: 'custom-control-input',
                            id: checkboxId,
                            name: 'editProductCategory[]',
                            value: category.id
                        });

                        // Check already selected categories
                        checkbox.prop(
                            'checked',
                            selectedCategories.includes(String(category.id))
                        );

                        const label = $('<label>', {
                            class: 'custom-control-label',
                            for: checkboxId
                        }).text(category.name);

                        wrapper.append(checkbox, label);

                        container.append(wrapper);
                    });
                }

                // Set current product image
                const imageContainer = $('#editViewProductImageLink');

                imageContainer.empty();

                if (product.image) {

                    const imagePath =
                        'image/products/' +
                        encodeURIComponent(product.image);

                    const imageLabel = $('<label>').text('Image');

                    const imageLink = $('<a>', {
                        href: imagePath,
                        target: '_blank',
                        rel: 'noopener noreferrer'
                    }).text(product.image);

                    imageContainer.append(
                        imageLabel,
                        $('<div>').append(imageLink)
                    );
                }

                // Set product status
                $('input[name="editProductStatus"]')
                    .prop('checked', false)
                    .filter(function () {
                        return this.value === String(product.status);
                    })
                    .prop('checked', true);

                // Open edit product modal
                $('#edit-product-modal').modal('show');
            },
            error: function (xhr, status, error) {

                $('#editProductCategoryList').text(
                    'Unable to load categories'
                );

                console.error('AJAX Error:', error);

                alert('Something went wrong');
            }
        });
      }
  }

    function addProduct(sAction) {
      if (sAction === 'add') {
        $.ajax({
            url: 'product-src.php',
            type: 'GET',
            dataType: 'json',
            data: {
                action: 'add'
            },
            beforeSend: function () {
                $('#productCategoryList').html(
                    '<span>Loading categories...</span>'
                );
            },
            success: function (response) {
                if (response.status === 'success') {
                    const categories = response.data || [];
                    const container = $('#productCategoryList');

                    container.empty();

                    if (categories.length === 0) {
                        container.text('No categories available');
                    } else {
                        $.each(categories, function (index, category) {
                            const checkboxId =
                                'productCategory_' + category.id;

                            const wrapper = $('<div>', {
                                class: 'custom-control custom-checkbox mb-2'
                            });

                            const checkbox = $('<input>',{
                                type: 'checkbox',
                                class: 'custom-control-input',
                                id: checkboxId,
                                name: 'productCategory[]',
                                value: category.id
                            });

                            const label = $('<label>',{
                                class: 'custom-control-label',
                                for: checkboxId
                            }).text(category.name);

                            wrapper.append(checkbox, label);

                            container.append(wrapper);
                        });
                    }
                    $('#add-product-modal').modal('show');
                } else {
                    $('#productCategoryList').text(
                        response.message || 'Unable to load categories'
                    );
                }
            },
            error: function (xhr, status, error) {
                $('#productCategoryList').text(
                    'Unable to load categories'
                );
            }
        });
        return;
      }   

      if(sAction == 'create'){
          // Get form element
          var form = $('#frmAddProduct')[0];
          if (!form) {
              console.error('Product form not found');
              return;
          }

          var formData = new FormData(form);
          var submitBtn = $('#btnAddSubmit');

          //Disabled inputs are excluded from FormData
          formData.set('productName', $('#productName').val());
          formData.set('productDescription', $('#productDescription').val());
          formData.set('productStatus', $('#productStatus').val());
          //formData.set('sliderFile', $('#sliderFile').val());

          $.ajax({
            url: 'product-src.php',
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
    }

    $(document).ready(function () {
        //Custom category checkbox validation
        $.validator.addMethod(
            'minCategory',
            function (value, element, min) {
                return $(element.form)
                    .find('input[name="productCategory[]"]:checked')
                    .length >= min;
            },
            'Please select at least one category'
        );

        //Initialize validation
        var validator = $('#frmProduct').validate({
        rules: {
          productName: {
              required: true,
              minlength: 3,
              maxlength: 100
          },
          productSlug: {
              required: true,
              minlength: 3,
              maxlength: 30
          },
          productType: {
              required: true,
              minlength: 3,
              maxlength: 30
          },
          productMaterial: {
              required: true,
              minlength: 3,
              maxlength: 30
          },
          productWeight: {
              required: true,
              minlength: 2,
              maxlength: 5,
              number: true
          },
          productDimension: {
              required: true,
              minlength: 2,
              maxlength: 5
          },
          productDesc: {
              required: true,
              minlength: 10,
              maxlength: 500
          },
          'productCategory[]': {
            required: true,
            minCategory: 1
          },
          productImage: {
            required: true,
            extension: "jpg|jpeg|png|webp"
          }
        },
        messages: {
          productName: {
              required: "Please enter name",
              minlength: "Minimum 3 characters required",
              maxlength: "Maximum 100 characters allowed"
          },
          productSlug: {
              required: "Please enter slug",
              minlength: "Minimum 3 characters required",
              maxlength: "Maximum 30 characters allowed"
          },
          productType: {
              required: "Please enter type",
              minlength: "Minimum 3 characters required",
              maxlength: "Maximum 10 characters allowed"
          },
          productMaterial: {
              required: "Please enter material",
              minlength: "Minimum 3 characters required",
              maxlength: "Maximum 30 characters allowed"
          },
          productWeight: {
              required: "Please enter weight",
              minlength: "Minimum 2 characters required",
              maxlength: "Maximum 5 characters allowed",
              number: 'Please enter only number'
          },
          productDimension: {
              required: "Please enter dimension",
              minlength: "Minimum 2 characters required",
              maxlength: "Maximum 5 characters allowed"
          },
          productDesc: {
              required: "Please enter description",
              minlength: "Minimum 10 characters required",
              maxlength: "Maximum 500 characters allowed"
          },
          'productCategory[]': {
            required: 'Please select at least one category',
            minCategory: 'Please select at least one category'
          },
          productImage: {
            required: "Please select an image",
            extension: "Only JPG, JPEG, PNG and WEBP images allowed"
          }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
            if (element.attr('name') === 'productCategory[]') {
                error.addClass('text-danger');
                error.appendTo('#productCategoryError');
            } else if (element.attr('type') === 'file') {
                error.addClass('invalid-feedback');
                error.insertAfter(element.closest('.input-group'));
            } else {
                error.addClass('invalid-feedback');
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

            // Count selected categories
            var categoryCount = $(form)
                .find('input[name="productCategory[]"]:checked')
                .length;

            // Additional validation if categories are not loaded
            if (categoryCount === 0) {
                $('#productCategoryError').text(
                    'Please select at least one category'
                );
                return false;
            }

            var formData = new FormData(form);
            var submitBtn = $('#btnSubmit');

            $.ajax({
                url: 'product-src.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function () {
                    submitBtn
                        .prop('disabled', true)
                        .text('Creating...');
                },
                success: function (response) {
                    if (response.status === 'success') {
                        $('#add-product-modal').modal('hide');
                        window.location.href =
                            'admin.php?pg-url=products&msg=1';
                    } else {
                        alert(
                            response.message || 'Product creation failed'
                        );
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Something went wrong');
                },
                complete: function () {
                    submitBtn
                        .prop('disabled', false)
                        .text('Submit');
                }
            });

            return false;
          }
        });

        // Validate dynamically generated category checkboxes
        $('#frmProduct').on(
            'change',
            'input[name="productCategory[]"]',
            function () {
                validator.element(this);
            }
        );

        // Reset validation when modal closes
        $('#add-product-modal').on('hidden.bs.modal', function () {
            validator.resetForm();
            $('#frmProduct').find('.is-invalid')
                .removeClass('is-invalid');
            $('#productCategoryError').empty();
        });  

        // Display selected image filename
        $('#productImage').on('change', function () {
            var filename = this.files.length
                ? this.files[0].name
                : 'Choose Image';
            $(this).next('.custom-file-label').text(filename);
        });      

        //Initialize validation once        
        $(document).ready(function () {
            $('#frmEditProduct').validate({
                rules: {
                    editProductName: {
                        required: true,
                        minlength: 3,
                        maxlength: 100
                    },
                    editProductSlug: {
                        required: true,
                        minlength: 3,
                        maxlength: 30
                    },
                    productMaterial: {
                        required: true,
                        minlength: 3,
                        maxlength: 10
                    },
                    productWeight: {
                        required: true,
                        minlength: 2,
                        maxlength: 5
                    },
                    editProductDesc: {
                        required: true,
                        minlength: 10,
                        maxlength: 500
                    },
                },
                messages: {
                    editProductName: {
                        required: "Please enter name",
                        minlength: "Minimum 3 characters required",
                        maxlength: "Maximum 100 characters allowed"
                    },
                    editProductSlug: {
                        required: "Please enter slug",
                        minlength: "Minimum 3 characters required",
                        maxlength: "Maximum 30 characters allowed"
                    },
                    productMaterial: {
                        required: "Please enter material",
                        minlength: "Minimum 3 characters required",
                        maxlength: "Maximum 10 characters allowed"
                    },
                    productWeight: {
                        required: "Please enter weight",
                        minlength: "Minimum 2 characters required",
                        maxlength: "Maximum 5 characters allowed"
                    },
                    editProductDesc: {
                        required: "Please enter description",
                        minlength: "Minimum 10 characters required",
                        maxlength: "Maximum 500 characters allowed"
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

                    // Use the submit button inside this form
                    var submitBtn = $(form).find(
                        'button[type="submit"]'
                    );

                    var originalText = submitBtn.text();

                    // Include product ID
                    formData.set(
                        'productId',
                        $('#editProductId').val()
                    );

                    // Set the update action expected by PHP
                    // formData.set('action', 'update');

                    $.ajax({
                        url: 'product-src.php',
                        type: 'POST',
                        data: formData,
                        dataType: 'json',
                        processData: false,
                        contentType: false,

                        beforeSend: function () {
                            submitBtn
                                .prop('disabled', true)
                                .text('Updating...');
                        },

                        success: function (response) {

                            if (response.status === 'success') {

                                $('#edit-product-modal').modal('hide');

                                window.location.href =
                                    'admin.php?pg-url=catalog&table=product&msg=2';

                            } else {
                                alert(
                                    response.message || 'Update failed'
                                );
                            }
                        },

                        error: function (xhr) {
                            console.error(
                                'Product update error:',
                                xhr.responseText
                            );

                            alert(
                                'Something went wrong while updating product.'
                            );
                        },

                        complete: function () {
                            submitBtn
                                .prop('disabled', false)
                                .text(originalText);
                        }
                    });

                    return false;
                }
            });

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