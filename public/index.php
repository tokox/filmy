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
		$user = $users[$hashes[$_COOKIE['logged_in']]];
		$repeat = "off";
		$next = "off";
		$volume = "0.5";
		if(isset($_COOKIE[$hashes[$_COOKIE['logged_in']]]))
			list($repeat, $next, $volume) = explode(',', $_COOKIE[$hashes[$_COOKIE['logged_in']]]);
		if(isset($_GET['r']))
			$repeat = $_GET['r'];
		if(isset($_GET['n']))
			$next = $_GET['n'];
		if(isset($_GET['v']))
			$volume = $_GET['v'];
		setcookie($hashes[$_COOKIE['logged_in']], $repeat.','.$next.','.$volume, time()+60*60*24*30);
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
#end {
display: none;
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
		echo '<header><a href="/">Główna</a>';
		$headers = explode("/", $location);
		$header = "";
		for($i = 0; $i < count($headers) && strlen($location); $i++) {
			$header .= (strlen($header)?'/'.$headers[$i]:$headers[$i]);
			echo " » <a href=\"?m={$header}\">{$headers[$i]}</a>";
		}
		echo '</header>';
		if($movie["type"] == "video") {
			$next_off_checked = ($next=="off"?"checked":"");
			$next_folder_checked = ($next=="folder"?"checked":"");
			$next_all_checked = ($next=="all"?"checked":"");
			$repeat_off_checked = ($repeat=="off"?"checked":"");
			$repeat_on_checked = ($repeat=="on"?"checked":"");
			$time = 0;
			if(isset($_GET['t']) && $next=="off" && !isset($_GET['e']))
				$time = $_GET['t'];
			$status = "play";
			if(isset($_GET['s']) && $next=="off" && !isset($_GET['e']))
				$status = $_GET['s'];
			$autoplay = (((!isset($_GET['e'])&&$status=="stop")||(isset($_GET['e'])&&$repeat=="off"&&$next=="off"))?"":"autoplay");
?>
<nav>
<form id="settings">
<input type="hidden" name="m" value="<?php echo $location; ?>">
<button form="settings" type="submit" name="g" value="prev" id="prev">← Poprzedni</button>
<input type="submit" value="⟳ reload">
<span>|</span>
<label>Przechodź dalej: </label>
<input type="radio" name="n" id="next-off" value="off" <?php echo $next_off_checked; ?>><label for="next-off">Wył.</label>
<input type="radio" name="n" id="next-folder" value="folder" <?php echo $next_folder_checked; ?>><label for="next-folder">Folder</label>
<input type="radio" name="n" id="next-all" value="all" <?php echo $next_all_checked; ?>><label for="next-all">Wszystko</label>
<span>|</span>
<label>Powtarzaj: </label>
<input type="radio" name="r" value="off" id="repeat-off" value="off" <?php echo $repeat_off_checked; ?>><label for="repeat-off">Nie</label>
<input type="radio" name="r" value="on" id="repeat-on" value="on" <?php echo $repeat_on_checked; ?>><label for="repeat-on">Tak</label>
<span>|</span>
<input type="submit" value="ok">
<input type="submit" name="e" value="" id="end">
<input type="hidden" name="v" value="<?php echo $volume; ?>" id="volume">
<input type="hidden" name="t" value="<?php echo $time; ?>" id="time">
<input type="hidden" name="s" value="<?php echo $status; ?>" id="status">
<button form="settings" type="submit" name="g" value="next" id="next">Następny →</button>
</form>
</nav>
<video src="/video.php?v=<?php echo $location; ?>" controls <?php echo $autoplay; ?> preload onended="document.getElementById('end').click()" onvolumechange="document.getElementById('volume').setAttribute('value', document.getElementsByTagName('video')[0].volume)" ontimeupdate="document.getElementById('time').setAttribute('value', document.getElementsByTagName('video')[0].currentTime)" onplay="document.getElementById('status').setAttribute('value', 'play')" onpause="document.getElementById('status').setAttribute('value', 'stop')">
<script>
document.getElementsByTagName('video')[0].volume = <?php echo $volume; ?>;
document.getElementsByTagName('video')[0].currentTime = <?php echo $time; ?>;
</script>
</video>
<?php
		} else if($movie["type"] == "directory") {
			echo '<ul>';
			foreach($movie["content"] as $name => $element) {
				if(strlen($element["age_limit"]) == 0 || strlen($user["birthday"]) == 0 || intval($element["age_limit"]) <= intval($user["birthday"])) {
					$path = (strlen($location)>0?$location.'/'.$name:$name);
					echo "<li><a href=\"?m={$path}\">";
					if($element["type"] == "directory")
						echo '<img src="/folder.svg">';
					if($element["type"] == "link")
						echo '<img src="/link.svg">';
					echo "{$name}";
					if($element["type"] == "link")
						echo " → {$element["content"]}";
					echo "</a></li>";
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
