<?php
namespace MakeEbook;

/**
 * parserUrl class is a extension from parser to extract urls of list/menu from html file 
 * @package makeEbook
 * @author  Fernando Dias
 */
class ParserUrl extends Parser {

    /**
     * main url to crawler/parser
     * @var string
     */
    private $mainUrl;
    
    /**
     * host url to crawler/parser
     * @var string
     */
    private $host;
    
    /**
     * array with urls from menu/list of files defined to generate file
     * @var array
     */
    private $menuUrls = array();
    
    /**
     * array with urls from menu that can be removed
     * @var array
     */
    private $removeUrls = array();

    /**
     * main url from files and set host url
     * @param string $url 
     */
    public function setMainUrl($url) {
        $this->mainUrl = $url;
        $this->host    = \parse_url($this->mainUrl, \PHP_URL_SCHEME) . '://' .
                         \parse_url($this->mainUrl, \PHP_URL_HOST);
    }
    
    /**
     * extract the urls from tag/id from dom 
     * 
     * @param type $tag_main
     * @param type $attr
     * @param type $id
     * @param type $tag_item 
     */
    public function setUrls($tag_main='div', $attr_name='id', $attr_value='menu') {
        try {

            $dom = new \domDocument;
            if( $dom->loadHTML($this->html)==false ) {
                throw new \Exception("HTML Error");
            }

            $dom->preserveWhiteSpace = false;

            // getting data of header/content div
            $xp = new \DomXPath($dom);

            // dom content
            $menu = $xp->query("//{$tag_main}[@$attr_name='{$attr_value}']");
            
            $links = array();
            
            foreach($menu as $menu_item) {
                $itens = $xp->query(".//a", $menu_item);
                foreach($itens as $item) {
                    $links[] = $item->getAttribute('href');
                }
            }
            
            // get each item from menu and add to array
            //foreach ($menu_itens as $item) {
            foreach ($links as $href) {
                
                //$href = $item->getAttribute('href');

                // ignore external links
                if(substr($href, 0, 7)=='http://' && strpos($href, $this->mainUrl)==FALSE) {
                    continue;
                }
                
                if(in_array($href, $this->removeUrls)) {
                    continue;
                }
                
                if(substr($href, 0, 1)=='/') {
                    $href = $this->host . $href;
                }
                else {
                    $href = $this->mainUrl . $href;
                }
                
                if(\in_array($href, $this->menuUrls)) {
                    continue;
                }
                
                $this->menuUrls[] = $href;
            }
        }
        catch(Exception $e) {
            throw new \Exception("Urls Error! {$e->getMessage()}");
        }
    }
    
    
    /**
     * return array with urls from menu div/id defined with parser
     * @return array
     */
    public function getUrls() {
        return $this->menuUrls;
    }
    
    /**
     * remove a unecessary url from array
     * @param array $urls
     */
    public function removeUrls($urls) {
        $this->removeUrls = $urls;
    }

}