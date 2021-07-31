# PHP Friendly URLs Class
Class that controls Friendly URL. It is focused on SEO best practices.

---

## Overview
This class `__construct` method accepts a initial $url to work with. Please note that the $url informed will be used as the ROOT URL (like the `<base href="<?php echo $url ?>" />` HTML tag).

It can also be omitted, but then the script will try to use $_SERVER values to build the URL.

This class will be updated whenever something new shows up for SEO practices. 

PLEASE NOTE: It is recommended to use a .htaccess file that redirects the user to a specific page (usually the root/home page) index, otherwise the URL that it will work with may result in a 50X error family (bad access) in some cases. It is not required, but it works better this way.
