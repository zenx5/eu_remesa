<?php 

require_once 'class-eu-ajax-suport.php';

class EuOperations {

    public static function get_operations($request){
        $body = json_decode($request->get_body(),true);
        if( true || EuAjaxData::is_token_valid( $body['token'] ) ) {
            return EuAjaxData::get_option_content('eu_remesa_operation', '[]', false);
        }
        return [];
    }

    public static function validate_operation($request){
     
        $new_operations = [];
        $body = json_decode($request->get_body(),true);
        $operations = EuAjaxData::get_option_content('eu_remesa_operation', '[]',false);
        foreach( $operations as $_operation ) {
            if( $_operation['id']==$body['id'] ) {
                EuOperations::currency_mount_change($_operation["currency_from"], floatval($_operation['mount']));
                EuOperations::currency_mount_change($_operation["currency_to"], floatval($_operation['mount']) * floatval($_operation['price']), -1 );
                $_operation['verify'] = true;
            }
            $new_operations[] = $_operation;
        }
     
        update_option('eu_remesa_operation', json_encode( $new_operations) );
        return $new_operations;
    }

    public static function currency_mount_change($currency_name, $mount, $gain = 1) {
        $new_currencies = [];
        $currencies = EuAjaxData::get_option_content('eu_remesa_currencies', '[]',false);
        foreach( $currencies as $currency ) {
            if( $currency["name"]==$currency_name ) {
                $new_currencies[] = [
                    "name" => $currency["name"],
                    "symbol" => $currency["symbol"],
                    "founds" => floatval( $currency["founds"] ) + $gain*$mount
                ];
            } else {
                $new_currencies[] = $currency;
            }
        }
        update_option('eu_remesa_currencies', json_encode( $new_currencies ) );
    }

    public static function set_operation($request){
        $update = false;
        $new_operations = [];
        $body = json_decode($request->get_body(),true);
        $operations = EuAjaxData::get_option_content('eu_remesa_operation', '[]',false);
        foreach( $operations as $_operation ) {
            if( $_operation['id']==$body['id'] ) {
                $update = true;
                $new_operations[] = [
                    "id" => $_operation['id'],
                    "currency_from" => isset($body['currency_from']) ? $body['currency_from'] : $_operation['currency_from'],
                    "currency_to" => isset($body['currency_to']) ? $body['currency_to'] : $_operation['currency_to'],
                    "price" => isset($body['price']) ? $body['price'] : $_operation['price'],
                    "rules" => isset($body['rules']) ? $body['rules'] : $_operation['rules'],
                    "verify" => isset($body['verify']) ? $body['verify'] : $_operation['verify'],
                    "mount" => isset($body['mount']) ? $body['mount'] : $_operation['mount']
                ];
            } else {
                $new_operations[] = $_operation;
            }
        }
        if( !$update ) {
            $operation = [
                "id" => $body['id'],
                "currency_from" => $body['currency_from'],
                "currency_to" => $body['currency_to'],
                "price" => $body['price'],
                "rules" => $body['rules'],
                "verify" => $body['verify'],
                "mount" => $body['mount'],
            ];
            $new_operations = [...$operations, $operation];
        }
        update_option('eu_remesa_operation', json_encode( $new_operations) );
        return $new_operations;
    }

    public static function remove_operation($request){
        $body = json_decode($request->get_body(),true);
        $operations = EuAjaxData::get_option_content('eu_remesa_operation', '[]',false);
        $new_operations = [];
        foreach($operations as $operation ) {
            if( $body['id']!=$operation['id'] ) {
                $new_operations[] = $operation;
            }
        }
        update_option('eu_remesa_operation', json_encode( $new_operations) );
        return $new_operations;
    }
    
}