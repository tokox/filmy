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
function get_data($file) {
	return json_decode(file_get_contents("../data/".$file.".json"), true);
}
function set_data($file, $data) {
	file_put_contents("../data/".$file.".json", json_decode($data, JSON_PRETTY_PRINT));
}
header("Content-Type: video/mp4");
if(isset($_COOKIE['logged_in'])) {
	$hashes = get_data("hashes");
	if(isset($hashes[$_COOKIE['logged_in']])) {
		if(isset($_GET['v'])) {
			$users = get_data("users");
			$user = $users[$hashes[$_COOKIE['logged_in']]];
			$movies = get_data("movies");
			$movie = find_movie($movies, $_GET['v']);
			if(intval($movie['age_limit']) >= intval($user['age'])) {
				return file_get_contents($movie['path']);
			}
		}
	}
}
?>
