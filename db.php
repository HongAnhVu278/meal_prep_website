
<?php

$server = "cray.cs.gettysburg.edu";
$dbase  = "f23_1";
$user   = "grotka01";
$pass   = "grotka01";

try {
    $db = new PDO("mysql:host=$server;dbname=$dbase", $user, $pass);
} catch (PDOException $e) {
    die("Error connecting to database " . $e->getMessage());
}

?>
