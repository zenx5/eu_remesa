<?php

class EuAppTemplate {
    public static $default_rate = '{"vef":"1","brl":"1","cop":"1","ars":"1"}';
    public static $default_found = '{"usd":"0","vef":"0","brl":"0","cop":"0","ars":"0"}';

    public static function get_template( $template )
    {
        return WP_PLUGIN_DIR.'/eu-remesa/templates/'.$template.'.php';
    }

    public static function admin_menu()
    {
        $pendings = json_decode( get_option('eu_remesa_pending', '[]' ), true );
        $confirms = json_decode( get_option('eu_remesa_confirms','[]'), true );
        $rates = get_option('eu_remesa_rates',EuAppTemplate::$default_rate);
        $founds = get_option('eu_remesa_founds', EuAppTemplate::$default_found);
        include self::get_template('admin_menu');
        include self::get_template('pending_list');
        
    }

    public static function render_form()
    {
        $rates = json_decode( get_option('eu_remesa_rates', EuAppTemplate::$default_rate ) );
        $founds = json_decode( get_option('eu_remesa_founds', EuAppTemplate::$default_found ) );
        ob_start();
        include self::get_template('render_form');
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    public static function pending_list()
    {
        $pendings = json_decode( get_option('eu_remesa_pending', '[]' ), true );
        include self::get_template('pending_list');
    }
}