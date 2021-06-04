<?php
/**
	* Main Config
	* Author 247Commerce
	* Date 22 FEB 2021
*/
define('BASE_URL','https://'.$_SERVER['HTTP_HOST'].'/');
define('STORE_URL','https://api.bigcommerce.com/stores/');

define('TOKEN_URL','*');
define('AUTH_SECRET','*');
define('AUTH_CLIENT','*');
define('ACCOUNT_URL','*');
define('MERCHANT_TOKEN','*');

define('RMSIFRAME_URL','https://gateway.cardstream.com/hosted/modal');

define('RMSIFRAME_SDK','https://gateway.cardstream.com/sdk/web/v1/js/hostedforms.min.js');
define('JSVALIDATE_SDK',BASE_URL.'js/jquery.validate.min.js');
define('JS_SDK',BASE_URL.'js/jquery.min.js');
?>