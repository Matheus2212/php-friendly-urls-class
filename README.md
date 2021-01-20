# PHP-Friendly-URLs
Class that controls Friendly URL. It is focused on SEO best practices.

---

## Overview
This class __construct method accepts a initial $url to work with. Please note that the $url informed will be used as the ROOT URL (like the `<base href="$url" />` HTML tag).

It can also be omitted, but then the script will try to use $_SERVER values to build the URL.

---

## Please Note
This class NEEDS a .htaccess file that redirects the user to a specific page (usually the root/home page), otherwise the URL that it will work with may result in a 5XX error family (bad access). 
