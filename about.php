<?php include_once 'site_connection.php'; ?>
<header class="header-v4">
<?php include_once 'header.php'; ?>
</header>
<?php
	$sql_select = "select * from `about`";
	$data = mysqli_query($conn,$sql_select);
	$row = mysqli_fetch_assoc($data);
 ?>
	<!-- Title page -->
	<section class="bg-img1 txt-center p-lr-15 p-tb-92" style="background-image: url('images/bg-01.jpg');">
		<h2 class="ltext-105 cl0 txt-center">
			About Us
		</h2>
	</section>
	<!-- Content page -->
	<section class="bg0 p-t-75 p-b-120">
		<div class="container">
			<div class="row p-b-148">
				<div class="col-md-12 col-lg-12">
					<div class="p-t-7 p-r-85 p-r-15-lg p-r-0-md">
						<h3 class="mtext-111 cl2 p-b-16">
							About Us
						</h3>
						<p class="stext-113 cl6 p-b-26">
							<?php echo $row['story_detail']; ?>
						</p>
					</div>
				</div>				
			</div>
		</div>
	</section>	

<?php include_once 'footer.php'; ?>


<?php include_once 'scripts.php'; ?>