<?php
  
/***************************************************************************//**
 *  This file contains functions that can parse the generated PHP array stored
 *  in ../publicsuffixlists/public_suffix_list.php.
 ******************************************************************************/
 
namespace simplePHPDomainParser;

/**
 * Cleans up a URL by removing protocol prefixes and subfolders
 * @param $url, a URL string passed by reference
 */
function cleanseURL(&$url) {
  $url = strtolower($url);
  
  // Check if we are dealing with an email address here
  $mailto = strpos($url, 'mailto:');
  $atsign = strpos($url, '@');
  
  if ($mailto === 0 || $atsign !== false) {
    $comma = strpos($url, ',');
    // Remove multiple email addresses
    if ($comma !== false && $comma > $atsign) {
      $url = substr($url, 0, $comma);
    }
    $url = substr($url, $atsign+1);
  }
  
  $doubleSlash = strpos($url, '//');
  $firstDot = strpos($url, '.');
  
  // Check if the double slash is present before the dot
  // This I think would hint at a locator in most cases
  if (
    $doubleSlash !== false && $firstDot !== false && $doubleSlash < $firstDot
  ) {
    // Remove the protocol from the string
    $url = substr($url, $doubleSlash+2);
  }
  
  // Check for the first forward slash to remove all sub folders
  $forwardSlash = strpos($url, '/');
  if ($forwardSlash !== false) {
    $url = substr($url, 0, $forwardSlash);
  }
  
  // Check for query strings and remove those as well
  $queries = strpos($url, '?');
  if ($queries !== false) {
    $url = substr($url, 0, $queries);
  }
  
  // Append a random char such as % at the end for faster TLD matching
  $url = $url.'%';
}

/**
 * Parses a URL into sub domain(s), registerable domain, and public suffix
 * @param $url a URL string
 * @param $icannDomains a PHP array containing ICANN domain data
 */
function getDomain($url) {
  global $icannDomains;
  $subDomain = '';
  $regDomain = '';
  $pubSuffix = '';
  
  // Clean the URL before processing
  cleanseURL($url);
  
  // Loop through the ICANN data
  foreach ($icannDomains as $tld => $id) {
    $urlTLD = strpos($url, '.'.$tld.'%');
    // TLD found
    if ($urlTLD > 0) {
      // Check for SLD+'s
      if (is_array($id)) {
        foreach($id as $sld) {
          $urlSuffix = strpos($url, '.'.$sld.'%');
          // SLD+ found
          if ($urlSuffix > 0) {
            // Parse and store
            $pubSuffix = $sld;
            $regDomain = substr($url, 0, $urlSuffix);
            $lastDot = strrpos($regDomain, '.');
            if($lastDot !== false) {
              $regDomain = substr($regDomain, $lastDot+1);  
            }
            $subDomain = substr($url, 0, $lastDot);
            return array($subDomain, $regDomain, $pubSuffix);
          }
        }
      }      
      // Continue when no SLD+'s are found
      $pubSuffix = $tld;
      $regDomain = substr($url, 0, $urlTLD);
      $lastDot = strrpos($regDomain, '.');
      if($lastDot !== false) {
        $regDomain = substr($regDomain, $lastDot+1);  
      }
      $subDomain = substr($url, 0, $lastDot);
      return array($subDomain, $regDomain, $pubSuffix);
    }
  }
  
  return false;
  
}

/**
 * Wrapper function to obtain only the subdomain
 * @param $url a URL string
 */ 
function getSubDomain(&$url) {
  global $icannDomains;
  return getDomain($url)[0];
}

/**
 * Wrapper function to obtain only the registrable domain
 * @param $url a URL string
 */ 
function getRegisterableDomain(&$url) {
  global $icannDomains;
  return getDomain($url)[1];
}

/**
 * Wrapper function to obtain only the public suffix
 * @param $url a URL string
 */ 
function getPublicSuffix(&$url) {
  global $icannDomains;
  return getDomain($url)[2];
}