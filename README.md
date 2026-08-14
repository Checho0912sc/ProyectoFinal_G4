GUIA PARA CONTINUAR EL PROYECTO COMUNIGEST
===========================================

La idea de este documento es explicar, sin complicarlo demasiado, cómo está
organizado el proyecto y qué debería hacer cada uno cuando le toque desarrollar
un módulo.

Lo más importante es que no mezclemos todo en un mismo archivo. La estructura
se hizo para que cada parte tenga una responsabilidad y para que podamos
trabajar en módulos distintos sin dañar lo que hicieron los demás.


1. IDEA GENERAL DE CÓMO FUNCIONA EL PROYECTO
=============================================

Cuando el usuario entra a una ruta, el proceso general es este:

Navegador
   -> index.php
   -> Controller
   -> Service
   -> Repository
   -> Base de datos

Después la información regresa en sentido contrario:

Base de datos
   -> Repository
   -> Service
   -> Controller
   -> View
   -> Navegador

No todos los módulos van a necesitar absolutamente todas las capas, pero esta
es la estructura que deberíamos seguir en la mayoría de los casos.


2. DE QUÉ SE ENCARGA CADA CARPETA
==================================

assets/
-------

Aquí se guardan los archivos que utiliza el navegador:

- assets/css: estilos del proyecto.
- assets/js: JavaScript del lado del navegador.
- assets/img: imágenes, iconos o recursos gráficos.

En esta carpeta no deberían existir consultas SQL ni conexiones a la base de
datos. JavaScript se puede usar para cosas visuales, filtros o interacciones,
pero las validaciones importantes también se deben hacer en PHP.


config/
-------

Contiene la configuración general.

- app.php guarda datos generales, como el nombre de la aplicación y si estamos
  mostrando errores de desarrollo.
- database.php crea y devuelve la conexión PDO con MySQL.

No hace falta crear una conexión diferente para cada módulo. Todos deberían
usar:

Database::getConnection()

Estos archivos son compartidos. No deberían modificarse por cada funcionalidad
nueva, salvo que el equipo acuerde un cambio de configuración.


core/
-----

Esta carpeta es como la caja de herramientas del proyecto. Aquí están las
funciones que pueden usar todos los módulos.

- Controller.php permite cargar vistas, redirigir y exigir un método HTTP.
- Auth.php maneja sesiones, permisos, roles, CSRF y cierre de sesión.
- helpers.php contiene funciones como url(), e(), colones(), fechaCorta() y
  horaCorta().

Los módulos usan estos archivos, pero normalmente no tienen que modificarlos.
Por ejemplo, un controlador puede usar $this->render(), pero no necesita tocar
Controller.php.


models/
-------

Los modelos representan los objetos principales del sistema. Por ejemplo,
Usuario.php representa un usuario.

Un modelo puede tener datos y comportamientos propios de ese objeto. No debería
abrir conexiones ni escribir consultas SQL.

Ejemplos futuros:

- Proyecto.php
- Tarea.php
- Grupo.php
- Actividad.php


repositories/
-------------

Los repositorios son los únicos que deberían hablar directamente con MySQL.
Aquí van los SELECT, INSERT, UPDATE y cualquier otra consulta.

Siempre se deben usar consultas preparadas con PDO. No se deben pegar valores
recibidos del formulario directamente dentro del SQL.

Ejemplos:

- UsuarioRepository.php busca usuarios y sus membresías.
- DashboardRepository.php obtiene los datos reales del dashboard.

El repositorio obtiene información, pero no decide si una acción está permitida
por las reglas del sistema. Esa parte corresponde al Service.


services/
---------

Aquí van las reglas del negocio, o sea, las decisiones que necesita tomar el
sistema.

Por ejemplo, AuthService.php decide si un inicio de sesión se acepta. Para eso
comprueba el formato del correo, el estado del usuario, la contraseña y si tiene
una comunidad activa.

Otro ejemplo: un futuro TareaService podría comprobar que la fecha final no sea
anterior a la inicial, que el proyecto pertenezca a la comunidad actual y que
el responsable sea un miembro activo.

En resumen:

Repository = consigue o guarda datos.
Service    = aplica las reglas.


controllers/
------------

El controlador recibe la solicitud y coordina el trabajo. Se puede ver como el
director de la operación.

Un controlador normalmente hace esto:

1. Comprueba sesión y permisos.
2. Lee los datos enviados por GET o POST.
3. Llama al Service.
4. Envía el resultado a una vista o redirige.

El controlador no debería tener consultas SQL grandes ni construir todo el
HTML.


views/
------

Aquí está el HTML dinámico que finalmente ve el usuario.

- views/layout contiene header.php, navbar.php y footer.php.
- views/auth contiene las pantallas de autenticación.
- views/dashboard contiene el dashboard dinámico.
- Cada módulo nuevo debería tener su propia carpeta.

Por ejemplo:

views/tareas/listado.php
views/tareas/formulario.php

Las vistas pueden usar variables enviadas por el controlador, recorrer datos y
decidir qué elementos mostrar. No deben conectarse directamente a MySQL.

Todo dato que se imprima debería pasar por e() cuando corresponda:

<?= e($dato) ?>

Esto evita que un texto guardado en la base se interprete como HTML o
JavaScript.


sql/
----

Contiene schema.sql, que reconstruye la base comunigest_db con las tablas,
relaciones y datos de prueba.

Hay que recordar que este instalador elimina la versión anterior de
comunigest_db antes de crearla. No se debe ejecutar sobre una base con datos que
queramos conservar sin hacer antes un respaldo.


docs/
-----

Aquí guardamos documentación para el equipo. No contiene código que se ejecute
en la aplicación.


layout/
-------

Esta es la carpeta del layout HTML viejo que usaba JavaScript para cargar la
navbar y el footer. Se mantiene mientras terminamos la migración, pero los
módulos MVC nuevos deben usar views/layout.


3. ARCHIVOS PRINCIPALES DE LA RAÍZ
===================================

index.html
----------

Sigue siendo la página pública original del proyecto.


index.php
---------

Es la entrada principal del MVC. Carga la configuración, inicia la sesión,
carga las clases automáticamente y revisa qué controlador y acción se pidieron.

Por ejemplo:

index.php?controller=auth&action=login

significa que se debe ejecutar:

AuthController->login()

Todas las rutas nuevas deben registrarse en el arreglo $rutas de index.php.


Archivos .html de los módulos
-----------------------------

usuario.html, proyecto.html, actividades.html, grupos.html, finanzas.html y
reportes.html todavía son prototipos estáticos. Conforme se migre cada módulo a
MVC, sus enlaces se deben cambiar por rutas de index.php.


4. LÓGICA GENERAL DE LA BASE DE DATOS
======================================

La base se llama comunigest_db y tiene 11 tablas. La idea principal es que el
sistema puede manejar varias comunidades sin mezclar su información.


comunidades
-----------

Representa cada asociación o comunidad administrada en ComuniGest. Es el nivel
principal para separar los datos.


roles
-----

Contiene los tipos de acceso disponibles:

- Administrador
- Coordinador
- Miembro

El rol no está guardado directamente en usuarios, porque una persona podría
tener un rol diferente en cada comunidad.


usuarios
--------

Contiene las cuentas globales: nombre, correo, hash de contraseña, teléfono,
estado y último acceso.

La contraseña nunca se guarda como texto normal. Se almacena en
contrasena_hash.


usuario_comunidad
-----------------

Es una de las tablas más importantes. Une un usuario con una comunidad y le
asigna un rol dentro de ella.

La relación correcta es:

usuarios -> usuario_comunidad -> comunidades
                         |
                         -> roles

Por eso nunca debemos buscar el rol directamente en la tabla usuarios.


grupos
------

Cada grupo pertenece a una comunidad y tiene una persona responsable. Los
grupos representan comités o equipos de trabajo.


usuario_grupo
-------------

Une usuarios con grupos. También guarda la función interna que la persona
cumple en ese grupo, por ejemplo Coordinador, Secretaria o Colaborador.

El rol_grupo no es lo mismo que el rol general de usuario_comunidad.


proyectos
---------

Cada proyecto pertenece a un grupo y tiene un responsable. El proyecto no
tiene id_comunidad directamente.

Para averiguar su comunidad se sigue esta relación:

comunidades -> grupos -> proyectos

No se debe inventar una relación directa entre proyectos y comunidades en las
consultas, porque la comunidad del proyecto se obtiene por medio de su grupo.


tareas
------

Cada tarea pertenece a un proyecto y tiene un responsable. La comunidad de la
tarea se obtiene siguiendo:

comunidades -> grupos -> proyectos -> tareas

El avance de un proyecto se calcula con las tareas finalizadas. No existe una
columna de avance que tengamos que actualizar manualmente.


actividades
-----------

Cada actividad pertenece directamente a una comunidad. También puede estar
relacionada con un proyecto, pero esa relación es opcional porque puede haber
actividades generales, como una reunión comunal.


actividad_participacion
-----------------------

Une usuarios con actividades. Permite registrar confirmación, asistencia,
ausencia o cancelación.


movimientos_financieros
-----------------------

Guarda ingresos y egresos. Cada movimiento pertenece directamente a una
comunidad, identifica al usuario que lo registró y puede relacionarse con un
proyecto de forma opcional.

Un movimiento anulado no debería borrarse. Se cambia su estado a Anulado para
conservar el historial.


Mapa resumido de relaciones
----------------------------

comunidades
   -> usuario_comunidad -> usuarios
                       -> roles
   -> grupos
        -> usuario_grupo -> usuarios
        -> proyectos
             -> tareas
   -> actividades
        -> actividad_participacion -> usuarios
   -> movimientos_financieros

Aunque las llaves foráneas evitan muchas relaciones inválidas, el Service debe
comprobar reglas adicionales. Por ejemplo, que el responsable elegido realmente
pertenezca a la comunidad activa.


5. CÓMO FUNCIONA LA AUTENTICACIÓN
==================================

El proceso general es este:

1. El usuario abre la vista de login.
2. El formulario envía correo, contraseña y token CSRF mediante POST.
3. AuthController recibe los datos.
4. AuthService aplica las validaciones.
5. UsuarioRepository busca la cuenta y sus membresías en MySQL.
6. Usuario verifica el hash usando password_verify().
7. Si tiene una comunidad activa, se guarda usuario, comunidad y rol en sesión.
8. Si tiene varias comunidades, primero debe elegir en cuál va a trabajar.
9. El dashboard utiliza el id_comunidad guardado en la sesión para filtrar sus
   consultas.


config/database.php
-------------------

Crea la conexión PDO. Los repositorios reciben esta conexión y la reutilizan.


core/Auth.php
-------------

Maneja la sesión completa:

- Iniciar y cerrar sesión.
- Saber qué usuario está conectado.
- Guardar comunidad y rol activos.
- Exigir login o determinados roles.
- Crear y comprobar tokens CSRF.
- Guardar mensajes temporales.

La sesión no guarda la contraseña.


models/Usuario.php
------------------

Representa la cuenta encontrada en MySQL. Comprueba si está activa y compara la
contraseña escrita con el hash usando password_verify(). El hash nunca se envía
a la vista ni se guarda en la sesión.


repositories/UsuarioRepository.php
----------------------------------

Busca el usuario por correo, obtiene sus comunidades y roles activos y actualiza
la fecha de último acceso.


services/AuthService.php
------------------------

Contiene las reglas para decidir si el acceso se acepta o se rechaza. No crea
HTML ni escribe consultas SQL.


controllers/AuthController.php
------------------------------

Coordina el formulario, el Service, la selección de comunidad, la sesión y las
redirecciones.


views/auth/login.php
--------------------

Muestra el formulario y los errores. La contraseña nunca se vuelve a colocar en
el formulario cuando hay un fallo.


views/auth/seleccionar-comunidad.php
------------------------------------

Solo aparece si una cuenta tiene más de una membresía activa. La selección
pendiente vence después de cinco minutos.


views/layout/navbar.php
-----------------------

Muestra el nombre, la comunidad y el rol de la sesión. También contiene el
formulario POST para cerrar sesión.

Ocultar un botón según el rol es únicamente una mejora visual. La seguridad
real siempre debe comprobarse también en el Controller.


6. PASOS PARA AÑADIR UN MÓDULO NUEVO
=====================================

Voy a usar Tareas como ejemplo, pero el mismo orden sirve para Proyectos,
Grupos, Actividades, Finanzas o Usuarios.


Paso 1: revisar la historia de usuario y la base
-------------------------------------------------

Antes de programar hay que responder:

- ¿Qué tabla utiliza?
- ¿Con cuáles tablas se relaciona?
- ¿Cómo se obtiene su comunidad?
- ¿Qué roles pueden consultar?
- ¿Qué roles pueden crear, editar o cambiar estados?

En Tareas, la comunidad no está directamente en tareas. Se obtiene mediante:

tareas -> proyectos -> grupos -> comunidades


Paso 2: crear el Model
----------------------

Crear:

models/Tarea.php

Aquí se representa la tarea y cualquier comportamiento propio que realmente
pertenezca al objeto.


Paso 3: crear el Repository
---------------------------

Crear:

repositories/TareaRepository.php

Aquí van las consultas para listar, buscar, insertar o actualizar tareas.

Todas las consultas deben ser preparadas. Además, al listar o modificar una
tarea se debe filtrar por la comunidad activa mediante los JOIN correctos. No
debemos confiar solamente en un id_tarea enviado por la URL.


Paso 4: crear el Service
------------------------

Crear:

services/TareaService.php

Aquí se validan títulos, fechas, estados, proyecto, responsable y cualquier otra
regla antes de guardar.


Paso 5: crear el Controller
---------------------------

Crear:

controllers/TareaController.php

Todos sus métodos deben empezar comprobando autenticación cuando corresponda:

Auth::exigirLogin();

Para acciones restringidas se puede usar:

Auth::exigirRol('Administrador', 'Coordinador');

El id de la comunidad se obtiene de:

$usuarioActual = Auth::usuario();
$idComunidad = (int) $usuarioActual['id_comunidad'];

No se debería recibir id_comunidad desde un input para decidir qué datos puede
ver un usuario.


Paso 6: crear las Views
-----------------------

Crear, por ejemplo:

views/tareas/listado.php
views/tareas/formulario.php

La vista recibe datos del Controller. No ejecuta SQL.

Los formularios que crean, editan, anulan o cambian información deben usar POST
y enviar:

<input type="hidden" name="csrf_token"
       value="<?= e(Auth::csrfToken()) ?>">

El Controller debe comprobarlo antes de hacer cambios:

Auth::validarCsrf($_POST['csrf_token'] ?? null)


Paso 7: registrar las rutas
---------------------------

En el arreglo $rutas de index.php se agregan únicamente las acciones públicas
del controlador. Un ejemplo sería:

'tarea' => [
    'index' => [TareaController::class, 'index'],
    'crear' => [TareaController::class, 'crear'],
    'guardar' => [TareaController::class, 'guardar'],
    'editar' => [TareaController::class, 'editar'],
    'actualizar' => [TareaController::class, 'actualizar'],
],

Así se podrían usar rutas como:

index.php?controller=tarea&action=index


Paso 8: actualizar navegación y estilos
----------------------------------------

Cuando el módulo ya funcione, se cambia su enlace estático por la ruta MVC en
views/layout/navbar.php o en los accesos rápidos del dashboard.

Los estilos van en assets/css y el JavaScript del navegador en assets/js.


Paso 9: probarlo completo
-------------------------

Hay que probar como mínimo:

- Entrar sin sesión.
- Entrar con cada rol.
- Intentar acceder escribiendo la URL manualmente.
- Enviar campos vacíos o inválidos.
- Intentar usar un registro de otra comunidad.
- Crear, editar y cambiar el estado.
- Enviar el formulario sin CSRF.
- Mostrar textos con caracteres especiales.
- Revisar que no se mezclen datos entre comunidades.


7. REGLAS QUE DEBERÍAMOS RESPETAR TODOS
========================================

1. Las consultas SQL van en Repository.

2. Las reglas y validaciones importantes van en Service.

3. Controller coordina; no debería convertirse en un archivo gigante con SQL y
   HTML mezclados.

4. View muestra información; no se conecta a MySQL.

5. Los datos impresos se protegen con e().

6. Los cambios en información se hacen con POST y CSRF.

7. Ocultar un botón no reemplaza Auth::exigirRol().

8. Las consultas siempre deben limitarse a la comunidad de la sesión.

9. No se guarda ninguna contraseña en texto normal. Al crear o cambiar una se
   usa password_hash(), y para comprobarla se usa password_verify().

10. No se debe modificar core o config solo para resolver algo de un módulo. Si
    se necesita un cambio compartido, primero se acuerda con el equipo.

11. index.php y views/layout/navbar.php son archivos compartidos. Conviene que
    una persona integre las rutas y enlaces para reducir conflictos de Git.

12. Los estados Inactivo, Cancelado o Anulado existen para conservar historial.
    No deberíamos borrar información importante sin revisar primero las
    relaciones.


8. FORMA RECOMENDADA DE TRABAJAR
===========================================

Antes de empezar un módulo:

1. Actualizar la rama con los cambios más recientes.
2. Confirmar qué archivos le corresponden a cada persona.
3. Evitar modificar archivos de otro módulo sin avisar.
4. Hacer commits pequeños y con mensajes claros.
5. Probar el módulo antes de subirlo.
6. Avisar si se necesita modificar index.php, navbar.php, core o config.

Una persona que trabaje en Tareas debería tocar principalmente:

models/Tarea.php
repositories/TareaRepository.php
services/TareaService.php
controllers/TareaController.php
views/tareas/

Y coordinar los cambios compartidos de:

index.php
views/layout/navbar.php
assets/css/style.css


9. ESTADO ACTUAL DEL PROYECTO
==============================

Actualmente ya están funcionando:

- Conexión PDO con comunigest_db.
- Autenticación con correo y contraseña.
- Contraseñas almacenadas como hash.
- Sesiones y cierre de sesión.
- Roles por comunidad.
- Selección de comunidad cuando hay varias membresías.
- Protección CSRF.
- Dashboard con información real filtrada por comunidad.

Los demás archivos HTML siguen siendo prototipos y deben migrarse uno por uno a
la estructura explicada en esta guía.


