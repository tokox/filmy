<?php
function find_movie($movies, $code) {
        $codes = explode('/', $code);
        $movie = $movies;
        for($i = 0; $i < count($codes) && strlen($code); $i++) {
                $movie = $movie["content"];
                if(!isset($movie[$codes[$i]]))
                        return -1;
                $movie = $movie[$codes[$i]];
                if($movie["type"] == "link") {
                        $movie = find_movie($movies, $movie["content"]);
                        if($movie == -1)
                                return -1;
                }
        }
        return $movie;
}
function get_data($file) {
	return json_decode(file_get_contents("../data/".$file.".json"), true);
}
function set_data($file, $data) {
	file_put_contents("../data/".$file.".json", json_decode($data, JSON_PRETTY_PRINT));
}
if(isset($_COOKIE['logged_in'])) {
	$hashes = get_data("hashes");
	if(isset($hashes[$_COOKIE['logged_in']])) {
		if(isset($_GET['a'])) {
			$users = get_data("users");
			$user = $users[$hashes[$_COOKIE['logged_in']]];
			$movies = get_data("movies");
			$movie = find_movie($movies, str_replace('_', ' ', $_GET['a']));
			if($movie != -1) {
				if($movie["type"] == "audio") {
					if(strlen($movie['age_limit']) == 0 || strlen($user['birthday']) == 0 || intval($movie['age_limit']) <= intval($user['birthday'])) {
						header("Content-Type: ".$movie['mime_type']);
						echo file_get_contents("../movies/".$movie['content']);
					} else {
						header("Content-Type: audio/wav");
						echo file_get_contents("../data/audio/age_limit_exceeded.wav");
					}
				} else {
					header("Content-Type: audio/wav");
					echo file_get_contents("../data/audio/file_type_is_not_audio.wav");
				}
			} else {
				header("Content-Type: audio/wav");
				echo file_get_contents("../data/audio/audio_not_exists.wav");
			}
		} else {
			header("Content-Type: audio/wav");
			echo file_get_contents("../data/audio/audio_not_specified.wav");
		}
	} else {
		header("Content-Type: audio/wav");
		echo file_get_contents("../data/audio/invalid_login_hash.wav");
	}
} else {
	header("Content-Type: audio/wav");
	echo file_get_contents("../data/audio/not_logged.wav");
}
?>
