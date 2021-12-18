<?php
function get_data($file) {
	return json_decode(file_get_contents("../data/".$file.".json"), true);
}
function set_data($file, $data) {
	file_put_contents("../data/".$file.".json", json_encode($data, JSON_PRETTY_PRINT));
}
$hashes = get_data("hashes");
$users = get_data("users");
if(isset($_COOKIE['logged_in']) && isset($hashes[$_COOKIE['logged_in']])) {
	header("Location: /");
} else if(isset($_POST['log_in']) && isset($_POST['login']) && isset($_POST['password']) && isset($users[$_POST['login']]) && password_verify($_POST['password'], $users[$_POST['login']]['hash_password'])) {
	$hash = str_shuffle("qwertyuiopasdfghjklzxcvbnm1234567890");
	while(isset($hashes[$hash]))
		$hash = str_shuffle("qwertyuiopasdfghjklzxcvbnm1234567890");
	$hashes[$hash] = $_POST['login'];
	set_data("hashes", $hashes);
	if(isset($_POST['remember']))
		setcookie("logged_in", $hash, time()+60*60*24*30);
	else
		setcookie("logged_in", $hash);
	header("Location: /");
} else {
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
<tr><td><input type="checkbox" name="remember"></td><td>Zapamiętaj mnie</td></tr>
<tr><td><input type="submit" name="log_in" value="Zaloguj"></td><td><a href="/register.php">Zarejestruj się</a></td></tr>
</tbody>
</table>
</form>
</body>
</html>
<?php
}
?>
