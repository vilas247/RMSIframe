<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
<meta http-equiv="Pragma" content="no-cache" />
<meta http-equiv="Expires" content="0" />
<?php
/**
	* Feed List Page
	* Author 247Commerce
	* Date 31 MAR 2021
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
	if(empty($result['merchant_id']) || empty($result['cardstream_signature']) || empty($result['acess_token']) || empty($result['store_hash'])){
		header("Location:settings.php");
	}
}else{
	header("Location:logout.php");
}
?>
<!DOCTYPE html>
<html lang="en">
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
      <title>CardStream</title>
      <!-- Bootstrap -->
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
   </head>
   <body style="background-color:#fbfbfd">
	  <header>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <img src="images/vendor_logo.jpg" style="height:90px;" alt="logo" class="img-responsive">
                </div>

                <div class="col-md-12 marTP-30">
                    <span class="title-head">
                        Dashboard
                    </span>

                    <span class="btn-secion">
                        <a class="btn btn-yellow" href="customButton.php" >Custom Payment Button</a>
                        <a class="btn btn-order" href="orderDetails.php" >Order Details</a>
                        <a class="btn btn-yellow" href="logout.php" >Logout</a>
                    </span>
                </div>                
            </div>
        </div>
    </header>
    <section class="order-section">
         <div class="container">
			<?php
				$stmt = $conn->prepare("select * from rms_token_validation where email_id='".$email_id."'");
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$result = $stmt->fetchAll();
				//print_r($result[0]);exit;
				if (isset($result[0])) {
					$result = $result[0];
					if(!empty($result['merchant_id']) && !empty($result['cardstream_signature'])){
						$payment_option = $result['payment_option'];
						$enabled = false;
						if($result['is_enable'] == 1){
							$enabled = true;
						}
			?>
				<div class="row">
					<div class="white-bg dash-head">
						<div class="col-md-12">
							<ul class="user-detail">
								<li>
									<h5 class="user-head">Name</h5>
									<p class="user-para">
										<?= $result['email_id'] ?>
									</p>
									<h5 class="user-head">Merchant Id</h5>
									<p class="user-para"><?= $result['merchant_id'] ?></p>
									<h5 class="user-head">Signature Key</h5>
									<p class="user-para"><?= $result['cardstream_signature'] ?></p>
								</li>
								<li>
									<h5 class="user-head">Access Token</h5>
									<p class="user-para"><?= $result['acess_token'] ?></p>
									
								</li>
								<li>
									<h5 class="user-head">Store Hash</h5>
									<p class="user-para"><?= $result['store_hash'] ?></p>
									
								</li>
								<li>
									<h5 class="user-head">Delete StoreToken</h5>
									<p class="user-para"><button class="btn btn-yellow" id="deleteStoreToken">Delete</button></p>
									
								</li>
								<li>
									<h5 class="user-head">Enable</h5>
									<label class="switch">
									  <input id="actionChange" type="checkbox" <?= ($enabled)?'checked':'' ?> value="<?= ($enabled)?'1':'0' ?>" />
									  <span class="slider round"></span>
									</label>
								</li>
							</ul>
						</div>
					</div>
					<span class="title-head" style="color: #000; margin-top:30px; ">
						Order Details

						<a href="orderDetails.php" style="float: right;margin-top: 10px;">View all</a>
					</span>
					<div class="white-bg marTP-30 od-block" style="width: 100%;">
						<form class="row gy-2 gx-3 align-items-center search-form">
							<div class="col-sm-12">
								<div class="input-group">
									<div class="input-group-text se-ico"><i class="fas fa-search"></i></div>
									<input type="text" class="form-control search-box" id="autoSizingInputGroup" placeholder="Order ID">
								</div>
							</div>
						</form>
						<table class="table order-table table-responsive-stack" id="tableOne">
							<thead class="thead-light">
								<th style="flex-basis: 9.09091%;">transaction<br/>ID</th>
								<th style="flex-basis: 9.09091%;">BigCommerce<br/>Order Id</th>
								<th style="flex-basis: 9.09091%;">Payment<br/>type</th>
								<th style="flex-basis: 9.09091%;">Payment<br/>Status</th>
								<th style="flex-basis: 9.09091%;">Currency</th>
								<th style="flex-basis: 9.09091%;">Total</th>
								<th style="flex-basis: 9.09091%;">Amount Paid</th>
								<th style="flex-basis: 9.09091%;">Created Date</th> 
							</thead>   

							<tbody>
								<?php
									$sql_res = "SELECT opd.api_response,opd.id,opd.settlement_status,opd.type,opd.amount_paid,opd.email_id as email,opd.order_id as nvoice_id,od.order_id,opd.status,opd.currency,opd.total_amount,opd.created_date FROM order_payment_details opd LEFT JOIN order_details od ON opd.order_id = od.invoice_id WHERE opd.email_id='".$email_id."' order by opd.id desc LIMIT 0,3";
									$stmt_res = $conn->prepare($sql_res);
									$stmt_res->execute();
									$stmt_res->setFetchMode(PDO::FETCH_ASSOC);
									$result_final = $stmt_res->fetchAll();
									if(count($result_final) > 0){
										foreach($result_final as $k=>$values) {
								?>
										<tr>
											<td style="flex-basis: 9.09091%;">
												<?php
													if(isset($values['api_response'])){
														$api_response = $values['api_response'];
														$api_response = json_decode($api_response,true);
														if(isset($api_response['transactionID'])){
															echo $api_response['transactionID'];
														}else{
															echo "";
														}
													}else{
														echo "";
													}
												?>
											</td>
											<td style="flex-basis: 9.09091%;"><?= $values['order_id'] ?></td>
											<td style="flex-basis: 9.09091%;">
												<?= $values['type'] ?>
											</td>
											<td style="flex-basis: 9.09091%;">
												<?php
													$status = '';
													if($values['status'] == "CONFIRMED"){
														$status = '<span class="badges1">Confirmed</span>';
													}else{
														$status = '<span class="badges">'.ucfirst($values['status']).'</span>';
													}
												?>
												<?= $status ?>
											</td>
											<td style="flex-basis: 9.09091%;">
												<?php
													$sstatus = '';
													if($values['type'] == "SALE"){
														$sstatus = '';
													}else{
														if($values['settlement_status'] == "CHARGE"){
															$sstatus = '<span class="badges1">'.ucfirst($values['settlement_status']).'</span>';
														}else{
															$sstatus = '<span class="badges">'.ucfirst($values['settlement_status']).'</span>';
														}
													}
												?>
												<?= $sstatus ?>
											</td>
											<td style="flex-basis: 9.09091%;">
												<?= $values['currency'] ?>
											</td>
											<td style="flex-basis: 9.09091%;">
												<?= $values['total_amount'] ?>
											</td>
											<td style="flex-basis: 9.09091%;">
												<?= $values['amount_paid'] ?>
											</td>
											<td style="flex-basis: 9.09091%;"><?= date("Y-m-d h:i A",strtotime($values['created_date'])) ?></td>
										</tr>
								<?php
										}
									}
								?>
							</tbody>                         
						</table>
						
					</div>
				</div>
			<?php }
			} ?>
		 </div>
      </section>
	  <!-- Modal -->
		<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
			  <div class="modal-content">
				<div class="modal-header">
				  <h5 class="modal-title" id="exampleModalLongTitle"><span><img src="images/icons/trash-purple.svg" style="margin-top: -5px;"></span> <span class="purple">Disable Retail Merchant</span>  </h5>
				  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<div class="modal-body" id="modalContent">
				  Are you sure you want to disable <strong>Retail Merchant in BigCommerce?</strong>.
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn btn-order" id="cancelConfirm" data-dismiss="modal">Cancel</button>
				  <button type="button" class="btn btn-order" id="deleteConfirm">Disable</button>
				</div>
			  </div>
			</div>
		  </div>
		<!-- Modal -->
		<div class="modal fade" id="deleteModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
			  <div class="modal-content">
				<div class="modal-header">
				  <h5 class="modal-title" id="exampleModalLongTitle"><span><img src="images/icons/trash-purple.svg" style="margin-top: -5px;"></span> <span class="purple">Delete Store Token</span>  </h5>
				  <button type="button" class="closeStore" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				  </button>
				</div>
				<div class="modal-body" id="modalContent">
				  Are you sure you want to Clear <strong>BigCommerce Store Token Details?</strong>.
				  <p>This will clear all your Data in BigCommerce</p>
				</div>
				<div class="modal-footer">
				  <button type="button" class="btn btn-order" id="cancelConfirmStore" data-dismiss="modal">Cancel</button>
				  <button type="button" class="btn btn-order" id="deleteConfirmStore">Delete</button>
				</div>
			  </div>
			</div>
		  </div>
      <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
      <script src="js/jquery.min.js"></script>
      <!-- Include all compiled plugins (below), or include individual files as needed -->
      <script src="js/bootstrap.min.js"></script>
      <script src="js/bootstrap.bundle.min.js"></script>
	  <style>
		.modal-backdrop{
			opacity: 0!important;
		}
	  </style>
      <script type="text/javascript">
		$(document).ready(function() {
			$(".modal-backdrop").remove();
			$('body').on('change','#actionChange',function(){
				var val = $(this).val();
				if(val == "0"){
					var url = 'enable.php';
					window.location.href = url;
				}else{
					$('body #exampleModalCenter').modal('show');
				}
			});
			$('body').on('click','#deleteConfirm',function(e){
				var url = 'disable.php';
				window.location.href = url;
			});
			$('body').on('click','#cancelConfirm,.close',function(e){
				$('body #exampleModalCenter').modal('hide');
				$('#actionChange').trigger('click');
			});
			
			$('body').on('click','#deleteStoreToken',function(e){
				$('body #deleteModalCenter').modal('show');
			});
			$('body').on('click','#cancelConfirmStore,.closeStore',function(e){
				$('body #deleteModalCenter').modal('hide');
			});
			$('body').on('click','#deleteConfirmStore',function(e){
				var url = 'uninstallStore.php';
				window.location.href = url;
			});
		});
      </script>
      <script>
         $('select[data-menu]').each(function() {
         
             let select = $(this),
                 options = select.find('option'),
                 menu = $('<div />').addClass('select-menu'),
                 button = $('<div />').addClass('button'),
                 list = $('<ul />'),
                 arrow = $('<em />').prependTo(button);
         
             options.each(function(i) {
                 let option = $(this);
                 list.append($('<li />').text(option.text()));
             });
         
             menu.css('--t', select.find(':selected').index() * -41 + 'px');
         
             select.wrap(menu);
         
             button.append(list).insertAfter(select);
         
             list.clone().insertAfter(button);
         
         });
         
         $(document).on('click', '.select-menu', function(e) {
         
             let menu = $(this);
         
             if(!menu.hasClass('open')) {
                 menu.addClass('open');
             }
         
         });
         
         $(document).on('click', '.select-menu > ul > li', function(e) {
         
             let li = $(this),
                 menu = li.parent().parent(),
                 select = menu.children('select'),
                 selected = select.find('option:selected'),
                 index = li.index();
         
             menu.css('--t', index * -41 + 'px');
             selected.attr('selected', false);
             select.find('option').eq(index).attr('selected', true);
         
             menu.addClass(index > selected.index() ? 'tilt-down' : 'tilt-up');
         
             setTimeout(() => {
                 menu.removeClass('open tilt-up tilt-down');
             }, 500);
         
         });
         
         $(document).click(e => {
             e.stopPropagation();
             if($('.select-menu').has(e.target).length === 0) {
                 $('.select-menu').removeClass('open');
             }
         })
		var getUrlParameter = function getUrlParameter(sParam) {
			var sPageURL = window.location.search.substring(1),
				sURLVariables = sPageURL.split("&"),
				sParameterName,
				i;

			for (i = 0; i < sURLVariables.length; i++) {
				sParameterName = sURLVariables[i].split("=");

				if (sParameterName[0] === sParam) {
					return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
				}
			}
			return false;
		};
		$(document).ready(function(){
			var enabled = getUrlParameter('enabled');
			if(enabled){
				alert("Retail Merchant Payments enabled for your Store");
			}
			var disabled = getUrlParameter('disabled');
			if(disabled){
				alert("Retail Merchant Payments disabled for your Store");
			}
		});
         
         
      </script>
   </body>
</html>