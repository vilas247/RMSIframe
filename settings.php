<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<?php
/**
	* Feed List Page
	* Author 247Commerce
	* Date 22 FEB 2021
*/
if(!isset($_SESSION)){
	session_start();
}
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once('db-config.php');
require_once('config.php');
require_once('hooks.php');

/*require 'log-autoloader.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;*/

$conn = getConnection();
$email_id = '';
if(isset($_SESSION['is247Email'])){
	$email_id = $_SESSION['is247Email'];
}

$stmt = $conn->prepare("select * from rms_token_validation where email_id='".$email_id."'");
$stmt->execute();
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$result = $stmt->fetchAll();
//print_r($result[0]);exit;
if (isset($result[0])) {
	$result = $result[0];
	if(!empty($result['merchant_id']) && !empty($result['cardstream_signature']) && !empty($result['acess_token']) && !empty($result['store_hash'])){
		header("Location:dashboard.php");
	}
}else{
	header("Location:logout.php");
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.83.1">
    <title>Settings</title>

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- font-awesome css-->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.1/css/all.css" integrity="sha384-O8whS3fhG2OnA5Kas0Y9l3cfpmYjapjI0E4theH4iuMD+pLhbf6JI0jIMfYcK3yZ" crossorigin="anonymous">

    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">

</head>
  <body>
    <main class="main-content py-5 rms-design">
      <div class="container">
        <div class="row">
          <form class="" action="validateToken.php" method="POST" >
		  <div class="col-md-12 box rms-8">
            <div class="row brd-btm">
                <div class="col-md-10 col-lg-8 mb-3">
                  <label class="me-3">Add your payment gateway to BigCommerce Store</label>
                  <button type="submit" class="btn btn-primary btn-lg btn-store" style="display:none;"><img src="images/add.png" class="me-2">Add</button>
                </div>
            </div>
            <div class="row py-4 brd-btm">
            <p class="mb-0"><label>MID ID and Signature Key will be available from Vendor name goes here (or RMS)</label></p>
              <div class="col-md-6 my-2">
                <span class="mb-2">MID:</span>
                <input class="form-control mt-2" type="text" name="merchant_id" value="<?= @$result['merchant_id'] ?>" required placeholder="MID" />
              </div>
              <div class="col-md-6 my-2">
                <span class="mb-2">Signature Key:</span>
                <input class="form-control mt-2 type="text" name="cardstream_signature" value="<?= @$result['cardstream_signature'] ?>" required placeholder="Signature" />
              </div>
            </div>
            <div class="row py-4">
            <p class="mb-0"><label>You can get the below details from BigCommerce Admin Panel.<a href="#">Click here</a> to find how</label></p>
              <div class="col-md-6 my-2">
                <span>BigCommerce Store API Token:</span>
                <input class="form-control mt-2" type="password" required name="acess_token" placeholder="Access Token"/>
              </div>
              <div class="col-md-6 my-2">
                <span>BigCommerce Store Hash:</span>
                <input class="form-control mt-2" type="password" required name="store_hash" placeholder="Store Hash" />
              </div>
            </div>
            <div class="row">
                <div class="col-md-8 col-lg-6">
                    <label>Status of your Payment Gateway</label>
                    <div class="btn-group ms-3 enable-but">
                          <select data-menu name="is_enable">
                                 <option selected>Enable</option>
                                 <option>Disable</option>
                              </select>
                    </div>
                </div>
            </div>
          </div>
          <div class="col-md-12 lanch-store mt-3">
            <p class="text-end"><button type="submit" class="btn btn-primary btn-lg btn-store"><img src="images/save.png" class="me-2">Save</button></p>
          </div>
		  </form>
        </div>
      </div>
    </main>
    <script src="js/jquery-min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
  </html>