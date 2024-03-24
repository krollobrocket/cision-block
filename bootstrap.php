<?php

/**
 * Plugin Name: Cision Block
 * Description: Imports a news feed from Cision.
 * Version: 2.9.5
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

require_once __DIR__ . '/vendor/autoload.php';

use CisionBlock\Backend\Backend;
use CisionBlock\Frontend\Frontend;

add_action('plugins_loaded', function () {
    if (is_admin()) {
        $GLOBALS['cb_backend'] = Backend::getInstance();
    } else {
        $GLOBALS['cb_frontend'] = Frontend::getInstance();
    }
});

register_activation_hook(__FILE__, array('CisionBlock\Backend\Backend', 'activate'));
register_uninstall_hook(__FILE__, array('CisionBlock\Frontend\Frontend', 'delete'));
