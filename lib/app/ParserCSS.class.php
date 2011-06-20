<?php
namespace MakeEbook;

class ParserCSS {

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
     * stylesheet urls 
     * @var array
     */
    private $urls = array();

    /**
     * css/style node
     * @var nodeList
     */
    private $css;
    
    /**
     * cralwer object to get css files
     * @var Crawler
     */
    private $objCSS;

    
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
        $this->urls = $urls;
        $this->setUrlsHostPath();
    }
    
    /**
     * get the nodeList from urls and set property of class
     * @param nodeList $urls 
     */
    public function setUrlsNodeList($urls) {
        foreach($urls as $item) {
            $href = $item->getAttribute('href');
            if(!\in_array($href, $this->urls)) {
                $this->urls[] = $href;
            }
        }
        $this->setUrlsHostPath();
    }
    
    /**
     * check if css url is from root (begin with /) or from
     * another path, to add full and correctly url to crawl
     * @param string $host
     * @param string $path
     */
    private function setUrlsHostPath() {
        foreach($this->urls as &$item) {
            if(\substr($item, 0, 7)=='http://') {
                continue;
            }
            else if(\substr($item, 0, 1)=='/') {
                $item = $this->host . $item;
            }
            else {
                $item = $this->host . $this->path . '/' . $item;
            }
        }
    }
    
    /**
     * return css rules
     * @return string 
     */
    public function getCSS() {
        return $this->css;
    }

    /**
     * exec crawler to get css files, parser e extract css rules to set css property
     */
    public function setCSS() {

        try {
            $this->objCSS = new \MakeEbook\Crawler($this->urls);
            $this->objCSS->setString();
            $this->objCSS->exec();

            $css = implode( $this->objCSS->getResult() );
            $css = \preg_replace('/font(-size)?:(.*)px;/', 'font$1:$2pt;', $css);
            $css = "<style> $css </style>";

            $dom = new \domDocument;
            if( $dom->loadHTML($css)==false ) {
                throw new \Exception("HTML Error");
            }

            $dom->preserveWhiteSpace = false;

            // getting data of header/content div
            $xp = new \DomXPath($dom);

            // dom content
            $css = $xp->query("//style");
            $this->css = $css->item(0);
        }
        catch(\Exception $e) {
            throw new \Exception("CSS Error! {$e->getMessage()}");
        }
    }

}
