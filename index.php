<?php
// Incluir la conexión PDO
require 'db.php';

// Consulta todas las tareas, uniendo con la tabla task_types para obtener el nombre del tipo y formateando la fecha de vencimiento
$sql = "
  SELECT
    t.id,
    t.task_name,
    DATE_FORMAT(t.due_date, '%Y-%m-%d') AS due_date,
    tt.type_name AS task_type,
    t.status
  FROM tasks AS t
  JOIN task_types AS tt
    ON t.task_type_id = tt.id
  ORDER BY t.created_at DESC
";
$stmt = $pdo->query($sql);
$tasks = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Lista de Tareas</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <!-- Incluimos jQuery desde el CDN -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <h1>Lista de Tareas</h1>
  <!-- Formulario para añadir una tarea sin recarga -->
  <form id="addTaskForm">
    <input
      type="text"
      id="task_name"
      name="task_name"
      placeholder="Descripción de la tarea"
      required
    >
    <input
      type="date"
      id="due_date"
      name="due_date"
    >
    <select id="task_type" name="task_type_id">
      <?php
      // Cargar opciones de tipo de tarea desde la tabla task_types
      $types = $pdo->query("SELECT id, type_name FROM task_types")->fetchAll();
      foreach ($types as $type) {
          echo "<option value=\"{$type['id']}\">{$type['type_name']}</option>";
      }
      ?>
    </select>
    <button type="submit">Añadir</button>
  </form>

  <!-- Tabla de tareas -->
  <table id="tasksTable">
    <thead>
      <tr>
        <th>Tarea</th>
        <th>Fecha límite</th>
        <th>Tipo</th>
        <th>Estado</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($tasks as $t): ?>
        <tr data-id="<?= $t['id'] ?>">
          <td><?= htmlspecialchars($t['task_name']) ?></td>
          <td><?= $t['due_date'] ?: '-' ?></td>
          <td><?= $t['task_type'] ?></td>
          <td><?= $t['status'] ?></td>
          <td>
            <!-- Botón de borrado que activará confirmación -->
            <button class="deleteBtn" data-id="<?= $t['id'] ?>">
              Eliminar
            </button>
          </td>
        </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</body>
</html>
