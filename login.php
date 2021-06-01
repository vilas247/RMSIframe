<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login</title>


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
								$signup=0;
								if(isset($_REQUEST['signup']) && $_REQUEST['signup'] == 1){
									$signup=1;
								} 
								$error=0;
								if(isset($_REQUEST['error']) && $_REQUEST['error'] == 1){
									$error=1;
								} 
							?>
						<div><span id="error_show" style="color:green;<?= ($signup == 1)?'':'display:none;' ?>" ><b>Singup Successful Please login here</b></span></div>
						<div><span id="error_show" style="color:red;<?= ($error == 1)?'':'display:none;' ?>" ><b>Email / Password is wrong</b></span></div>
                      <form method="POST" action="loginValid.php" >
                      <div class="form-group input-height">
                         <label for="exampleInputEmail1">Enter Your Email Id</label>
                         <input type="email" name="email_id" class="form-control" id="exampleInputEmail1" placeholder="hello@rms.com">
                      </div>
                      <div class="form-group input-height">
                          <label>Password</label>
                          <div class="input-group" id="show_hide_password">
                            <input class="form-control" name="password" type="password" placeholder="••••••••••">
                            <div class="input-group-addon">
                              <a href=""><i class="fas fa-eye"></i></a>
                            </div>
                          </div>
                          <p class="text-end"><a href="#" class="btn-forget">Forget Password?</a></p>
                      </div>
                       <div class="form-group">
                        <button type="submit" class="btn btn-acc d-block w-100 btn-lg btn-create">Login</button>
                      </div>
                   </form>
                </div>
                <div class="col-md-12 vendor-logo text-center"><img src="images/vendor_logo.jpg"></div>
             </div>
          </div>
       </div>
       <div class="col-md-12 signin text-center"><p>I'm a new user.<a href="signup.php">Sign Up</a></p></div>
    </div>    
  </div>

<script src="js/jquery-min.js"></script>
<script src="js/bootstrap.min.js"></script>
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
    </script>
</body>
</html>