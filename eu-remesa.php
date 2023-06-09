<?php
/**
 * Plugin Name:       eu-remesa
 * Plugin URI:        https://github.com/zenx5
 * Description:       Eu Remesa
 * Author:            Octavio Martinez
 * Author URI:        https://github.com/zenx5
 * Version:           1.0.0
 */

require_once('classes/class-eu-remesa.php');

register_activation_hook(__FILE__, array('EuRemesa', 'active'));
register_deactivation_hook(__FILE__, array('EuRemesa', 'deactive'));

add_action('init', array('EuRemesa','init'));