<?php
require_once 'config/database.php';

$database = new Database();

// Create database and tables
$database->createDatabase();

echo "Database initialized successfully! Events tables have been created.";
echo "<br><a href='index.php'>Go to Homepage</a>";
?> 