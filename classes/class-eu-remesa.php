<?php 

require_once 'class-eu-app-template.php';

class EuRemesa {
    public static $default_rate = '{"vef":"1","brl":"1","cop":"1","ars":"1"}';
    public static $default_found = '{"usd":"0","vef":"0","brl":"0","cop":"0","ars":"0"}';
    public static function active()
    {
        update_option('eu_remesa_pending', '[]');
        //add_action('init', array('WP_Subsk', 'create_subs_type'));
        //add_action('admin_head', array('WP_Subsk', 'insert_uicomponents'));
        //add_action('add_meta_boxes_subs_types', array('WP_Subsk', 'create_metas'));
    }

    public static function deactive()
    {
    }

    public static function init()
    {
        add_shortcode('remesa-form', array('EuAppTemplate', 'render_form'));
        add_action('admin_menu', ['EuRemesa','admin_menu']);
        add_action('wp_ajax_save_rate', ['EuRemesa','save_rate']);
        add_action('wp_ajax_save_found', ['EuRemesa','save_found']);
        add_action('wp_ajax_get_founds', ['EuRemesa','get_founds']);
        add_action('wp_ajax_send_confirm', ['EuRemesa','send_confirm']);
    }

    public static function send_confirm(){
        if( isset($_POST['currency']) && isset($_POST['mount']) && isset($_POST['reference'])  ){
            $confirm = [
                "currency" => $_POST['currency'],
                "mount" => $_POST['mount'],
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

    public static function get_founds(){
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
                EuAppTemplate::admin_menu();
            },
            "",
            6
        );
    }

    

}