<?php
$users = json_decode(file_get_contents("../users.json"), true);
$wusers = json_decode(file_get_contents("../wusers.json"), true);
$movies = json_decode(file_get_contents("../movies.json"), true);
if($login == "admin") {
	for($i = 0; $i < count($users); $i++) {
		if(isset($_POST["a-{$i}-{$users[$i][0]}"])) {
			auser($i);
		} else if(isset($_POST["d-{$i}-{$users[$i][0]}"])) {
			duser($i);
		}
	}
	for($i = 0; $i < count($users); $i++) {
		if(isset($_POST["r-{$i}-{$users[$i][0]}"])) {
			ruser($i);
		}
	}
}
if(!$logged) {
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>filmy</title>
<style>
input[type=submit] {
margin-right: 5px;
margin-left: 5px;
}
input[type=checkbox] {
display: block;
margin-left: auto;
}
</style>
<link rel="icon" sizes="any" type="image/svg+xml" href="/favicon.svg">
</head>
<body>
<form method="post">
<table>
<tbody>
<tr><td>Login: </td><td><input type="text" name="login"></td></tr>
<tr><td>Hasło: </td><td><input type="password" name="password"></td></tr>
<tr><td><input type="checkbox" name="remember"></td><td>Zapamiętaj mnie</td></tr>
<tr><td colspan="2"><input type="submit" name="log_in" value="Zaloguj"><input type="submit" name="register" value="Zarejestruj"></td></tr>
</tbody>
</table>
</form>
</body>
</html>
<?php
} else {
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
clear:both;
}
.admin_main {
float: right;
}
</style>
<link rel="icon" sizes="any" type="image/svg+xml" href="?icon=">
</head>
<body>
<div class="panel">
<a href="?logout=">Wyloguj</a>
<?php
	if($login == "admin") {
?>
<span>|</span>
<a href="?admin_view=">Widok admina</a>
<?php
	}
?>
<span>|</span>
<a href="/">Strona Główna</a>
</div>
<?php
	if(isset($_GET['admin_view'])) {
?>
<form method="post">
<table>
<tbody>
<?php
		$wuserstoprint = wusers();
		$userstoprint = users();
		for($i = 0; $i < count($userstoprint) || $i < count($wuserstoprint); $i++) {
			echo '<tr>';
			if($i < count($userstoprint)) {
				echo "<td>{$userstoprint[$i][0]}</td><td><input type=\"submit\" name=\"r-{$i}-{$userstoprint[$i][0]}\" value=\"Usuń\"></td>";
			} else {
				if($i == 0) {
					echo "<td colspan=\"2\">Brak użytkowników</td>";
				} else {
					echo "<td></td><td></td>";
				}
			}
			if($i < count($wuserstoprint)) {
				echo "<td>{$wuserstoprint[$i][0]}</td><td><input type=\"submit\" name=\"a-{$i}-{$wuserstoprint[$i][0]}\" value=\"Akceptuj\"></td><td><input type=\"submit\" name=\"d-{$i}-{$wuserstoprint[$i][0]}\" value=\"Odrzuć\"></td>";
			} else {
				if($i == 0) {
					echo "<td colspan=\"2\">Brak użytkowników</td>";
				} else {
					echo "<td></td><td></td>";
				}
			}
			echo '</tr>';
		}
?>
</table>
</tbody>
</form>
<?php
	} else {
		$location = "";
		if(isset($_GET['m'])) {
			$location = str_replace("//", "/", str_replace(".mp4", "", str_replace("..", "", $_GET['m'])));
		}
		clearstatcache();
		if(!is_readable("../filmy/".$location)) {
			if(!is_readable("../filmy/".$location.".mp4")) {
				$location = "";
			} else
				$location = $location.".mp4";
		}
		echo '<header>';
		if(strlen($location)) {
			$headers = explode("/", str_replace(".mp4", "", $location));
			$header = "";
			for($i = 0; $i < count($headers); $i++) {
				$header = (strlen($header) ? $header."/" : "").$headers[$i];
				if($i != 0)
					echo ' » ';
				echo "<a href=\"?m={$header}\">{$headers[$i]}</a>";
			}
		}
		echo '</header>';
		if(is_dir("../filmy/".$location)) {
			$filmy = filmy($location);
			echo '<ul>';
			foreach($filmy as $film) {
				if(is_dir("../filmy/".$film[0])) {
					echo '</ul><ul class="folder">';
					echo "<li><a href=\"?m={$film[0]}\">{$film[1]}</a></li>";
					echo '</ul><ul>';
				} else
					echo "<li><a href=\"?m={$film[0]}\">{$film[1]}</a></li>";
			}
			echo '</ul>';
		} else {
			$location = str_replace(".mp4", "", $location);
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
</div>
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
}
file_put_contents("../users.json", json_encode($users, JSON_PRETTY_PRINT));
file_put_contents("../wusers.json", json_encode($wusers, JSON_PRETTY_PRINT));
file_put_contents("../movies.json", json_encode($movies, JSON_PRETTY_PRINT));
?>
