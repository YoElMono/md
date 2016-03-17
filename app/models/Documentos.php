<?php

class Documentos extends \Phalcon\Mvc\Model
{



    public function __set($attr , $value)
    {
        $this->$attr = $value;
        return $this;
    }


    public function __setData($Data)
    {
        foreach($Data as $key => $value){
            $this->$key = $value;
        }
        return $this;
    }



    public function __get($attr)
    {
        return $this->$attr;
    }


}
