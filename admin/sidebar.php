<!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="admin.php?pg-url=home" class="brand-link">
      <img src="dist/img/AdminLTELogo.png" alt="NachiyarTRADERS Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
      <span class="brand-text font-weight-light">NachiyarTRADERS</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image">
          <img src="../images/01.jpg" class="img-circle elevation-2" alt="User Image">
        </div>
        <div class="info">
          <a href="#" class="d-block">NachiyarTRADERS</a>
        </div>
      </div>
      <!-- SidebarSearch Form -->
      <div class="form-inline">
        <div class="input-group" data-widget="sidebar-search">
          <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
          <div class="input-group-append">
            <button class="btn btn-sidebar">
              <i class="fas fa-search fa-fw"></i>
            </button>
          </div>
        </div>
      </div>
      <!-- Dashboard -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
          <li class="nav-item <?=$dashTreeMenu?>">
            <a href="admin.php?pg-url=home" class="nav-link <?=$dashboardActive;?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>
                Dashboard
              </p>
            </a>
          </li>
        </ul>
        <!-- Slider -->        
        <ul class="nav nav-pills nav-sidebar flex-column"
             data-widget="treeview"
             role="menu"
             data-accordion="false">
          <!-- Sliders -->
          <li class="nav-item <?= $slidersTreeMenu ?>">
              <a href="#" class="nav-link <?= $sliderParentActive ?>">
                  <i class="nav-icon fas fa-photo-video"></i>
                  <p>Sliders
                      <i class="right fas fa-angle-left"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview">
                  <!-- <li class="nav-item">
                      <a href="admin.php?pg-url=add-slider"
                         class="nav-link <?= $addSliderActive ?>">
                          <i class="nav-icon fas fa-plus-circle"></i>
                          <p>Add Slider</p>
                      </a>
                  </li> -->
                  <li class="nav-item">
                      <a href="admin.php?pg-url=sliders"
                         class="nav-link <?= $slidersActive ?>">
                          <i class="nav-icon fas fa-images"></i>
                          <p>Sliders</p>
                      </a>
                  </li>
              </ul>
          </li>
        </ul>
        <!-- Products -->        
        <ul class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
          <!-- Products -->
          <li class="nav-item <?=$productsTreeMenu ?>">
              <a href="#" class="nav-link <?= $productsActive ?>">
                  <i class="nav-icon fas fa-boxes"></i>
                  <p>Products
                      <i class="right fas fa-angle-left"></i>
                  </p>
              </a>
              <ul class="nav nav-treeview">
                  <li class="nav-item">
                      <a href="admin.php?pg-url=add-product"
                         class="nav-link <?= $addProductActive ?>">
                          <i class="nav-icon fas fa-plus-circle"></i>
                          <p>Add Product</p>
                      </a>
                  </li>
                  <li class="nav-item">
                      <a href="admin.php?pg-url=products"
                         class="nav-link <?= $viewProductActive ?>">
                          <i class="nav-icon fas fa-box-open"></i>
                          <p>Products</p>
                      </a>
                  </li>
              </ul>
          </li>
        </ul>
        <!-- Orders -->
        <ul class="nav nav-pills nav-sidebar flex-column"
             data-widget="treeview"
             role="menu"
             data-accordion="false">
            <li class="nav-item <?= $ordersTreeMenu ?>">
                <!-- Orders Parent Menu -->
                <a href="#" class="nav-link <?= $ordersActive ?>">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>
                        Orders
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <!-- Orders Submenus -->
                <ul class="nav nav-treeview">
                    <!-- New Orders -->
                    <li class="nav-item">
                        <a href="admin.php?pg-url=new-orders"
                           class="nav-link <?= $newOrdersActive ?>">
                            <i class="nav-icon fas fa-cart-plus"></i>
                            <p>New Orders</p>
                        </a>
                    </li>
                    <!-- Past Orders -->
                    <li class="nav-item">
                        <a href="admin.php?pg-url=all-orders"
                           class="nav-link <?= $allOrdersActive ?>">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Past Orders</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- CMS -->        
        <ul class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <li class="nav-item <?= $pageTreeMenu ?>">
                <!-- Pages Parent Menu -->
                <a href="#" class="nav-link <?= $pageActive ?>">
                    <i class="nav-icon fas fa-file-alt"></i>
                    <p>
                        Pages
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <!-- Pages Submenus -->
                <ul class="nav nav-treeview">
                    <!-- Manage About Us -->
                    <li class="nav-item">
                        <a href="admin.php?pg-url=about"
                           class="nav-link <?= $aboutActive ?>">
                            <i class="nav-icon fas fa-info-circle"></i>
                            <p>About Us</p>
                        </a>
                    </li>
                    <!-- Manage Policy -->
                    <li class="nav-item">
                        <a href="admin.php?pg-url=policy"
                           class="nav-link <?= $policyActive ?>">
                            <i class="nav-icon fas fa-shield-alt"></i>
                            <p>Policy</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- Contact Us -->
        <ul class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <li class="nav-item <?= $contactusTreeMenu ?>">
                <!-- Contact Us Parent Menu -->
                <a href="#" class="nav-link <?= $contactusActive ?>">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p>
                        Contact Us
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <!-- Contact Us Submenu -->
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="admin.php?pg-url=contactus"
                           class="nav-link <?= $contactusActive ?>">
                            <i class="far fa-circle nav-icon"></i>
                            <p>Contact Us</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- Blog -->        
        <ul class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <li class="nav-item <?= $blogTreeMenu ?>">
                <!-- Blog Parent Menu -->
                <a href="#" class="nav-link <?= $blogActive ?>">
                    <i class="nav-icon fas fa-blog"></i>
                    <p>
                        Blog
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <!-- Blog Submenus -->
                <ul class="nav nav-treeview">
                    <!-- Add Blog -->
                    <li class="nav-item">
                        <a href="admin.php?pg-url=add-blog"
                           class="nav-link <?= $addBlogActive ?>">
                            <i class="nav-icon fas fa-plus-circle"></i>
                            <p>Add Blog</p>
                        </a>
                    </li>
                    <!-- View/Manage Blogs -->
                    <li class="nav-item">
                        <a href="admin.php?pg-url=blogs"
                           class="nav-link <?= $viewBlogActive ?>">
                            <i class="nav-icon fas fa-newspaper"></i>
                            <p>Blogs</p>
                        </a>
                    </li>

                </ul>
            </li>
        </ul>
        <!-- Log-out -->        
        <ul class="nav nav-pills nav-sidebar flex-column"
            data-widget="treeview"
            role="menu"
            data-accordion="false">
            <li class="nav-item">
                <a href="admin.php?pg-url=logout" class="nav-link">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <p>Logout</p>
                </a>
            </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>