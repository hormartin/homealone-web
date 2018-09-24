<?php
    session_start() or die("Munkamenet létrehozási hiba<br>");
    if(!isset($_SESSION["authenticated"])) {
        $_SESSION["authenticated"] = false;
    }
    
    if($_SESSION["authenticated"])
        echo file_get_contents("./settings.html");
    else
        header("Location: /");

?>