$(document).ready(function () {
    //  Referencias a los elementos del formulario
    var $nameInput = $('#task_name');
    var $dateInput = $('#due_date');
    var $typeSelect = $('#task_type');
    var $addButton = $('#addTaskBtn');

    // Función que comprueba si el formulario es válido
    function updateAddButton() {
        // Nombre no vacío y menor de 50 caracteres
        var name = $nameInput.val().trim();
        var validName = name.length > 0 && name.length <= 50;

        // Fecha no vacía
        var dueDate = $dateInput.val();
        var validDate = dueDate !== '';

        // Habilitar sólo si TODO es válido
        if (validName && validDate) {
            $addButton.prop('disabled', false);
        } else {
            $addButton.prop('disabled', true);
        }
    }

    // Ejecutar validación en cada cambio de input/select
    $nameInput.on('input', updateAddButton);
    $dateInput.on('change', updateAddButton);
    $typeSelect.on('change', updateAddButton);

    // Inicializar estado del botón al cargar la página
    updateAddButton();

    // Manejador de envío igual
    $('#addTaskForm').on('submit', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'add.php',
            method: 'POST',
            dataType: 'json',
            data: {
                task_name: $nameInput.val().trim(),
                due_date: $dateInput.val(),
                task_type_id: $typeSelect.val()
            },
            success: function (response) {
                if (response.status === 'success') {
                    var t = response.task;
                    var row = '<tr data-id="' + t.id + '">' +
                        '<td class="px-6 py-4">' +
                        '<div class="flex flex-col">' +
                        '<span class="text-sm font-medium text-gray-900 break-words max-w-xs">' + t.task_name + '</span>' +
                        '<span class="text-sm text-gray-500">' + t.due_date + '</span>' +
                        '</div>' +
                        '</td>' +
                        '<td  class="px-6 py-4 whitespace-nowrap capitalize">' + t.task_type + '</td>' +
                        '<td  class="px-6 py-4 whitespace-nowrap capitalize">' + t.status + '</td>' +
                        '<td  class="px-6 py-4 whitespace-nowrap space-x-2">' +
                        '<a href="edit.php?id=' + t.id + '" class="text-indigo-600 hover:text-indigo-900">Editar</a> ' +
                        '<button  class="deleteBtn text-red-600 hover:text-red-900" data-id="' + t.id + '">Eliminar</button>' +
                        '</td>' +
                        '</tr>';
                    $('#tasksTable tbody').prepend(row);
                    $('#addTaskForm')[0].reset();
                    updateAddButton(); // volver a deshabilitar hasta nueva validación
                } else {
                    alert('Error al agregar tarea: ' + response.message);
                }
            },
            error: function () {
                alert('Error en la petición AJAX.');
            }
        });
    });
});