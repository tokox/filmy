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
        file_put_contents("../data/".$file.".json", json_encode($data, JSON_PRETTY_PRINT));
}
if(isset($_COOKIE['logged_in'])) {
	$hashes = get_data("hashes");
	if(isset($hashes[$_COOKIE['logged_in']])) {
		$users = get_data("users");
		$user = $users[$hashes[$_COOKIE['logged_in']]];
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>filmy</title>
<style>
.panel {
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
video {
width: 100%;
background-color: black;
}
span {
margin-top: 3px;
margin-bottom: 3px;
margin-left: 5px;
margin-right: 5px;
}
.prev {
float: left;
}
.next {
float: right;
}
.folder {
list-style-image: url("/folder.svg");
}
nav > div {
display: inline-block;
}
ul {
margin-top: 0px;
margin-bottom: 0px;
padding-top: 0px;
padding-bottom: 0px;
}
nav {
text-align: center;
margin: 15px;
}
nav:after {
content: '';
display: block;
clear: both;
}
</style>
<link rel="icon" sizes="any" type="image/svg+xml" href="/favicon.svg">
</head>
<body>
<div class="panel">
<a href="/logout.php">Wyloguj</a>
<?php
	if($user['special_permissions'] == "true") {
?>
<span>|</span>
<a href="/admin.php">Widok admina</a>
<?php
	}
?>
<span>|</span>
<a href="/">Strona Główna</a>
</div>
<?php
		$location = "";
		if(isset($_GET['m']))
			$location = str_replace("//", "/", str_replace("..", "", $_GET['m']));
		$movies = get_data("movies");
		$movie = find_movie($movies, $location);
		echo '<header><a href="?m=">Główna</a>';
		$headers = explode("/", $location);
		for($i = 0; $i < count($headers); $i++) {
			echo ' » ';
			echo "<a href=\"?m={$header}\">{$headers[$i]}</a>";
		}
		echo '</header>';
		if() {
		} else {
?>
<nav>
<a href="?m=<?php echo $location; ?>&g=prev" class="prev">← Poprzedni</a>
<form class="settings" style="display:none;">
<label>Przechodź dalej: </label>
<input type="radio" name="n" id="next-off"><label for="next-off">Wył.</label>
<input type="radio" name="n" id="next-folder"><label for="next-folder">Folder</label>
<input type="radio" name="n" id="next-all"><label for="next-all">Wszystko</label>
<span>|</span>
<label>Powtarzaj: </label>
<input type="radio" name="r" value="on" id="repeat-on"><label for="repeat-on">Tak</label>
<input type="radio" name="r" value="off" id="repeat-off"><label for="repeat-off">Nie</label>
<a href="?m=<?php echo $location; ?>&g=next" class="next">Następny →</a>
</nav>
<video src="?v=<?php echo $location; ?>" controls autoplay preload></video>
<?php
		}
	}
?>
</body>
</html>
<?php
		exit;
	}
}
header("Location: /login.php");
?>
