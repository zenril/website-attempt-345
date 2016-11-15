<?php

namespace AppBundle\Entity;

//new MultiParam()

class MultiParam extends SingleParam 
{

    private $key;
    private $results;
    
    public function __construct( $command, $regex, $callback, $run = false)
    {
        $this->command = $command;
        $this->regex = $regex;
        $this->callback = $callback;
        
        $this->type = "multi";
        $this->results = array();
        
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $key;
    }

    public function addResult($key, $matches)
    {
        $this->results[$key] = $matches;
    }

    public function getResults()
    {

        return $this->results;
    }


    public function getKey()
    {
        return $this->key;
    }

    public function parseSlackParams($slack_response){
        self::parse($slack_response, array($this->getRegex()));
    }
    
    public static function parse($slack_response, $multiParams = array()){
        foreach ($multiParams as $paramKey => $param) {
            
            if($slack_response['command'] == $param->getCommand() ){
                $continue = true;
                foreach ( $param->regex as $key => $value ) {
                    preg_match_all($value[1], $slack_response["text"], $out, PREG_SET_ORDER);
                    if($value[2] == true && count($out) > 0){
                        $param->addResult($value[0], $out);
                    } else {
                        $continue = false;
                        continue;
                    }
                }
                if($continue){
                    $param->trigger($param, $param->getResults());
                }
            }
        }       
    }
    
}

