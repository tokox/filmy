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
#prev {
float: left;
}
#next {
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
<div id="panel">
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
			$location = $_GET['m'];
		$movies = get_data("movies");
		$movie = find_movie($movies, $location);
		if($movie == -1) {
			header("Location: /");
			exit;
		}
		while($movie["type"] == "link")
			$movie = find_movie($movies, $movie["content"]);
		echo '<header><a href="/">Główna</a>';
		$headers = explode("/", $location);
		for($i = 0; $i < count($headers) && strlen($location); $i++)
			echo " » <a href=\"?m={$location}\">{$headers[$i]}</a>";
		echo '</header>';
		if($movie["type"] == "video") {
?>
<nav>
<form id="settings">
<button form="settings" type="submit" name="g" value="prev" id="prev">← Poprzedni</button>
<label>Przechodź dalej: </label>
<input type="radio" name="n" id="next-off" value="off"><label for="next-off">Wył.</label>
<input type="radio" name="n" id="next-folder" value="folder"><label for="next-folder">Folder</label>
<input type="radio" name="n" id="next-all" value="all"><label for="next-all">Wszystko</label>
<span>|</span>
<label>Powtarzaj: </label>
<input type="radio" name="r" value="on" id="repeat-on" value="on"><label for="repeat-on">Tak</label>
<input type="radio" name="r" value="off" id="repeat-off" value="off"><label for="repeat-off">Nie</label>
<button form="settings" type="submit" name="g" value="next" id="next">Następny →</button>
</form>
</nav>
<video src="/video.php?v=<?php echo $location; ?>" controls autoplay preload></video>
<?php
		} else if($movie["type"] == "directory") {
			echo '<ul>';
			foreach($movie["content"] as $name => $element) {
				if(strlen($element["age_limit"]) == 0 || strlen($user["birthday"]) == 0 || intval($element["age_limit"]) <= intval($user["birthday"])) {
					$path = (strlen($location)>0?$location.'/'.$name:$name);
					echo "<li";
					if($element["type"] == "directory")
						echo ' class="folder"';
					echo "><a href=\"?m={$path}\">{$name}</a></li>";
				}
			}
			echo '</ul>';
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
