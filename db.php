<?php
function getDB() {
$dbhost="localhost";
$dbuser="cooladmin";
$dbpass="c00lp4ss1";
$dbname="Convenire";
$dbConnection = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass); $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); return $dbConnection;
} ?>