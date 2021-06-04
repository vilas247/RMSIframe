<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once('db-config.php');
require_once('config.php');

//print_r($_REQUEST);exit;
$data = array();
$data['status'] = false;
$data['error'] = '';

try
{
	$conn = getConnection();
	$urlT    = TOKEN_URL;
	$request = '';
	define('TIMEOUT', 1000000);
	$httpheadersT[] = 'Accept:application/json';
	$httpheadersT[] = 'Content-Type:application/json';
	$httpheadersT[] = 'X-Auth-Secret:' . AUTH_SECRET;
	$httpheadersT[] = 'X-Auth-Client:' . AUTH_CLIENT;

	$chT = curl_init($urlT);
	curl_setopt($chT, CURLOPT_HTTPHEADER, $httpheadersT);
	curl_setopt($chT, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($chT, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($chT, CURLOPT_POST, true);
	curl_setopt($chT, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($chT, CURLOPT_TIMEOUT, TIMEOUT);
	curl_setopt($chT, CURLOPT_POSTFIELDS, $request);

	$resultT = curl_exec($chT);

	$err = curl_error($chT);

	$resultT = json_decode($resultT, true);

	$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
	$stmt= $conn->prepare($log_sql);
	$stmt->execute(["info@insert.com","bigCommerce_launch","token_creation",addslashes($urlT),'',addslashes(json_encode($resultT, true))]);
			
	if (isset($resultT['data']['access_token']) && $resultT['data']['access_token'] != '')
	{
		$refreshToken   = $resultT['data']['access_token'];
		$accountRequest = '{
			"name": "' . $_POST['storeName'] . '",
			"user_email": "' . $_POST['storeEmail'] . '",
			"primary_contact": {
				"first_name": "' . $_POST['firstName'] . '",
				"last_name": "' . $_POST['LastName'] . '",
				"email": "' . $_POST['storeEmail'] . '",
				"district": "' . $_POST['County'] . '",
				"address_line_1": "' . $_POST['address1'] . '",
				"city": "' . $_POST['City'] . '",
				"postal_code": "' . $_POST['postalCode'] . '",
				"country": "' . $_POST['Country'] . '",
				"phone_number": "' . $_POST['phoneNumber'] . '"
			}
		}';

		$urlC    = ACCOUNT_URL;
		$request = json_encode(json_decode($accountRequest), true);

		$httpheadersC[] = 'Accept:application/json';
		$httpheadersC[] = 'Content-Type:application/json';
		$httpheadersC[] = 'X-Auth-Token:' . $refreshToken;
		$httpheadersC[] = 'X-Auth-Client:' . AUTH_CLIENT;

		$chC = curl_init($urlC);
		curl_setopt($chC, CURLOPT_HTTPHEADER, $httpheadersC);
		curl_setopt($chC, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($chC, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($chC, CURLOPT_POST, true);
		curl_setopt($chC, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($chC, CURLOPT_TIMEOUT, TIMEOUT);
		curl_setopt($chC, CURLOPT_POSTFIELDS, $request);

		$resultC = curl_exec($chC);

		$err = curl_error($chC);

		$resultC  = json_decode($resultC, true);

		$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
		$stmt= $conn->prepare($log_sql);
		$stmt->execute(["info@insert.com","bigCommerce_launch","account_creation",addslashes($urlC),addslashes($request),addslashes(json_encode($resultC, true))]);
	
		if (isset($resultC['data']['id']) && $resultC['data']['id'] != '')
		{
			$accountId    = $resultC['data']['id'];
			$storeRequest = '{
					  "plan_sku": "STORE-TRIAL-15DAY",
					  "store_name": "' . $_POST['storeName'] . '",
					  "country": "' . $_POST['Country'] . '"
				}';
			$urlS         = 'https://api.integration.zone/franchises/' . MERCHANT_TOKEN . '/v1/accounts/' . $accountId . '/stores';

			$request = json_encode(json_decode($storeRequest), true);

			$httpheadersS[] = 'Accept:application/json';
			$httpheadersS[] = 'Content-Type:application/json';
			$httpheadersS[] = 'X-Auth-Token:' . $refreshToken;
			$httpheadersS[] = 'X-Auth-Client:' . AUTH_CLIENT;

			$chS = curl_init($urlS);
			curl_setopt($chS, CURLOPT_HTTPHEADER, $httpheadersS);
			curl_setopt($chS, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($chS, CURLOPT_SSL_VERIFYPEER, false);

			curl_setopt($chS, CURLOPT_POST, true);
			curl_setopt($chS, CURLOPT_FOLLOWLOCATION, true);
			curl_setopt($chS, CURLOPT_TIMEOUT, TIMEOUT);
			curl_setopt($chS, CURLOPT_POSTFIELDS, $request);

			$resultS = curl_exec($chS);
			$resultS = json_decode($resultS, true);

			$log_sql = 'insert into api_log(email_id,type,action,api_url,api_request,api_response) values(?,?,?,?,?,?)';
			$stmt= $conn->prepare($log_sql);
			$stmt->execute(["info@insert.com","bigCommerce_launch","store_creation",addslashes($urlS),addslashes($request),addslashes(json_encode($resultS, true))]);

			if (isset($resultS['data']['id']))
			{
				$storeId          = $resultS['data']['id'];
				$data['store_id'] = $storeId;
				$data['status'] = true;
			}else{
				$data['error']        = "Something error While creating Bigcommerce account";
			}
		}
		else
		{
			$data['error']        = $resultC['errors'];
		}
	}
	else
	{
			$data['error']        = $resultT['errors'];
	}
}
catch (\Exception $e)
{
	$data['error']        = $e->getMessage();
}

echo json_encode($data,true);exit;
?>