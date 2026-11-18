<?php
	$pgUrl = $_GET['pg-url'] ?? 'home';

	// Initialize all variables
	$dashTreeMenu = $dashboardActive = '';

	$slidersTreeMenu = $sliderParentActive = '';
	$addSliderActive = $slidersActive = '';

	$productsTreeMenu = $productsActive = '';
	$addProductActive = $viewProductActive = '';

	$ordersTreeMenu = $ordersActive = '';
	$newOrdersActive = $allOrdersActive = '';

	$pageTreeMenu = $pageActive = '';
	$aboutActive = $policyActive = '';

	$contactusTreeMenu = '';
	$contactusActive = '';

	$blogTreeMenu = $blogActive = '';
	$addBlogActive = $viewBlogActive = '';

	$pageName = $pgTitle = $pgSlAction = $pgMsg = '';

	$sid = '';
	// Switch Case
	switch ($pgUrl) {

	    // Dashboard
	    case 'home':
	        $pageName = 'dashboard.php';
	        $pgTitle = 'Dashboard';
	        $dashboardActive = 'active';
	        break;

	    // Sliders
	    case 'add-slider':
			if ((isset($_GET['sid'])) && (!empty($_GET['sid']))) {
		      $sid = $_GET['sid'];
		    } elseif ((isset($_POST['sId'])) && (!empty($_POST['sId']))) {
		      $sid = $_POST['sId'];
		    }

		    if ((isset($_GET['slAction'])) && (!empty($_GET['slAction']))) {
		      $pgSlAction = $_GET['slAction'];
		    } elseif ((isset($_POST['slAct'])) && (!empty($_POST['slAct']))) {
		      $pgSlAction = $_POST['slAct'];
		    }

	        $pageName = 'add-slider.php';
	        if (!empty($sid)) {
	        	$pgTitle = 'Edit Slider';
	        } else {
	        	$pgTitle = 'Add Slider';
	        }

	        $slidersTreeMenu = 'menu-open';
	        $sliderParentActive = 'active';
	        $addSliderActive = 'active';
	        break;

	    case 'sliders':
	    	if ((isset($_GET['sid'])) && (!empty($_GET['sid']))) {
		      $sid = $_GET['sid'];
		    } elseif ((isset($_POST['sId'])) && (!empty($_POST['sId']))) {
		      $sid = $_POST['sId'];
		    }

		    if ((isset($_GET['slAction'])) && (!empty($_GET['slAction']))) {
		      $pgSlAction = $_GET['slAction'];
		    }

		    if ((isset($_GET['msg'])) && (!empty($_GET['msg']))) {
		      $pgMsg = $_GET['msg'];
		    }

	        $pageName = 'view-slider.php';
	        $pgTitle = 'Sliders';
	        $slidersTreeMenu = 'menu-open';
	        $sliderParentActive = 'active';
	        $slidersActive = 'active';
	        break;

	    // Products
	    case 'add-product':
	        $pageName = 'add-product.php';
	        $pgTitle = 'Add Product';
	        $productsTreeMenu = 'menu-open';
	        $productsActive = 'active';
	        $addProductActive = 'active';
	        break;

	    case 'products':
	        $pageName = 'view-product.php';
	        $pgTitle = 'Products';
	        $productsTreeMenu = 'menu-open';
	        $productsActive = 'active';
	        $viewProductActive = 'active';
	        break;

	    // Orders
	    case 'new-orders':
	        $pageName = 'view-received-order.php';
	        $pgTitle = 'New Orders';
	        $ordersTreeMenu = 'menu-open';
	        $ordersActive = 'active';
	        $newOrdersActive = 'active';
	        break;

	    case 'all-orders':
	        $pageName = 'view-all-orders.php';
	        $pgTitle = 'Past Orders';
	        $ordersTreeMenu = 'menu-open';
	        $ordersActive = 'active';
	        $allOrdersActive = 'active';
	        break;

	    // Pages
	    case 'about':
	        $pageName = 'about.php';
	        $pgTitle = 'About Us';
	        $pageTreeMenu = 'menu-open';
	        $pageActive = 'active';
	        $aboutActive = 'active';
	        break;

	    case 'policy':
	        $pageName = 'policy.php';
	        $pgTitle = 'Policy';
	        $pageTreeMenu = 'menu-open';
	        $pageActive = 'active';
	        $policyActive = 'active';
	        break;

	    // Contact Us
	    case 'contactus':
	        $pageName = 'contactus.php';
	        $pgTitle = 'Contact Us';
	        $contactusActive = 'active';
	        break;

	    // Blogs
	    case 'add-blog':
	        $pageName = 'add-blog.php';
	        $pgTitle = 'Add Blog';
	        $blogTreeMenu = 'menu-open';
	        $blogActive = 'active';
	        $addBlogActive = 'active';
	        break;

	    case 'blogs':
	        $pageName = 'view-blog.php';
	        $pgTitle = 'Blogs';
	        $blogTreeMenu = 'menu-open';
	        $blogActive = 'active';
	        $viewBlogActive = 'active';
	        break;

	    case 'logout':
	        $pageName = 'logout.php';
	        break;

	    // Default Dashboard
	    default:
	        $pgUrl = 'home';
	        $pageName = 'dashboard.php';
	        $pgTitle = 'Dashboard';
	        $dashboardActive = 'active';
	        break;
	}

	/*echo $pageName;
	exit;*/
?>