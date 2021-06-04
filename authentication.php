<?php
/**
	* Token Validation Page
	* Author 247Commerce
	* Date 30 SEP 2020
*/
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Credentials: true');
}

require_once('config.php');
require_once('db-config.php');

require 'log-autoloader.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$res = array();
$res['status'] = false;
$res['data'] = '';
$res['msg'] = '';

$logger = new Logger('Authentication');
$logger->pushHandler(new StreamHandler('var/logs/RMS_auth_log.txt', Logger::INFO));
$logger->info("authKey: ".$_REQUEST['authKey']);
$logger->info("cartData: ".$_REQUEST['cartData']);

if(isset($_REQUEST['authKey'])){
	$valid = validateAuthentication($_REQUEST);
	if($valid){
		$email_id = json_decode(base64_decode($_REQUEST['authKey']));
		if (filter_var($email_id, FILTER_VALIDATE_EMAIL)) {
			$conn = getConnection();
			$stmt = $conn->prepare("select * from rms_token_validation where email_id=?");
			$stmt->execute([$email_id]);
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$result = $stmt->fetchAll();

			if (isset($result[0])) {
				$result = $result[0];
				$payment_option = $result['payment_option'];
				if(!empty($result['merchant_id']) && !empty($result['cardstream_signature']) && !empty($result['acess_token']) && !empty($result['store_hash'])){
					$sellerdb = $result['sellerdb'];
					$acess_token = $result['acess_token'];
					$store_hash = $result['store_hash'];
					$cartAPIRes = getCartData($email_id,$_REQUEST['cartId'],$acess_token,$store_hash);
					if(!is_array($cartAPIRes) || (is_array($cartAPIRes) && count($cartAPIRes) == 0)) {

						$res['status'] = false;
						echo json_encode($res);
						exit;
					}

					//to use cart data from server API response to avoid manipulation from UI side
					$cartData = $cartAPIRes;					
					
					/*$string = base64_decode($_REQUEST['cartData']);
					$string = preg_replace("/[\r\n]+/", " ", $string);
					$json = utf8_encode($string);
					$cartData = json_decode($json,true);*/
					
					if(!empty($cartData) && isset($cartData['id'])){
						$totalAmount = $cartData['grand_total'];
						
						$currency = $cartData['cart']['currency']['code'];
						$billingAddress = $cartData['billing_address'];
						$invoiceId = "247RMS".time();
						
						$transaction_type = "SALE";
						if($payment_option == "CFO"){
							$transaction_type = "SALE";
						}
						if($payment_option == "CFS"){
							$transaction_type = "captureDelay";
						}
						
						$isql = 'insert into order_payment_details(type,email_id,order_id,cart_id,total_amount,amount_paid,currency,status,params) values(?,?,?,?,?,?,?,?,?)';
						$stmt= $conn->prepare($isql);
						$stmt->execute([$transaction_type, $email_id, $invoiceId,$cartData['id'],$cartData['grand_total'],"0.00",$currency,"PENDING",base64_encode(json_encode($cartData))]);
						
						$action = "CFO";
						if($payment_option == "CFO"){
							$action = "SALE";
						}
						if($payment_option == "CFS"){
							$action = "captureDelay";
						}
						$billingAddress = $cartData['billing_address'];
						
						$key = $result['cardstream_signature'];
						
						$unique_id = uniqid();
						$req = array(
							'merchantID' => $result['merchant_id'],
							'action' => "SALE",
							'type' => "1",
							'countryCode' => $billingAddress['country_code'],
							'currencyCode' => $cartData['cart']['currency']['code'],
							'amount' => sprintf("%.2f",$cartData['grand_total']),
							'orderRef' => $invoiceId,
							'transactionUnique' => $unique_id,
							'redirectURL' => BASE_URL.'success.php'////,
							////'customerName' => $billingAddress['first_name'],
							////'customerEmail' => $billingAddress['email'],
							////'customerPhone' => $billingAddress['phone'],
							////'customerAddress' => $billingAddress['address1'],
							////'customerPostCode' => $billingAddress['postal_code'],
							////'authenticity_token'=>"424654961f7349222a72a5c91f66a3496217b6b0a6b40225ce1a5e941d094d0c"
						);
						$req['signature'] = createSignature($req, $key).'|merchantID,action,type,countryCode,currencyCode,amount,orderRef,transactionUnique,redirectURL';
						$data = array(
									'id'=>'247rms_form',
									'url'=>RMSIFRAME_URL,
									'modal'=>true,
									'data'=>$req,
								);
						$res['status'] = true;
						$res['data'] = array();
						$res['data'] = $data;
						$res['form_id'] = '#247rms_form';
					}
				}
			}
		}
	}
}
echo json_encode($res);exit;

function createSignature(array $data, $key) {
	// Sort by field name
	ksort($data);
	
	// Create the URL encoded signature string
	$ret = http_build_query($data, '', '&');
	
	// Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
	$ret = str_replace(array('%0D%0A', '%0A%0D', '%0D'), '%0A', $ret);
	
	// Hash the signature string and the key together
	return hash('SHA512', $ret . $key);
}

function validateAuthentication($request){
	$valid = true;
	if(isset($request['authKey'])){
		
	}else{
		$valid = false;
	}
	if(isset($request['cartId'])){
		
	}else{
		$valid = false;
	}
	if(isset($request['cartData'])){
		
	}else{
		$valid = false;
	}
	return $valid;
}
function getCartData($email_id,$cartId,$acess_token,$store_hash){
	$data = array();
	if(!empty($cartId) && !empty($email_id)){
		$conn = getConnection();
		$header = array(
				"store_hash: ".$store_hash,
				"X-Auth-Token: ".$acess_token,
				"Accept: application/json",
				"Content-Type: application/json"
			);
		$request = '';
		$url = STORE_URL.$store_hash.'/v3/checkouts/'.$cartId;
		//print_r($url);exit;
		$ch = curl_init($url); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		//curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
		//curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$res = curl_exec($ch);
		curl_close($ch);
		//print_r($res);exit;
		$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
				
		$stmt = $conn->prepare($log_sql);
		$stmt->execute([$email_id,"BigCommerce","Checkout",addslashes($url),addslashes($request),addslashes($res)]);
		
		if(!empty($res)){
			$res = json_decode($res,true);
			if(isset($res['data'])){
				$data = $res['data'];
			}
		}
	}
	
	return $data;
}
?>