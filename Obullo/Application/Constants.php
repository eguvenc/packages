<?php
/*
|---------------------------------------------------------------
| ESSENTIAL CONSTANTS
|---------------------------------------------------------------
| ROOT              - The root path of your server
| APP               - The full server path to the "app" folder
| CONFIG            - The full server path to the "config" folder
| ASSETS            - The full server path to the "assets" folder
| DATA              - The full server path to the "data" folder
| CLASSES           - The full server path to the user "classes" folder
| TEMPLATES         - The full server path to the user "templates" folder
| RESOURCES         - The full server path to the user "resources" folder
| MODULES       	- The full "dynamic" server path to the "modules" folder
| TASK_FILE         - The file name for $php task operations.
| TASK              - The full "static" path of the native cli task folder.
| INDEX_PHP         - The path of your index.php file.
*/
define('APP',  ROOT .'app/');
define('CONFIG', APP);
define('RESOURCES',  ROOT .'resources/');
define('ASSETS',  ROOT. 'public/assets/');
define('DATA',  RESOURCES .'data/');
define('TRANSLATIONS',  RESOURCES .'translations/');
define('CLASSES',  APP .'classes/');
define('TEMPLATES',  RESOURCES . 'templates/');
define('MODULES', APP .'modules/');
define('TASKS', APP .'modules/tasks/');
define('TASK_FILE', 'task');
define('TASK', PHP_PATH .' '. APP .'tasks/cli/');
define('INDEX_PHP', 'index.php');