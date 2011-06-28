<?php

error_reporting(E_ERROR);

require_once('bootstrap.php');

try {

    // file name
    $filename = 'progit-book';
    
    // getting the urls from book
    $urls = new \MakeEbook\getUrls('http://progit.org/book/');
    $urls->removeTags('div', 'buy-book-toc');
    $urls->setMenu('ul', 'id', 'toc');

    // generate html/pdf from urls 
    //$ebook = new \MakeEbook\makeEbookHTML($urls->getUrls);
    //$ebook = new \MakeEbook\makeEbookFILE($urls->getUrls(), "{$filename}.html");
    $ebook = new \MakeEbook\makeEbookPDF($urls->getUrls(), "{$filename}.pdf");

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
