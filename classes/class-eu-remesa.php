<?php 

require_once 'class-eu-changes.php';
require_once 'class-eu-operations.php';
require_once 'class-eu-app-template.php';
require_once 'class-eu-jwt.php';

class EuRemesa {
    // decrepeted
    public static $default_rate = '{"vef":"1","brl":"1","cop":"1","ars":"1"}';
    public static $default_found = '{"usd":"0","vef":"0","brl":"0","cop":"0","ars":"0"}';
    public static $defualt_timeout = 2;
    public static function active()
    {
        update_option('eu_remesa_change_currency','[]');
        update_option('eu_remesa_pending', '[]');
        update_option('eu_remesa_rates', self::$default_rate);
        update_option('eu_remesa_founds', self::$default_found);
    }

    public static function deactive()
    {
        
    }

    public static function init()
    {
        add_shortcode('remesa-form', array('EuAppTemplate', 'render_form'));
        add_action('admin_menu', ['EuRemesa','admin_menu']);
        add_action('wp_ajax_save_rate', ['EuRemesa','save_rate']);

        add_action('rest_api_init',['EuRemesa','api_rest']);
    }

    public static function api_rest(){
        $methods_changes = [
            'get_changes',
            'set_change',
            'remove_change',
            'add_rule',
            'remove_rule'
        ];
        $methods_operations = [
            'get_operations',
            'set_operation',
            'validate_operation',
            'remove_operation'
        ];
        $methods_others = [
            'save_found',
            'get_rate',
            'get_found',
            'get_found_of',
            'send_confirm',
            'get_confirms',
            'reset_all',
            'get_currencies',
            'set_currency',
            'remove_currency',
        ];
        register_rest_route("remesa/v1", 'status', array(
            'methods' => 'get',
            'callback' => function(){
                return [
                    "status" => "Running"
                ];
            }
        ));
        foreach( $methods_changes as $method) {
            register_rest_route("remesa/v1", $method, array(
                'methods' => 'post',
                'callback' => ['EuChanges', $method]
            ));
        }
        foreach( $methods_operations as $method) {
            register_rest_route("remesa/v1", $method, array(
                'methods' => 'get, post',
                'callback' => ['EuOperations', $method]
            ));
        }
        
        foreach( $methods_others as $method) {
            register_rest_route("remesa/v1", $method, array(
                'methods' => 'post',
                'callback' => ['EuRemesa', $method]
            ));
        }

    }

    public static function get_currencies($request) {
        $body = json_decode($request->get_body(),true);
        if( true  ) { // self::is_token_valid( $body['token'] )
            return self::get_option_content('eu_remesa_currencies', '[]', false);
        }
        return [];
    }

    public static function set_currency($request) {
        $updated = false;
        $body = json_decode($request->get_body(),true);
        $currencies = EuAjaxData::get_option_content('eu_remesa_currencies', '[]',false);
        $new_currencies = [];
        foreach( $currencies as $item ) {
            if( $body["name"]===$item["name"] ) {
                $updated = true;
                $new_currencies[] = [
                    "name"      => $body["name"],
                    "founds"    => $body["founds"],
                    "symbol"    => $body["symbol"]
                ];
            } else {
                $new_currencies[] = $item;
            }
        }
        if( !$updated ) {
            $new_currencies[] = [
                "name"      => $body["name"],
                "founds"    => $body["founds"],
                "symbol"    => $body["symbol"]
            ];
        }
        update_option('eu_remesa_currencies', json_encode( $new_currencies ) );
        return $new_currencies;
    }

    public static function remove_currency($request) {
        $body = json_decode($request->get_body(),true);
        $currencies = EuAjaxData::get_option_content('eu_remesa_currencies', '[]',false);
        $new_currencies = [];
        foreach($currencies as $currency ) {
            if( $body['name']!=$currency['name'] ) {
                $new_currencies[] = $currency;
            }
        }
        update_option('eu_remesa_currencies', json_encode( $new_currencies) );
        return $new_currencies;
    }


    public static function create_token(){
        $jwt = new EuJWT('eu-remesa', [
            "checkpoint" => date("h:i:sa")
        ]);
        return $jwt->getToken();
    }

    public static function reset_all(){
        update_option('eu_remesa_pending', '[]');
        update_option('eu_remesa_confirms', '[]');
        update_option('eu_remesa_rates', self::$default_rate);
        update_option('eu_remesa_founds', self::$default_found);
        echo 1;
        die();
    }

    private static function is_token_valid($request_token){
        return true;
        $token = new EuJWT($request_token);
        $data = $token->getData($request_token);
        $diff = date_diff( $data->checkpoint, date("h:i:sa") );
        return floatval($diff->i) < self::$defualt_timeout;
    }

    private static function get_option_content($tag, $default_value, $is_echo = true){
        $result = get_option($tag, $default_value);
        if( !$is_echo ) {
            return json_decode($result,true);
        }
        echo $result;
        die();
    }

    public static function get_rate($request){
        $body = json_decode($request->get_body(),true);
        if( self::is_token_valid( $body['token'] ) ) {
            return self::get_option_content('eu_remesa_rates', self::$default_rate, false);
        }
        return self::$default_rate;
    }

    public static function get_found(){
        $body = json_decode($request->get_body(),true);
        if( self::is_token_valid( $body['token'] ) ) {
            return self::get_option_content('eu_remesa_founds', self::$default_found, false);
        }
        return self::$default_found;
    }

    public static function get_confirms(){
        $body = json_decode($request->get_body(),true);
        if( self::is_token_valid( $body['token'] ) ) {
            return self::get_option_content('eu_remesa_confirms', '[]', false);
        }
        return [];
    }

    public static function get_pendings(){
        $body = json_decode($request->get_body(),true);
        if( self::is_token_valid( $body['token'] ) ) {
            return self::get_option_content('eu_remesa_pending', '[]', false);
        }
        return [];
    }

    public static function send_confirm($request){
        $body = json_decode($request->get_body(),true);
        if( isset($body['currency_from']) && isset($body['mount_from']) && isset($body['reference'])  ){
            $confirm = [
                "currency_from" => $body['currency_from'],
                "mount_from" => $body['mount_from'],
                "currency_to" => $body['currency_to'],
                "mount_to" => $body['mount_to'],
                "reference" => $body['reference']
            ];
            $confirms = json_decode( get_option('eu_remesa_confirms','[]'), true );
            update_option('eu_remesa_confirms', json_encode([...$confirms, $confirm]) );
        }
        return [
            "message" => "Esperando confirmacion",
            "code" => 0
        ];
    }

    public static function get_found_of($request){
        $body = json_decode($request->get_body(),true);
        $mount      = floatval( $body['mount'] );
        $currency   = $body['currency'];
        $founds     = json_decode( get_option('eu_remesa_founds', EuRemesa::$default_found), true );

        $pendings = json_decode( get_option('eu_remesa_pending','[]'), true );
        $mount_pending = 0;
        foreach( $pendings as $pending){
            $mount_pending += $pending['mount'];
        }
        
        $mount_avalaible = floatval($founds[$currency]) - $mount_pending;
        $new_pending = [];

        if( $mount_avalaible>= $mount ) {
            $new_pending = [
                "id" => count($pendings),
                "currency" => $currency,
                "mount" => $mount
            ];
            update_option('eu_remesa_pending', json_encode([...$pendings, $new_pending]) );
        } 
        return [
            "currency" => $currency,
            "founds" => $mount_avalaible,
            "avalaible" => $mount_avalaible >= $mount,
            "pending" => $new_pending
        ];
    }

    public static function save_rate($request){
        $body = json_decode($request->get_body(),true);
        $oldrates = json_decode( get_option('eu_remesa_rates', EuRemesa::$default_rate) );
        $rates = [
            "vef" => isset($body['vef']) ? $body['vef'] : $oldrates->vef,
            "brl" => isset($body['brl']) ? $body['brl'] : $oldrates->brl,
            "cop" => isset($body['cop']) ? $body['cop'] : $oldrates->cop,
            "ars" => isset($body['ars']) ? $body['ars'] : $oldrates->ars
        ];
        update_option('eu_remesa_rates', json_encode($rates) );
        return $rates;
    }

    public static function save_found($request){
        $body = json_decode($request->get_body(),true);
        $oldfounds = json_decode( get_option('eu_remesa_founds', EuRemesa::$default_found) );
        $founds = [
            "usd" => isset($body['usd']) ? $body['usd'] : $oldfounds->usd,
            "vef" => isset($body['vef']) ? $body['vef'] : $oldfounds->vef,
            "brl" => isset($body['brl']) ? $body['brl'] : $oldfounds->brl,
            "cop" => isset($body['cop']) ? $body['cop'] : $oldfounds->cop,
            "ars" => isset($body['ars']) ? $body['ars'] : $oldfounds->ars
        ];
        update_option('eu_remesa_founds', json_encode($founds) );
        return $founds;
    }

    public static function admin_menu()
    {
        add_menu_page(
            "Remesa",
            "Remesa",
            "manage_options",
            "menu-remesa",
            function(){
                ?>
                    <iframe width="100%" height="800px" src="<?=get_site_url().'/wp-content/plugins/eu-remesa/app.php?template=admin_menu&type=php&script=base,root,admin&token='.self::create_token()?>"></iframe>
                <?php 
            },
            "",
            6
        );
    }
    

}