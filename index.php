<?php 

include("UrlClass.php");

$url = new URL("http://127.0.0.1/common/php-friendly-urls-class");

$redirect_robots = true;
/* Force ROBOTS.TXT redirection to NOT cached version*/
if($redirect_robots && preg_match("/\/robots\.txt/",$_SERVER['REQUEST_URI'])){
	header("HTTP/1.1 301 Moved Permanently"); 
	header('Location:/robots.txt?v='.date("YMD"));
	exit();
}

?>
