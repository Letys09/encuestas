<?php
	ini_set('display_errors',1);

	if(!defined('SITE_NAME')) define('SITE_NAME', 'EncuestasATM');
	if(!defined('URL_ROOT')) define('URL_ROOT',  'https://trasladosuniversales.com.mx/app/encuestas/public'); 
	if(!defined('SITE_ROOT')) define('SITE_ROOT',  'https://trasladosuniversales.com.mx/app/encuestas/public'); 
	if(!defined('URL_API')) define('URL_API',  'https://trasladosuniversales.com.mx/app/encuestas/public/'); 
	
	if (!isset($_SESSION)) session_start();
	date_default_timezone_set('America/Mexico_City');
	
	// $_SESSION['mail_username'] = "encuestas.atm@trasladosuniversales.com.mx";
	// $_SESSION['mail_pwd'] = 'o941H5*!R@uO';

	// $_SESSION['mail_username'] = "encuestas.atm@atmexicana.com.mx";
	// $_SESSION['mail_pwd'] = 't@jjWp34il';

	$_SESSION['mail_username'] = "atm@ddsmedia.net";
	$_SESSION['mail_pwd'] = 'SushiM4y3';
?>