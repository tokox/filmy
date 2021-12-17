<?php
function find_movie($movies, $code) {
	$codes = explode('/', $code);
	$akt = $movies;
	while(count($codes)) {
		if(!isset($movies[$codes[0]]))
			return -1;
		$akt = $movies[$codes[0]];
	}
	return $akt;
}
header("Content-Type: video/mp4");
if(isset($_COOKIE['logged_in'])) {
	$hashes = json_decode(file_get_contents("../hashes.json"), true);
	if(isset($hashes[$_COOKIE['logged_in']])) {
		$users = json_decode(file_get_contents("../users.json"), true);
		$user = $users[$hashes[$_COOKIE['logged_in']]];
		$movies = json_decode(file_get_contents("../movies.json"), true);
		$movie = find_movie($movies, $_GET['v']);
	}
}
?>
