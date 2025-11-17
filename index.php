<?php
include 'db/Database.php';
include 'models/Property.php';

$db = new Database();
$property = new Property($db->conn);

// pagination params
$limit = 5;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$data = $property->getAll($limit, $offset, $search);
$total = $property->getCount($search);

include 'views/list.php';
?>