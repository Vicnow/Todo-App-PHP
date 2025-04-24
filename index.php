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
    <!-- Incluimos Tailwind CSS desde el CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Estilo CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Script de interacción -->
    <script src="assets/js/app.js"></script>
</head>

<body>
    <!-- Lista de tareas -->
    <div class="max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <h1 class="text-2xl font-bold p-4 border-b">Lista de Tareas</h1>

        <!-- Formulario de alta de tarea -->
        <form id="addTaskForm" class="p-4 flex space-x-2">
            <input
                type="text"
                name="task_name"
                id="task_name"
                class="flex-1 border rounded px-3 py-2"
                placeholder="Descripción de la tarea"
                maxlength="50"
                required>
            <input
                type="date" name="due_date" id="due_date"
                class="border rounded px-3 py-2"
                required>
            <select
                name="task_type_id" id="task_type"
                class="border rounded px-3 py-2">
                <?php
                // Cargar opciones de tipo de tarea desde la tabla task_types
                $types = $pdo->query("SELECT id, type_name FROM task_types ORDER BY id ASC")->fetchAll();
                foreach ($types as $type) {
                    echo "<option value=\"{$type['id']}\">{$type['type_name']}</option>";
                }
                ?>
            </select>
            <button
                type="submit"
                id="addTaskBtn"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 disabled:opacity-50"
                disabled>
                Añadir
            </button>
        </form>

        <!-- Si el array $tasks está vacío -->
        <?php if (count($tasks) === 0): ?>
            <p class="p-4 text-center text-gray-500">
                No hay tareas registradas. ¡Agrega la primera arriba!
            </p>
        <?php else: ?>
            <!-- Tabla de tareas hay tareas -->
            <table id="tasksTable" class="table-auto min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tarea</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($tasks as $t): ?>
                        <tr data-id="<?= $t['id'] ?>">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium text-gray-900 break-words max-w-xs"><?= $t['task_name'] ?></span>
                                    <span class="text-sm text-gray-500"><?= $t['due_date'] ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize"><?= $t['task_type'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize"><?= $t['status'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                <a
                                    href="edit.php?id=<?= $t['id'] ?>"
                                    class="text-indigo-600 hover:text-indigo-900">
                                    Editar
                                </a>
                                <button
                                    class="deleteBtn text-red-600 hover:text-red-900"
                                    data-id="<?= $t['id'] ?>">
                                    Eliminar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</body>

</html>