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
            $menu = $xp->query("//{$tag_main}[@$attr_name='{$attr_value}']");
            
            $links = array();
            
            foreach($menu as $menu_item) {
                $itens = $xp->query(".//a", $menu_item);
                foreach($itens as $item) {
                    $links[] = $item->getAttribute('href');
                }
            }
            //@todo query get all items from menu (loop), not just item 0, 
            // sometimes it's necessary because there is a lot of div/items with links that would
            // like to get href (links)
            //$menu_itens = $xp->query(".//a", $menu->item(0));
            
            // get each item from menu and add to array
            //foreach ($menu_itens as $item) {
            foreach ($links as $href) {
                
                //$href = $item->getAttribute('href');
                                
                if(substr($href, 0, 7)=='http://') {
                    continue;
                }
                
                if(substr($href, 0, 1)=='/') {
                    $host = (\parse_url($this->main_url, \PHP_URL_SCHEME)) . '://' .
                    (\parse_url($this->main_url, \PHP_URL_HOST));
                    $href = $host . $href;
                }
                else {
                    $href = $this->main_url . $href;
                }
                
                if(\in_array($href, $this->menu_urls)) {
                    continue;
                }
                
                $this->menu_urls[] = $href;
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