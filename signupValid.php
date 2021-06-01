<?php
/**
	* Token Validation Page
	* Author 247Commerce
	* Date 22 MAR 2021
*/
require_once('config.php');
require_once('db-config.php');

if(isset($_REQUEST['email_id']) && isset($_REQUEST['password'])){
	$conn = getConnection();
	$email_id = @$_REQUEST['email_id'];
	if(!empty($email_id)){
		$stmt = $conn->prepare("select * from rms_token_validation where email_id='".$email_id."'");
		$stmt->execute();
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		//print_r($result[0]);exit;
		if (isset($result[0])) {
			header("Location:signup.php?error=1&errorMsg=".base64_encode("Email already Exists Please Login."));
		}else{
			$sellerdb = '247c'.strtotime(date('y-m-d h:m:s'));
			$sql = 'insert into rms_token_validation(email_id,sellerdb,password) values("'.$email_id.'","'.$sellerdb.'","'.$_REQUEST['password'].'")';
			$conn->exec($sql);
			header("Location:login.php?signup=1");
		}
	}else{
		header("Location:signup.php");
	}
}else{
	header("Location:signup.php");
}
?>