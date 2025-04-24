# Lista de Tareas (TODO-APP-PHP)

Aplicación web para gestionar una lista de tareas con operaciones CRUD.

---

## Descripción

- **Objetivo**: Crear, leer, actualizar y eliminar tareas.
- Cada tarea incluye:
  - Descripción
  - Fecha límite
  - Categoría (`personal`, `trabajo`, etc.)
  - Estado (`pendiente` / `completada`)
- Interfaz responsiva con **Tailwind CSS**.
- Interacción mediante **jQuery + AJAX** para crear tareas sin recarga.

---

## Tecnologías y dependencias

- **PHP 8.2** con **PDO**
- **MySQL**
- **Tailwind CSS** (vía CDN)
- **jQuery** (v3.6)
- **Git**

---

## Requisitos previos

- Servidor web local (Apache) con PHP
- MySQL en ejecución
- Git instalado para clonar el repositorio

---

## Instalación y configuración

1. **Clonar el repositorio**
    https://github.com/Vicnow/Todo-App-PHP

2. **Crear la base de datos y tablas**

3. **Configurar conexión**
    `db.php`:
     ```php
     $host     = '127.0.0.1';
     $port     = '3306';
     $dbname   = 'todo_app';
     $user     = 'root';
     $password = 'root';
     ```

4. **Instalar dependencias de front-end**
   - Se usan CDN para Tailwind y jQuery.

5. **Iniciar servidor**
   - Con XAMPP / MAMP / LAMP: coloca la carpeta en `htdocs` o `www`.

---

## Uso

1. Accede a: `http://localhost/Todo-App-PHP/index.php`.
2. **Añadir tarea**: completa descripción, fecha y tipo. El botón se habilita al validar.
3. **Listar**: la tabla muestra tareas existentes; si no hay ninguna, aparece un mensaje ilustrado.
4. **Editar**: haz clic en “Editar”, modifica los campos y guarda.
5. **Eliminar**: pulsa “Eliminar” y confirma el diálogo.

---