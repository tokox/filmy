<?php
function find_movie(&$movies, $code) {
	$codes = explode('/', $code);
	$movie = &$movies;
	for($i = 0; $i < count($codes) && strlen($code); $i++) {
		$movie = &$movie["content"];
		if(!isset($movie[$codes[$i]]))
			return -1;
		$movie = &$movie[$codes[$i]];
		if($movie["type"] == "link") {
			$movie = &find_movie($movies, $movie["content"]);
			if($movie == -1)
				return -1;
		}
	}
	return $movie;
}
function create_movie(&$movies, $code, $data) {
	$codes = explode('/', $code);
	$movie = &$movies;
	for($i = 0; $i < count($codes) && strlen($code); $i++) {
		$movie = &$movie["content"];
		if(!isset($movie[$codes[$i]]))
			$movie[$codes[$i]] = ["type" => "directory", "path" => "", "age_limit" => $data["age_limit"], "content" => []];
		$movie[$codes[$i]]["age_limit"] = ($movie[$codes[$i]]["age_limit"] > $data["age_limit"]?$data["age_limit"]:$movie[$codes[$i]]["age_limit"]);
		$movie = &$movie[$codes[$i]];
		if($movie["type"] == "link") {
			$r = &find_movie($movies, $movie["content"]);
			if($r == -1) {
				$movie = &create_movie($movies, $movie["content"], ["type" => "directory", "path" => "", "age_limit" => $data["age_limit"], "content" => []]);
			} else {
				$movie = &$r;
			}
		}
	}
	$movie = $data;
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
		$movies = get_data("movies");
		$error_path = "";
		$error_limit = "";
		$error_file = "";
		$path = "";
		$age_limit = (isset($_POST['age_limit'])?$_POST['age_limit']:"");
		if(isset($_POST['upload'])) {
			if(!isset($_FILES['file'])) {
				$error_file .= "<br>Musisz wybrać plik!";
			} else {
				if($_FILES['file']['error']) {
					$error_file .= "<br>Wystąpił błąd przy przetwarzaniu pliku na serwerze. Sprawdź, czy plik nie jest za duży.";
				} else {
					if($_FILES['file']['size'] == 0)
						$error_file .= "<br>Plik nie może być pusty!";
				}
			}
			if(!isset($_POST['path'])) {
				$error_path .= "<br>Musisz podać ścieżkę!";
			} else {
				if(find_movie($movies, $path) != -1)
					$error_path .= "<br>Plik o tej nazwie już istnieje na serwerze!";
			}
			if($age_limit != "" && !is_integer($age_limit))
				$error_limit .= "<br>Limit wiekowy musi być liczbą!";
			else if(intval($age_limit) < 0)
				$error_limit .= "<br>Limit wiekowy musi być dodatni!";
			if(strlen($error_path) == 0 && strlen($error_file) == 0 && strlen($error_limit) == 0) {
				create_movie($movies, $path, ["type" => $type, "mime_type" => $_FILES['file']['type'], "path" => "", "content" => $file);
				set_data("movies", $movies);
				file_put_contents('../movies/uploaded/'.strval(time()).'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION), file_get_contents($_FILES['file']['tmp_name']));
			}
		}
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
span {
margin-top: 3px;
margin-bottom: 3px;
margin-left: 5px;
margin-right: 5px;
}
.error {
color: red;
}
input[type=submit], input[type=file] {
margin: auto;
display: block;
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
<header><a href="/upload.php">Dodaj film</a></header>
<form method="post" enctype="multipart/form-data">
<table>
<tbody>
<tr><td>Ścieżka: </td><td><input type="text" name="path" value="<?php echo $path; ?>"><span class="error"><?php echo $error_path; ?></span></td></tr>
<tr><td>Limit wiekowy: </td><td><input type="number" name="age_limit" value="<?php echo $age_limit; ?>"><span class="error"><?php echo $error_limit; ?></span></td></tr>
<tr><td colspan="2"><input type="file" name="file" accept="audio/*, video/*"><span class="error"><?php echo $error_file; ?></span></td></tr>
<tr><td colspan="2"><input type="submit" name="upload" value="Dodaj film"></td></tr>
</tbody>
</table>
</form>
</body>
</html>
<?php
		exit;
	}
}
header("Location: /login.php");
?>
