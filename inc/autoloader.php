<?php
/**
 * Dynamically loads the class attempting to be instantiated elsewhere in the
 * plugin.
 *
 * @package WebSmarter\Inc
 */
namespace WebSmarter\Inc;

use WebSmarter\Includes\AutoLoader;

require dirname( dirname( __FILE__ ) ) . '/includes/auto-loader.php';

AutoLoader::init();