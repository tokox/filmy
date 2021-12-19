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
<h1>Uploadowanie plików na serwer jest jeszcze niegotowe. Możesz dodać pliki ręcznie na serwerze.</h2>
<h1><a href="/">Wróć do głównej strony</a></h1>
</body>
