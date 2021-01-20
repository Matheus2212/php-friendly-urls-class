<?php

/**
 * [URL] - Classe responsável por manipular a URL informada na barra de endereço. 
 * 
 * 16/04/2020 -> Documentação Revisada
 * 16/01/2021 -> Feitos ajustes em métodos
 * 16/01/2021 -> __construct (com/sem a barra no final)
 * 16/01/2021 -> getId (para trabalhar melhor com SEO e URLs amigáveis);
 * 16/01/2021 -> getApos (refatorado método)
 * 16/01/2021 -> getURLApos (refatorado método)
 * 16/01/2021 -> getIdApos (refatorado método)
 * 16/01/2021 -> getAntes (refatorado método)
 * 16/01/2021 -> getURLAntes (refatorado método)
 * 16/01/2021 -> getIdAntes (refatorado método)
 * 16/01/2021 -> Renomeado método gerarLinkWebsite para gerarLinkInterno
 * 
 */

class url {

    private $site = null; // [ATRIBUTO] - Nome do Website onde está a classe;
    private $url = null; // [ATRIBUTO] - URL informada na Barra de Endereço.
    private $url_agora = null; // [ATRIBUTO] - URL atual.
    private $partes = null; // [ATRIBUTO] - Partes da URL informada na Barra de Endereço.
    private $regras = array(); // [ATRIBUTO] - Regras da adicionadas pelo usuário.
    private $get = false; // [ATRIBUTO] - Filtra o $_GET da URL para interação com a classe. Pode ser retirado.

    /**
     * __construct Monta a URL assim que a página é aberta.
     */
    public function __construct($url = false, $get = false) {
        $this->setGet($get);
        $this->url = ($url ? $url : ($this->get ? preg_replace("/\?.*/", "", preg_replace("/http(s)?\:\/\//", "", $_SERVER['REQUEST_URI'])) : preg_replace("/http(s)?\:\/\//", "", $_SERVER['REQUEST_URI'])));
        $lastChar = substr($this->url, -1);
        if($lastChar!=="/"){
            $this->url = $this->url."/";
        }
        $this->site = $_SERVER['HTTP_HOST'];
        $this->url_agora = $this->site . $this->url;
        $partes = explode("/", $this->url);
        if ($url) {
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
        }
        unset($partes[0]);
        unset($partes[count($partes)]);
        $partes = explode("/", implode("/", $partes));
        $this->partes = $partes;
    }

    /**
     * @param int $parte Recebe a posição desejada.
     * @return string $posicao Retorna o texto da url na posicao especificada.
     */
    public function get($parte) {
        if (array_key_exists($parte, $this->partes)) {
            return $this->partes[$parte];
        }
        if (array_key_exists($parte, $this->regras)) {
            return $this->partes[$this->regras[$parte]];
        }
        return "";
    }

    /**
     * @return string $string Retorna o $host do site.
     */
    public function getSite() {
        return $this->site;
    }

    /**
     * @return array $array Retorna todas as partes da URL.
     */
    public function getPartes() {
        return $this->partes;
    }

    /**
     * @return string Retorna a URL no momento
     */
    public function agora() {
        return $this->url_agora;
    }

    /**
     * @param string $palavra Recebe a palavra alvo para pesquisar.
     * @return bool Verifica se possui o o que for informado aqui na url. Se tiver, retorna true.
     */
    public function contem($palavra) {
        if ((in_array($palavra, $this->partes)) || ( in_array($this->tratar($palavra), $this->partes)) || preg_match("/$palavra/", implode('/',$this->partes))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param bool $get Define se a url irá retornar o endereço com ou sem valores de $_GET
     */
    public function setGet($get = false) {
        $this->get = ($get);
    }

    /**
     * 
     * @param string $nome Recebe o nome da regra.
     * @param int $posicao Recebe a posição da regra.
     */
    public function addRegra($nome, $posicao) {
        $this->regras[$nome] = $posicao;
    }

    /**
     * @param string $posicao = null Recebe a URL para procurar por uma ID.
     * @return int $id Retorna a Id que pode estar na URL
     */
    public function getId($posicao = false) {
        $id = 0;
        if ($posicao === false) {
            $last = (count($this->partes))-1;
            $aux = array_reverse(explode("-",$this->partes[$last]));
            $id = (int) $aux[0];
        } else {
            if ((is_int($posicao) || is_string($posicao)) and $this->get($posicao)!=="") {
                $id = (int) array_reverse(explode('-',$this->get($posicao)))[0];
            }
        }
        return $id;
    }

    /**
     * @param string $palavra Recebe a palavra para pegar o que há depois dela.
     * @return string Retorna toda a url Depois da Palavra informada.
     */
    public function getApos($palavra,$onlyKey = false) {
        if ($this->contem($palavra)) {
            $wordKey = null;
            foreach($this->partes as $key =>$value){
                if($value==$palavra){
                    $wordKey = $key;
                    break;
                }
            }
            return ($onlyKey?(isset($this->partes[$wordKey+1])?$wordKey+1:""):(isset($this->partes[$wordKey+1])?$this->partes[$wordKey+1]:""));
        }
        return false;
    }

    /**
     * @param string $palavra Recebe a palavra para retornar o que há antes dela.
     * @return string Retorna toda a url Antes da Palavra informada.
     */
    public function getURLApos($palavra) {
        if ($this->contem($palavra)) {
            $wordKey = $this->getApos($palavra,true);
            $contar = false;
            $url = array();
            foreach($this->partes as $key => $value){
                if($key==$wordKey){
                    $contar = true; 
                }
                if($contar){
                    $url[] = $value;
                }
            }
            return implode("/",$url);
        }
        return false;
    }

    /**
     * @param string $palavra Recebe a palavra para procurar o que há depois dela.
     * @return int $id Retorna a ID que está depois da palavra informada
     */
    public function getIdApos($palavra) {
        return $this->getId($this->getApos($this->tratar($palavra),true));
    }
 
    /**
     * @param string $palavra Recebe a palavra para retornar o que há antes dela.
     * @return string Retorna toda a url Antes da Palavra informada.
     */
    public function getAntes($palavra,$onlyKey = false) {
        if ($this->contem($palavra)) {
            $wordKey = null;
            foreach($this->partes as $key =>$value){
                if($value==$palavra){
                    $wordKey = $key;
                    break;
                }
            }
            return ($onlyKey?(isset($this->partes[$wordKey-1])?$wordKey-1:""):(isset($this->partes[$wordKey-1])?$this->partes[$wordKey-1]:""));
        }
        return false;
    }

    /**
     * @param string $palavra Recebe a palavra para retornar o que há antes dela.
     * @return string Retorna toda a url Antes da Palavra informada.
     */
    public function getURLAntes($palavra) {
        if ($this->contem($palavra)) {
            $wordKey = $this->getAntes($palavra,true);
            if($wordKey!==""){
                $quebrar = null;
                $url = array();
                $url[] = $this->site;
                foreach($this->partes as $key => $value){
                    if($quebrar!==null){
                        break;
                    }
                    if($key ==$wordKey){
                        $quebrar = true;
                    }
                    $url[] = $value;
                }
                return implode("/",$url);
            }else{
                return false;
            }
        }
        return false;
    }
        

    /**
     * @param string $palavra Recebe a palavra para retornar a ID que há antes dela.
     * @return int $id Retorna a ID que está antes da palavra informada.
     */
    public function getIdAntes($palavra) {
        return $this->getId($this->getAntes($this->tratar($palavra),true));
    }

    /**
     * @param string $string Recebe um texto para converter em URL.
     * @return string $novaUrl Esta função gera uma url para um link. Só deve ser usada para links diretos (sem "/")
     */
    public function URLizer($string) {        
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
     * @param string $texto Recebe o texto a ser tornado link.
     * @param int $id Recebe uma Id, caso utilize $_GET com a página destino.
     * @return string Retorna o Link gerado.
     */
    public function gerarLink($texto, $id = false) {
        $texto = $this->URLizer($texto);
        if (strlen($texto) > 50) {
            $texto = substr($texto, 0, 50);
        }
        return $texto . ($id ? "-". $id: "");
    }

    /**
     * 
     * @param string $texto Recebe o texto a ser tornado link.
     * @param int $id Recebe uma Id, caso utilize $_GET com a página destino.
     * @return string Retorna o Link gerado de forma que seja possível acessar externamente.
     */
    public function gerarLinkInterno($texto, $id = false) {
        return $this->site . $this->url . $this->gerarLink($texto, $id);
    }

}
