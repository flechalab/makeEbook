<?php
namespace MakeEbook;

/**
 * class extended to generate file (html/txt)
 * @package makeEbook
 * @author  Fernando Dias
 */
class makeEbookFile extends makeEbook {

    /**
     * path and filename to generate
     * @var string
     */
    private $filename;

    /**
     * object constructor, exec main makeEbook construct
     * set config to crawler to generate file from the result
     * @param string $url
     * @param string $filaname
     */
    public function __construct($url, $filename) {
        try {
            parent::__construct($url);
            $this->filename = makeEbook::MAKEEBOOK_ROOT_PATH . 
                              makeEbook::MAKEEBOOK_FILESAVE_PATH . $filename;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * download file
     * @return void
     */
    public function output() {
        try {
            $this->file->makeFile($this->filename);
            $this->setOutputLog();
            $this->setLog('File generated in ' . $this->filename);
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * return files's path and filename
     */
    public function getFilename() {
        return $this->filename;
    }
}