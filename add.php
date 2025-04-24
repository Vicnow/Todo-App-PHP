<?php
header('Content-Type: application/json; charset=utf-8');

try {
    // Incluir la configuración de conexión PDO
    require 'db.php';

    // Validación de la solicitud
    if (empty($_POST['task_name']) || empty($_POST['task_type_id'])) {
        throw new Exception('Faltan datos obligatorios (nombre o tipo de tarea).');
    }
    $taskName   = trim($_POST['task_name']);
    $dueDate    = !empty($_POST['due_date'])
                  ? $_POST['due_date']
                  : null;
    $typeId     = (int) $_POST['task_type_id'];

    // Preparar la consulta INSERT para crear la tarea
    $sql = "
      INSERT INTO tasks (task_name, due_date, task_type_id)
      VALUES (:task_name, :due_date, :type_id)
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':task_name' => $taskName,
        ':due_date'  => $dueDate,
        ':type_id'   => $typeId
    ]);

    // Obtener el ID de la tarea recién creada
    $newId = $pdo->lastInsertId();

    // Recuperar los datos completos (incluyendo el nombre del tipo y la fecha formateada)
    $sql = "
      SELECT
        t.id,
        t.task_name,
        DATE_FORMAT(t.due_date, '%Y-%m-%d') AS due_date,
        tt.type_name   AS task_type,
        t.status
      FROM tasks AS t
      JOIN task_types AS tt
        ON t.task_type_id = tt.id
      WHERE t.id = :id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $newId]);
    $task = $stmt->fetch();

    // Enviar respuesta JSON con el nuevo registro
    echo json_encode([
        'status' => 'success',
        'task'   => $task
    ]);

} catch (PDOException $e) {
    // Errores de base de datos
    http_response_code(500);
    echo json_encode([
        'status'  => 'error',
        'message' => 'Error en la base de datos: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    // Otros errores
    http_response_code(400);
    echo json_encode([
        'status'  => 'error',
        'message' => $e->getMessage()
    ]);
}
