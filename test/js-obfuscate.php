<?php

require_once dirname(__FILE__).'/../lib/js-obfuscator.php';

$f = $_GET['f'] ?? false;
$script = JSObfuscator::getFile($f);

header('Content-Type: text/javascript');
die($script);

?>