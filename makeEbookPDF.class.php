<?php
/**
 * Description : Class to generate PDF from specific url
 * @package makeEbook
 * @author  Fernando Dias
 */
namespace MakeEbook;

// tcpdf
require_once('lib/third/tcpdf/config/lang/por.php');
require_once('lib/third/tcpdf/tcpdf.php');


class makeEbookPDF extends makeEbook {

    /**
     * path and filename to generate
     * @var string
     */
    private $filename;

    /**
     * object constructor, exec main makeEbook construct
     * set config to crawler to generate pdf from the result
     * @param string $url
     * @param string $filaname
     */
    public function __construct($url, $filename) {
        try {
            parent::__construct($url);
            $this->filename = makeEbook::MAKEEBOOK_FILESAVE_PATH . $filename;
        }
        catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * drop pdf file generated
     * @return void (pdf-file)
     */
    public function output() {
        ErrorHandler::set();
        try {
            //$header = ;
            $this->file->makePdf(__DIR__ . '/' . $this->filename, trim($this->parser->getHeader()
                    ->item(0)->nodeValue));
            $this->setOutputLog();
            $this->setLog('PDF generated');
        }
        catch(Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * return files's path and filename
     */
    public function getFilename() {
        return __DIR__ . '/' . $this->filename;
    }
}