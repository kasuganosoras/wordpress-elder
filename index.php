<?php
/**
 * Plugin Name:       你好，蛤蛤！
 * Plugin URI:        https://blog.kasuganosora.cn/
 * Description:       这不是一个普通的插件，它象征着一代人的膜蛤精神，以及大家对长者的热情，它会在前台或者后台每个页面显示一句来自长者的经典语录，不但能够让访客变得Excited，还能让你的博客变得更快。
 * Version:           2.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            KasuganoSora
 * Author URI:        https://blog.kasuganosora.cn/
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       hello-haha
 *
 * @package Hello_Haha
 */

defined( 'ABSPATH' ) || exit;

define( 'HELLO_HAHA_VERSION', '2.0.0' );
define( 'HELLO_HAHA_PLUGIN_FILE', __FILE__ );
define( 'HELLO_HAHA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HELLO_HAHA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HELLO_HAHA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

require_once HELLO_HAHA_PLUGIN_DIR . 'includes/class-hello-haha.php';

Hello_Haha::instance()->init();
