<?php 

include("url.class.php");

$url = new URL("http://127.0.0.1/testes/php-friendly-urls-class/api",true);

$url->addRule("posicao",0);
var_dump($url->get('posicao'));



?>