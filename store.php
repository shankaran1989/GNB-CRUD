<?php
include 'db/Database.php';
include 'models/Property.php';
include 'controllers/PropertyController.php';

$db = new Database();
$property = new Property($db->conn);
$controller = new PropertyController($property, __DIR__.'/uploads');

// simple validation could be added here
$controller->store();
?>