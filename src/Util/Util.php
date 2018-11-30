<?php
namespace Drupal\dispatcher\Util;

class Util {
    /**
     * @param $field
     */
    public static function processField($field){
        //Is a reference to an entity
        if (isset($field[0]['target_id'])){
            print_r('I am set...: '.$field[0]['target_id']."\n");
        }
        else if (isset($field[0]['value'])){
            print_r($field[0]['value']."\n");
        }
        else{
            //@todo create and throw type not supported exception
        }
    }
}