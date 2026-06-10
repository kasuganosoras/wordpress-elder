<?php
/**
 * Core plugin functionality.
 *
 * @package Hello_Haha
 */

defined( 'ABSPATH' ) || exit;

/**
 * Main plugin class.
 */
final class Hello_Haha {

	/**
	 * Plugin singleton instance.
	 *
	 * @var Hello_Haha|null
	 */
	private static ?Hello_Haha $instance = null;

	/**
	 * Option key for font size.
	 */
	public const OPTION_SIZE = 'plugin_haha_size';

	/**
	 * Option key for custom quote list.
	 */
	public const OPTION_TEXT = 'plugin_haha_text';

	/**
	 * Option key for frontend display toggle.
	 */
	public const OPTION_FRONT = 'plugin_haha_front';

	/**
	 * Default font size in pixels.
	 */
	public const DEFAULT_SIZE = 12;

	/**
	 * Returns the singleton instance.
	 *
	 * @return Hello_Haha
	 */
	public static function instance(): Hello_Haha {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		require_once HELLO_HAHA_PLUGIN_DIR . 'includes/default-lyrics.php';
		require_once HELLO_HAHA_PLUGIN_DIR . 'includes/class-hello-haha-admin.php';

		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		Hello_Haha_Admin::instance()->init();

		add_action( 'admin_notices', array( $this, 'render_admin_notice' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		if ( 'true' === get_option( self::OPTION_FRONT, 'false' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_assets' ) );
		}
	}

	/**
	 * Loads plugin translations.
	 *
	 * @return void
	 */
	public function load_textdomain(): void {
		load_plugin_textdomain(
			'hello-haha',
			false,
			dirname( HELLO_HAHA_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Returns the configured quote list as an array of lines.
	 *
	 * @return string[]
	 */
	public function get_quote_lines(): array {
		$text = get_option( self::OPTION_TEXT, '' );

		if ( '' === $text ) {
			$text = hello_haha_get_default_lyrics();
		}

		$lines = preg_split( '/\r\n|\r|\n/', $text );
		$lines = array_values(
			array_filter(
				array_map( 'trim', $lines ),
				static function ( string $line ): bool {
					return '' !== $line;
				}
			)
		);

		return $lines;
	}

	/**
	 * Returns a random quote.
	 *
	 * @return string
	 */
	public function get_random_quote(): string {
		$lines = $this->get_quote_lines();

		if ( empty( $lines ) ) {
			return '';
		}

		$index = wp_rand( 0, count( $lines ) - 1 );

		return wptexturize( $lines[ $index ] );
	}

	/**
	 * Returns the configured font size.
	 *
	 * @return int
	 */
	public function get_font_size(): int {
		$size = absint( get_option( self::OPTION_SIZE, self::DEFAULT_SIZE ) );

		if ( $size < 8 ) {
			return 8;
		}

		if ( $size > 72 ) {
			return 72;
		}

		return $size;
	}

	/**
	 * Outputs the admin notice quote.
	 *
	 * @return void
	 */
	public function render_admin_notice(): void {
		$quote = $this->get_random_quote();

		if ( '' === $quote ) {
			return;
		}

		printf(
			'<p id="haha">%s</p>',
			esc_html( $quote )
		);
	}

	/**
	 * Enqueues admin styles.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_admin_assets( string $hook_suffix ): void {
		wp_enqueue_style(
			'hello-haha-admin-notice',
			HELLO_HAHA_PLUGIN_URL . 'assets/css/admin-notice.css',
			array(),
			HELLO_HAHA_VERSION
		);

		wp_add_inline_style(
			'hello-haha-admin-notice',
			sprintf(
				'#haha { font-size: %dpx; }',
				$this->get_font_size()
			)
		);
	}

	/**
	 * Enqueues frontend script with a random quote.
	 *
	 * @return void
	 */
	public function enqueue_front_assets(): void {
		$quote = $this->get_random_quote();

		if ( '' === $quote ) {
			return;
		}

		wp_enqueue_script(
			'hello-haha-front',
			HELLO_HAHA_PLUGIN_URL . 'assets/js/front-quote.js',
			array(),
			HELLO_HAHA_VERSION,
			true
		);

		wp_localize_script(
			'hello-haha-front',
			'helloHaha',
			array(
				'quote' => $quote,
			)
		);
	}
}
