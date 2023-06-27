<?php

class EuAjaxData{

    public static $defualt_timeout = 2;

    public static function is_token_valid($request_token){
        $token = new EuJWT($request_token);
        $data = $token->getData($request_token);
        $diff = date_diff( $data->checkpoint, date("h:i:sa") );
        return floatval($diff->i) < self::$defualt_timeout;
    }

    public static function get_option_content($tag, $default_value, $is_echo = true){
        $result = get_option($tag, $default_value);
        if( !$is_echo ) {
            return json_decode($result,true);
        }
        echo $result;
        die();
    }
}