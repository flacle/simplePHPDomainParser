<?php

/***************************************************************************//**
 *  This file process the ICANN list to PHP. The conversion goes as follows:
 *  1) Extract suffixes from the input dat file of fhe Mozilla foundation.
 *  2) Create a PHP array from those entries.
 *  3) Place the PHP in ../publicsuffixlists/.
 *
 * Note: For our purposes, we are not interested in setting cookies, so the  
 * rule of the exclamation point or asterisk will be handled differently.
 ******************************************************************************/

/***************************************************************************//**
 *  Copy of the specification from https://publicsuffix.org/list/ provided here:
 *  ----------------------------------------------------------------------------
 *  The list is a set of rules, with one rule per line.
 *
 *  Each line is only read up to the first whitespace; entire lines can also be 
 *  commented using //.
 *
 *  Each line which is not entirely whitespace or begins with a comment contains
 *  a rule.
 *
 *  Each rule lists a public suffix, with the subdomain portions separated by 
 *  dots (.) as usual. There is no leading dot.
 *
 *  The wildcard character * (asterisk) matches any valid sequence of characters
 *  in a hostname part. (Note: the list uses Unicode, not Punycode forms, and is
 *  encoded using UTF-8.) Wildcards are not restricted to appear only in the 
 *  leftmost position, but they must wildcard an entire label. (I.e. *.*.foo is 
 *  a valid rule: *bar.foo is not.)
 *
 *  Wildcards may only be used to wildcard an entire level. That is, they must 
 *  be surrounded by dots (or implicit dots, at the beginning of a line).
 * 
 *  If a hostname matches more than one rule in the file, the longest matching 
 *  rule (the one with the most levels) will be used. An exclamation mark (!) at
 *  the start of a rule marks an exception to a previous wildcard rule. An 
 *  exception rule takes priority over any other matching rule.
 ******************************************************************************/

namespace simplePHPDomainParser;

$inputDir = '../publicsuffixlists/';
// Must be regularly updated from https://github.com/publicsuffix/list 
$datFile = 'public_suffix_list.dat';
$outputDir = $inputDir;
$phpFile = 'public_suffix_list.php';

// Get file contents into memory
$fileLines = file(
  $inputDir.$datFile, 
  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);

// Declare booleans to decide which lines to parse
$icannDomains = false;
$commentLine = false;

// TLD array with subTLDs
$tldArr = array();

// Loop through each line of the loaded input file
foreach ($fileLines as $lineNum => $line) {
  
  if (stripos($line, 'END ICANN DOMAINS') !== false) {
    $icannDomains = false;
  }
  if (strpos($line, '//') === 0) {
    $commentLine = true;
  } else {
    $commentLine = false;
  }
  
  // Only parse public ICANN domains that are not comment lines
  if ($icannDomains && !$commentLine) {
    
    // Remove a star followed by a dot
    if (strpos($line, '*.') === 0) {
      $line = substr($line, 2); 
    }
    
    // Or an exclamation point at the beginning
    if (strpos($line, '!') === 0) {
      $line = substr($line, 1);      
    }
    
    // Search for the last dot to separate the TLD from SLD+'s
    $tldDot = strrpos($line, '.');
    
    // Check if there are SLD+'s
    if ($tldDot > 0) {
      $tld = substr($line, $tldDot+1);
      if (isset($tldArr[$tld])) {
        if (is_array($tldArr[$tld])) {
          // Append to sub-array with existing values
          $tldArr[$tld][] = $line;
        } else {
          // Init just one array with the new value
          $tldArr[$tld] = array($line);  
        } 
      } else {
        // Store just a string, not an array
        $tldArr[$line] = $line;  
      }
    } else {
      // Else just store the TLD as the key
      $tldArr[$line] = $line;
    }
  }
  
  if (stripos($line, 'BEGIN ICANN DOMAINS') !== false) {
    $icannDomains = true;
  }
}


$outputStr = '';
$outputStr.= '<?php'.PHP_EOL.PHP_EOL;

// Declare array as string
$outputStr.= '$icannDomains = ';

// Append the parsed contents to the string
$outputStr.= var_export($tldArr, true).';'.PHP_EOL.PHP_EOL;

// Write to file
try{
  if(file_put_contents($outputDir.$phpFile, $outputStr, LOCK_EX) !== false) {
    echo 'Data written to: '.$outputDir.$phpFile; 
  }
} catch (\Exception $e) {
  echo 'Unable to write to: '.$outputDir.$phpFile.'<br>';
  echo 'Caught Exception: '.$e->getMessage();
}
