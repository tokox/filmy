<?php
function get_data($file) {
        return json_decode(file_get_contents("../data/".$file.".json"), true);
}
function set_data($file, $data) {
        file_put_contents("../data/".$file.".json", json_encode($data, JSON_PRETTY_PRINT));
}
if(isset($_COOKIE['logged_in'])) {
	$hashes = get_data("hashes");
	unset($hashes[$_COOKIE['logged_in']]);
	setcookie("logged_in", "");
	set_data("hashes", $hashes);
	header("Location: /login.php");
}
?>
