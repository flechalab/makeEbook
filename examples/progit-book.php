<?php
// set to report only errors
error_reporting(E_ERROR);

/**
 * including bootstrap
 */
require_once('../config/bootstrap.php');

try {

    // file name
    $filename = 'progit-book-img';
    
    // getting the urls from book
    $urls = new \MakeEbook\getUrls('http://progit.org/book/');
    $urls->removeTags('div', 'buy-book-toc');
    $urls->removeUrls(array('commands.html'));
    $urls->setMenu('ul', 'id', 'toc');
    
    // generate html/pdf from urls 
    $ebook = new \MakeEbook\makeEbookFILE($urls->getUrls(), "{$filename}.html");

    $ebook->setHeader('header');
    $ebook->setContent('content');
    $ebook->setClear(array(array('id'=>'nav'), array('class'=>'clearfix')));
    
    //$ebook->removeImgs();
    //$ebook->useCSS();

    $ebook->exec();
    $ebook->output();

    // print log
    echo (implode(PHP_EOL, $ebook->getLog()));
}
catch (Exception $e) {
    echo $e->getMessage();
}
