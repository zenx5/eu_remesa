<?php 

require_once 'class-eu-changes.php';
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
        
        add_action('wp_ajax_get_changes', ['EuChanges','get_changes']);
        add_action('wp_ajax_set_change', ['EuChanges','set_change']);
        add_action('wp_ajax_add_rule', ['EuChanges','add_rule']);
        add_action('wp_ajax_remove_rule', ['EuChanges','remove_rule']);

        add_action('wp_ajax_save_found', ['EuRemesa','save_found']);
        add_action('wp_ajax_get_rate', ['EuRemesa','get_rate']);
        add_action('wp_ajax_get_found', ['EuRemesa','get_found']);
        add_action('wp_ajax_get_found_of', ['EuRemesa','get_found_of']);
        add_action('wp_ajax_send_confirm', ['EuRemesa','send_confirm']);
        add_action('wp_ajax_get_confirms', ['EuRemesa','get_confirms']);
        add_action('wp_ajax_reset_all', ['EuRemesa','reset_all']);
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

    private static function is_token_valid(){
        $token = new EuJWT($_POST['token']);
        $data = $token->getData($_POST['token']);
        $diff = date_diff( $data->checkpoint, date("h:i:sa") );
        return floatval($diff->i) < self::$defualt_timeout;
    }

    private static function get_option_content($tag, $default_value, $is_echo = true){
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

    public static function get_rate(){
        self::get_option_content('eu_remesa_rates', self::$default_rate);
    }

    public static function get_found(){
        self::get_option_content('eu_remesa_founds', self::$default_found);
    }

    public static function get_confirms(){
        self::get_option_content('eu_remesa_confirms', '[]');
    }

    public static function get_pendings(){
        self::get_option_content('eu_remesa_pending', '[]');
    }

    public static function send_confirm(){
        if( isset($_POST['currency_from']) && isset($_POST['mount_from']) && isset($_POST['reference'])  ){
            $confirm = [
                "currency_from" => $_POST['currency_from'],
                "mount_from" => $_POST['mount_from'],
                "currency_to" => $_POST['currency_to'],
                "mount_to" => $_POST['mount_to'],
                "reference" => $_POST['reference']
            ];
            $confirms = json_decode( get_option('eu_remesa_confirms','[]'), true );
            update_option('eu_remesa_confirms', json_encode([...$confirms, $confirm]) );
        }
        echo json_encode([
            "message" => "Esperando confirmacion",
            "code" => 0
        ]);
        die();        
    }

    public static function get_found_of(){
        $mount      = floatval( $_POST['mount'] );
        $currency   = $_POST['currency'];
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
        echo json_encode([
            "currency" => $currency,
            "founds" => $mount_avalaible,
            "avalaible" => $mount_avalaible >= $mount,
            "pending" => $new_pending
        ]);
        die();
    }

    public static function save_rate(){
        // $_POST['token']
        $oldrates = json_decode( get_option('eu_remesa_rates', EuRemesa::$default_rate) );
        $rates = [
            "vef" => isset($_POST['vef']) ? $_POST['vef'] : $oldrates->vef,
            "brl" => isset($_POST['brl']) ? $_POST['brl'] : $oldrates->brl,
            "cop" => isset($_POST['cop']) ? $_POST['cop'] : $oldrates->cop,
            "ars" => isset($_POST['ars']) ? $_POST['ars'] : $oldrates->ars
        ];
        update_option('eu_remesa_rates', json_encode($rates) );
        echo json_encode($rates);
        die();
    }

    public static function save_found(){
        $oldfounds = json_decode( get_option('eu_remesa_founds', EuRemesa::$default_found) );
        $founds = [
            "usd" => isset($_POST['usd']) ? $_POST['usd'] : $oldfounds->usd,
            "vef" => isset($_POST['vef']) ? $_POST['vef'] : $oldfounds->vef,
            "brl" => isset($_POST['brl']) ? $_POST['brl'] : $oldfounds->brl,
            "cop" => isset($_POST['cop']) ? $_POST['cop'] : $oldfounds->cop,
            "ars" => isset($_POST['ars']) ? $_POST['ars'] : $oldfounds->ars
        ];
        update_option('eu_remesa_founds', json_encode($founds) );
        echo json_encode($founds);
        die();
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