<?php

class EuAjaxData{

    public static $defualt_timeout = 2;

    public static function is_token_valid(){
        $token = new EuJWT($_POST['token']);
        $data = $token->getData($_POST['token']);
        $diff = date_diff( $data->checkpoint, date("h:i:sa") );
        return floatval($diff->i) < self::$defualt_timeout;
    }

    public static function get_option_content($tag, $default_value, $is_echo = true){
        $result = $default_value;
        if( self::is_token_valid() ) {
            $result = get_option($tag, $default_value);
        }
        if( !$is_echo ) {
            return json_decode($result,true);
        }
        echo $result;
        die();
    }
}