<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Forgot Password</title>


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
	<link rel="stylesheet" href="css/toaster/toaster.css">
	<link rel="stylesheet" href="css/247rmsiframeloader.css">

</head>
<body>
  <div class="container rms-design">
      <div class="row content-area custom-width">
       <div class="col-md-4 offset-md-4">
          <div class="row">
             <div class="box">
                <div class="col-md-12">
                   <h1>Hello!</h1>
                   <h4>We’re happy to have you here again!</h4>
				   <?php
								$error=0;
								if(isset($_REQUEST['error']) && $_REQUEST['error'] == 1){
									$error=1;
								} 
							?>
						<div><span id="error_show" style="color:red;<?= ($error == 1)?'':'display:none;' ?>" ><b>Email not registered</b></span></div>
                      <form method="POST" action="forgotPasswordValid.php" >
                      <div class="form-group input-height">
                         <label for="exampleInputEmail1">Enter Registered Email Id</label>
                         <input type="email" name="email_id" class="form-control" id="exampleInputEmail1" placeholder="name@youremail.com" required>
                      </div>
                       <div class="form-group">
                        <button type="submit" class="btn btn-acc d-block w-100 btn-lg btn-create">Submit</button>
                      </div>
                   </form>
                </div>
                <div class="col-md-12 vendor-logo text-center"><img src="images/logo.png"></div>
             </div>
          </div>
       </div>
       <div class="col-md-12 signin text-center"><p>I'm already member.<a href="login.php">Login</a></p></div>
    </div>    
  </div>
<style>
.vendor-logo>img{
	height:130px;
}
@media (min-width: 360px) {
	.vendor-logo>img{
		height:105px;
	}
}
</style>
<script src="js/jquery-min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" charset="utf8" src="js/toaster/jquery.toaster.js"></script>
<script type="text/javascript" charset="utf8" src="js/247rmsiframeloader.js"></script>
<script type="text/javascript">
      $(document).ready(function() {
    $("#show_hide_password a").on('click', function(event) {
              event.preventDefault();
              if($('#show_hide_password input').attr("type") == "text"){
                  $('#show_hide_password input').attr('type', 'password');
                  $('#show_hide_password i').addClass( "fa-eye-slash" );
                  $('#show_hide_password i').removeClass( "fa-eye" );
              }else if($('#show_hide_password input').attr("type") == "password"){
                  $('#show_hide_password input').attr('type', 'text');
                  $('#show_hide_password i').removeClass( "fa-eye-slash" );
                  $('#show_hide_password i').addClass( "fa-eye" );
              }
          });
      });
	  var text = "Please wait...";
		var current_effect = "bounce";
		$('body').on('submit','#signupValid',function(e){
				$("body").waitMe({
					effect: current_effect,
					text: text,
					bg: "rgba(255,255,255,0.7)",
					color: "#000",
					maxSize: "",
					waitTime: -1,
					source: "images/img.svg",
					textPos: "vertical",
					fontSize: "",
					onClose: function(el) {}
				});
		});
    </script>
</body>
</html>