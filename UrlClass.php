<?php

/**
 * [URL] - This class is responsible to retrieve data and set data using the friendly URL best practice.
 * 
 * 2020-04-16 -> Readme updated
 * 2021-01-16 -> Methods updated - __construct accepts URLs with or without https://
 * 2021-01-15 -> getId (best practice within SEO and URL best practices);
 * 2021-01-16 -> Renamed method to create a new link using URL class
 * 2021-01-21 -> Improved overall class performance - created URLencode method
 * 2021-07-31 -> Changed README.md to English
 * 2021-10-27 -> Renamed almost ALL methods to english and parameters as well
 * 2021-10-27 -> Finished documentation
 * 2022-03-25 -> Fixed $get definition and added __get magic function
 * 2022-09-25 -> Renamed to be able to use PHP Unit
 */

class URL
{

    private $site = null; // website name
    private $url = null; // website URL
    private $url_now = null; // current URL
    private $parts = null; // URL parts
    private $regras = array(); // Custom user rules
    private $get = false; // You can decide if the $_GET will be considered or ignored

    /**
     * __construct Creates the URL within the new class instance
     */
    public function __construct($url = false, $get = false)
    {
        $this->setGet($get);
        $this->site = $_SERVER['HTTP_HOST'];
        $this->url = ($url ? $url : $_SERVER["REQUEST_SCHEME"] . "://" . $this->site . $_SERVER['REQUEST_URI']);
        if (substr($this->url, -1) !== "/") {
            $this->url = $this->url . "/";
        }
        $this->url_now = $_SERVER["REQUEST_SCHEME"] . "://" . $this->site . $_SERVER["REQUEST_URI"];
        if (substr($this->url_now, -1) !== "/") {
            $this->url_now = $this->url_now . "/";
        }
        $parts = str_replace(preg_replace("/http(s)?\:\/\//", "", $this->url), '', preg_replace("/http(s)?\:\/\//", "", explode("?", $this->url_now)[0]));
        $parts = explode("/", $parts);
        if ($this->get) {
            $get = explode("?", $this->url_now);
            $get = explode("&", $get[1]);
        }
        if ($parts[count($parts) - 1] == "") {
            unset($parts[count($parts) - 1]);
        }
        if (is_array($parts)) {
            if ($this->get) {
                $parts = array_merge($parts, $get);
            }
            $this->parts = $parts;
        }
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    public function __get($key)
    {
        return $this->$key;
    }

    /**
     * @param int $part Receives the desired URL part
     * @return string $position Returns the informed URL part text
     */
    public function get($part)
    {
        if (array_key_exists($part, $this->parts)) {
            return "/" . $this->parts[$part];
        }
        if (array_key_exists($part, $this->regras)) {
            return "/" . $this->parts[$this->regras[$part]];
        }
        return "/";
    }

    /**
     * @return string $string Returns website's HOST
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @return string $string Returns website's HOST. If $ip switches 'localhost' by SERVER IP
     */
    public function getURL($ip = false)
    {
        $url = $this->url;
        $serverIp = $_SERVER["SERVER_ADDR"] == "::1" ? "127.0.0.1" : $_SERVER["SERVER_ADDR"];
        if ($ip) {
            $url = str_replace("localhost", $serverIp, $this->url);
        }
        return $url;
    }

    /**
     * @return array $array Returns all URL parts in an array
     */
    public function getParts()
    {
        return array_map(function ($part) {
            return "/" . $part;
        }, $this->parts);
    }

    /**
     * @return string Returns the current URL
     */
    public function now($ip = false)
    {
        $url = $this->url_now;
        $serverIp = $_SERVER["SERVER_ADDR"] == "::1" ? "127.0.0.1" : $_SERVER["SERVER_ADDR"];
        if ($ip) {
            $url = str_replace("localhost", $serverIp, $url);
        }
        return $url;
    }

    /**
     * @param string $word Receives a word to search within the URL parts
     * @return bool If the word exists on URL, returns true
     */
    public function has($word)
    {
        if ($word !== "" && (in_array($word, $this->parts) || (in_array($this->URLencode($word), $this->parts)))) {
            return true;
        }
        $word = str_replace("/", "\/", $word);
        if ($word !== "" && (preg_match("/" . $word . "/", $this->now()))) {
            return true;
        }
        return false;
    }

    /**
     * 
     * @param bool $get Defines if $_GET will be considered or ignored
     */
    public function setGet($get = false)
    {
        $this->get = ($get);
    }

    /**
     * 
     * @param string $name Receives the rule name
     * @param int $position It is the rule itself
     */
    public function addRule($name, $position)
    {
        $this->regras[$name] = $position;
    }

    /**
     * @param string $position = null Searches for an ID on the URL
     * @return int $id Returns the ID if it is on the URL
     */
    public function getId($position = false)
    {
        $id = 0;
        if ($position === false) {
            $last = (count($this->parts)) - 1;
            $aux = array_reverse(explode("-", $this->parts[$last]));
            $id = (int) $aux[0];
        } else {
            if ((is_int($position) || is_string($position)) and $this->get($position) !== "") {
                $id = (int) array_reverse(explode('-', $this->get($position)))[0];
            }
        }
        return $id;
    }

    /**
     * @param string $word This acts as a break point for the current URL
     * @return string Returns all URL parts after the given word
     */
    public function getAfter($word, $onlyKey = false)
    {
        if ($this->has($word)) {
            $wordKey = null;
            foreach ($this->parts as $key => $value) {
                if ($value == $word) {
                    $wordKey = $key;
                    break;
                }
            }
            return ($onlyKey ? (isset($this->parts[$wordKey + 1]) ? $wordKey + 1 : false) : (isset($this->parts[$wordKey + 1]) ? $this->parts[$wordKey + 1] : false));
        }
        return false;
    }

    /**
     * @param string $word This acts as a break point for the current URL
     * @return string Returns all URL parts before the given word
     */
    public function getURLAfter($word)
    {
        if ($this->has($word)) {
            $wordKey = $this->getAfter($word, true);
            $contar = false;
            $url = array();
            foreach ($this->parts as $key => $value) {
                if ($key == $wordKey) {
                    $contar = true;
                }
                if ($contar) {
                    $url[] = $value;
                }
            }
            return implode("/", $url);
        }
        return false;
    }

    /**
     * @param string $word Receives a word to search for an ID after the word
     * @return int $id Returns the ID if found
     */
    public function getIdAfter($word)
    {
        return $this->getId($this->getAfter($this->URLencode($word), true));
    }

    /**
     * @param string $word This acts as a break point for the current URL
     * @return string Returns all the URL parts before the given word
     */
    public function getBefore($word, $onlyKey = false)
    {
        if ($this->has($word)) {
            $wordKey = null;
            foreach ($this->parts as $key => $value) {
                if ($value == $word) {
                    $wordKey = $key;
                    break;
                }
            }
            return ($onlyKey ? (isset($this->parts[$wordKey - 1]) ? $wordKey - 1 : false) : (isset($this->parts[$wordKey - 1]) ? $this->parts[$wordKey - 1] : false));
        }
        return false;
    }

    /**
     * @param string $word This acts as a break point for the current URL
     * @return string Returns all URL parts before the given word
     */
    public function getURLBefore($word)
    {
        if ($this->has($word)) {
            $wordKey = $this->getBefore($word, true);
            if ($wordKey !== "") {
                $quebrar = null;
                $url = array();
                $url[] = $this->site;
                foreach ($this->parts as $key => $value) {
                    if ($quebrar !== null) {
                        break;
                    }
                    if ($key == $wordKey) {
                        $quebrar = true;
                    }
                    $url[] = $value;
                }
                return implode("/", $url);
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * @param string $word Receives a word to search for an ID before the word
     * @return int $id Returns the ID if given word and ID is found
     */
    public function getIdBefore($word)
    {
        return $this->getId($this->getBefore($this->URLencode($word), true));
    }

    /**
     * @param string $string Receives some text to normalize for URL format
     * @return string $novaUrl This function will returns the same given string, but in "URL format"
     */
    public function URLencode($string)
    {
        $string = preg_replace('/[áàãâä]/ui', 'a', $string);
        $string = preg_replace('/[éèêë]/ui', 'e', $string);
        $string = preg_replace('/[íìîï]/ui', 'i', $string);
        $string = preg_replace('/[óòõôö]/ui', 'o', $string);
        $string = preg_replace('/[úùûü]/ui', 'u', $string);
        $string = preg_replace('/[ç]/ui', 'c', $string);
        $string = preg_replace('/[^a-z0-9]/i', '_', $string);
        $string = preg_replace('/_+/', '-', $string);
        return $string;
    }

    /**
     * 
     * @param string $string Receives a text to render as link
     * @param int $id Receievs an ID to append on the link
     * @return string Returns generated link
     */
    public function makeLink($string, $id = false)
    {
        $string = $this->URLencode($string);
        if (strlen($string) > 50) {
            $string = substr($string, 0, 50);
        }
        return $string . ($id ? "-" . $id : "");
    }

    /**
     * 
     * @param string $string Receives some text to render as your own website link
     * @param int $id Receives an ID to append on the generated link
     * @return string Returns the generated link as if it is on your own website
     */
    public function makeHostLink($string, $id = false)
    {
        return $this->site . $this->url . $this->makeLink($string, $id);
    }
}
