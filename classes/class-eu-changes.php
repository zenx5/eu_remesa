<?php

require_once 'class-eu-ajax-suport.php';

class EuChanges {

    public static function get_changes($request){
        $body = json_decode($request->get_body(),true);
        if( true || EuAjaxData::is_token_valid( $body['token'] ) ) {
            return EuAjaxData::get_option_content('eu_remesa_change_currency', '[]', false);
        }
        return [];
    }

    public static function set_change($request){
        $body = json_decode($request->get_body(),true);
        $changes = EuAjaxData::get_option_content('eu_remesa_change_currency', '[]',false);
        $change = [
            "id" => $body['currency_from'].$body['currency_to'],
            "currency_from" => $body['currency_from'],
            "currency_to" => $body['currency_to'],
            "price" => $body['price'],
            "rules" => []
        ];
        
        $new_changes = [...$changes, $change];
        update_option('eu_remesa_change_currency', json_encode( $new_changes) );
        return $new_changes;
    }

    public static function remove_change($request){
        $body = json_decode($request->get_body(),true);
        $changes = EuAjaxData::get_option_content('eu_remesa_change_currency', '[]',false);
        $new_changes = [];
        foreach($changes as $change ) {
            if( $body['id']!=$change['id'] ) {
                $new_changes[] = $change;
            }
        }
        update_option('eu_remesa_change_currency', json_encode( $new_changes) );
        return $new_changes;
    }

    public static function add_rule($request){
        $new_changes = [];
        $body = json_decode($request->get_body(),true);
        $changes = EuAjaxData::get_option_content('eu_remesa_change_currency', '[]',false);
        foreach( $changes as $change ){
            if( $change['id'] === $body['id'] ) {
                $new_changes[] = $change;
                $index = count($new_changes);
                $new_changes[$index - 1]["rules"][] = [
                    "relation" => $body["relation"],
                    "deposit" => $body["deposit"],
                    "value_format" => $body["value_format"],
                    "value" => $body["value"]
                ];
            }else{
                $new_changes[] = $change;
            }
        }
        update_option('eu_remesa_change_currency', json_encode($new_changes));
        return $new_changes;
    }

    public static function remove_rule($request){
        $new_changes = [];
        $body = json_decode($request->get_body(),true);
        $changes = EuAjaxData::get_option_content('eu_remesa_change_currency', '[]',false);
        foreach( $changes as $change ){
            if( $change['id'] === $body['id'] ) {
                $new_rules = [];
                $index = 0;
                foreach( $change["rules"] as $rule ) {
                    if( $index != $body['index'] ) {
                        $new_rules[] = $rule;
                    }
                    $index++;
                }
                $new_changes[] = [
                    "id" => $change['id'],
                    "currency_from" => $change['currency_from'],
                    "currency_to" => $change['currency_to'],
                    "price" => $change['price'],
                    "rules" => $new_rules
                ];
            }else{
                $new_changes[] = $change;
            }
        }
        update_option('eu_remesa_change_currency', json_encode($new_changes));
        return $new_changes;
    }
}