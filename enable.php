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

$conn = getConnection();
$email_id = '';
if(isset($_SESSION['is247Email'])){
	$email_id = $_SESSION['is247Email'];
}

	if(!empty($email_id)){
		$stmt = $conn->prepare("select * from rms_token_validation where email_id=?");
		$stmt->execute([$email_id]);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		//print_r($result[0]);exit;
		if ($result[0]) {
			$result = $result[0];
			if(!empty($result['merchant_id']) && !empty($result['cardstream_signature']) && !empty($result['acess_token']) && !empty($result['store_hash'])){
				$sellerdb = $result['sellerdb'];
				$acess_token = $result['acess_token'];
				$store_hash = $result['store_hash'];
				$res = createScripts($sellerdb,$acess_token,$store_hash,$email_id);
				if($res == "1"){
					$usql = "update rms_token_validation set is_enable=? where email_id=?";
					//echo $usql;exit;
					$stmt = $conn->prepare($usql);
					$stmt->execute(['1',$email_id]);
				}
				header("Location:dashboard.php?enabled=1");
			}else{
				header("Location:dashboard.php");
			}
		}else{
			header("Location:dashboard.php");
		}
	}else{
		header("Location:dashboard.php");
	}

function createScripts($sellerdb,$acess_token,$store_hash,$email_id){
	$conn = getConnection();
	$url = array();
	$rStatus = 0;
	$url[] = JS_SDK;
	$url[] = RMSIFRAME_SDK;
	$url[] = JSVALIDATE_SDK;
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
		  "name": "RetailMerchantIframeApp",
		  "description": "RetailMerchantIframe payment files",
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
?>