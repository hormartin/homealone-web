<?php

session_start() or die("Munkamenet létrehozási hiba<br>");
if(!isset($_SESSION["authenticated"])) {
	$_SESSION["authenticated"] = false;
}

$db = mysqli_connect("localhost", "root", "123456", "homealone") or die('Nem sikerült kapcsolódni.<br>');

if(!$_SESSION["authenticated"] and isset($_POST["pass"]) and isset($_POST["user"])) {
	ctype_alnum($_POST["user"]) or die("nem jó username<br>");
	$q = mysqli_query($db, "SELECT PASSWORD FROM users WHERE USER = '" . $_POST["user"] . "'") or die("sql hiba");
	$user = $q->fetch_assoc() or die("nincs ilyen user<br>");
	if($user["PASSWORD"] != sha1($_POST["pass"])) {
		die("rossz jelszó");
		header("Refresh: 0");
	} else {
		header("Refresh: 0");
		print "jó jelszó<br>";
		$_SESSION["username"] = $_POST["user"];
		$_SESSION["authenticated"] = true;
	}
} else if($_SESSION["authenticated"] and isset($_POST["action"])) {
	if($_POST["action"] == "logout") {
		$_SESSION["authenticated"] = false;
		header("Refresh: 0");
		die("kijelentkeztél<br>");

	} else if($_POST["action"] == "getusername") {
		print $_SESSION["username"];

	} else if($_POST["action"] == "getvalueat" and isset($_POST["sensor"]) and isset($_POST["time"])) {
		ctype_alnum(str_replace(" ", "", str_replace("-", "", str_replace(":", "", $_POST["time"])))) or die("rossz adat<br>");
		ctype_alnum($_POST["sensor"]) or die("nem jó szenzor<br>");
		$q = mysqli_query($db, "select VALUE from comm where TS < '" . $_POST["time"] . "' and field = '" . $_POST["sensor"] . "' order by TS DESC limit 1;") or die("sql hiba");
		$sensor = $q->fetch_assoc() or die("hiba<br>");
		print $sensor["VALUE"];

	} else if($_POST["action"] == "getvalue" and isset($_POST["sensor"])) {
		ctype_alnum($_POST["sensor"]) or die("nem jó szenzor<br>");
		$q = mysqli_query($db, "select VALUE from comm where field = '" . $_POST["sensor"] . "' order by TS DESC limit 1;") or die("sql hiba");
		$sensor = $q->fetch_assoc() or die("hiba<br>");
		print $sensor["VALUE"];

	} else if($_POST["action"] == "getconfig" and isset($_POST["device"]) and isset($_POST["name"])) {
		ctype_alnum(str_replace("!", "", $_POST["device"])) or die("rossz adat<br>");
		ctype_alnum(str_replace(".", "", $_POST["name"])) or die("rossz adat<br>");
		$q = mysqli_query($db, "select VALUE from config where SECTION = '" . $_POST["device"] . "' and FIELD = '" . $_POST["name"] . "'") or die("sql hiba");
		$cfg = $q->fetch_assoc() or die("hiba<br>");
		//select VALUE from config where SECTION = 'AL' and FIELD = 'display';
		print $cfg["VALUE"];

	} else if($_POST["action"] == "setconfig" and isset($_POST["device"]) and isset($_POST["name"]) and isset($_POST["value"])) {
		ctype_alnum(str_replace("!", "", $_POST["device"])) or die("rossz adat<br>");
		ctype_alnum(str_replace(".", "", $_POST["name"])) or die("rossz adat<br>");
		ctype_alnum($_POST["value"]) or die("rossz adat<br>");

		print "Ez még nincs kész";

	}
} else if($_SESSION["authenticated"]) {
	print file_get_contents("main.html");
} else {
	print file_get_contents("login.html");
}

?>
