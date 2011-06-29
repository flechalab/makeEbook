<?php
/**
 * class parser to get images from html file
 * @package makeEbook
 * @author  Fernando Dias
 */
namespace MakeEbook;

/**
 * parser to get images from html file
 * @package makeEbook
 */
class ParserImg {

    /**
     * host from url
     * @var string
     */
    private $host;
    
    /**
     * path from url
     * @var string
     */
    private $path;
    
    /**
     * img url/file array
     * @var array
     */
    private $urls = array();
    
    /**
     * img node
     * @var nodeList
     */
    private $img;
    
    /**
     * cralwer object to get img files
     * @var Crawler
     */
    private $objImg;

    
    /**
     * start object setting host and path
     * @param string $host
     * @param string $path 
     */
    public function __construct($host, $path) {
        $this->host = $host;
        $this->path = $path;
    }
    
    /**
     * get the urls array and set property of class
     * @param array $urls 
     */
    public function setUrlsArray($urls) {
        if(!is_array($urls)) { throw new Exception('Urls must be an array'); }       
        foreach($urls as $item) {
            if(!\in_array($item, $this->urls)) {
                $this->urls[] = array('img' => $item, 'url' => '');
            }
        }
        $this->setUrlsHostPath();
    }
    
    /**
     * get the nodeList from urls and set property of class
     * @param nodeList $urls 
     */
    public function setUrlsNodeList($urls) {
        foreach($urls as $item) {
            $href = $item->getAttribute('src');
            if(!\in_array($href, $this->urls)) {
                $this->urls[] = array('img' => $href, 'url' => '');
            }
        }
        $this->setUrlsHostPath();
    }
    
    /**
     * check if img path is from root (begin with /) or from
     * another path, to add full and correctly url to crawl
     * @param string $host
     * @param string $path
     */
    private function setUrlsHostPath() {

        foreach($this->urls as &$item) {
            if(\substr($item['img'], 0, 7)=='http://') {
                $item['url'] = $item['url'];
            }
            else if(\substr($item['img'], 0, 1)=='/') {
                $item['url'] = $this->host . $item['img'];
                
                $item['img'] = substr($item['img'], 1);
            }
            else {
                $item['url'] = $this->host . $this->path . '/' . $item['img'];
            }
        }
    }
    
    /**
     * return path files img array
     * @return string 
     */
    public function getImg() {
        return $this->img;
    }

    /**
     * exec crawler to get img files, store localy and set array with local path
     */
    public function setImg() {

        try {

            foreach($this->urls as $item) {
                $this->objImg = new \MakeEbook\Crawler;
                $this->objImg->setUrls($item['url']);
                $this->objImg->setString();
                $this->objImg->exec();
            

                //$filename = (substr($item['img'],0,1)=='/') ? substr($item['img'],1) : $item['img'];
                
                $filename = makeEbook::MAKEEBOOK_ROOT_PATH . 
                            makeEbook::MAKEEBOOK_FILESAVE_PATH . $item['img'];
                             

                /*
                $pathinfo = pathinfo($item['img']);
                $filename = makeEbook::MAKEEBOOK_ROOT_PATH . makeEbook::MAKEEBOOK_FILESAVE_PATH . 
                            $pathinfo['dirname'] . '/' . $pathinfo['basename'];
                */
                
                $bin = $this->objImg->getResult();
                
                $this->objFile = new \MakeEbook\FileMaker;
                $this->objFile->setHtml($bin[0]);
                $this->objFile->makeFile($filename);
                
                $this->objImg->close();
            }
            
        }
        catch(\Exception $e) {
            throw new \Exception("Img Error! {$e->getMessage()}");
        }
    }

}
