<?php
  
/***************************************************************************//**
 *  This file contains some example code to demonstrate the features of the lib.
 ******************************************************************************/
 
/***************************************************************************//**
 *  Short intro: This lib was built to be used as a simple URL-parts splitter.
 *  You provide a URL, and it will return the sub domains, registrable domain,
 *  and the public suffix seperately. Example:
 *  Example URL: http://shop.retailer.mystore.co.uk
 *  will return:
 *  [0] = shop.retailer (sub domains)
 *  [1] = mystore (registrable domain)
 *  [2] = co.uk (public suffix)
 *
 * The data comes from https://github.com/publicsuffix/list which is updated 
 * regularly and if you decide to use it you should update the .dat file stored
 * in /publicsuffixlists/ from time to time. The lib parses only public ICANN
 * domains, and not private ones, however you can fork and adapt the code as
 * as you see fit.
 *
 * The lib actually uses a pre-generated PHP array instead of the .dat file for 
 * performance reasons (no pre-processing required, and no external data loads).
 *
 * Every time you update the .dat file, you should also run 
 * /src/serializeToPHP.php to update the PHP array as well.
 *
 * Finally this simplified parser was inspired by:
 * - https://github.com/jeremykendall/php-domain-parser
 * - https://github.com/peerigon/parse-domain
 ******************************************************************************/
 
require_once './index.php';
 
 $urls = array(
   'com',
   'example.COM',
   'WwW.example.COM',
   '.com',
   '.example',
   'example.uk.com',
   'test.jp',
   'www.test.jp',
   'www.test.k12.ak.us',
   'shop.retail.mystore.co.uk',
   'test.test.at',
   'buynow.com.br',
   'http://username:password@firstexample.com/',
   'mailto:someone@example.com',
   'mailto:someone@example.com?subject=This%20is%20the%20subject'. //cont...
   '&cc=someone_else@example.com&body=This%20is%20the%20body',
   'mailto:someone@example.com,someoneelse@example.com', // parses just one here
   'anotherone@example.com',
   'mytest.com?myquery',
   'welovearuba.com.aw/subfolder1/subfolder2',
 );
 
 foreach ($urls as $url) {
   echo 'URL: '.$url;
   echo '<pre>';
     echo var_dump(\simplePHPDomainParser\getDomain($url));
   echo '</pre><br />---<br />';
 }