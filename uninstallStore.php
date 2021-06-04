<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once('config.php');
require_once('db-config.php');
require_once('hooks.php');

/*require 'log-autoloader.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;*/

$conn = getConnection();
$email_id = '';
if(isset($_SESSION['is247Email'])){
	$email_id = $_SESSION['is247Email'];
}

if(!empty($email_id)){
	unInstallScripts($email_id);
	header("Location:dashboard.php");
}

function unInstallScripts($email){
	if(!empty($email)){
		$conn = getConnection();
				
		$stmt = $conn->prepare("select * from rms_token_validation where email_id=?");
		$stmt->execute([$email]);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		//print_r($result[0]);exit;
		if (isset($result[0])) {
			$result = $result[0];
			try{
				deleteScripts($result['sellerdb'],$result['acess_token'],$result['store_hash'],$email);
			}catch(Exception $e) {
			}
			
			try{
				deleteCustomPage($result['sellerdb'],$result['acess_token'],$result['store_hash'],$email);
			}catch(Exception $e) {
			}
			
			try{
				uninstallWebhooks($email,$result['store_hash'],$result['acess_token']);
			}catch(Exception $e) {
			}
			try{
				$usql = "update rms_token_validation set is_enable=0,acess_token='',store_hash='' where email_id='".$email."'";
				//echo $usql;exit;
				$stmt = $conn->prepare($usql);
				$stmt->execute();
			}catch(Exception $e) {
			}
		}
	}
}
   
function deleteScripts($sellerdb,$acess_token,$store_hash,$email_id){
	$rStatus = 0;
	$conn = getConnection();
	$stmt = $conn->prepare("select * from rms_scripts where script_email_id=?");
	$stmt->execute([$email_id]);
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll();
	//print_r($result[0]);exit;
	if (count($result) > 0) {
		foreach($result as $k=>$v){
			//$auth_token = '4ir2j1tpf5cw3pzx7ea4ual2jrei8cd';
			$header = array(
				"X-Auth-Client: ".$acess_token,
				"X-Auth-Token: ".$acess_token,
				"Accept: application/json",
				"Content-Type: application/json"
			);
			$request = '';
			//print_r($request);exit;
			$url = STORE_URL.$store_hash.'/v3/content/scripts/'.$v['script_code'];
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
			curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$res = curl_exec($ch);
			//print_r($res);exit;
			curl_close($ch);
			$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
			$stmt= $conn->prepare($log_sql);
			$stmt->execute([$email_id, "BigCommerce", "script_tag_deletion",addslashes($url),addslashes($request),addslashes($res)]);

			$sql = 'delete from rms_scripts where script_id='.$v['script_id'];
			//echo $sql;exit;
			$conn->exec($sql);
		}
	}
}
function deleteCustomPage($sellerdb,$acess_token,$store_hash,$email_id){
	$rStatus = 0;
	$conn = getConnection();
	$stmt = $conn->prepare("select * from 247custompages where email_id=?");
	$stmt->execute([$email_id]);
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll();
	//print_r($result[0]);exit;
	if (count($result) > 0) {
		foreach($result as $k=>$v){
			$header = array(
				"X-Auth-Token: ".$acess_token,
				"Accept: application/json",
				"Content-Type: application/json"
			);
			$request = '';
			//print_r($request);exit;
			$url = STORE_URL.$store_hash.'/v2/pages/'.$v['page_bc_id'];
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
			curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$res = curl_exec($ch);
			//print_r($res);exit;
			curl_close($ch);
			$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
			$stmt= $conn->prepare($log_sql);
			$stmt->execute([$email_id, "BigCommerce", "Custom Page Deletion",addslashes($url),addslashes($request),addslashes($res)]);
			if(empty($res)){
				$sql = 'delete from 247custompages where id='.$v['id'];
				//echo $sql;exit;
				$conn->exec($sql);
			}
		}
	}
}
function uninstallWebhooks($email_id,$store_hash,$acess_token){

	$conn = getConnection();
	$stmt = $conn->prepare("select * from 247webhooks where email_id=?");
	$stmt->execute([$email_id]);
	$stmt->setFetchMode(PDO::FETCH_ASSOC);
	$result = $stmt->fetchAll();
	//print_r($result[0]);exit;
	if (count($result) > 0) {
		foreach($result as $k=>$v){
			$url = STORE_URL.$store_hash.'/v3/hooks/'.$v['webhook_bc_id'];
			$header = array(
				"X-Auth-Token: ".$acess_token,
				"Accept: application/json",
				"Content-Type: application/json"
			);
			$request = json_encode($request);
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST,"DELETE");
			curl_setopt($ch, CURLOPT_ENCODING, "gzip,deflate");
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			
			$res = curl_exec($ch);
			curl_close($ch);
			//print_r($res);exit;
			$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
			$stmt= $conn->prepare($log_sql);
			$stmt->execute([$email_id, "BigCommerce", "Webhooks",addslashes($url),addslashes($request),addslashes($res)]);
			if(!empty($res)){
				$check_errors = json_decode($res);
				if(isset($check_errors->errors)){
				}else{
					if(json_last_error() === 0){
						$res = json_decode($res,true);
						if(isset($res['data']['id'])){
							$data = $res['data'];
						}
					}
				}
			}
			$sqli = "delete from 247webhooks where id='".$v['id']."'";
			$conn->query($sqli);
		}
	}
}

?>