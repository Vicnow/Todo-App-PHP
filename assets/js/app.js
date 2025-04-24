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

                    // Si es la PRIMERA tarea, oculta el mensaje y muestra la tabla
                    if ($('#noTasksMessage').is(':visible')) {
                        $('#noTasksMessage').hide();
                        $('#tasksContainer').removeClass('hidden');
                    }

                    var row = '<tr data-id="' + t.id + '">' +
                        '<td class="px-6 py-4">' +
                        '<div class="flex flex-col">' +
                        '<span class="text-sm font-medium text-gray-900 break-words max-w-xs">' + t.task_name + '</span>' +
                        '<span class="text-sm text-gray-500">' + t.due_date + '</span>' +
                        '</div>' +
                        '</td>' +
                        '<td  class="px-6 py-4 whitespace-nowrap capitalize">' + t.task_type + '</td>' +
                        '<td class="px-6 py-4 whitespace-nowrap capitalize">' +
                        '<div class="flex flex-row">' +
                        '<span class="text-sm font-medium pr-2">' + t.status + '</span>';
                    if (t.status === 'completed') {
                        row += '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />' +
                            '</svg>';
                    } else {
                        row += '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">' +
                            '<path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />' +
                            '</svg>';
                    }
                    row += '</div>' +
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

    // Manejador de eliminación
    $('#tasksTable').on('click', '.deleteBtn', function () {
        var taskId = $(this).data('id');
        if (confirm('¿Estás seguro de que deseas eliminar esta tarea?')) {
            window.location.href = 'delete.php?id=' + taskId;
        }
    });
});