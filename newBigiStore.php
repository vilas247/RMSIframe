<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
require_once('db-config.php');
require_once('config.php');
?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.83.1">
    <title>Store Launch</title>

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
	<link rel="stylesheet" href="css/toaster/toaster_latest.css">
    <link href="css/247rmsiframeloader.css" rel="stylesheet">
	<style>
		.hbspt-form{
			display:none;
		}
	</style>
</head>
  <body>
    <main class="main-content py-5">
      <div class="container">
        <div class="row">
          <form name="bigistoreForm" id="bigistoreForm" method="POST" action="newBigiStoreSubmit.php">
		  <div class="col-md-12 box lanch-store">         
            <div class="row brd-btm">
                <div class="col-md-6 mb-4">
                  <h3>BigCommerce Store Launch</h3>
                </div>
                <div class="col-md-6 mb-4">
                  <p class="text-md-end"><img src="images/logo-bigcommerce.svg" width="200" alt=""></p>
                </div>               
            </div>
            <div class="row py-2">
              <div class="col-md-6 my-2">
                <label>Company/Business Name</label>
                <input type="text" class="form-control" name="storeName" id="storeName" placeholder="rms" required>
              </div>
              <div class="col-md-6 my-2">
                <label>Company Email</label>
                <input type="email" class="form-control" name="storeEmail" id="storeEmail" placeholder="name@youremail.com" required>
              </div>
            </div>
            <div class="row py-2">
              <div class="col-md-6 my-2">
                <label>First Name</label>
                <input type="text" class="form-control" name="firstName" id="firstName" placeholder="" required>
              </div>
              <div class="col-md-6 my-2">
                <label>Last Name</label>
                <input type="text" class="form-control" name="LastName" id="LastName" placeholder="" required>
              </div>
            </div>
            <div class="row py-2">
              <div class="col-md-6 my-2">
                <label>Address Line1</label>
                <input type="text" class="form-control" name="address1" id="address1" placeholder="" required />
              </div>
              <div class="col-md-6 my-2">
                <label>Address Line2</label>
                <input type="text" class="form-control" id="confi-id" placeholder="">
              </div>
            </div>
            <div class="row py-2">
              <div class="col-md-4 my-2">
                <label>City</label>
                <input type="text" class="form-control" name="City" id="City" placeholder="" required />
              </div>
              <div class="col-md-4 my-2">
                <label>Postal Code</label>
                <input type="text" class="form-control" name="postalCode" id="postalCode" placeholder="" required />
              </div>
              <div class="col-md-4 my-2">
                <label>County</label>
                <input type="text" class="form-control" name="County" id="County" placeholder="" required />
              </div>
            </div>
            <div class="row py-2">
              <div class="col-md-4 my-2">
                <label>Country</label>
                <select class="form-control custom-input" name="Country" id="Country" required>
					<option value="AF">Afghanistan</option>
					<option value="AX">Åland Islands</option>
					<option value="AL">Albania</option>
					<option value="DZ">Algeria</option>
					<option value="AS">American Samoa</option>
					<option value="AD">Andorra</option>
					<option value="AO">Angola</option>
					<option value="AI">Anguilla</option>
					<option value="AQ">Antarctica</option>
					<option value="AG">Antigua and Barbuda</option>
					<option value="AR">Argentina</option>
					<option value="AM">Armenia</option>
					<option value="AW">Aruba</option>
					<option value="AU">Australia</option>
					<option value="AT">Austria</option>
					<option value="AZ">Azerbaijan</option>
					<option value="BS">Bahamas</option>
					<option value="BH">Bahrain</option>
					<option value="BD">Bangladesh</option>
					<option value="BB">Barbados</option>
					<option value="BY">Belarus</option>
					<option value="BE">Belgium</option>
					<option value="BZ">Belize</option>
					<option value="BJ">Benin</option>
					<option value="BM">Bermuda</option>
					<option value="BT">Bhutan</option>
					<option value="BO">Bolivia, Plurinational State of</option>
					<option value="BQ">Bonaire, Sint Eustatius and Saba</option>
					<option value="BA">Bosnia and Herzegovina</option>
					<option value="BW">Botswana</option>
					<option value="BV">Bouvet Island</option>
					<option value="BR">Brazil</option>
					<option value="IO">British Indian Ocean Territory</option>
					<option value="BN">Brunei Darussalam</option>
					<option value="BG">Bulgaria</option>
					<option value="BF">Burkina Faso</option>
					<option value="BI">Burundi</option>
					<option value="KH">Cambodia</option>
					<option value="CM">Cameroon</option>
					<option value="CA">Canada</option>
					<option value="CV">Cape Verde</option>
					<option value="KY">Cayman Islands</option>
					<option value="CF">Central African Republic</option>
					<option value="TD">Chad</option>
					<option value="CL">Chile</option>
					<option value="CN">China</option>
					<option value="CX">Christmas Island</option>
					<option value="CC">Cocos (Keeling) Islands</option>
					<option value="CO">Colombia</option>
					<option value="KM">Comoros</option>
					<option value="CG">Congo</option>
					<option value="CD">Congo, the Democratic Republic of the</option>
					<option value="CK">Cook Islands</option>
					<option value="CR">Costa Rica</option>
					<option value="CI">Côte d'Ivoire</option>
					<option value="HR">Croatia</option>
					<option value="CU">Cuba</option>
					<option value="CW">Curaçao</option>
					<option value="CY">Cyprus</option>
					<option value="CZ">Czech Republic</option>
					<option value="DK">Denmark</option>
					<option value="DJ">Djibouti</option>
					<option value="DM">Dominica</option>
					<option value="DO">Dominican Republic</option>
					<option value="EC">Ecuador</option>
					<option value="EG">Egypt</option>
					<option value="SV">El Salvador</option>
					<option value="GQ">Equatorial Guinea</option>
					<option value="ER">Eritrea</option>
					<option value="EE">Estonia</option>
					<option value="ET">Ethiopia</option>
					<option value="FK">Falkland Islands (Malvinas)</option>
					<option value="FO">Faroe Islands</option>
					<option value="FJ">Fiji</option>
					<option value="FI">Finland</option>
					<option value="FR">France</option>
					<option value="GF">French Guiana</option>
					<option value="PF">French Polynesia</option>
					<option value="TF">French Southern Territories</option>
					<option value="GA">Gabon</option>
					<option value="GM">Gambia</option>
					<option value="GE">Georgia</option>
					<option value="DE">Germany</option>
					<option value="GH">Ghana</option>
					<option value="GI">Gibraltar</option>
					<option value="GR">Greece</option>
					<option value="GL">Greenland</option>
					<option value="GD">Grenada</option>
					<option value="GP">Guadeloupe</option>
					<option value="GU">Guam</option>
					<option value="GT">Guatemala</option>
					<option value="GG">Guernsey</option>
					<option value="GN">Guinea</option>
					<option value="GW">Guinea-Bissau</option>
					<option value="GY">Guyana</option>
					<option value="HT">Haiti</option>
					<option value="HM">Heard Island and McDonald Islands</option>
					<option value="VA">Holy See (Vatican City State)</option>
					<option value="HN">Honduras</option>
					<option value="HK">Hong Kong</option>
					<option value="HU">Hungary</option>
					<option value="IS">Iceland</option>
					<option value="IN">India</option>
					<option value="ID">Indonesia</option>
					<option value="IR">Iran, Islamic Republic of</option>
					<option value="IQ">Iraq</option>
					<option value="IE">Ireland</option>
					<option value="IM">Isle of Man</option>
					<option value="IL">Israel</option>
					<option value="IT">Italy</option>
					<option value="JM">Jamaica</option>
					<option value="JP">Japan</option>
					<option value="JE">Jersey</option>
					<option value="JO">Jordan</option>
					<option value="KZ">Kazakhstan</option>
					<option value="KE">Kenya</option>
					<option value="KI">Kiribati</option>
					<option value="KP">Korea, Democratic People's Republic of</option>
					<option value="KR">Korea, Republic of</option>
					<option value="KW">Kuwait</option>
					<option value="KG">Kyrgyzstan</option>
					<option value="LA">Lao People's Democratic Republic</option>
					<option value="LV">Latvia</option>
					<option value="LB">Lebanon</option>
					<option value="LS">Lesotho</option>
					<option value="LR">Liberia</option>
					<option value="LY">Libya</option>
					<option value="LI">Liechtenstein</option>
					<option value="LT">Lithuania</option>
					<option value="LU">Luxembourg</option>
					<option value="MO">Macao</option>
					<option value="MK">Macedonia, the former Yugoslav Republic of</option>
					<option value="MG">Madagascar</option>
					<option value="MW">Malawi</option>
					<option value="MY">Malaysia</option>
					<option value="MV">Maldives</option>
					<option value="ML">Mali</option>
					<option value="MT">Malta</option>
					<option value="MH">Marshall Islands</option>
					<option value="MQ">Martinique</option>
					<option value="MR">Mauritania</option>
					<option value="MU">Mauritius</option>
					<option value="YT">Mayotte</option>
					<option value="MX">Mexico</option>
					<option value="FM">Micronesia, Federated States of</option>
					<option value="MD">Moldova, Republic of</option>
					<option value="MC">Monaco</option>
					<option value="MN">Mongolia</option>
					<option value="ME">Montenegro</option>
					<option value="MS">Montserrat</option>
					<option value="MA">Morocco</option>
					<option value="MZ">Mozambique</option>
					<option value="MM">Myanmar</option>
					<option value="NA">Namibia</option>
					<option value="NR">Nauru</option>
					<option value="NP">Nepal</option>
					<option value="NL">Netherlands</option>
					<option value="NC">New Caledonia</option>
					<option value="NZ">New Zealand</option>
					<option value="NI">Nicaragua</option>
					<option value="NE">Niger</option>
					<option value="NG">Nigeria</option>
					<option value="NU">Niue</option>
					<option value="NF">Norfolk Island</option>
					<option value="MP">Northern Mariana Islands</option>
					<option value="NO">Norway</option>
					<option value="OM">Oman</option>
					<option value="PK">Pakistan</option>
					<option value="PW">Palau</option>
					<option value="PS">Palestinian Territory, Occupied</option>
					<option value="PA">Panama</option>
					<option value="PG">Papua New Guinea</option>
					<option value="PY">Paraguay</option>
					<option value="PE">Peru</option>
					<option value="PH">Philippines</option>
					<option value="PN">Pitcairn</option>
					<option value="PL">Poland</option>
					<option value="PT">Portugal</option>
					<option value="PR">Puerto Rico</option>
					<option value="QA">Qatar</option>
					<option value="RE">Réunion</option>
					<option value="RO">Romania</option>
					<option value="RU">Russian Federation</option>
					<option value="RW">Rwanda</option>
					<option value="BL">Saint Barthélemy</option>
					<option value="SH">Saint Helena, Ascension and Tristan da Cunha</option>
					<option value="KN">Saint Kitts and Nevis</option>
					<option value="LC">Saint Lucia</option>
					<option value="MF">Saint Martin (French part)</option>
					<option value="PM">Saint Pierre and Miquelon</option>
					<option value="VC">Saint Vincent and the Grenadines</option>
					<option value="WS">Samoa</option>
					<option value="SM">San Marino</option>
					<option value="ST">Sao Tome and Principe</option>
					<option value="SA">Saudi Arabia</option>
					<option value="SN">Senegal</option>
					<option value="RS">Serbia</option>
					<option value="SC">Seychelles</option>
					<option value="SL">Sierra Leone</option>
					<option value="SG">Singapore</option>
					<option value="SX">Sint Maarten (Dutch part)</option>
					<option value="SK">Slovakia</option>
					<option value="SI">Slovenia</option>
					<option value="SB">Solomon Islands</option>
					<option value="SO">Somalia</option>
					<option value="ZA">South Africa</option>
					<option value="GS">South Georgia and the South Sandwich Islands</option>
					<option value="SS">South Sudan</option>
					<option value="ES">Spain</option>
					<option value="LK">Sri Lanka</option>
					<option value="SD">Sudan</option>
					<option value="SR">Suriname</option>
					<option value="SJ">Svalbard and Jan Mayen</option>
					<option value="SZ">Swaziland</option>
					<option value="SE">Sweden</option>
					<option value="CH">Switzerland</option>
					<option value="SY">Syrian Arab Republic</option>
					<option value="TW">Taiwan, Province of China</option>
					<option value="TJ">Tajikistan</option>
					<option value="TZ">Tanzania, United Republic of</option>
					<option value="TH">Thailand</option>
					<option value="TL">Timor-Leste</option>
					<option value="TG">Togo</option>
					<option value="TK">Tokelau</option>
					<option value="TO">Tonga</option>
					<option value="TT">Trinidad and Tobago</option>
					<option value="TN">Tunisia</option>
					<option value="TR">Turkey</option>
					<option value="TM">Turkmenistan</option>
					<option value="TC">Turks and Caicos Islands</option>
					<option value="TV">Tuvalu</option>
					<option value="UG">Uganda</option>
					<option value="UA">Ukraine</option>
					<option value="AE">United Arab Emirates</option>
					<option value="GB" selected="selected">United Kingdom</option>
					<option value="US">United States</option>
					<option value="UM">United States Minor Outlying Islands</option>
					<option value="UY">Uruguay</option>
					<option value="UZ">Uzbekistan</option>
					<option value="VU">Vanuatu</option>
					<option value="VE">Venezuela, Bolivarian Republic of</option>
					<option value="VN">Viet Nam</option>
					<option value="VG">Virgin Islands, British</option>
					<option value="VI">Virgin Islands, U.S.</option>
					<option value="WF">Wallis and Futuna</option>
					<option value="EH">Western Sahara</option>
					<option value="YE">Yemen</option>
					<option value="ZM">Zambia</option>
					<option value="ZW">Zimbabwe</option>
				</select>
              </div>
              <div class="col-md-4 my-2">
                <label>Store Name</label>
                <input type="text" class="form-control" id="confi-id" placeholder="RMS" required>
              </div>
              <div class="col-md-4 my-2">
                <label>Store Plan</label>
				<select class="form-control custom-input" name="Storeplan" id="Storeplan">
					<option value="Standard">Standard ($29.95/mo)</option>
					<option value="Plus">Plus ($79.95/mo)</option>
					<option value="Pro">Pro ($299.95/mo)</option>
				</select>
              </div>
            </div>
			<div class="row py-2">
			   <div class="col-md-4 my-2">
                <label>Phone Number</label>
                <input type="text" class="form-control" name="phoneNumber" id="phoneNumber" placeholder="" required />
              </div>
              <div class="col-md-4 my-2">
                <label>Referral Description</label>
                <textarea class="form-control" id="referral_description" name="referral_description" placeholder="" required></textarea>
              </div>
              <div class="col-md-4 my-2">
                <label>BIGCOMMERCE Referral agent name</label>
                <textarea class="form-control" id="eat_pos_agent_name" name="eat_pos_agent_name" placeholder="" required></textarea>
              </div>
            </div>
          </div>
          <div class="col-md-12 lanch-store mt-3">
            <p class="text-end"><button type="submit" class="btn btn-primary btn-lg btn-store"><img src="images/shuttle.png" width="28" class="me-2"/>Launch Store</button></p>
          </div>
		  </form>
        </div>
      </div>
    </main>
    <script src="js/jquery-min.js"></script>
    <script src="js/bootstrap.min.js"></script>
	<script type="text/javascript" charset="utf8" src="js/toaster/toaster_latest.js"></script>
    <script src="js/247rmsiframeloader.js"></script>
<script charset="utf-8" type="text/javascript" src="//js.hsforms.net/forms/v2.js"></script>
  </body>
  </html>
  <script>
	toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": false,
  "positionClass": "toast-top-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "0",
  "hideDuration": "0",
  "timeOut": "0",
  "extendedTimeOut": "0",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
};
	var app_base_url = "<?= BASE_URL ?>";
	var text = "Please wait...";
	var current_effect = "bounce";
	$(document).ready(function() {
		$("body").on('submit','#bigistoreForm', function(e) {
			e.preventDefault();
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
			var firstname = $('body #firstName').val();
			var lastname = $('body #LastName').val();
			var phone = $('body #phoneNumber').val();
			var email = $('body #storeEmail').val();
			var company = $('body #storeName').val();
			var referral_description = $('body #referral_description').val();
			var eat_pos_agent_name = $('body #eat_pos_agent_name').val();
			var hubspotData = {firstname:firstname,lastname:lastname,phone:phone,email:email,company:company,referral_description:referral_description,eat_pos_agent_name:eat_pos_agent_name};
			
			$('body #firstname-4ddbffe9-f4b3-41e0-84d1-60342833365f_636').val(firstname);
			$('body #lastname-4ddbffe9-f4b3-41e0-84d1-60342833365f_636').val(lastname);
			$('body #phone-4ddbffe9-f4b3-41e0-84d1-60342833365f_636').val(phone);
			$('body #email-4ddbffe9-f4b3-41e0-84d1-60342833365f_636').val(email);
			$('body #company-4ddbffe9-f4b3-41e0-84d1-60342833365f_636').val(company);
			$('body #referral_description-4ddbffe9-f4b3-41e0-84d1-60342833365f_636').val(referral_description);
			$('body #eat_pos_agent_name-4ddbffe9-f4b3-41e0-84d1-60342833365f_636').val(eat_pos_agent_name);
			$('body #hsForm_4ddbffe9-f4b3-41e0-84d1-60342833365f_636').submit();
			$.ajax({ 
				type: 'post',
				url: app_base_url+"newBigiStoreSubmit.php", 
				data:$('body #bigistoreForm').serialize(),
				success: function (res) {
					res = $.parseJSON(res);
					$("body").waitMe("hide");
					if(res.status){
						toastr.success('BigCommerce store launched successfully. Please check your email to activate.');
					}else{
						toastr.error('This email is already assigned to another account. New BigCommerce Store launch Failed!');
					}
				}
			});
		});
	});
</script>
<script>
hbspt.forms.create({
	region: "na1",
	portalId: "2417425",
	//formId: "4ddbffe9-f4b3-41e0-84d1-60342833365f",
	formId: "4ddbffe9-f4b3-41e0-84d1-60342833365f",
	formInstanceId: '636',
	pageId: '44587340327',
});
</script>