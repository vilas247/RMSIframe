<?php
/**
	* Token Validation Page
	* Author 247Commerce
	* Date 22 FEB 2021
*/
if(!isset($_SESSION)){
	session_start();
}

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once('config.php');
require_once('db-config.php');
require_once('helper.php');

$conn = getConnection();
$email_id = '';
if(isset($_SESSION['is247Email'])){
	$email_id = $_SESSION['is247Email'];
}
//print_r($_REQUEST);exit;
if(isset($_REQUEST['merchant_id']) && isset($_REQUEST['cardstream_signature']) && isset($_REQUEST['acess_token']) && isset($_REQUEST['store_hash'])){
	$conn = getConnection();
	if(!empty($email_id)){
		$stmt = $conn->prepare("select * from rms_token_validation where email_id=?");
		$stmt->execute([$email_id]);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		//print_r($result[0]);exit;
		if (isset($result[0])) {
			$result = $result[0];
			$sellerdb = $result['sellerdb'];
			if(!empty($_REQUEST['merchant_id']) && !empty($_REQUEST['cardstream_signature']) && !empty($_REQUEST['acess_token']) && !empty($_REQUEST['store_hash'])){
				$valid = createCustomPage($email_id,$_REQUEST['store_hash'],$_REQUEST['acess_token']);
				if($valid){
					$data = createFolder($sellerdb,$email_id);
					$sql = 'update rms_token_validation set merchant_id=?,cardstream_signature=?,acess_token=?,store_hash=? where email_id=?';
					//echo $sql;exit;
					$stmt = $conn->prepare($sql);
					$stmt->execute([$_REQUEST['merchant_id'],$_REQUEST['cardstream_signature'],$_REQUEST['acess_token'],$_REQUEST['store_hash'],$email_id]);
					if(isset($_REQUEST['is_enable']) && $_REQUEST['is_enable'] == "Enable"){
						$stmt_s = $conn->prepare("select * from rms_scripts where script_email_id=?");
						$stmt_s->execute([$email_id]);
						$stmt_s->setFetchMode(PDO::FETCH_ASSOC);
						$result_s = $stmt_s->fetchAll();
						//print_r($result[0]);exit;
						if (isset($result_s[0])) {
						}else{
							$res = createScripts($sellerdb,$_REQUEST['acess_token'],$_REQUEST['store_hash'],$email_id);
							if($res == "1"){
								$usql = "update rms_token_validation set is_enable=1 where email_id='".$email_id."'";
								//echo $usql;exit;
								$stmt_u = $conn->prepare($usql);
								$stmt_u->execute();
							}
						}
					}
					header("Location:dashboard.php?enable=1");
				}else{
					header("Location:dashboard.php?error=1");
				}
			}else{
				header("Location:dashboard.php");
			}
		}else{
			header("Location:dashboard.php");
		}
	}else{
		header("Location:dashboard.php");
	}
}else{
	header("Location:dashboard.php");
}
function createCustomPage($email_id,$store_hash,$acess_token){
	
	$conn = getConnection();
	$valid = false;
	$url = STORE_URL.$store_hash.'/v2/pages';
	$header = array(
		"X-Auth-Token: ".$acess_token,
		"Accept: application/json",
		"Content-Type: application/json"
	);
	$request = array(
			  "body"=> "<head>
						<script type=\"text/javascript\">var app_base_url = '".BASE_URL."';</script>
						<link rel=\"stylesheet\" href=\"".BASE_URL."css/order-confirmation.css\">
						<script src=\"".BASE_URL."js/jquery-min.js\"></script>
						<script src=\"".BASE_URL."js/order-confirmation.js\"></script>
						</head>
						<body>
						<h1>Please Wait</h1>
						</body>",
			  "channel_id"=> 1,
			  "has_mobile_version"=> false,
			  "is_customers_only"=> false,
			  "is_homepage"=> false,
			  "is_visible"=> false,
			  "mobile_body"=> "",
			  "name"=> "RMS Iframe Custom Order Confirmation",
			  "parent_id"=> 0,
			  "search_keywords"=> "",
			  "sort_order"=> 0,
			  "type"=> "raw",
			  "url"=> "/rmsiframe-custom-order-confirmation"
			);
	$request = json_encode($request,true);
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
	curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
	$res = curl_exec($ch);
	curl_close($ch);
	//print_r($res);exit;
	$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
		$stmt= $conn->prepare($log_sql);
		$stmt->execute([$email_id,"BigCommerce","Custom Page",addslashes($url),addslashes($request),addslashes($res)]);
	if(!empty($res)){
		$check_errors = json_decode($res);
		if(isset($check_errors->errors)){
		}else{
			if(json_last_error() === 0){
				$res = json_decode($res,true);
				if(isset($res['id'])){
					$valid = true;
					$sqli = "insert into 247custompages(email_id,page_bc_id,api_response) values(?,?,?)";
					$stmt= $conn->prepare($sqli);
					$stmt->execute([$email_id,$res['id'],addslashes(json_encode($res))]);
				}
			}
		}
	}
	return $valid;
}
function createScripts($sellerdb,$acess_token,$store_hash,$email_id){
	$conn = getConnection();
	$url = array();
	$rStatus = 0;
	$url[] = JS_SDK;
	$url[] = JSVALIDATE_SDK;
	$url[] = RMSIFRAME_SDK;
	$url[] = BASE_URL.$sellerdb.'/custom_script.js';
	foreach($url as $k=>$v){
		//$auth_token = '4ir2j1tpf5cw3pzx7ea4ual2jrei8cd';
		$header = array(
			"X-Auth-Client: ".$acess_token,
			"X-Auth-Token: ".$acess_token,
			"Accept: application/json",
			"Content-Type: application/json"
		);
		$location = 'head';
		$cstom_url = BASE_URL.$sellerdb.'/custom_script.js';
		if($v == $cstom_url){
			$location = 'footer';
		}
		$request = '{
		  "name": "RmsIframeApp",
		  "description": "RmsIframe payment files",
		  "html": "<script src=\"'.$v.'\"></script>",
		  "auto_uninstall": true,
		  "load_method": "default",
		  "location": "'.$location.'",
		  "visibility": "checkout",
		  "kind": "script_tag",
		  "consent_category": "essential"
		}';
		//print_r($request);exit;
		$url = STORE_URL.$store_hash.'/v3/content/scripts';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$res = curl_exec($ch);
		curl_close($ch);
		//print_r($res);exit;
		$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
		$stmt= $conn->prepare($log_sql);
		$stmt->execute([$email_id, "BigCommerce", "script_tag_injection",addslashes($url),addslashes($request),addslashes($res)]);
		if(!empty($res)){
			$response = json_decode($res,true);
			if(isset($response['data']['uuid'])){
				$sql = 'insert into rms_scripts(script_email_id,script_filename,script_code,status,api_response) values(?,?,?,?,?)';
				//echo $sql;exit;
				$stmt= $conn->prepare($sql);
				$stmt->execute([$email_id, basename($v), $response['data']['uuid'],"1",addslashes($res)]);
				$rStatus++;
			}
		}
	}
	if($rStatus >= 4){
		return 1;
	}
	if($rStatus >= 4){
		return 0;
	}
}
/* creating folder Based on Seller */
function createFolder($sellerdb,$email_id){
	$conn = getConnection();
	if(!empty($sellerdb)){
		$folderPath = './'.$sellerdb;
		$filecontent = '$("head").append("<script src=\"'.BASE_URL.'js/247rmsiframeloader.js\" ></script>");';
		$filecontent .= '$("head").append("<link rel=\"stylesheet\" type=\"text/css\" href=\"'.BASE_URL.'css/247rmsiframeloader.css\" />");';
		$filecontent .= '$("head").append("<link rel=\"stylesheet\" type=\"text/css\" href=\"'.BASE_URL.'css/rmsiframe-hosted-fields.css\" />");';
		$filecontent .= 'var rmsiframehformInitialized = false;$(document).ready(function() {
	var stIntIdRmsiframe = setInterval(function() {
		if($(".checkout-step--payment").length > 0) {
			if($("#247rmsiframepayment").length == 0){
				var rmsiframebtnHTML = \'<div id="247rmsiframepayment" class="checkout-form " style="padding: 1px;display:none;"> <div id="247RmsiframeErr" style="color:red"></div> <form id="form" name="rmsiframepaymentForm" method="POST" novalidate="novalidate" lang="en" action="https://gateway.cardstream.com/direct/"> <input type="hidden" id="247rmsiframekey" value="'.base64_encode(json_encode($email_id,true)).'"><input type="hidden" id="merchantID" name="merchantID"><input type="hidden" id="action" name="action"><input type="hidden" id="type" name="type"><input type="hidden" id="countryCode" name="countryCode"><input type="hidden" id="currencyCode" name="currencyCode"><input type="hidden" id="amount" name="amount"><input type="hidden" id="orderRef" name="orderRef"><input type="hidden" id="transactionUnique" name="transactionUnique"><input type="hidden" id="redirectURL" name="redirectURL"><input type="hidden" id="signature" name="signature"> <ul class="form-checklist optimizedCheckout-form-checklist bootstrap-wrapper"> <li class="form-checklist-item optimizedCheckout-form-checklist-item form-checklist-item--selected optimizedCheckout-form-checklist-item--selected"> <div class="form-checklist-header form-checklist-header--selected"> <div class="row"> <div class="col-sm-12 col-md-8"> <div class="form-field"> <!-- <input name="paymentProviderRadio" class="form-checklist-checkbox optimizedCheckout-form-checklist-checkbox" id="radio-rmsiframe" type="radio" value="rms" checked> --><label for="radio-rmsiframe" class="form-label optimizedCheckout-form-label"><span class="paymentProviderHeader-name" data-test="payment-method-name">Retail Merchant Payments</span></label> </div> </div> <div class="col-sm-12 col-md-4"> <div class="paymentProviderHeader-cc"><ul class="creditCardTypes-list"><li class="creditCardTypes-list-item"><span class="cardIcon"><div class="icon cardIcon-icon icon--medium" data-test="credit-card-icon-visa"><svg height="100" viewBox="0 0 148 100" width="148" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path d="M148 84c0 6.6-5.55 12-12 12H12C5.55 96 0 90.6 0 84V12C0 5.4 5.55 0 12 0h124c6.45 0 12 5.4 12 12v72z" fill="#F3F4F4"></path><path d="M0 24V12C0 5.4 5.74 0 12 0h124c6.26 0 12 5.4 12 12v12" fill="#01579F"></path><path d="M148 76v12c0 8.667-5.74 12-12 12H12c-6.26 0-12-3.333-12-12V76" fill="#FAA41D"></path><path d="M55.01 65.267l4.72-29.186h7.546l-4.72 29.19H55.01M89.913 36.8c-1.49-.59-3.85-1.242-6.77-1.242-7.452 0-12.7 3.974-12.73 9.656-.063 4.19 3.756 6.52 6.613 7.918 2.92 1.428 3.913 2.36 3.913 3.633-.04 1.957-2.36 2.857-4.54 2.857-3.014 0-4.628-.465-7.08-1.552l-.996-.466-1.055 6.55c1.77.808 5.03 1.52 8.415 1.553 7.92 0 13.075-3.912 13.137-9.967.03-3.322-1.987-5.868-6.334-7.948-2.64-1.336-4.256-2.236-4.256-3.602.032-1.242 1.367-2.514 4.348-2.514 2.453-.06 4.254.53 5.62 1.12l.684.31L89.91 36.8m10.03 18.13c.62-1.675 3.013-8.165 3.013-8.165-.03.062.62-1.707.994-2.794l.525 2.52s1.428 6.986 1.74 8.445H99.94zm9.317-18.846h-5.84c-1.8 0-3.17.53-3.945 2.424L88.265 65.27h7.918s1.305-3.6 1.585-4.377h9.687c.217 1.024.9 4.377.9 4.377h6.987l-6.082-29.19zm-60.555 0l-7.39 19.904-.807-4.037c-1.37-4.652-5.653-9.713-10.435-12.23l6.77 25.52h7.98L56.68 36.09H48.7" fill="#3B5CAA"></path><path d="M34.454 36.08H22.312l-.124.59c9.47 2.423 15.744 8.26 18.32 15.277L37.87 38.534c-.436-1.863-1.77-2.39-3.416-2.453" fill="#F8A51D"></path></g></svg></div></span></li><li class="creditCardTypes-list-item"><span class="cardIcon"><div class="icon cardIcon-icon icon--medium" data-test="credit-card-icon-american-express"><svg height="104" viewBox="0 0 156 104" width="156" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd"><path d="M144 104H12c-6.15 0-12-5.85-12-12V12C0 5.85 5.85 0 12 0h132c6.15 0 12 5.85 12 12v80c0 6.15-5.85 12-12 12z" fill="#60C7EE"></path><g fill="#FFF"><path d="M95.05 46.532v3.68h12.93v4.723H95.05V59.5h12.79l5.244-6.824-4.673-6.144H95.05m-59.707 9.382h5.906l-2.97-8.324-2.94 8.324"></path><path d="M128.833 52.77l11.29-15.125h-19.067l-2.536 3.9-2.608-3.9h-46.59l-1.254 4.224-1.264-4.227H31.27L17.72 68.687h17.326l1.31-3.822h3.824l1.345 3.822h73.594l3.28-4.594 3.28 4.594h19.36l-4.867-6.343-7.342-9.574zM83.185 64.744H76.38v-17.66l-5.243 17.66h-6.16l-5.233-17.66v17.66H44.318l-1.345-3.823H33.54l-1.312 3.826h-8.483L33.85 41.588h9.065L52.94 64.56V41.59h10.927l4.214 14.09 4.187-14.09h10.92v23.156zm40.524 0l-5.31-7.44-5.31 7.44H86.72V41.588h27.085l4.76 7.124 4.63-7.124h9.062l-8.37 11.215 9.16 11.94h-9.338z"></path></g></g></svg></div></span></li><li class="creditCardTypes-list-item"><span class="cardIcon"><div class="icon cardIcon-icon icon--medium" data-test="credit-card-icon-mastercard"><svg viewBox="0 0 131.39 86.9" xmlns="http://www.w3.org/2000/svg"><path d="M48.37 15.14h34.66v56.61H48.37z" fill="#ff5f00"></path><path d="M51.94 43.45a35.94 35.94 0 0113.75-28.3 36 36 0 100 56.61 35.94 35.94 0 01-13.75-28.31z" fill="#eb001b"></path><path d="M120.5 65.76V64.6h.5v-.24h-1.19v.24h.47v1.16zm2.31 0v-1.4h-.36l-.42 1-.42-1h-.36v1.4h.26V64.7l.39.91h.27l.39-.91v1.06zM123.94 43.45a36 36 0 01-58.25 28.3 36 36 0 000-56.61 36 36 0 0158.25 28.3z" fill="#f79e1b"></path></svg></div></span></li></ul></div> </div> </div> </div> <div style="" id="247rmsiframePaynowButton" class="form-checklist-body"> <input type="hidden" name="paymentToken" value=""> <div class="row"> <div class="col-sm-12 col-md-9"> <div class="form-group"><label for="form-card-number">Card Number:</label><input id="form-card-number" type="hostedfield:cardNumber" name="card-number" autocomplete="ccnumber" class="form-control form-controlhosted" style="background: #f2f8fb;" required></div> </div> <div class="col-sm-12 col-md-3"> <div class="form-group"><label for="form-card-expiry-date">Expiration:</label><input id="form-card-expiry-date" type="hostedfield:cardExpiryDate" name="card-expirydate" autocomplete="cc-exp" class="form-control form-control-hosted" placeholder="MM / YY" required></div> </div> </div> <div class="row"> <div class="col-sm-12 col-md-9"> <div class="form-group"><label for="form-customer-name">Name on Card:</label><input id="form-customer-name" type="text" name="paymentToken[customerName]" autocomplete="ccname" class="form-control form-control-native hostedfield-tokenise" required></div> </div> <div class="col-sm-12 col-md-3"> <div class="form-group form-cvv"><label for="form-card-cvv">CVV:</label><input id="form-card-cvv" type="hostedfield:cardCVV" name="card-cvv" autocomplete="cc-csc" class="form-control form-control-hosted" required></div> </div> </div> <button id="form-submit" type="submit" class="button button--action button--large button--slab optimizedCheckout-buttonPrimary" style="background-color: #424242;border-color: #424242;color: #fff;" disabled>Continue</button> </div> </li> </ul> </form></div>\';
				$(".checkout-step--payment .checkout-view-header").after(rmsiframebtnHTML);
				loadRmsiframeStatus();
				clearInterval(stIntIdRmsiframe);
				/**
					when user is logged in and billing/shipping 
					address set show custom payment button 
				*/
				checkRmsiframePayBtnVisibility();
			}
		}
	}, 1000);
	$("body").on("click","button[data-test=\'step-edit-button\'], button[data-test=\'sign-out-link\']",function(e){
		//hide Rms payment button
		$("#247rmsiframepayment").hide();
	});

	$("body").on("click", "button#checkout-customer-continue, button#checkout-shipping-continue, button#checkout-billing-continue", function() {
		checkRmsiframePayBtnVisibility();
	});
	$("body").on("submit", "form[name=\'rmsiframepaymentForm\']", function() {
		var text = "Please wait...";
		var current_effect = "bounce";
		var $form = $("#form"); 
		$form.on({
			\'hostedform:valid\': function (event) {
			   console.log(\'Form valid inside\');
			   $("#247rmsiframepayment").waitMe({
					effect: current_effect,
					text: text,
					bg: "rgba(255,255,255,0.7)",
					color: "#000",
					maxSize: "",
					waitTime: -1,
					source: "'.getenv('app.ASSETSPATH').'images/img.svg",
					textPos: "vertical",
					fontSize: "",
					onClose: function(el) {}
				});
			}
		});
	});
});
function rmsiframebillingAddressValdation(billingAddress){
	var errorCount = 0;
	if(typeof(billingAddress.firstName) != "undefined" && billingAddress.firstName !== null && billingAddress.firstName !== "") {
		
	}else{
		errorCount++;
	}
	if(typeof(billingAddress.lastName) != "undefined" && billingAddress.lastName !== null && billingAddress.lastName !== "") {
		
	}else{
		errorCount++;
	}
	if(typeof(billingAddress.address1) != "undefined" && billingAddress.address1 !== null && billingAddress.address1 !== "") {
		
	}else{
		errorCount++;
	}
	if(typeof(billingAddress.email) != "undefined" && billingAddress.email !== null && billingAddress.email !== "") {
		
	}else{
		errorCount++;
	}
	if(typeof(billingAddress.city) != "undefined" && billingAddress.city !== null && billingAddress.city !== "") {
		
	}else{
		errorCount++;
	}
	if(typeof(billingAddress.postalCode) != "undefined" && billingAddress.postalCode !== null && billingAddress.postalCode !== "") {
		
	}else{
		errorCount++;
	}
	if(typeof(billingAddress.country) != "undefined" && billingAddress.country !== null && billingAddress.country !== "") {
		
	}else{
		errorCount++;
	}
	
	return errorCount;
}

function rmsiframeshippingAddressValdation(shippingAddress){
	var errorCount = 0;
	if(shippingAddress.length > 0){
		if(typeof(shippingAddress[0].shippingAddress) != "undefined" && shippingAddress[0].shippingAddress !== null && shippingAddress[0].shippingAddress !== "") {
			shippingAddress = shippingAddress[0].shippingAddress;
			if(typeof(shippingAddress.firstName) != "undefined" && shippingAddress.firstName !== null && shippingAddress.firstName !== "") {
				
			}else{
				errorCount++;
			}
			if(typeof(shippingAddress.lastName) != "undefined" && shippingAddress.lastName !== null && shippingAddress.lastName !== "") {
				
			}else{
				errorCount++;
			}
			if(typeof(shippingAddress.address1) != "undefined" && shippingAddress.address1 !== null && shippingAddress.address1 !== "") {
				
			}else{
				errorCount++;
			}
			if(typeof(shippingAddress.city) != "undefined" && shippingAddress.city !== null && shippingAddress.city !== "") {
				
			}else{
				errorCount++;
			}
			if(typeof(shippingAddress.postalCode) != "undefined" && shippingAddress.postalCode !== null && shippingAddress.postalCode !== "") {
				
			}else{
				errorCount++;
			}
			if(typeof(shippingAddress.country) != "undefined" && shippingAddress.country !== null && shippingAddress.country !== "") {
				
			}else{
				errorCount++;
			}
		}
	}else{
		errorCount++;
	}
	return errorCount;
}
function checkOnlyDownloadableProducts(cartData){
	var status = false;
	if(cartData != ""){
		if(cartData.physicalItems.length > 0 || cartData.customItems.length > 0){
			status = true;
		}
		else{
			if(cartData.digitalItems.length > 0){
				status = false;
			}
		}
	}
	return status;
}
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
function loadRmsiframeStatus(){
	var key = getUrlParameter("rmsiframeinv");
	if(key != "undefined" && key != ""){
		$.ajax({
			type: "POST",
			dataType: "json",
			crossDomain: true,
			url: "'.BASE_URL.'getPaymentStatus.php",
			dataType: "json",
			data:{"authKey":key},
			success: function (res) {
				if(res.status){
					$("body #247RmsiframeErr").text(res.msg);
				}
			}
		});
	}
}
';
$filecontent .= 'function checkRmsiframePayBtnVisibility() {
	var checkDownlProd = false;
	var key = $("body #247rmsiframekey").val();
	$.ajax({
		type: "GET",
		dataType: "json",
		url: "/api/storefront/cart",
		success: function (res) {
			if(res.length > 0){
				if(res[0]["id"] != undefined){
					var cartId = res[0]["id"];
					var cartCheck = res[0]["lineItems"];
					checkDownlProd = checkOnlyDownloadableProducts(cartCheck);
					if(cartId != ""){
						$.ajax({
							type: "GET",
							dataType: "json",
							url: "/api/storefront/checkouts/"+cartId,
							success: function (cartres) {
								var cartData = window.btoa(unescape(encodeURIComponent(JSON.stringify(cartres))));
								var billingAddress = "";
								var consignments = "";
								var bstatus = 0;
								var sstatus = 0;
								if(typeof(cartres.billingAddress) != "undefined" && cartres.billingAddress !== null) {
									billingAddress = cartres.billingAddress;
									bstatus = rmsiframebillingAddressValdation(billingAddress);
								}
								if(checkDownlProd){
									if(typeof(cartres.consignments) != "undefined" && cartres.consignments !== null) {
										consignments = cartres.consignments;
										sstatus = rmsiframeshippingAddressValdation(consignments);
									}
								}

								if(bstatus ==0 && sstatus == 0) {

									//hide Rms payment button
									$("#247rmsiframepayment").show();
									$.ajax({
										type: "POST",
										dataType: "json",
										crossDomain: true,
										url: "'.BASE_URL.'authentication.php",
										dataType: "json",
										data:{"authKey":key,"cartId":cartId,cartData:cartData},
										success: function (res) {
											$("#247rmsiframepayment").waitMe("hide");
											if(res.status){
												var data = res.data;
												var form_id = res.form_id;
												//var form = $("#247rmsiframePaynowButton").hostedForm(data);
												/*var form = new window.hostedForms.classes.Form("247rmsiframePaynowButton", data);
												$("body "+form_id).submit();*/

												$("#merchantID").val(data.data.merchantID);
												$("#action").val(data.data.action);
												$("#type").val(data.data.type);
												$("#countryCode").val(data.data.countryCode);
												$("#currencyCode").val(data.data.currencyCode);
												$("#amount").val(data.data.amount);
												$("#orderRef").val(data.data.orderRef);
												$("#transactionUnique").val(data.data.transactionUnique);
												$("#redirectURL").val(data.data.redirectURL);
												$("#signature").val(data.data.signature);

												$("#form-submit").removeAttr(\'disabled\');

												if(!rmsiframehformInitialized) {

													//hide other payment buttons if present
													////rmsiframecontrolOtherPayButtons();

													setTimeout(hostedCardFields, 50);
													rmsiframehformInitialized = true;
												}

											}
										},error: function() {
											$("#247rmsiframepayment").waitMe("hide");
										}
									});	
								}


							}
						});
					}
				}
			}
		}

	});
}

function rmsiframecontrolOtherPayButtons() {
	
	$("input[name=\'paymentProviderRadio\']").each(function(index) {
	    $(this).prop("checked", false);
	});

	$(".form-checklist-body").hide();
	$("#radio-rmsiframe").prop("checked", true);
	$("#247rmsiframePaynowButton").show();
	$("#checkout-payment-continue").attr("disabled", true);


	$("input[name=\'paymentProviderRadio\']").click(function() {

		console.log($(this).attr("id"));

		$("input[name=\'paymentProviderRadio\']").each(function(index) {
		    $(this).prop("checked", false);
		});
		$(".form-checklist-body").hide();

		$(this).prop("checked", true);

		if($(this).attr("id") == "radio-rmsiframe") {			
			$("#checkout-payment-continue").attr("disabled", true);
			
		} else {
			$("#checkout-payment-continue").removeAttr("disabled");			
		}

		$(this).parent().parent().parent().find(".form-checklist-body").show();

	});

}';
$filecontent .= file_get_contents(BASE_URL."js/rmsiframehostedFields.js");
		$filename = 'custom_script.js';
		$res = saveFile($filename,$filecontent,$folderPath);
	}
}
?>