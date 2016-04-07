<?php
/*
|---------------------------------------------------------------
| ESSENTIAL CONSTANTS
|---------------------------------------------------------------
| ROOT              - The root path of your server
| APP               - The full server path to the "app" folder
| CONFIG            - The full server path to the "config" folder
| RESOURCES         - The full server path to the user "resources" folder
| TRANSLATIONS      - The full server path to the user "resources/translations" folder
| ASSETS            - The full server path to the "assets" folder
| DATA              - The full server path to the "data" folder
| CLASSES           - The full server path to the user "classes" folder
| TEMPLATES         - The full server path to the user "templates" folder
| FOLDERS       	- The full "dynamic" server path to the "modules" folder
| TASK_FILE         - The file name for $php task operations.
| TASKS         	- The path of "app/folders/tasks" folder.
| INDEX_PHP         - The name of index.php file.
*/
define('APP',  ROOT .'app/');
define('CONFIG', APP. 'config/');
define('RESOURCES',  ROOT .'resources/');
define('ASSETS',  ROOT .'public/assets/');
define('DATA',  RESOURCES .'data/');
define('TRANSLATIONS',  RESOURCES .'translations/');
define('CLASSES',  APP .'classes/');
define('TEMPLATES',  RESOURCES . 'templates/');
define('FOLDERS', APP .'folders/');
define('TASKS', APP .'folders/tasks/');
define('TASK_FILE', 'task');
define('INDEX_PHP', 'index.php');