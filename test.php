<?php
$in = fopen('php://stdin', 'r');
while(true) {
	$string = fgets($in);
	echo urlencode($string) . PHP_EOL;
	echo rawurlencode($string) . PHP_EOL;
	echo PHP_EOL;
}
fclose($in);
?>
