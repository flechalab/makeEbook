<?php

$a = '<p class="bla">teste de texto "bla bla bla " tendeu\'s ? `  joão ´  ~ ^ ê ';

echo $a;
echo '<br />' . chr(10);
echo mb_convert_encoding($a, 'html-entities', 'utf-8');
//echo mb_convert_encoding($a, 'html-entities', 'utf-8');
