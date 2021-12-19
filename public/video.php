<?php
function find_movie($movies, $code) {
	$codes = explode('/', $code);
        for($i = 0; $i < count($codes) && strlen($code); $i++) {
                $movies = $movies["content"];
                if(!isset($movies[$codes[$i]]))
                        return -1;
		$movies = $movies[$codes[$i]];
	}
        return $movies;
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
			$movie = find_movie($movies, $_GET['v']);
			if($movie != -1 && (strlen($movie['age_limit']) == 0 || strlen($user['age']) == 0 || intval($movie['age_limit']) <= intval($user['age']))) {
				echo file_get_contents("../movies/".$movie['path']);
			}
		}
	}
}
?>
