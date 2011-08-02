<?php
namespace MakeEbook;

/**
 * class extended to print html
 * @package makeEbook
 * @author  Fernando Dias
 */

class makeEbookHTML extends makeEbook {

    /**
     * object constructor, extends main makeEbook construct
     * set config to crawler to string result
     * @param mixed $url
     */
    public function __construct($urls) {
        try {
            parent::__construct($urls);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * print html data
     * @return string
     */
    public function output() {
        try {
            $this->file->makeHtml();
            $this->setOutputLog();
            $this->setLog('String printed');
        }
        catch(\Exception $e) {
            throw new \Exception(makeEbook::MAKEEBOOK_MSG_ERROR . $e->getMessage());
        }
    }

    public function getHtml() {
        try {
            return $this->file->getHtml();
        }
        catch (\Exception $e) {
            throw new \Exception(makeEbook::MAKEEBOOK_MSG_ERROR . $e->getMessage());
        }

    }
}