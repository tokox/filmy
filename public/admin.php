<?php
function printhtmldisk($disk, $name="") {
	echo '<ul>';
	foreach($disk['content'] as $file => $data) {
		echo '<li';
		if($data["type"] == "directory") {
			echo " class=\"directory\"><details><summary>{$file} <div class=\"attributes\">[<br>    ";
			$first = true;
			foreach($data as $key => $value) {
				if(!$first)
					echo ',<br>    ';
				$first = false;
				$rvalue = $value;
				$disabled = "";
				$iname = "name=\"{$name}/{$file}/{$key}\"";
				if(is_array($rvalue)) {
					$rvalue = "Array";
					$disabled = "disabled";
					$iname = "";
				}
				$iname = str_replace(" ", "_", $iname);
				echo "\"{$key}\" => \"<input type=\"text\" {$iname} value=\"{$rvalue}\" {$disabled}>\"";
			}
			echo "<br>]</div></summary>";
			printhtmldisk($data, $name.'/'.$file);
			echo "</details>";
		} else {
			echo ">{$file} <div class=\"attributes\">[<br>    ";
			$first = true;
			foreach($data as $key => $value) {
				if(!$first)
					echo ',<br>    ';
				$first = false;
				$rvalue = $value;
				$disabled = "";
				$iname = "name=\"{$name}/{$file}/{$key}\"";
				if(is_array($rvalue)) {
					$rvalue = "Array";
					$disabled = "disabled";
					$iname = "";
				}
				$iname = str_replace(" ", "_", $iname);
				echo "\"{$key}\" => \"<input type=\"text\" {$iname} value=\"{$rvalue}\" {$disabled}>\"";
			}
			echo "<br>]</div>";
		}
		echo '</li>';
	}
	echo '</ul>';
}
function updategetdisk(&$disk, $name="") {
	foreach($disk['content'] as $file => &$data) {
		if($data["type"] == "directory")
			updategetdisk($data, $name.'/'.$file);
		foreach($data as $key => &$value) {
			if(!is_array($value)) {
				$iname = "{$name}/{$file}/{$key}";
				$iname = str_replace(" ", "_", $iname);
				if(isset($_POST[$iname]))
					$value = $_POST[$iname];
			}
		}
	}
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
		if($user["special_permissions"] == "true") {
			$wusers = get_data("wusers");
			$ignore = [];
			$movies = get_data("movies");
			foreach($users as $login => $data) {
				$newlogin = $login;
				$newdata = $data;
				if(isset($_POST[$login.'-login']))
					$newlogin = $_POST[$login.'-login'];
				if(isset($_POST[$login.'-hash_password']))
					$newdata['hash_password'] = $_POST[$login.'-hash_password'];
				if(isset($_POST[$login.'-birthday']))
					$newdata['birthday'] = $_POST[$login.'-birthday'];
				if(isset($_POST[$login.'-special_permissions']))
					$newdata['special_permissions'] = $_POST[$login.'-special_permissions'];
				unset($users[$login]);
				$users[$newlogin] = $newdata;
				if(isset($_POST[$login.'-delete']))
					unset($users[$newlogin]);
				else if(isset($_POST[$login.'-move'])) {
					$wusers[$newlogin] = $newdata;
					unset($users[$newlogin]);
					$ignore[$login] = true;
				}
			}
			foreach($wusers as $login => $data) {
				if(isset($ignore[$login]))
					continue;
				$newlogin = $login;
				$newdata = $data;
				if(isset($_POST[$login.'-login']))
					$newlogin = $_POST[$login.'-login'];
				if(isset($_POST[$login.'-hash_password']))
					$newdata['hash_password'] = $_POST[$login.'-hash_password'];
				if(isset($_POST[$login.'-birthday']))
					$newdata['birthday'] = $_POST[$login.'-birthday'];
				if(isset($_POST[$login.'-special_permissions']))
					$newdata['special_permissions'] = $_POST[$login.'-special_permissions'];
				unset($wusers[$login]);
				$wusers[$newlogin] = $newdata;
				if(isset($_POST[$login.'-delete']))
					unset($wusers[$newlogin]);
				else if(isset($_POST[$login.'-move'])) {
					$users[$newlogin] = $newdata;
					unset($wusers[$newlogin]);
				}
			}
			foreach($hashes as $hash => $login) {
				$newhash = $hash;
				$newlogin = $login;
				if(isset($_POST[$hash.'-hash']))
					$newhash = $_POST[$hash.'-hash'];
				if(isset($_POST[$hash.'-login']))
					$newlogin = $_POST[$hash.'-login'];
				unset($hashes[$hash]);
				$hashes[$newhash] = $newlogin;
				if(isset($_POST[$hash.'-delete']))
					unset($hashes[$newhash]);
			}
			updategetdisk($movies);
			set_data("users", $users);
			set_data("wusers", $wusers);
			set_data("hashes", $hashes);
			set_data("movies", $movies);
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
li {
font-size: large;
margin-top: 7px;
margin-left: 40px;
cursor: default;
}
li.directory {
list-style-type: none;
margin-left: 20px;
cursor: pointer;
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
padding-left: 0px;
}
section {
margin: 20px;
width: calc(100% - 40px);
display: block;
overflow: auto;
}
section.small {
width: calc(33% - 40px);
display: block;
float: left;
overflow: auto;
}
table {
border-collapse: collapse;
width: 100%;
}
td, th {
border: 1px solid black;
font-weight: normal;
padding: 5px;
text-align: center;
}
caption {
font-size: 1.2em;
}
th {
font-size: 1.1em;
}
section.small input {
width: calc(100% - 20px);
}
input[value=Save] {
width: calc(100% - 20px);
}
input[type=submit] {
margin: 3px;
}
.dane {
display: inline-block;
}
.attributes {
display: none;
font-size: 0.7em;
}
li:hover > .attributes {
display: block;
position: absolute;
background-color: white;
border: 1px solid black;
padding: 5px;
margin-left: 100px;
}
summary:hover > .attributes {
display: block;
margin-left: 120px;
position: absolute;
background-color: white;
border: 1px solid black;
padding: 5px;
}
</style>
<link rel="icon" sizes="any" type="image/svg+xml" href="/favicon.svg">
</head>
<body>
<div id="panel">
<a href="/logout.php">Wyloguj</a>
<span>|</span>
<a href="/">Strona Główna</a>
<span>|</span>
<a href="/admin.php">Widok admina</a>
<span>|</span>
<a href="/upload.php">Dodaj film</a>
<span>|</span>
<a href="/favourites.php">Ulubione</a>
</div>
<form method="post">
<input type="submit" value="Save">
<section class="small">
<table>
<caption>Users</caption>
<thead>
<tr><th>Login</th><th>Password hash</th><th>Birthday</th><th>Special permissions</th><th>Actions</th></tr>
</thead>
<tbody>
<?php
			foreach($users as $login => $data)
				echo "<tr><td><input type=\"text\" name=\"{$login}-login\" value=\"{$login}\"></td><td><input type=\"text\" name=\"{$login}-hash_password\" value=\"{$data["hash_password"]}\"></td><td><input type=\"text\" name=\"{$login}-birthday\" value=\"{$data["birthday"]}\"></td><td><input type=\"hidden\" name=\"{$login}-special_permissions\" value=\"false\"><input type=\"checkbox\" name=\"{$login}-special_permissions\" value=\"true\" ".($data["special_permissions"]=="true"?"checked":"")."></td><td><input type=\"submit\" name=\"{$login}-delete\" value=\"Delete\"><input type=\"submit\" name=\"{$login}-move\" value=\"Move\"></td></tr>";
?>
</tbody>
</table>
</section><section class="small">
<table>
<caption>Waiting users</caption>
<thead>
<tr><th>Login</th><th>Password hash</th><th>Birthday</th><th>Special permissions</th><th>Actions</th></tr>
</thead>
<tbody>
<?php
			foreach($wusers as $login => $data)
				echo "<tr><td><input type=\"text\" name=\"{$login}-login\" value=\"{$login}\"></td><td><input type=\"text\" name=\"{$login}-hash_password\" value=\"{$data["hash_password"]}\"></td><td><input type=\"text\" name=\"{$login}-birthday\" value=\"{$data["birthday"]}\"></td><td><input type=\"hidden\" name=\"{$login}-special_permissions\" value=\"false\"><input type=\"checkbox\" name=\"{$login}-special_permissions\" value=\"true\" ".($data["special_permissions"]=="true"?"checked":"")."></td><td><input type=\"submit\" name=\"{$login}-delete\" value=\"Delete\"><input type=\"submit\" name=\"{$login}-move\" value=\"Move\"></td></tr>";
?>
</tbody>
</table>
</section>
<section class="small">
<table>
<caption>Hashes</caption>
<thead>
<tr><th>Hash</th><th>Login</th><th>Actions</th></tr>
</thead>
<tbody>
<?php
			foreach($hashes as $hash => $login)
				echo "<tr><td><input type=\"text\" name=\"{$hash}-hash\" value=\"{$hash}\"></td><td><input type=\"text\" name=\"{$hash}-login\" value=\"{$login}\"></td><td><input type=\"submit\" name=\"{$hash}-delete\" value=\"Delete\"></tr>";
?>
</tbody>
</table>
</section>
<section>
<?php
			printhtmldisk($movies);
?>
</section>
</form>
</body>
</html>
<?php
		} else
			header("Location: /");
		exit;
	}
}
header("Location: /login.php");
?>
