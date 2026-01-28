<?php

$host = '127.0.0.1';
$port = 3307;
$user = 'root';
$pass = '';
$dbname = 'barqapp_filament';
$sqlFile = 'barqapp_v2_structure.sql';

try {
    // 1. Create Database
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server.\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    echo "Database `$dbname` checked/created.\n";
    
    // 2. Import SQL
    $pdo->exec("USE `$dbname`");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    
    if (file_exists($sqlFile)) {
        echo "Reading $sqlFile...\n";
        $sql = file_get_contents($sqlFile);
        
        // Remove comments potentially
        // But simply executing might work if the driver allows multiple queries.
        // If not, we split by semicolon.
        
        $pdo->exec($sql); 
        echo "Imported SQL structure successfully.\n";
    } else {
        echo "SQL file not found: $sqlFile\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    // If syntax error in SQL dump with multiple queries, we can try splitting.
    // But typically mysqldump output works with PDO::exec if emulation is on (default).
}
