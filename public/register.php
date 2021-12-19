<?php
function get_data($file) {
	return json_decode(file_get_contents("../data/".$file.".json"), true);
}
function set_data($file, $data) {
	file_put_contents("../data/".$file.".json", json_encode($data, JSON_PRETTY_PRINT));
}
$login = "";
$password = "";
$birthday = "";
$error_login = "";
$error_password = "";
$error_birthday = "";
if(isset($_POST['register'])) {
	$users = get_data("users");
	$wusers = get_data("wusers");
	if(!isset($_POST['login'])) {
		$error_login .= '<br>Musisz podać login!';
	} else if(strlen($_POST['login']) == 0) {
		$error_login .= '<br>Musisz podać login!';
	} else {
		$login = $_POST['login'];
		if(strlen($login) < 3)
			$error_login .= '<br>Login musi mieć przynajmniej trzy znaki!';
		if(strlen($login) > 32)
			$error_login .= '<br>Login nie może mieć więcej niż 32 znaki!';
		if(!preg_match('/^\w*$/', $login))
			$error_login .= '<br>Login może zawierać tylko litery, cyfry i znak podkreślenia!';
		if(isset($users[$login]))
			$error_login .= '<br>Login już istnieje!';
		if(isset($wusers[$login]))
			$error_login .= '<br>Login już istnieje!';
	}
	if(!isset($_POST['password'])) {
		$error_password .= '<br>Musisz podać hasło!';
	} else if(strlen($_POST['password']) == 0) {
		$error_password .= '<br>Musisz podać hasło!';
	} else {
		$password = $_POST['password'];
		if(strlen($password) < 8)
			$error_password .= '<br>Hasło musi mieć przynajmiej osiem znaków!';
		if(strlen($password) > 128)
			$error_password .= '<br>Hasło nie może miec więcej niż 128 znaków!';
	}
	if(!isset($_POST['birthday'])) {
		$error_birthday .= '<br>Musisz podać datę urodzenia!';
	} else if(strlen($_POST['birthday']) == 0) {
		$error_birthday .= '<br>Musisz podać datę urodzenia!';
	} else {
		$birthday = $_POST['birthday'];
		if(!preg_match('/^\d\d\d\d-\d\d-\d\d$/', $birthday))
			$error_birthday .= '<br>Data urodzenia jest w nieprawidłowym formacie!';
	}
	if(strlen($error_login) == 0 && strlen($error_password) == 0 && strlen($error_birthday) == 0) {
		$wusers[$_POST['login']] = ['hash_password' => password_hash($_POST['password'], PASSWORD_DEFAULT), 'birthday' => $_POST['birthday'], 'special_permissions' => 'false'];
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
a {
float: right;
}
span {
color: red;
}
</style>
<link rel="icon" sizes="any" type="image/svg+xml" href="/favicon.svg">
</head>
<body>
<form method="post">
<table>
<tbody>
<tr><td>Login: </td><td><input type="text" name="login" value="<?php echo $login; ?>" required><span><?php echo $error_login; ?></span></td></tr>
<tr><td>Hasło: </td><td><input type="password" name="password" value="<?php echo $password; ?>" required><span><?php echo $error_password; ?></span></td></tr>
<tr><td>Data urodzenia: </td><td><input type="date" name="birthday" value="<?php echo $birthday; ?>" required><span><?php echo $error_birthday; ?></span></td></tr>
<tr><td><input type="submit" name="register" value="Zarejestruj"></td><td><a href="/login.php">Zaloguj się</a></td></tr>
</tbody>
</table>
</form>
</body>
</html>
