<?php
include "../data/functions.php";

if(!isset($_COOKIE['logged']))
	error_exit_video('not_logged');

$hashes = get_data("hashes");
$users = get_data("users");

if(!isset($hashes[$_COOKIE['logged']]) || !isset($users[$hashes[$_COOKIE['logged']]['login']]))
	error_exit_video('invalid_login_hash');

if(!isset($_GET['v']))
	error_exit_video('video_not_specified');

$user =& $users[$hashes[$_COOKIE['logged']]['login']];
$movie = find_movie(get_data("movies"), $_GET['a']);

if($movie == -1)
	error_exit_video('video_not_exists');

if($movie["type"] != "video")
	error_exit_video('file_type_is_not_video');

if(strlen($movie['age_limit']) != 0 && strlen($user['birthday']) != 0 && (time()-strtotime($user['birthday'])) >= (intval($movie['age_limit'])*31536000))
	error_exit_video('age_limit_exceeded');

header("Content-Type: ".$movie['mime_type']);
echo get_movie($movie['content']);
?>
