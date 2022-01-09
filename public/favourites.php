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
        file_put_contents("../data/".$file.".json", json_encode($data, JSON_PRETTY_PRINT));
}
if(isset($_COOKIE['logged_in'])) {
	$hashes = get_data("hashes");
	if(isset($hashes[$_COOKIE['logged_in']])) {
		$users = get_data("users");
		$user = &$users[$hashes[$_COOKIE['logged_in']]];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>filmy</title>
<style>
#panel {
position: fixed;
top: 0px;
right: 0px;
background-color: white;
border: 1px solid black;
margin: 5px;
padding: 5px;
}
body {
margin: 20px;
}
a:link {
text-decoration: none;
color: blue;
}
a:visited {
text-decoration: none;
color: darkblue;
}
a:hover {
text-decoration: underline;
}
a:active {
color: black;
}
header {
margin: 20px;
font-size: 2em;
}
li {
font-size: large;
margin-top: 7px;
}
img {
height: 20px;
margin: -2px;
margin-right: 5px;
}
span {
margin-top: 3px;
margin-bottom: 3px;
margin-left: 5px;
margin-right: 5px;
}
ul {
margin-top: 0px;
margin-bottom: 0px;
padding-top: 0px;
padding-bottom: 0px;
}
input {
margin: 5px;
margin-left: 15px;
padding: 0;
}
</style>
<link rel="icon" sizes="any" type="image/svg+xml" href="/favicon.svg">
</head>
<body>
<div id="panel">
<a href="/logout.php">Wyloguj</a>
<span>|</span>
<a href="/">Strona Główna</a>
<?php
	if($user['special_permissions'] == "true") {
?>
<span>|</span>
<a href="/admin.php">Widok admina</a>
<?php
	}
?>
<span>|</span>
<a href="/upload.php">Dodaj film</a>
<span>|</span>
<a href="/favourites.php">Ulubione</a>
</div>
<header><a href="/favourites.php">Ulubione</a></header>
<form method="post">
<ul>
<?php
		$movies = get_data("movies");
		for($i = 0; $i < count($user["favourites"]); $i++) {
			$el = str_replace(' ', '_', $user["favourites"][$i]);
			if(isset($_POST[$el.'-delete'])) {
				array_splice($user["favourites"], $i, 1);
				$i--;
				continue;
			}
			$movie = find_movie($movies, $user["favourites"][$i]);
			if($movie == -1) {
				array_splice($user["favourites"], $i, 1);
				$i--;
				continue;
			}
			$name = explode('/', $user["favourites"][$i])[substr_count($user["favourites"][$i], '/')];
			echo "<li><a href=\"/?m={$el}\"><img src=\"/{$movie["type"]}.svg\">{$name}</a><input type=\"submit\" name=\"{$el}-delete\" value=\"Usuń\"></li>";
		}
?>
</ul>
</form>
</body>
</html>
<?php
		set_data("users", $users);
		exit;
	}
}
header("Location: /login.php");
?>
