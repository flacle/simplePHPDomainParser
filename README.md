# simplePHPDomainParser
A very simple domain parser for PHP version 5.6.2+. It splits a URL into subdomain(s), registrable domain, and public suffix(es).

## Why simple and why custom?
I am working on a big data processor and needed a domain parsing utility that is lightweight and fast. While I haven't benchmarked the performance of the app, I opted to use basic string fuctions such as `strpos` instead of more [intensive](http://maettig.com/code/php/php-performance-benchmarks.php) regex functions for string pattern matching. While this utility uses an externally maintained reference list, there are no external requests being made as the reference list is pre-processed into a PHP array that can be loaded once per runtime. Because it's a small utility I also made it entirely procedural instead of object oriented.

As experienced by other parser developers, domain parsing is tricky business. For instance, think about the number of segments (such as http://a.b.c.d.e). This complexity comes at a cost where it becomes difficult to accurately parse a domain from an input URL into sub, registrable, and suffixes. One way to quite accurately parse a domain is to compare the input URL with a maintained list of the ICANN database, which is what this utility does.

There are also minor [issues](http://php.net/manual/en/function.parse-url.php#116150) that I've encountered with PHP's own `parse_url()` function, and so this utility does not make use of PHP’s own built in URL parser, nor any regex functions for that matter. Please have a have a look at **demo.php** to see some tests with several URLs.

## Installation
This utility is procedural and does not require classes to be auto loaded. It has a namespace `simplePHPDomainParser` for encapsulation, but that's also it. To incorporate the utility into your own project, paste the folder and include it by adding a statement such as `require_once '../util/simplePHPDomainParser/index.php';` at the top of your script.

## Usage
### Parsing URLs for domains
The below snippet of code:
```php
require_once './index.php';
$url = 'http://shop.retail.mystore.co.uk';
var_dump(\simplePHPDomainParser\getDomain($url));
```
Would output:
```php
array(3) {
  [0]=>
  string(11) "shop.retail"
  [1]=>
  string(7) "mystore"
  [2]=>
  string(5) "co.uk"
}
```
By including `index.php` into your project you automatically include the file `parser.php` that contains the utilities' logic. The main function is `getDomain($url)`. For convenience you can also ask for specific components. Calling `getSubDomain($url)` would return just `shop.retail`. Have a look **demo.php** that contains an array of test URLs.

### Maintaining the ICANN reference list
The ICANN public suffix list comes from https://github.com/publicsuffix/list (thanks Mozilla!). This list is maintained from time to time and if you decide to use it you should also update [public_suffix_list.dat](https://github.com/flacle/simplePHPDomainParser/blob/master/publicsuffixlists/public_suffix_list.dat) from time to time stored in folder `/publicsuffixlists/`. The util parses only public ICANN domains, and not private ones, however feel free to fork and adapt the code as as you see fit. Every time you update the .dat file, you should also run `/src/serializeToPHP.php` to update the PHP array as well.

### Contributor
* [Francis Laclé](http://visualacuity.nl), blog [visualacuity.nl](http://visualacuity.nl)
