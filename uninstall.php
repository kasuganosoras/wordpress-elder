<?php
/**
 * Uninstall routine.
 *
 * @package Hello_Haha
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_option( 'plugin_haha_size' );
delete_option( 'plugin_haha_text' );
delete_option( 'plugin_haha_front' );
