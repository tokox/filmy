<?php
function get_data($file) {
	return json_decode(file_get_contents("../data/".$file.".json"), true);
}
function set_data($file, $data) {
	file_put_contents("../data/".$file.".json", json_encode($data, JSON_PRETTY_PRINT));
}
if(isset($_POST['register']) && isset($_POST['login']) && isset($_POST['password'])) {
	$users = get_data("users");
	$wusers = get_data("wusers");
	if(!isset($users[$_POST['login']]) && !isset($wusers[$_POST['login']]) && strlen($_POST['login']) >= 3 && strlen($_POST['login']) <= 256 && strlen($_POST['password']) >= 8 && strlen($_POST['password']) <= 256 && preg_match("/^\w+$/", $_POST['login']) && (isset($_POST['name'])?preg_match("/^[a-zA-Z ]+$/", $_POST['name']):true)) {
		$wusers[$_POST['login']] = ["hash_password" => strval(password_hash($_POST['password'], PASSWORD_DEFAULT)), "age" => strval(isset($_POST['age'])?$_POST['age']:"0"), "name" => strval(isset($_POST['name'])?$_POST['name']:""), "special_permissions" => "false", "favourite" => []];
		set_data("wusers", $wusers);
		header("Location: /login.php");
		exit;
	}
}
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
a {
float: right;
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
<tr><td>Imię/nazwisko: </td><td><input type="text" name="name"></td></tr>
<tr><td>Wiek: </td><td><input type="number" name="age"></td></tr>
<tr><td><input type="submit" name="register" value="Zarejestruj"></td><td><a href="/login.php">Zaloguj się</a></td></tr>
</tbody>
</table>
</form>
</body>
</html>
