<?php
/**
 * Plugin Name: Cision Block
 * Description: Imports a news feed from cision.
 * Version: 1.5.2
 * Author: Cyclonecode
 * Author URI: https://stackoverflow.com/users/1047662/cyclonecode?tab=profile
 * Copyright: Cyclonecode
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: cision-block
 * Domain Path: /languages
 *
 * @package Cision Block
 * @author Cyclonecode
 */
namespace CisionBlock;

/**
 * Exit if accessed directly.
 */
if (!defined('ABSPATH')) {
    exit;
}

define('CISION_BLOCK_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CISION_BLOCK_PLUGIN_ADMIN_DIR', CISION_BLOCK_PLUGIN_DIR . '/admin');
define('CISION_BLOCK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CISION_BLOCK_PLUGIN_FILE', __FILE__);

require_once CISION_BLOCK_PLUGIN_DIR . '/public/cision-block.php';

add_action('plugins_loaded', function() {
    if (is_admin()) {
        CisionBlockAdmin::getInstance()->initialize();
    }
    else {
        CisionBlock::getInstance()->initialize();
    }
});

register_activation_hook(__FILE__, array('CisionBlock\CisionBlockAdmin', 'activate'));
register_uninstall_hook(__FILE__, array('CisionBlock\CisionBlock', 'delete'));
