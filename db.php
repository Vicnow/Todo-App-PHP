<?php

// Parámetros de conexión
$host     = '127.0.0.1';
$port     = '3306';
$dbname   = 'todo_app';
$user     = 'root';
$password = 'root';

// DSN (Data Source Name)
$dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

try {
    // Creamos la instancia de PDO
    $pdo = new PDO($dsn, $user, $password);
    
    // Excepciones en caso de error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Error en conexión
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
