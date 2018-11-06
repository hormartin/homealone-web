<?php

//ALTER TABLE `config` ADD `MIN_VALUE` INT(255) NOT NULL AFTER `COMMENT`;
//ALTER TABLE `config` ADD `MAX_VALUE` INT(255) NOT NULL AFTER `MIN_VALUE`;
//ALTER TABLE `config` ADD `VALUE_TYPE` INT(1) NOT NULL DEFAULT '1' AFTER `MAX_VALUE`;

session_start() or die("Munkamenet létrehozási hiba<br>");
if(!isset($_SESSION["authenticated"])) {
	$_SESSION["authenticated"] = false;
}

$db = mysqli_connect("localhost", "user", "jelszó", "database") or die('Nem sikerült kapcsolódni.<br>');
$db->set_charset('utf8');

function containsSpecialChar(string $text) {
	// /[^a-zA-Z0-9\s.,őúöüóéáűíŐÚÖÜÓÉÁŰÍ]/
	$hu = array('/é/','/É/','/á/','/Á/','/ó/','/Ó/','/ö/','/Ö/','/ő/','/Ő/','/ú/','/Ú/','/ű/','/Ű/','/ü/','/Ü/','/í/','/Í/');
	$en = array('e','E','a','A','o','O','o','O','o','O','u','U','u','U','u','U','i','I');
	$text = preg_replace($hu, $en, $text);
	
	return preg_replace("/[^a-zA-Z0-9\s.,-]/u", "", $text) !== $text;
}

function isDateValid(string $date) {
	if (DateTime::createFromFormat('Y-m-d\TG:i', $date) !== FALSE) {
		return true;
	}

	return false;
}


if(!$_SESSION["authenticated"] and isset($_POST["user"]) and isset($_POST["pass"])) {
	ctype_alnum($_POST["user"]) or die("bad-user");
	$q = mysqli_query($db, "SELECT PASSWORD, ADMIN FROM users WHERE USER = '" . $_POST["user"] . "'") or die("sql hiba");
	$user = $q->fetch_assoc() or die("bad-user");
	if($user["PASSWORD"] != sha1($_POST["pass"])) {
		die("bad-pass");
		//header("Refresh: 0");
	} else {
		//header("Refresh: 0");
		
		$_SESSION["username"] = $_POST["user"];
		$_SESSION["usertype"] = $user["ADMIN"];
		$_SESSION["authenticated"] = true;
		
		die("success");
	}
} else if($_SESSION["authenticated"] and isset($_POST["action"])) {
	if($_POST["action"] == "logout") {
		$_SESSION["authenticated"] = false;
		header("Refresh: 0");
		die("kijelentkeztél<br>");

	} else if($_POST["action"] == "getusername") {
		print $_SESSION["username"];

	} else if($_POST["action"] == "getusertype") {
		print $_SESSION["usertype"];
	}else if($_POST["action"] == "getusers") {
		if($_SESSION["usertype"] == "N") die("no-permission");

		$q = mysqli_query($db, "SELECT ID_USER, USER, ADMIN FROM users");
		
		$userlist = array();
		while($users = $q->fetch_assoc()){
			array_push($userlist, array($users["ID_USER"], $users["USER"], $users["ADMIN"]));
		}

		print(json_encode($userlist));

	}else if($_POST["action"] == "setrole" and isset($_POST["id"]) and isset($_POST["role"])) {
		if($_SESSION["usertype"] == "N") die("no-permission");
		
		if(strcmp($_POST["role"], "Y") !== 0) { 
			if(strcmp($_POST["role"], "N") !== 0) die("wrong-data");
		}		

		//if (strcmp($_POST["role"], "N") != 0 || strcmp($_POST["role"], "Y") != 0) die("wrong-data");
		if(is_numeric($_POST["id"]) == false) die("non-numeric");

		mysqli_query($db, "UPDATE users SET ADMIN = '".$_POST['role']."' WHERE ID_USER = ".$_POST['id']) or die("mysql-error");
		print("success");
	
	}else if($_POST["action"] == "changepass" and isset($_POST["oldpass"]) and isset($_POST["newpass"])) {

		if(containsSpecialChar($_POST["oldpass"]) == true) die("bad-data");
		if(containsSpecialChar($_POST["newpass"]) == true) die("bad-data");

		$q = mysqli_query($db, "SELECT PASSWORD FROM users WHERE USER = '" . $_SESSION["username"] . "'") or die("sql-hiba");
		$user = $q->fetch_assoc() or die("error");
		
		if($user["PASSWORD"] != sha1($_POST["oldpass"])) {
			die("bad-pass");
		}

		$newpass = sha1($_POST["newpass"]);
		//die("UPDATE users SET PASSWORD = '".$newpass."'' WHERE USER = '" . $_SESSION["username"] . "';");
		$q = mysqli_query($db, "UPDATE users SET PASSWORD = '".$newpass."' WHERE USER = '" . $_SESSION["username"] . "'");

		print "success";
		//print $_SESSION["usertype"];

	}else if($_POST["action"] == "getlastvalue" and isset($_POST["sensor"])){
		//SELECT * FROM `comm` WHERE `FIELD` = 'LP' ORDER BY `comm`.`TS` DESC LIMIT 4
		$q = mysqli_query($db, "SELECT VALUE FROM comm WHERE FIELD = '".$_POST['sensor']."' ORDER BY `comm`.`TS` DESC LIMIT 4") or die("sql-error");
		$datalist = array();
		while($data = $q->fetch_assoc()) {
			array_push($datalist, $data["VALUE"]);
		}

		print(json_encode($datalist));
	
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
		if($_SESSION["usertype"] == "N") die("no-permission");
		ctype_alnum(str_replace("!", "", $_POST["device"])) or die("rossz adat<br>");
		ctype_alnum(str_replace(".", "", $_POST["name"])) or die("rossz adat<br>");
		$q = mysqli_query($db, "select VALUE from config where SECTION = '" . $_POST["device"] . "' and FIELD = '" . $_POST["name"] . "'") or die("sql hiba");
		$cfg = $q->fetch_assoc() or die("error");
		//select VALUE from config where SECTION = 'AL' and FIELD = 'display';
		print $cfg["VALUE"];

	} else if($_POST["action"] == "setconfig" and isset($_POST["device"]) and isset($_POST["name"]) and isset($_POST["value"])) {
		
		if($_SESSION["usertype"] == "N") die("no-permission");

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
	}else if($_POST["action"] == "getgraphdata" and isset($_POST["device"]) and isset($_POST["from-date"]) and isset($_POST["to-date"])) {
		//SELECT * FROM `comm` WHERE TS >= '2018-08-14 06:10:00' and TS <= '2018-08-14 08:10:00'

		if(isDateValid($_POST["from-date"]) === false) die("wrong-data");
		if(isDateValid($_POST["from-date"]) === false) die("wrong-data");

		$q = mysqli_query($db, "select * from config where SECTION ='".$_POST['device']."'");
		$configvalid = 0;

		while($config = $q->fetch_assoc()){
			$configvalid++;
		}

		if($configvalid > 0){
			$q = mysqli_query($db, "SELECT TS, VALUE FROM comm WHERE TS >= '".$_POST['from-date']."' and TS <= '".$_POST['to-date']."' AND FIELD = '".$_POST['device']."'");
			
			$darray = array();
			//die("SELECT TS, VALUE FROM comm WHERE TS >= '".$_POST['from-date']."' and TS <= '".$_POST['to-date']."' AND FIELD = '".$_POST['device']."'");

			//$string = "SELECT TS, VALUE FROM 'comm' WHERE TS >= '"+$_POST["from-date"]+"' and TS <= '"+$_POST["to-date"]+"' AND FIELD = '"+$_POST['device']+"'";
			while($data = $q->fetch_assoc()){
				array_push($darray, array($data["TS"], $data["VALUE"]));
			}

			print(json_encode($darray));

		}else die("error");

	}

} else if($_SESSION["authenticated"]) {
	print file_get_contents("main.html");
} else {
	print file_get_contents("login.html");
}


?>
