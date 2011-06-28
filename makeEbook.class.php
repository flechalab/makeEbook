<?php
/**
 * Class to generate html/pdf/file from specifics url
 * @package makeEbook
 * @author  Fernando Dias
 */
namespace MakeEbook;


abstract class makeEbook {

    /**
     * root project's path
     */
    const MAKEEBOOK_ROOT_PATH = __DIR__;
    
    /**
     * path to save files
     */
    const MAKEEBOOK_FILESAVE_PATH = '/files/';

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
     * define if remove img tags from html on parser
     * @var boolean 
     */
    protected $removeImgs;

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

    /**
     * set the host and path (dir) from main url
     * @param mixed $url string/array with urls
     */
    public function parserUrl($url) {
        // setting host url
        $url = \is_array($url) ? $url : array($url);
        $this->url_host = (\parse_url($url[0], \PHP_URL_SCHEME)) . '://' .
                (\parse_url($url[0], \PHP_URL_HOST));

        // setting path url
        $path     = \parse_url($url[0], \PHP_URL_PATH) . '/';
        $pathinfo = pathinfo($path);
        $this->url_path = $pathinfo['dirname'];
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
     * @param array $clear
     */
    public function setClear($clear) {
        if(!is_array($clear)) {
            return;
        }
        $this->clear_id = $clear;
    }

    /**
     * set the exec mode to get css from source
     */
    public function useCSS() {
        $this->useCSS = TRUE;
    }

    public function removeImgs() {
        $this->removeImgs = TRUE;
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

            // used to remove img tags from html/document
            if($this->removeImgs) {
                $this->parser->parserRemoveTagsImgs();
            }

            
            // foreach item of result make parser
            foreach($crawler as $item) {
                // parsing html file, get node list from header/content
                $this->parser->parserHTML($item);
                // generate new dom structure
                $this->parser->setDom($this->content_id, $this->header_id, $this->clear_id);
            }
            
            if( count($this->parser->getImgs()) > 0 ) {
                $this->parserImg = new \MakeEbook\ParserImg($this->url_host, $this->url_path);
                //$this->parserImg->setUrlsArray(array($item));
                $this->parserImg->setUrlsArray($this->parser->getImgs());
                $this->parserImg->setImg();
            }
            
            // getting css from source, if set the option
            if($this->useCSS) {
                $this->parserCSS = new \MakeEbook\ParserCSS($this->url_host, $this->url_path);
                //$this->parserCSS->setUrlsNodeList($this->parser->getCSS());
                $this->parserCSS->setUrlsArray($this->parser->getCSS());
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

