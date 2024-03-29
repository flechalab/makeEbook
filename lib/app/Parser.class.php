<?php
namespace MakeEbook;

/**
 * main parser class to extract div ou specific part of html file
 * @package makeEbook
 * @author  Fernando Dias
 */
class Parser {

    /**
     * object to set new dom to generate output file
     * @var DomDocument
     */
    protected $newDom;

    /**
     * full html
     * @var html
     */
    protected $html;

    /**
     * array with info to remove tags during parser
     * @var array 
     */
    protected $parserRemoveTags = array();
    
    /**
     * header html
     * @var nodeList
     */
    protected $header;

    /**
     * nodes from each page
     * @var nodeList
     */
    protected $content;

    /**
     * nodes to remove from content
     * @var nodeList
     */
    protected $clear;

    /**
     * css array with list from css files from html
     * @var array 
     */
    protected $css = array();
    
    /**
     * imgs path array
     * @var array 
     */
    protected $imgs = array();
    
    /**
     * content id with a number to avoid duplicate id in HTML
     * @var integer
     */
    protected $pages  = 0;
    
    /**
     * constructor set propetie newDom with DOMDocument object
     */
    public function __construct() {
        $this->newDom = new \DOMDocument;
    }

    /**
     * parsing HTML, removing header, script and others invalid, unnecessary tags
     * and encoding correcly HTML
     *
     * @param string $html
     */
    public function parserHTML($html) {

        if(!is_string($html)) {
            throw new \Exception('HTML Error, it is not an string');
        }
        
        try {
            // extracting head / body text and removing tags that can make
            // problems to load html with DomDocument class

            preg_match_all('/<head(.*)<\/head>/s', $html, $head);
            preg_match_all('/<body(.*)<\/body>/s', $html, $body);
            if(isset($head[0][0])) {
                $head = preg_replace('@<script[^>](.*?)>(.*?)</script>@si', '', $head[0][0]);
            }
            else {
                $head = false;
            }
            
            if(isset($body[0][0])) {
                $body = preg_replace('@<script[^>](.*?)>(.*?)</script>@si', '', $body[0][0]);
            }
            else {
                $body = false;
            }
            
            $body = preg_replace('/<\/?center>/s', '', $body);

            // removing div
            foreach($this->parserRemoveTags as $item) {
                $body = preg_replace($item, '', $body);
            }
            
            // full html
            $html = '<html>' . $head . $body . '</html>';

            // escaped entities use
            
            $this->html = mb_convert_encoding($html, 'html-entities', 'utf-8');
            // (or)
            //$this->html = htmlentities($html, FALSE,'utf-8');
            //$this->html = htmlspecialchars_decode( $html, ENT_NOQUOTES );
        }
        catch(Exception $e) {
            throw new \Exception("Parser Error! \r\n {$e->getMessage()}");
        }
    }
    
    /**
     * define rules to execute regex and remove unecessary tags
     * IMPORTANT: set this method before parserHTML
     * @param string $tag
     * @param string $id
     * @param string $class
     */
    public function parserRemoveTags($tag='[a-z]*', $id=false, $class=false) {
        if ($tag=='[a-z]*' && !$id && $class) return;

        $exp = '/<' . $tag;

        // TODO --- including id ---
        if($id) {
            // @todo do it better
            //$exp .= '(.*[^>])? id=[\\\'\"]?' . $id . '[\\\'\"]?(.*[^>])?';
            //$exp .= ' id=[\\\'\"]?' . $id . '[\\\'\"]?';
            $exp .= ' id="' . $id . '"';
        }
        
        // TODO --- including clsss ---
        if($class) {
            $exp .= '(.*[^>])? class="' . $class . '" (.*[^>])?';
        }

        $exp .= '>(.*?)[^<]';
        
        
        $exp .= '<\/' . $tag . '>/is';

        $this->parserRemoveTags[] = $exp;
    }
    
    /**
     * define rule to remove img tags
     */
    public function parserRemoveTagsImgs() {
        //TODO - do a better regex
        //$this->parserRemoveTags[] = '/<img(.*)?src=\"(.*).[png|jpg|jpeg|gif]\"(.*)?\/>/i';
        $this->parserRemoveTags[] = '/<img(.*)?src=\"(.*)\"(.*)?\/>/i';
        $this->parserRemoveTags[] = "/<img(.*)?src='(.*)'(.*)?\/>/i";
    }
    
    /**
     * get the html file load to dom, extract defined data and insert in new Dom
     * @param string $content_id
     * @param string $header_id
     * @param array  $clear_id    key = attribute / value = value
     */
    public function setDom($content_id, $header_id=false, $clear_id=false) {

        if(!$content_id) {
            throw new \Exception('Contend Id not defined !');
        }

        // loading DOM
		$dom = new \domDocument;
		if(@$dom->loadHTML($this->html)==false) {
			throw new \Exception("HTML Dom Error");
		}

        try {
            $dom->formatOutput = true;
            $dom->preserveWhiteSpace = false;

            // getting data of header/content div
            $xp = new \DomXPath($dom);

            // dom content
            $this->content = $xp->query("//*[@id = '{$content_id}']");

            // dom header
            if(isset($header_id)) {
                $this->header = $xp->query("//*[@id = '{$header_id}']");
            }

            // dom clear
            foreach ($clear_id as $item) {
                $attr   = key($item);
                $value  = current($item);
                $remove = $xp->query("//*[@{$attr} = '{$value}']");
                
                $removeQt = $remove->length;
                if($removeQt>0) {
                    $this->content->item(0)->removeChild($remove->item(0));
                }
            }

            // css
            //$this->css  = $xp->query("//link[@rel = 'stylesheet'] ");
            $css_nodes = $xp->query("//link[@rel = 'stylesheet'] ");

            if($css_nodes->length>0) {
                foreach ($css_nodes as $item) {
                    if(!in_array($item->getAttribute('href'), $this->css)) {
                        $this->css[] = $item->getAttribute('href');    
                    }
                }
            }

            // img
            $img_nodes  = $xp->query("//img");
            
            if($img_nodes->length>0) {
                foreach ($img_nodes as $item) {
                    $this->imgs[] = $item->getAttribute('src');
                }
            }
            
            // importing header node
            if($this->pages==0 && isset($header_id) && $this->header->length>0) {
                $this->appendDom($this->header->item(0));
            }

            // increase the page control var
            $this->pages++;

            // set content id with page number
            $this->content->item(0)->setAttribute('id', 'content-'.$this->pages);

            // importing content node to newDom object
            $this->appendDom($this->content->item(0));
        }
        catch(Exception $e) {
            throw new \Exception("Parser/Dom Error! {$e->getMessage()}");
        }
    }

    
    public function appendDom($node) {
        $this->newDom->appendChild($this->newDom->importNode($node, true));
    }
    
    /**
     * return new parsed html
     * @return string
     */
    public function getHTML() {
        try {
            return $this->newDom->saveHTML();
        }
        catch(\Exception $e) {
            throw new \Exception("HTML Error! {$e->getMessage()}");
        }
    }

    /**
     * return node list with header data
     * @return nodeList
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * return node list from content data
     * @return nodeList
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * return array list with css files attached in html
     * @return array 
     */
    public function getCSS() {
        return $this->css;
    }
    
    /**
     * return array list with imgs paths in html
     * @return array 
     */
    public function getImgs() {
        return $this->imgs;
    }

}
