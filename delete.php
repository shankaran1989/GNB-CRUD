<?php
include 'db/Database.php';
include 'models/Property.php';
include 'controllers/PropertyController.php';

$db = new Database();
$property = new Property($db->conn);
$controller = new PropertyController($property, __DIR__.'/uploads');

$controller->delete(isset($_GET['id'])?$_GET['id']:0);
?>