<?php

//ALTER TABLE `config` ADD `MIN_VALUE` INT(255) NOT NULL AFTER `COMMENT`;
//ALTER TABLE `config` ADD `MAX_VALUE` INT(255) NOT NULL AFTER `MIN_VALUE`;
//ALTER TABLE `config` ADD `VALUE_TYPE` INT(1) NOT NULL DEFAULT '1' AFTER `MAX_VALUE`;

session_start() or die("Munkamenet létrehozási hiba<br>");
if(!isset($_SESSION["authenticated"])) {
	$_SESSION["authenticated"] = false;
}

$db = mysqli_connect("localhost", "root", "123456", "homealone") or die('Nem sikerült kapcsolódni.<br>');
$db->set_charset('utf8');

function containsSpecialChar(string $text) {
	// /[^a-zA-Z0-9\s.,őúöüóéáűíŐÚÖÜÓÉÁŰÍ]/
	$hu = array('/é/','/É/','/á/','/Á/','/ó/','/Ó/','/ö/','/Ö/','/ő/','/Ő/','/ú/','/Ú/','/ű/','/Ű/','/ü/','/Ü/','/í/','/Í/');
	$en = array('e','E','a','A','o','O','o','O','o','O','u','U','u','U','u','U','i','I');
	$text = preg_replace($hu, $en, $text);
	
	return preg_replace("/[^a-zA-Z0-9\s.,-]/u", "", $text) !== $text;
}

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

	} else if($_POST["action"] == "getsensorlist") {
		$q = mysqli_query($db, "select distinct FIELD from comm") or die("sql hiba");
		$sensorlist = array();
		while($sensor = $q->fetch_assoc()) {
			array_push($sensorlist, $sensor["FIELD"]);
		}
		print(json_encode($sensorlist));

	} else if($_POST["action"] == "getconfig" and isset($_POST["device"]) and isset($_POST["name"])) {
		ctype_alnum(str_replace("!", "", $_POST["device"])) or die("rossz adat<br>");
		ctype_alnum(str_replace(".", "", $_POST["name"])) or die("rossz adat<br>");
		$q = mysqli_query($db, "select VALUE from config where SECTION = '" . $_POST["device"] . "' and FIELD = '" . $_POST["name"] . "'") or die("sql hiba");
		$cfg = $q->fetch_assoc() or die("hiba<br>");
		//select VALUE from config where SECTION = 'AL' and FIELD = 'display';
		print $cfg["VALUE"];

	} else if($_POST["action"] == "setconfig" and isset($_POST["device"]) and isset($_POST["name"]) and isset($_POST["value"])) {
		//ctype_alnum(str_replace("!", "", $_POST["device"])) or die("rossz adat");
		//ctype_alnum(str_replace(".", "", $_POST["name"])) or die("rossz adat<br>");
		//ctype_alnum($_POST["value"]) or die("rossz adat<br>");

		/*if (preg_replace("/[^a-zA-Z0-9\s.,]/", "", $_POST["device"]) !== $_POST["device"]) 	die("wrong-data");
		if (preg_replace("/[^a-zA-Z0-9\s.,]/", "", $_POST["name"]) !== $_POST["name"]) 		die("wrong-data");
		if (preg_replace("/[^a-zA-Z0-9\s.,]/", "", $_POST["value"]) !== $_POST["value"]) 		die("wrong-data");*/

		if(containsSpecialChar($_POST["device"])) die("wrong-data");
		if(containsSpecialChar($_POST["name"])) die("wrong-data");
		if(containsSpecialChar($_POST["value"])) die("wrong-data");

		$q = mysqli_query($db, "select * from config where SECTION ='".$_POST['device']."' AND FIELD = '".$_POST['name']."'");
		$configvalid = 0;

		$type = 1;
		$min = 0;
		$max = 0;

		while($config = $q->fetch_assoc()){
			$configvalid++;
			$type = $config["VALUE_TYPE"];
			$min = $config["MIN_VALUE"];
			$max = $config["MAX_VALUE"];
		}

		$value = $_POST["value"];
		if($configvalid > 0){

			if($type == 0) {
				if(is_numeric($_POST["value"]) == false) die("non-numeric");
				//if($_POST["value"] % 1 != 0) die("non-whole-num");

				$old = doubleval($_POST["value"]);
				$val = floor($_POST["value"]);

				if($val !== $old) die("non-whole-num");

				if($_POST["value"] > $max) die("max-value");
				if($_POST["value"] < $min) die("min-value");
				$value = (int)$_POST["value"];
			}


			$q = mysqli_query($db, "UPDATE config SET VALUE = '".$value."' WHERE SECTION = '".$_POST['device']."' AND FIELD = '". $_POST['name'] ."'");
			
			print("success");
		}else print("error");

		//print "Ez még nincs kész";

	}else if($_POST["action"] == "getsensorlistname") {
		$q = mysqli_query($db, "select * from config WHERE FIELD = 'display';") or die("sql hiba");
		$sensorlist = array();
		while($sensor = $q->fetch_assoc()) {
			array_push($sensorlist, array($sensor["SECTION"], $sensor["VALUE"]));
		}
		//die(print_r($sensorlist));
		print(json_encode($sensorlist));
	}else if($_POST["action"] == "getallconfig" and isset($_POST["device"])) {

		$q = mysqli_query($db, "select * from config WHERE SECTION = '". $_POST['device'] ."'") or die("sql hiba");
		$configlist = array();
		while($config = $q->fetch_assoc()) {
			array_push($configlist, array($config["FIELD"], $config["VALUE"], $config["VALUE_TYPE"], $config["MIN_VALUE"], $config["MAX_VALUE"]));
		}

		//die(print_r($configlist));
		print(str_replace("","", json_encode($configlist)));
	}

} else if($_SESSION["authenticated"]) {
	print file_get_contents("main.html");
} else {
	print file_get_contents("login.html");
}


?>
