<?php
/**
 * crawler to get files/html from internet 
 * @package makeEbook
 * @author  Fernando Dias
 */
namespace MakeEbook;

/**
 * crawler to get files/html from internet 
 * @package makeEbook
 * @author  Fernando Dias
 */
 class Crawler {

    /**
     * @var cURL handle
     */
    private $ch;
    /**
     * @var array urls to go 
     */
    private $urls;
    /**
     * @var handlerfile
     */
    private $handler;
    /**
     * var store string with url return/result
     * @var array
     */
    private $result = array();
    /**
     * @var bool indicate string is set
     */
    private $string;
    
    /**
     * log with errors during curl exec
     * @var array 
     */
    private $log;

    /**
     * start curl handle
     */
    public function __construct($urls=false) {
        try {
            $this->ch = curl_init();
            $this->setUrls($urls);
            $this->settings();
        } catch (Exception $e) {
            throw new Exception('Error to loud CURL Module to execute Crawler.');
        }
    }

    /**
     * set property url with url string/array
     * @param mixed $urls 
     */
    public function setUrls($urls) {
        $this->urls = (isset($urls)) ? (is_array($urls) ? $urls : array($urls)) : false;
    }

    /**
     * return array with urls to crawler
     * @return array
     */
    public function getUrls() {
        return $this->urls;
    }

    /**
     * Set default settings
     */
    public function settings() {
        try {
            curl_setopt($this->ch, CURLOPT_HEADER, 0);
        } catch (Exception $e) {
            throw new Exception("Crawler Settings Error! \r\n ({$e->getMessage()})");
        }
    }

    /**
     * Define the filename to curl drop content
     * @param string $filename name of the file to write content
     */
    private function setFile($filename) {
        throw new Exception("Method Disabled !)");
        /*
        try {
            // create and open file
            $this->handler = \fopen(__DIR__ . '/' . makeEbook::MAKEEBOOK_FILESAVE_PATH . $filename, 'w');
            // curl option to file
            curl_setopt($this->ch, CURLOPT_FILE, $this->handler);
        } catch (Exception $e) {
            throw new Exception("Crawler File Error! \r\n ({$e->getMessage()})");
        }
         * 
         */
    }

    /**
     * Define curl to drop a string with the content
     */
    public function setString() {
        try {
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, TRUE);
            $this->string = TRUE;
        }
        catch (Exception $e) {
            throw new Exception("Crawler Settings Error! \r\n ({$e->getMessage()})");
        }
    }

    /**
     * execute curl process
     */
    public function exec() {

        try {

            // if output configs is not defined
            if (!isset($this->handler) && !isset($this->string)) {
                throw new \Exception("Crawler Output not defined!");
            }

            // loop for urls, exec curl and put return in the array result
            foreach ($this->urls as $item) {
                curl_setopt($this->ch, CURLOPT_URL, $item);
                $result = curl_exec($this->ch);

                if($this->info_status()==200) {
                    $this->result[] = $result;
                }
                else {
                    $this->log[] = "{$item} not crawled";
                }
            }

            // closing curl handler
            if (isset($this->handler)) {
                fclose($this->handler);
            }

        }
        catch (Exception $e) {
            throw new Exception("Crawler Exec Error! \r\n ({$e->getMessage()})");
        }
    }

    /**
     * get array result from crawlers
     * @return array
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * display info about curl execution (var_dump)
     * @return mixed
     */
    public function info() {
        return \var_dump(curl_getinfo($this->ch));
    }

    /**
     * info about actual curl transfer
     * @return mixed 
     */
    public function info_status() {
        return curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
    }
    
    /**
     * return log data
     * @return array
     */
    public function getLog() {
        return $this->log;
    }

    /**
     * close curl handler
     */
    public function close() {
        curl_close($this->ch);
    }

}
