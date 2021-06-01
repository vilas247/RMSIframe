<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<?php
/**
	* Initial Page
	* Author 247Commerce
	* Date 22 FEB 2021
*/
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once('config.php');
require_once('db-config.php');
require_once('hooks.php');

/*creating DB connection */
$conn = getConnection();
$email_id = '';
if(isset($_SESSION['is247Email'])){
	$email_id = $_SESSION['is247Email'];
}

/* check zoovu token is validated or not 
	If already Verified redirect to Home Page
*/
if(!empty($email_id)){
	$stmt = $conn->prepare("select * from rms_token_validation where email_id='".$email_id."'");
	$stmt->execute();
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll();
	//print_r($result[0]);exit;
	if (isset($result[0])) {
		$result = $result[0];
		if(empty($result['merchant_id']) && empty($result['cardstream_signature']) && empty($result['acess_token']) && empty($result['store_hash'])){
			header("Location:dashboard.php");
		}
	}else{
		header("Location:dashboard.php");
	}
}else{
	header("Location:dashboard.php");
}


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Retail Merchant Payments</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans|Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Droid+Serif:400i" rel="stylesheet">

    <!-- font-awesome css-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

    <!-- Google font-Poppins css-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">


    <!-- Bootstrap Core CSS -->
    <link href="css/custom/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/custom/style.css" rel="stylesheet">
    <link href="css/custom/main.css" rel="stylesheet">
    <link href="css/custom/media.css" rel="stylesheet">

</head>

<body style="background-color: #f9f9fa;">

<section class="inner-top">
	<div class="container">
		<div class="row">
			<div class="col-md-12 text-center logo"> <img src="images/vendor_logo.jpg" alt="logo" class="img-responsive"></div>
		</div>
	</div>
</section>

<section class="order-details">

	<div class="container">
	
		<div class="row">
		
			<div class="col-md-6 col-8 text-left"><h4>Custom Payment Button</h4></div>
			<div class="col-md-6 col-4 text-right">
				<a href="dashboard.php?bc_email_id=<?= $email_id ?>">
					<h5><i class="fas fa-arrow-left"></i> Back To Dashboard</h5>
				</a>
			</div>
		
		</div>
		<div class="row rows">
			<div class="order-details-bg settle">
				<form action="updateCustomButton.php" method="POST" >
					<input type="hidden" name="bc_email_id" value="<?= @$_REQUEST['bc_email_id'] ?>" />
					<?php
						$container_id = '.checkout-step--payment .checkout-view-header';
						$html_code = '<button id="form-submit" type="submit" class="button button--action button--large button--slab optimizedCheckout-buttonPrimary" style="background-color: #424242;border-color: #424242;color: #fff;" disabled>Continue</button>';
						$css_prop = '#form-submit{display:block; background-color: #00FF00 !important; color: #000000 !important; border-color: #FF0000 !important;}';
						$stmt_c = $conn->prepare("select * from custom_rmspay_button where email_id='".$email_id."'");
						$stmt_c->execute();
						$stmt_c->setFetchMode(PDO::FETCH_ASSOC);
						$result_c = $stmt_c->fetchAll();
						if(count($result_c) > 0){
							$result_c = $result_c[0];
						}else{
							$result_c['container_id'] = $container_id;
							$result_c['css_prop'] = $css_prop;
							$result_c['html_code'] = $html_code;
						}
						//print_r($result_c);exit;
						$enable = '';
						if(isset($result_c['is_enabled']) && $result_c['is_enabled'] == "1"){
							$enable = "checked";
						}
					?>
					Container Id / Class
					<input type="text" name="container_id" id="container_id" required value="<?= @$result_c['container_id'] ?>" class="form-control" placeholder="Container Id / Class">
					<br/>
					Css Properties 
					<textarea name="css_prop" id="css_prop" class="signin form-control" placeholder="#form-submit{display:block; background-color: #00FF00 !important; color: #000000 !important; border-color: #FF0000 !important;}"><?= @$result_c['css_prop'] ?></textarea>
					<br/>
					Html Code
					<textarea class="form-control" required name="html_code" id="html_code" placeholder='<button id="form-submit" type="submit" class="button button--action button--large button--slab optimizedCheckout-buttonPrimary" style="background-color: #424242;border-color: #424242;color: #fff;" disabled>Continue</button>' id="exampleFormControlTextarea1" rows="5"><?= @$result_c['html_code'] ?></textarea><br/>
					<input type="checkbox" name="is_enabled" <?= $enable ?> />    Enable Custom Button 
					<div class="btn-secion">
						<button type="button" id="resetCustom" class="btn order-btn">Reset</button>&nbsp;&nbsp;&nbsp;
						<button type="submit" class="btn btn-order">Save</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<script src="js/jquery.min.js"></script>
<script>
			var id = '<?= $container_id ?>';
			var css = '<?= base64_encode($css_prop) ?>';
			var html_code = '<?= $html_code ?>';
			$('body').on('click','#resetCustom',function(){
				$('body #container_id').val(id);
				$('body #css_prop').val(window.atob(css));
				$('body #html_code').val(html_code);
			});
</script>
</body>

</html>