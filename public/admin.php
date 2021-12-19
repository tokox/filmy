<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>filmy</title>
<script>
function change_href() {
	document.getElementsByTagName("a")[0].setAttribute("href", "javascript:history.go(-1)");
	document.getElementsByTagName("a")[0].innerHTML = "Wróć do poprzedniej strony";
}
</script>
</head>
<body onload="change_href()">
<h1>Widok administratora jest jeszcze niegotowy. Możesz zarządzać użytkownikami i filmami zmieniając pliki na serwerze.</h2>
<h1><a href="/">Wróć do głównej strony</a></h1>
</body>
