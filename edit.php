<?php
// Incluir la conexión PDO
require 'db.php';

try {
    // Determinar si es petición POST (envío del formulario) o GET (mostrar formulario)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validar que llegue el ID por URL y los datos por POST
        if (empty($_GET['id'])) {
            throw new Exception('ID de tarea no proporcionado.');
        }
        $taskId    = (int) $_GET['id'];
        $name      = trim($_POST['task_name']);
        $dueDate   = $_POST['due_date'];
        $typeId    = (int) $_POST['task_type_id'];
        $status    = $_POST['status'] === 'completada' ? 'completada' : 'pendiente';

        // Validar datos del formulario
        if ($name === '' || strlen($name) > 50) {
            throw new Exception('El nombre de la tarea es obligatorio y debe tener máximo 50 caracteres.');
        }
        if ($dueDate === '') {
            throw new Exception('Debe seleccionar una fecha límite.');
        }

        // Preparar y ejecutar la consulta UPDATE con parámetros vinculados
        $sql = "
          UPDATE tasks
          SET
            task_name = :task_name,
            due_date = :due_date,
            task_type_id = :type_id,
            status = :status
          WHERE id = :id
        ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':task_name'  => $name,
            ':due_date'   => $dueDate,
            ':type_id'    => $typeId,
            ':status'     => $status,
            ':id'         => $taskId
        ]);

        // Redirigir de vuelta a la lista de tareas
        header('Location: index.php');
        exit;
    }

    // Si es GET, validar que venga el ID y obtener datos actuales
    if (empty($_GET['id'])) {
        throw new Exception('ID de tarea no proporcionado.');
    }
    $taskId = (int) $_GET['id'];

    // Recuperar la tarea a editar
    $sql = "
      SELECT
        t.id,
        t.task_name,
        DATE_FORMAT(t.due_date, '%Y-%m-%d') AS due_date,
        t.task_type_id,
        t.status
      FROM tasks AS t
      WHERE t.id = :id
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $taskId]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception('Tarea no encontrada.');
    }

    // Cargar todos los tipos de tarea ordenados ascendentemente
    $types = $pdo
        ->query("SELECT id, type_name FROM task_types ORDER BY id ASC")
        ->fetchAll();
} catch (Exception $e) {
    // En caso de error, mostramos el mensaje y detenemos la ejecución
    die('Error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Tarea</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
    <div class="sm:w-full lg:max-w-3xl mx-auto bg-white shadow-md rounded-lg overflow-hidden">
        <div class="w-full flex justify-center">
            <img class="h-24 object-cover" src="assets/img/logo.png" alt="Logo Terraenergy"></img>
        </div>
        <h1 class="text-2xl lg:text-3xl text-center font-bold pb-4 border-b">Editar Tarea</h1>

        <!-- Formulario de edición -->
        <form action="edit.php?id=<?= $task['id'] ?>" method="POST" class="p-4 flex flex-col lg:space-x-2 text-sm lg:text-lg">
            <!-- Nombre de la tarea -->
            <div class="mb-4">
                <label for="task_name" class="block text-sm font-medium text-gray-700">Descripción</label>
                <input
                    type="text"
                    name="task_name"
                    id="task_name"
                    value="<?= htmlspecialchars($task['task_name']) ?>"
                    maxlength="50"
                    required
                    class="w-full flex-1 border rounded px-3 py-2">
            </div>
            <!-- Fecha límite -->
            <div class="mb-4">
                <label for="due_date" class="block text-sm font-medium text-gray-700">Fecha límite</label>
                <input
                    type="date"
                    name="due_date"
                    id="due_date"
                    value="<?= $task['due_date'] ?>"
                    required
                    class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <!-- Tipo de tarea -->
            <div class="mb-4">
                <label for="task_type_id" class="block text-sm font-medium text-gray-700">Tipo</label>
                <select
                    name="task_type_id"
                    id="task_type_id"
                    required
                    class="mt-1 block w-full border rounded px-3 py-2 capitalize">
                    <?php foreach ($types as $type): ?>
                        <option
                            value="<?= $type['id'] ?>"
                            <?= $type['id'] === (int)$task['task_type_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type['type_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Estado de la tarea -->
            <div class="mb-4">
                <span class="block text-sm font-medium text-gray-700">Estado</span>
                <div class="mt-2 w-full flex justify-around">
                    <label class="inline-flex items-center mt-2">
                        <input
                            type="radio"
                            name="status"
                            value="pendiente"
                            <?= $task['status'] === 'pendiente' ? 'checked' : '' ?>
                            class="form-radio">
                        <span class="ml-2">Pendiente</span>
                    </label>
                    <label class="inline-flex items-center mt-2 ml-6">
                        <input
                            type="radio"
                            name="status"
                            value="completada"
                            <?= $task['status'] === 'completada' ? 'checked' : '' ?>
                            class="form-radio">
                        <span class="ml-2">Completada</span>
                    </label>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-between mt-4">
                <a
                    href="index.php"
                    class="deleteBtn text-red-600 hover:text-red-900">
                    Cancelar
                </a>
                <button
                    type="submit"
                    class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</body>

</html>