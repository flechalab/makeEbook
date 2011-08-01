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
    
    $classfile = __DIR__ . '/../lib/app/' . $class . '.class.php';

    if(file_exists($classfile)) {
        require_once($classfile);
    }
    else {
        throw new Exception("Class Not Found! ({$classfile})");
    }

}

spl_autoload_register('loadClass');