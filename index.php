<?php
  
/***************************************************************************//**
 *  This file functions as an "initializer" for the library.
 ******************************************************************************/

// Declare namespace  
namespace simplePHPDomainParser;

// Include the pre-generated ICANN domains array
require_once 'publicsuffixlists/public_suffix_list.php';

// Load the parser
require_once 'src/parser.php';