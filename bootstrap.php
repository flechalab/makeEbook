<?php
/**
 * bootstrap file
 * @package makeEbook
 * @author  Fernando Dias
 */

/**
 * Main Auto Load Class Function
 * @param string $class
 */
function loadClass($class) {
    $class = str_replace('MakeEbook\\', '', $class);

    $class_root = __DIR__ . '/' . $class . '.class.php';
    $class_app  = __DIR__ . '/lib/app/' . $class . '.class.php';

    if(file_exists($class_root)) {
        require_once($class_root);
    }
    else if(file_exists($class_app)) {
        require_once($class_app);
    }
    else {
        //echo $class_app;
        throw new Exception("Class Not Found! ({$class})");
    }

}

spl_autoload_register('loadClass');