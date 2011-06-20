<?php
/**
 * Class to generate html/pdf/file from specifics url
 * @package makeEbook
 * @author  Fernando Dias
 */
namespace MakeEbook;


abstract class makeEbook {

    /**
     * path to save files
     */
    const MAKEEBOOK_FILESAVE_PATH = 'files/';

    /**
     * constant to identify the message error of makeEbook class
     */
    const MAKEEBOOK_MSG_ERROR = "MakeEbook App Error \r\n";

    /**
     * url (scheme://host/)
     * @var string
     */
    protected $url_host;

    /**
     * url path
     * @var string
     */
    protected $url_path;

    /**
     * crawler object to get the html from urls
     * @var Crawler
     */
    protected $crawler;
    /**
     * parser object to get the appropriate content
     * @var Parser
     */
    protected $parser;
    /**
     * parser object to get the appropriate content from CSS
     * @var ParserCSS
     */
    protected $parserCSS;
    /**
     * object to generate the specific filetype
     * @var FileMaker
     */
    protected $file;

    /**
     * log info
     * @var array
     */
    protected $log;

    /**
     * header id
     * @var string
     */
    protected $header_id;

    /**
     * content id
     * @var string
     */
    protected $content_id;

    /**
     * array with ids to remove from dom
     * @var mixed
     */
    protected $clear_id;

    /**
     * define if use css from source
     * @var boolean
     */
    protected $useCSS;

    /**
     * object constructor, build a crwaler object, parser object and makefile object
     * @param mixed $url
     */
    protected function __construct($url) {

        try {
            // get main url
            $this->parserUrl($url);

            // set objects
            $this->crawler = new Crawler($url);
            $this->parser  = new Parser();
            $this->file    = new FileMaker();

            // set to save the crawler in an string to generate html file
            $this->crawler->setString();
        }
        catch (Exception $e) {
            throw new \Exception(makeEbook::MAKEEBOOK_MSG_ERROR . $e->getMessage());
        }
    }

    public function parserUrl($url) {
        $url = \is_array($url) ? $url : array($url);
        $this->url_host = (\parse_url($url[0], \PHP_URL_SCHEME)) . '://' .
                (\parse_url($url[0], \PHP_URL_HOST));

        $this->url_path = \parse_url($url[0], \PHP_URL_PATH) . '/';
    }

    /**
     * header id
     * @param string $header
     */
    public function setHeader($header) {
        $this->header_id = $header;
    }

    /**
     * content id
     * @param string $content
     */
    public function setContent($content) {
        $this->content_id = $content;
    }

    /**
     * array with ids to remove from dom 
     * @param mixed $clear
     */
    public function setClear($clear) {
        $this->clear_id = is_array($clear) ? $clear : array($clear);
    }

    /**
     * set the exec mode to get css from source
     */
    public function useCSS() {
        $this->useCSS = TRUE;
    }

    /**
     * executing crawler / parser e putting the result in makefile object
     */
    public function exec() {

        try {
            // executing crawler
            $this->crawler->exec();

            // get the array with results
            $crawler = $this->crawler->getResult();

            // foreach item of result make parser
            foreach($crawler as $item) {
                // parsing html file, get node list from header/content
                $this->parser->parserHTML($item);
                // generate new dom structure
                $this->parser->setDom($this->content_id, $this->header_id, $this->clear_id);
            }

            // getting css from source, if set the option
            if($this->useCSS==\TRUE) {
                $this->parserCSS = new \MakeEbook\ParserCSS($this->url_host, $this->url_path);
                $this->parserCSS->setUrlsNodeList($this->parser->getCSS());
                $this->parserCSS->setCSS();
                $this->parser->appendDom($this->parserCSS->getCSS());
            }
            
            // get html from new dom (parser) and set it (file)
            $this->file->setHtml($this->parser->getHTML());

            // closing curl crawler
            $this->crawler->close();

            // log
            $this->log[] = 'Crawler / Parser executed.';
        }
        catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * implet output function to generate file / print data
     */
    abstract function output();

    /**
     * return log from app
     * @return array
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * set a new item in log's array
     * @param string $message
     */
    protected function setLog($message) {
        $this->log[] = $message;
    }

    /**
     * set a output info in log's array
     */
    protected function setOutputLog() {
        $this->log[] = 'Output Executed (' . \get_class($this) . ').';
    }

}

