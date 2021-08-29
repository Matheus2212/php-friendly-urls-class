<?php

/**
 * [URL] - This class is responsible to retrieve data and set data using the friendly URL best practice.
 * 
 * 2020-04-16 -> Readme updated
 * 2021-01-16 -> Methods updated - __construct accepts URLs with or without https://
 * 2021-01-15 -> getId (best practice within SEO and URL best practices);
 * 2021-01-16 -> Renamed method to create a new link using URL class
 * 2021-01-21 -> Improved overall class performance - created URLizer method
 * 2021-07-31 -> Changed README.md to English
 */

class URL
{

    private $site = null; // website name
    private $url = null; // website URL
    private $url_agora = null; // current URL
    private $partes = null; // URL parts
    private $regras = array(); // Custom user rules
    private $get = false; // You can decide if the $_GET will be considered or ignored

    /**
     * __construct Creates the URL within the new class instance
     */
    public function __construct($url = false, $get = false)
    {
        $this->setGet($get);
        $this->url = ($url ? $url : ($this->get ? preg_replace("/\?.*/", "", preg_replace("/http(s)?\:\/\//", "", $_SERVER['REQUEST_URI'])) : preg_replace("/http(s)?\:\/\//", "", $_SERVER['REQUEST_URI'])));
        if (substr($this->url, -1) !== "/") {
            $this->url = $this->url . "/";
        }
        $this->site = $_SERVER['HTTP_HOST'];
        $this->url_agora = $_SERVER["REQUEST_SCHEME"] . "://" . $this->site . $_SERVER["REQUEST_URI"];

        $partes = str_replace(preg_replace("/http(s)?\:\/\//", "", $this->url), '', preg_replace("/http(s)?\:\/\//", "", $this->url_agora));
        $partes = explode("/", $partes);
        /*if ($url) {
            if (preg_match("/\//", $url)) {
                $informada = explode("/", $url);
                foreach ($informada as $key => $value) {
                    if (in_array($value, $partes)) {
                        unset($partes[array_search($value, $partes)]);
                    }
                }
            } else {
                unset($partes[array_search($url, $partes)]);
            }
        }*/
        if (is_array($partes)) {
            $this->partes = $partes;
        }
    }

    public function __set($key, $value)
    {
        $this->$key = $value;
        return $this;
    }

    /**
     * @param int $parte Receives the desired URL part
     * @return string $posicao Returns the informed URL part text
     */
    public function get($parte)
    {
        if (array_key_exists($parte, $this->partes)) {
            return $this->partes[$parte];
        }
        if (array_key_exists($parte, $this->regras)) {
            return $this->partes[$this->regras[$parte]];
        }
        return "";
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
    public function getPartes()
    {
        return $this->partes;
    }

    /**
     * @return string Returns the current URL
     */
    public function agora($ip=false)
    {
        $url = $this->url_agora;
        $serverIp = $_SERVER["SERVER_ADDR"] == "::1" ? "127.0.0.1" : $_SERVER["SERVER_ADDR"];
        if ($ip) {
            $url = str_replace("localhost", $serverIp, $url);
        }
        return $url;
    }

    /**
     * @param string $palavra Receives a word to search within the URL parts
     * @return bool If the word exists on URL, returns true
     */
    public function contem($palavra)
    {
        $palavra = str_replace("/", "\/", $palavra);
        if ($palavra !== "" && ((in_array($palavra, $this->partes)) || (in_array($this->URLizer($palavra), $this->partes)) || $this->get($palavra) !== "" || preg_match("/" . $palavra . "/", $this->url))) {
            return true;
        } else {
            return false;
        }
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
     * @param string $nome Receives the rule name
     * @param int $posicao It is the rule itself
     */
    public function addRegra($nome, $posicao)
    {
        $this->regras[$nome] = $posicao;
    }

    /**
     * @param string $posicao = null Searches for an ID on the URL
     * @return int $id Returns the ID if it is on the URL
     */
    public function getId($posicao = false)
    {
        $id = 0;
        if ($posicao === false) {
            $last = (count($this->partes)) - 1;
            $aux = array_reverse(explode("-", $this->partes[$last]));
            $id = (int) $aux[0];
        } else {
            if ((is_int($posicao) || is_string($posicao)) and $this->get($posicao) !== "") {
                $id = (int) array_reverse(explode('-', $this->get($posicao)))[0];
            }
        }
        return $id;
    }

    /**
     * @param string $palavra This acts as a break point for the current URL
     * @return string Returns all URL parts after the given word
     */
    public function getApos($palavra, $onlyKey = false)
    {
        if ($this->contem($palavra)) {
            $wordKey = null;
            foreach ($this->partes as $key => $value) {
                if ($value == $palavra) {
                    $wordKey = $key;
                    break;
                }
            }
            return ($onlyKey ? (isset($this->partes[$wordKey + 1]) ? $wordKey + 1 : false) : (isset($this->partes[$wordKey + 1]) ? $this->partes[$wordKey + 1] : false));
        }
        return false;
    }

    /**
     * @param string $palavra This acts as a break point for the current URL
     * @return string Returns all URL parts before the given word
     */
    public function getURLApos($palavra)
    {
        if ($this->contem($palavra)) {
            $wordKey = $this->getApos($palavra, true);
            $contar = false;
            $url = array();
            foreach ($this->partes as $key => $value) {
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
     * @param string $palavra Receives a word to search for an ID after the word
     * @return int $id Returns the ID if found
     */
    public function getIdApos($palavra)
    {
        return $this->getId($this->getApos($this->URLizer($palavra), true));
    }

    /**
     * @param string $palavra This acts as a break point for the current URL
     * @return string Returns all the URL parts before the given word
     */
    public function getAntes($palavra, $onlyKey = false)
    {
        if ($this->contem($palavra)) {
            $wordKey = null;
            foreach ($this->partes as $key => $value) {
                if ($value == $palavra) {
                    $wordKey = $key;
                    break;
                }
            }
            return ($onlyKey ? (isset($this->partes[$wordKey - 1]) ? $wordKey - 1 : false) : (isset($this->partes[$wordKey - 1]) ? $this->partes[$wordKey - 1] : false));
        }
        return false;
    }

    /**
     * @param string $palavra This acts as a break point for the current URL
     * @return string Returns all URL parts before the given word
     */
    public function getURLAntes($palavra)
    {
        if ($this->contem($palavra)) {
            $wordKey = $this->getAntes($palavra, true);
            if ($wordKey !== "") {
                $quebrar = null;
                $url = array();
                $url[] = $this->site;
                foreach ($this->partes as $key => $value) {
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
     * @param string $palavra Receives a word to search for an ID before the word
     * @return int $id Returns the ID if given word and ID is found
     */
    public function getIdAntes($palavra)
    {
        return $this->getId($this->getAntes($this->URLizer($palavra), true));
    }

    /**
     * @param string $string Receives some text to normalize for URL format
     * @return string $novaUrl This function will returns the same given string, but in "URL format"
     */
    public function URLizer($string)
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
     * @param string $texto Receives a text to render as link
     * @param int $id Receievs an ID to append on the link
     * @return string Returns generated link
     */
    public function gerarLink($texto, $id = false)
    {
        $texto = $this->URLizer($texto);
        if (strlen($texto) > 50) {
            $texto = substr($texto, 0, 50);
        }
        return $texto . ($id ? "-" . $id : "");
    }

    /**
     * 
     * @param string $texto Receives some text to render as your own website link
     * @param int $id Receives an ID to append on the generated link
     * @return string Returns the generated link as if it is on your own website
     */
    public function gerarLinkInterno($texto, $id = false)
    {
        return $this->site . $this->url . $this->gerarLink($texto, $id);
    }
}
