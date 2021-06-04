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
	
if(isset($_REQUEST['email_id'])){
	$conn = getConnection();
	$email_id = @$_REQUEST['email_id'];
	if(!empty($email_id)){
		$stmt = $conn->prepare("select * from rms_token_validation where email_id=?");
		$stmt->execute([$email_id]);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$result = $stmt->fetchAll();
		//print_r($result[0]);exit;
		if (isset($result[0])) {
			// String of all alphanumeric character
			$str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$newpassword = random_strings(8);
			$usql = "update rms_token_validation set password=? where email_id=?";
			$stmt_u = $conn->prepare($usql);
			$stmt_u->execute([$newpassword,$email_id]);
			sendMail($email_id,$newpassword);
			header("Location:fpsuccess.php");
		}else{
			header("Location:forgotPassword.php?error=1");
		}
	}else{
		header("Location:forgotPassword.php?error=1");
	}
}else{
	header("Location:forgotPassword.php?error=1");
}

// This function will return a random
// string of specified length
function random_strings($length_of_string)
{
  
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
  
    // Shufle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result), 
                       0, $length_of_string);
}

function sendMail($email,$password){
	if(!empty($email)){
		require_once('PHPMailer/PHPMailerAutoload.php');
		$mail = new PHPMailer;
		$mail->isSMTP();                                      // Set mailer to use SMTP
		$mail->Host = 'auth.smtp.1and1.co.uk';                       // Specify main and backup server
		$mail->SMTPAuth = true;                               // Enable SMTP authentication			

		$mail->Username = 'notifications@247cloudhub.co.uk';                   // SMTP username
		$mail->Password = '*987Commerce12';         

		$mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted
		$mail->Port = 587;                                    //Set the SMTP port number - 587 for authenticated TLS
		$mail->setFrom('notifications@247cloudhub.co.uk', 'Retail Merchant Services');     //Set who the message is to be sent from
		$mail->addAddress($email);  // Add a recipient
		//$mail->AddCC('krishnamaneni.villu@gmail.com');
		//$mail->addAddress('krishnamaneni.villu@gmail.com');  // Add a recipient
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = 'Password Reset(Retail Merchant Services)';
		$mail->msgHTML('<html>
			<body>
				<p style="padding: 20px 0px;">Your new password for loging in is as follows: '.$password.'</p>
		   </body>
		</html>');
		$mailresp = $mail->send();
		//print_r($mailresp);
	}
}
?>