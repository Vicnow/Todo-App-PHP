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
// Verificamos si hay tareas
$hasTasks = count($tasks) > 0;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="img/logo.ico" />
    <link rel="shortcut icon" href="img/logo.ico" />
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
    <div class="sm:w-full lg:max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden my-4">
        <div class="w-full flex justify-center">
            <img class="h-24 object-cover" src="assets/img/logo.png" alt="Logo Terraenergy"></img>
        </div>
        <h1 class="text-2xl lg:text-3xl text-center font-bold pb-4 border-b">Lista de Tareas</h1>

        <!-- Formulario de alta de tarea -->
        <form id="addTaskForm" class="p-4 flex flex-col lg:flex-row lg:space-x-2 text-sm lg:text-lg">
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
                class="border rounded lg:px-3 mt-2 lg:mt-0 px-3 py-2"
                required>
            <select
                name="task_type_id" id="task_type"
                class="border rounded lg:px-3 mt-2 lg:mt-0 px-3 py-2 capitalize">
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
                class="bg-green-500 text-white mt-2 lg:mt-0 px-4 px-3 py-2 rounded hover:bg-green-600 disabled:opacity-50"
                disabled>
                Añadir
            </button>
        </form>

        <!-- Si el array $tasks está vacío -->
        <div
            id="noTasksMessage"
            class="w-full flex flex-col justify-center items-center p-4 <?= $hasTasks ? 'hidden' : '' ?>">
            <h2 class="text-lg font-bold">No hay tareas registradas</h2>
            <img class="w-1/2 my-4" src="assets/img/no_task.png" alt="No hay tareas">
            <p class="p-4 text-center text-gray-500">
                ¡Agrega la primera arriba!
            </p>
        </div>
        <div
            id="tasksContainer"
            class="md:w-full lg:max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden <?= $hasTasks ? '' : 'hidden' ?>">
            <!-- Tabla de tareas hay tareas -->
            <table id="tasksTable" class="table-auto min-w-full divide-y divide-gray-200 hidden md:table">
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
                            <td class="px-6 py-4 whitespace-nowrap capitalize">
                                <div class="flex flex-row">
                                    <span class="text-sm font-medium pr-2"><?= $t['status'] ?></span>
                                    <?php if ($t['status'] === 'completada'): ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>
                                    <?php else: ?>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                        </svg>
                                    <?php endif; ?>
                                </div>
                            </td>
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
            <!-- Listado para móviles  -->
            <div id="tasksMobileList" class="space-y-4 md:hidden">
                <?php foreach ($tasks as $t): ?>
                    <div class="bg-white p-4 rounded-lg shadow">
                        <!-- Nombre de la tarea -->
                        <div>
                            <span class="block text-base font-medium text-gray-900">
                                <?= ($t['task_name']) ?>
                            </span>
                        </div>
                        <!-- Fecha límite -->
                        <div class="mt-1">
                            <span class="text-sm text-gray-500">
                                <?= $t['due_date'] ?: '-' ?>
                            </span>
                        </div>
                        <!-- Tipo y Estado en la misma línea -->
                        <div class="mt-2 flex items-center space-x-1">
                            <span class="text-sm text-gray-700 capitalize">
                                <?= ($t['task_type']) ?>
                            </span>
                            <div class="flex flex-row">
                                <span class="text-sm font-medium pr-2 capitalize">- <?= $t['status'] ?></span>
                                <?php if ($t['status'] === 'completada'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-green-500">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 text-red-300">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                        </div>
                        <!-- Acciones apiladas en su propia línea -->
                        <div class="mt-3 flex space-x-4">
                            <!-- Enlace a editar -->
                            <a
                                href="edit.php?id=<?= $t['id'] ?>"
                                class="text-indigo-600 hover:text-indigo-900 text-sm">
                                Editar
                            </a>
                            <!-- Botón de eliminar -->
                            <button
                                class="deleteBtn text-red-600 hover:text-red-900 text-sm"
                                data-id="<?= $t['id'] ?>">
                                Eliminar
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="flex flex-col text-center sm:flex-row sm:text-left justify-center mt-4">
            <span>App realizada por Víctor Hugo Morales Martínez para&nbsp;</span>
            <a href="https://terraenergy.mx/" class="text-green-500" target="_blank">Terra Energy MX</a>
        </div>
        <div class="flex justify-center mb-4">
            <span>Repositorio del codigo aquí:&nbsp;</span>
            <a href="https://github.com/Vicnow/Todo-App-PHP" class="text-blue-500" target="_blank"> Link</a>
        </div>
</body>

</html>