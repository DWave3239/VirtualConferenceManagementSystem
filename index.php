<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

// ----------------------- STATIC DEFINES ------------------------
define('LOG_ACCESS', 		false);
define('BASE_DIR', 			realpath('.'));
define('ASSETS_DIR', 		BASE_DIR."/assets/");
define('SYSTEM_DIR', 		BASE_DIR."/system/");
define('CONTROLLER_DIR', 	BASE_DIR."/controllers/");
define('CONFIG_DIR', 		BASE_DIR."/config/");
define('VENDOR_DIR', 		BASE_DIR."/vendor/");

// ----------------------- AUTH LEVELS ---------------------------
define('NO_AUTH', 			0);
define('PARTICIPANT',		10);
define('PRESENTER',			20);
define('SESSION_CHAIR',		30);
define('PROGRAM_CHAIR', 	40);
define('ADMINISTRATOR', 	50);

// get db access info, base_url and jaasauth key file path
if(getcwd() == '/var/www/html/bakk'){
	require "/home/marvin/serverSpecifics.php";
}else{
	require('/home/pi/serverSpecifics.php');
}
if(!isset($db) || empty($_GET)){
	echo file_get_contents("404.html");
	return;
}
define('ASSETS_URL', BASE_URL.'assets/');

define('_BASE_', BASE_URL."index.php?");


$route = array_keys($_GET)[0];
define("CURRENT_ROUTE", $route);
$split = explode('/', $route);

$controller = $split[0];
//if(!isset($split[1])) $split[1] = 'index'; // standard function
$method = $split[1];
$params = array_slice($split, 2);

// --------------------- ROUTING ------------------------
require_once SYSTEM_DIR."base.php";

if(!is_file(CONTROLLER_DIR.$controller.".php")){
	new Base(); // overwrite the exception handling
	throw new Exception("No controller ".$controller." found!");
}

require_once CONTROLLER_DIR.$controller.".php";
$controller_instance = new $controller($db);

if(!method_exists($controller_instance, $method)){
	new Base();
	throw new Exception("No method ".$method." in controller ".$controller." found!");
}

// ----------------- LOG BEGIN ---------------------------
if(LOG_ACCESS){
	// only log existing queries
	$ip = $_SERVER['REMOTE_ADDR'];

	if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
		$ipv4 = $ip;
		$ipv6 = "-";
	}else{
		$ipv4 = "-";
		$ipv6 = $ip;
	}

	$qres = $controller_instance->db->query("INSERT INTO requestLog (requestIPv4, requestIPv6, requestURI) VALUES (?, ?, ?)", array($ipv4, $ipv6, $route));
}
// ----------------- LOG END ---------------------------

// call routed function
$res = call_user_func_array(array($controller_instance, $method), $params);

?>
