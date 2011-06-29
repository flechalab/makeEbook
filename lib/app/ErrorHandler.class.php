<?php
/**
 * class to generate throws exceptions when a warning is generated
 * @package makeEbook
 * @author  Fernando Dias
 */
namespace MakeEbook;

/**
 * catch errors / warnings / notices and throws exceptions
 * @package makeEbook
 */
class ErrorHandler {
    /**
     * catch errors / warnings / notices and throws exception
     * @param int $errNo
     * @param string $errStr
     */
    public static function handle($errNo, $errStr=NULL) {
        switch ($errNo) {
            
            case E_WARNING:
                //throw new \RuntimeException($errStr, $errNo);
                throw new \Exception($errStr, $errNo);
            break;

            default:
                throw new \Exception($errStr, $errNo);
            break;
        }
    }

    /**
     * overwrites error handler
     */
    public static function set() {
        set_error_handler(array(__CLASS__ , 'handle'));
    }
}