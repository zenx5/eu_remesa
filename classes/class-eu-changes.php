<?php

require_once 'class-eu-ajax-suport.php';

class EuChanges {

    public static function get_changes(){
        EuAjaxData::get_option_content('eu_remesa_change_currency', '[]');
    }

    public static function set_change(){
        EuAjaxData::get_option_content('eu_remesa_change_currency', '[]',false);
        $change = [
            "id" => $_POST['currency_from'].$_POST['currency_to'],
            "currency_from" => $_POST['currency_from'],
            "currency_to" => $_POST['currency_to'],
            "rules" => []
        ];
        $new_changes = json_encode([...$changes, $change]);
        update_option('eu_remesa_change_currency', $new_changes);
        echo $new_changes;
        die();
    }

    public static function add_rule(){
        $new_changes = [];
        $changes = EuAjaxData::get_option_content('eu_remesa_change_currency', '[]',false);
        foreach( $changes as $change ){
            if( $change['id'] === $_POST['id'] ) {
                $new_changes[] = $change;
                $index = count($new_changes);
                $new_changes[$index - 1]["rules"][] = [
                    "relation" => $_POST["relation"],
                    "deposit" => $_POST["deposit"],
                    "value_format" => $_POST["value_format"],
                    "value" => $_POST["value"]
                ];
            }else{
                $new_changes[] = $change;
            }
        }
        update_option('eu_remesa_change_currency', json_encode($new_changes));
        echo json_encode($new_changes);
        die();
    }

    public static function remove_rule(){
        $new_changes = [];
        $changes = EuAjaxData::get_option_content('eu_remesa_change_currency', '[]',false);
        foreach( $changes as $change ){
            if( $change['id'] === $_POST['id'] ) {
                $new_rules = [];
                $index = 0;
                foreach( $change["rules"] as $rule ) {
                    if( $index != $_POST['index'] ) {
                        $new_rules[] = $rule;
                    }
                    $index++;
                }
                $new_changes[] = [
                    "id" => $change['id'],
                    "currency_from" => $change['currency_from'],
                    "currency_to" => $change['currency_to'],
                    "rules" => $new_rules
                ];
            }else{
                $new_changes[] = $change;
            }
        }
        update_option('eu_remesa_change_currency', json_encode($new_changes));
        echo json_encode($new_changes);
        die();
    }
}