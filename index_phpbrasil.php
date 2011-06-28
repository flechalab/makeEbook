<?php

error_reporting(E_ERROR);

require_once('bootstrap.php');

try {

    // file name
    $filename = 'phpbrasil-php5-news';

    // getting the urls from book
    $urls_main = new \MakeEbook\getUrls('http://phpbrasil.com/artigos/autor/7zs7YNUFzcSK/douglas-v-pasqua');

    $urls_main->setMenu('div', 'class', 'title');    
    
	$urls_list = $urls_main->getUrls();
    $urls_list = array_reverse($urls_list);

    $urls_all = array();

    foreach($urls_list as $item) {
        // getting the urls from book
        $urls = new \MakeEbook\getUrls($item);
        $urls->setMenu('div', 'class', 'pagination');
        $urls_all = array_merge($urls_all, array($item), $urls->getUrls());
        
    }
        
    // generate html/pdf from urls 
    //$ebook = new \MakeEbook\makeEbookHTML($urls->getUrls);
    //$ebook = new \MakeEbook\makeEbookFILE($urls_all, "{$filename}.html");
    $ebook = new \MakeEbook\makeEbookPDF($urls_all, "{$filename}.pdf");

    $ebook->setContent('content');
    $ebook->setClear(array( array('class'=>'title-vote'), 
                            array('class'=>'author'), 
                            array('class'=>'pagination'), 
                            array('id'=>'comment_pagination')
                          ));
    
    //$ebook->removeImgs();
    $ebook->useCSS();

    $ebook->exec();
    $ebook->output();

    // print log
    echo (implode(PHP_EOL, $ebook->getLog()));
}
catch (Exception $e) {
    echo $e->getMessage();
}
