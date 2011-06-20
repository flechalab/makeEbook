<?php

namespace MakeEbook;

class ParserUrl extends Parser {

    /**
     * main url to crawler/parser
     * @var string
     */
    private $main_url;
    
    /**
     * array with urls from menu/list of files defined to generate file
     * @var array
     */
    private $menu_urls = array();

    /**
     * main url from files 
     * @param string $url 
     */
    public function setUrl($url) {
        $this->main_url = $url;
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
            $menu       = $xp->query("//{$tag_main}[@$attr_name='{$attr_value}']");
            $menu_itens = $xp->query(".//a", $menu->item(0));
            
            // get each item from menu and add to array
            foreach ($menu_itens as $item) {
                
                $href = $item->getAttribute('href');
                
                if(\in_array($href, $this->menu_urls)) {
                    continue;
                }
                
                if(substr($href, 0, 7)=='http://') {
                    continue;
                }
                
                $this->menu_urls[] = $this->main_url . $href;
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
        return $this->menu_urls;
    }

}