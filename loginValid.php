<?php
/**
	* Token Validation Page
	* Author 247Commerce
	* Date 22 MAR 2021
*/
require_once('config.php');
require_once('db-config.php');

if(!isset($_SESSION)){
	session_start();
}
	
if(isset($_REQUEST['email_id']) && isset($_REQUEST['password'])){
	$conn = getConnection();
	$email_id = @$_REQUEST['email_id'];
	if(!empty($email_id)){
		$stmt = $conn->prepare("select * from rms_token_validation where email_id='".$email_id."' and password='".$_REQUEST['password']."'");
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		//print_r($result[0]);exit;
		if (isset($result[0])) {
			$_SESSION['is247Auth'] = true;
			$_SESSION['is247Email'] = $email_id;
			$_SESSION['is247ValidId'] = $result[0]['validation_id'];
			header("Location:settings.php");
		}else{
			header("Location:login.php?error=1");
		}
	}else{
		header("Location:signup.php");
	}
}else{
	header("Location:signup.php");
}
?>