<?php 

include("url.class.php");

$url = new URL();

echo "<pre>";
//print_r($url->agora());
print_r($_GET);
print_r($url->contem("Testes"));
echo "</pre>";



?>