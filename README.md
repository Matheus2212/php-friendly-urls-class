# PHP Friendly URLs Class
Class that controls Friendly URL. It is focused on SEO best practices.

---

## Overview
This class `__construct` method accepts a initial $url to work with. Please note that the $url informed will be used as the ROOT URL (like the `<base href="<?php echo $url ?>" />` HTML tag).

It can also be omitted, but then the script will try to use $_SERVER values to build the URL.

This class will be updated whenever something new shows up for SEO practices. 

PLEASE NOTE: It is recommended to use a .htaccess file that redirects the user to a specific page (usually the root/home page) index, otherwise the URL that it will work with may result in a 50X error family (bad access) in some cases. It is not required, but it works better this way.

--
## Methods and how to use

### __construct($url = false, $get = false)
Common PHP __construct() method. It will try to build up the application URL by itself, or you can inform a specific URL to use. You can also define to ignore or consider URL queries.

### __set($key, $value)
Common PHP __set method. Use it to set some info on the class instance

### get($part)
This method will retrieve the given fragment corresponding to a number, or a key value. 

Example: 
URL -> http://127.0.0.1/tests/
$class->get(0); // will return "/tests"

###  getSite()
Will return the website URL

### getURL()
Will return the URL

### getParts()
Returns All URL fragments

### now()
Return current URL

### has($word)
Will check if the URL has a given string

### setGet($get = false)
Toggles to ignore or not URL parameters

### addRule
Adds a rule to a specific URL fragment. 

Example: 
$class->addRule('my_fragment',3);
$class->get('my_fragment'); // it's the same as $class->get(3), but in a human friendly way

### getId($position)
Will return the ID on the given position.

Example:
URL -> http://127.0.0.1/tests/some-string-here-123
$class->getId(1); // will return '123'. The ID must be informed on the end, due to SEO friendly patterns

### getAfter($word)
Will return all URL parts after the given part, if it exists

### getURLAfter($word)
Will return the URL fragments after the given word if it exists

### getIdAfter($word)
Will return the ID after the given word if it exists

### getBefore($word)
Returns all URL fragments before the given word

### getURLBefore($word)
Returns the URL before the given word

### getIdBefore($word)
Returns the ID before the given word

### URLencode($string)
Makes the string be a URL acceptable way

### makeLink($string, $id = false)
Makes the string acceptable for URL, and concatenates the ID if its given

### makeHostLink($string, $id = false)
Uses the makeLink method, but to use on self