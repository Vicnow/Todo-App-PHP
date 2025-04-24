<?php
// Conexión a la base de datos
require 'db.php';

try {
    // Verificar que nos llega un parámetro 'id' vía GET
    if (empty($_GET['id'])) {
        throw new Exception('ID de tarea no proporcionado.');
    }
    // Convertir el ID a entero
    $taskId = (int) $_GET['id'];

    // Consulta DELETE
    $sql = "DELETE FROM tasks WHERE id = :id";
    $stmt = $pdo->prepare($sql);

    // Ejecutar la consulta, pasando el ID de la tarea a borrar
    $stmt->execute([':id' => $taskId]);

    // Validar si la tarea fue eliminada
    if ($stmt->rowCount() === 0) {
        // No existía esa tarea o ya había sido eliminada
        throw new Exception('Tarea no encontrada o ya eliminada.');
    }

    // Redirigir de vuelta a la lista de tareas
    header('Location: index.php');
    exit;

} catch (PDOException $e) {
    // Error de base de datos
    die('Error al eliminar la tarea: ' . $e->getMessage());
} catch (Exception $e) {
    // Otros errores
    die('Error: ' . $e->getMessage());
}
