<?php
namespace MakeEbook;

/**
 * Class to get a url and a defined id/class to get all the a href links
 * @package makeEbook
 * @author  Fernando Dias
 */
class getUrls {

    /**
     * crawler object
     * @var Crawler 
     */
    private $crawler;
    
    /**
     * parser object
     * @var ParserUrl
     */
    private $parser;
    
    /**
     * result (file content) from crawler
     * @var array 
     */
    private $result;


    /**
     * set the url and exec crawler
     * @param type $url
     */
    public function __construct($url) {
        $this->crawler = new \MakeEbook\Crawler($url);
        $this->crawler->setString();
        $this->crawler->exec();
        $this->result  = $this->crawler->getResult();
        $this->parser  = new \MakeEbook\ParserUrl();
        $this->parser->setUrl($url);
    }
    
    /**
     * remove unappropriate tags
     * @param string $tag
     * @param string $id
     * @param string $class 
     */
    public function removeTags($tag, $id=false, $class=false) {
        $this->parser->parserRemoveTags($tag, $id, $class);
    }
    
    /**
     * parsing html, create dom and set urls from menu 
     * @param string $div
     * @param string $attr
     * @param string $value
     * @param string $item 
     */
    public function setMenu($div, $attr, $value) {
        $this->parser->parserHTML($this->result[0]);
        $this->parser->setUrls($div, $attr, $value);
    }

    /**
     * return array with urls
     * @return array 
     */
    public function getUrls() {
        return $this->parser->getUrls();
    }

}
