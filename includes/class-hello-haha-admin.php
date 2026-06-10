<?php
/**
 * Admin settings page and Settings API integration.
 *
 * @package Hello_Haha
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin functionality.
 */
final class Hello_Haha_Admin {

	/**
	 * Settings page slug.
	 */
	public const PAGE_SLUG = 'plugin-haha';

	/**
	 * Settings group identifier.
	 */
	public const SETTINGS_GROUP = 'hello_haha_settings';

	/**
	 * Plugin singleton instance.
	 *
	 * @var Hello_Haha_Admin|null
	 */
	private static ?Hello_Haha_Admin $instance = null;

	/**
	 * Returns the singleton instance.
	 *
	 * @return Hello_Haha_Admin
	 */
	public static function instance(): Hello_Haha_Admin {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Registers admin hooks.
	 *
	 * @return void
	 */
	public function init(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_settings_assets' ) );
		add_filter( 'plugin_action_links_' . HELLO_HAHA_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
	}

	/**
	 * Adds the settings page under Settings.
	 *
	 * @return void
	 */
	public function register_menu(): void {
		add_options_page(
			__( '你好，蛤蛤！', 'hello-haha' ),
			__( '你好，蛤蛤', 'hello-haha' ),
			'manage_options',
			self::PAGE_SLUG,
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Registers plugin options with the Settings API.
	 *
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			self::SETTINGS_GROUP,
			Hello_Haha::OPTION_SIZE,
			array(
				'type'              => 'integer',
				'sanitize_callback' => array( $this, 'sanitize_size' ),
				'default'           => Hello_Haha::DEFAULT_SIZE,
			)
		);

		register_setting(
			self::SETTINGS_GROUP,
			Hello_Haha::OPTION_FRONT,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_front' ),
				'default'           => 'false',
			)
		);

		register_setting(
			self::SETTINGS_GROUP,
			Hello_Haha::OPTION_TEXT,
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_text' ),
				'default'           => '',
			)
		);

		add_settings_section(
			'hello_haha_general',
			__( '基本设置', 'hello-haha' ),
			array( $this, 'render_general_section' ),
			self::PAGE_SLUG
		);

		add_settings_field(
			'hello_haha_size',
			__( '字体大小', 'hello-haha' ),
			array( $this, 'render_size_field' ),
			self::PAGE_SLUG,
			'hello_haha_general',
			array(
				'label_for' => 'hello_haha_size',
			)
		);

		add_settings_field(
			'hello_haha_front',
			__( '前台显示', 'hello-haha' ),
			array( $this, 'render_front_field' ),
			self::PAGE_SLUG,
			'hello_haha_general',
			array(
				'label_for' => 'hello_haha_front',
			)
		);

		add_settings_field(
			'hello_haha_text',
			__( '语录列表', 'hello-haha' ),
			array( $this, 'render_text_field' ),
			self::PAGE_SLUG,
			'hello_haha_general',
			array(
				'label_for' => 'hello_haha_text',
			)
		);
	}

	/**
	 * Sanitizes the font size option.
	 *
	 * @param mixed $value Raw option value.
	 * @return int
	 */
	public function sanitize_size( $value ): int {
		$size = absint( $value );

		if ( $size < 8 ) {
			return 8;
		}

		if ( $size > 72 ) {
			return 72;
		}

		return $size;
	}

	/**
	 * Sanitizes the frontend toggle option.
	 *
	 * @param mixed $value Raw option value.
	 * @return string
	 */
	public function sanitize_front( $value ): string {
		return 'true' === $value ? 'true' : 'false';
	}

	/**
	 * Sanitizes the quote list option.
	 *
	 * @param mixed $value Raw option value.
	 * @return string
	 */
	public function sanitize_text( $value ): string {
		return sanitize_textarea_field( (string) $value );
	}

	/**
	 * Adds a settings link on the plugins screen.
	 *
	 * @param string[] $links Existing action links.
	 * @return string[]
	 */
	public function add_settings_link( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'options-general.php?page=' . self::PAGE_SLUG ) ),
			esc_html__( '设置', 'hello-haha' )
		);

		array_unshift( $links, $settings_link );

		return $links;
	}

	/**
	 * Renders the general settings section description.
	 *
	 * @return void
	 */
	public function render_general_section(): void {
		echo '<p>' . esc_html__( '在后台每个页面显示一句来自长者的经典语录。', 'hello-haha' ) . '</p>';
	}

	/**
	 * Renders the font size field.
	 *
	 * @return void
	 */
	public function render_size_field(): void {
		$value = Hello_Haha::instance()->get_font_size();

		printf(
			'<input type="number" class="small-text" id="hello_haha_size" name="%1$s" value="%2$d" min="8" max="72" step="1" /> px',
			esc_attr( Hello_Haha::OPTION_SIZE ),
			esc_attr( $value )
		);
		echo '<p class="description">' . esc_html__( '后台语录显示的字体大小。', 'hello-haha' ) . '</p>';
	}

	/**
	 * Renders the frontend toggle field.
	 *
	 * @return void
	 */
	public function render_front_field(): void {
		$value = get_option( Hello_Haha::OPTION_FRONT, 'false' );

		printf(
			'<select id="hello_haha_front" name="%1$s">',
			esc_attr( Hello_Haha::OPTION_FRONT )
		);

		printf(
			'<option value="true"%1$s>%2$s</option>',
			selected( $value, 'true', false ),
			esc_html__( '吼啊', 'hello-haha' )
		);

		printf(
			'<option value="false"%1$s>%2$s</option>',
			selected( $value, 'false', false ),
			esc_html__( '不吼', 'hello-haha' )
		);

		echo '</select>';
		echo '<p class="description">';
		printf(
			/* translators: %s: HTML code snippet for theme integration. */
			esc_html__( '开启后，在需要显示语录的位置插入 %s。', 'hello-haha' ),
			'<code>&lt;p class="haha"&gt;&lt;/p&gt;</code>'
		);
		echo '</p>';
	}

	/**
	 * Renders the quote list field.
	 *
	 * @return void
	 */
	public function render_text_field(): void {
		$value = get_option( Hello_Haha::OPTION_TEXT, '' );

		if ( '' === $value ) {
			$value = hello_haha_get_default_lyrics();
		}

		printf(
			'<textarea class="large-text code" rows="20" id="hello_haha_text" name="%1$s">%2$s</textarea>',
			esc_attr( Hello_Haha::OPTION_TEXT ),
			esc_textarea( $value )
		);
		echo '<p class="description">' . esc_html__( '每行一条语录。留空则使用内置默认语录。', 'hello-haha' ) . '</p>';
	}

	/**
	 * Enqueues assets for the settings page.
	 *
	 * @param string $hook_suffix Current admin page hook suffix.
	 * @return void
	 */
	public function enqueue_settings_assets( string $hook_suffix ): void {
		if ( 'settings_page_' . self::PAGE_SLUG !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'hello-haha-admin-settings',
			HELLO_HAHA_PLUGIN_URL . 'assets/css/admin-settings.css',
			array(),
			HELLO_HAHA_VERSION
		);

		wp_enqueue_script(
			'hello-haha-admin-settings',
			HELLO_HAHA_PLUGIN_URL . 'assets/js/admin-settings.js',
			array(),
			HELLO_HAHA_VERSION,
			true
		);
	}

	/**
	 * Renders the settings page.
	 *
	 * @return void
	 */
	public function render_settings_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( '你们呀，不要想喜欢弄大新闻，说现在已经钦定了，就把我批判一番。', 'hello-haha' ) );
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<div class="hello-haha-settings-layout">
				<div class="hello-haha-settings-main">
					<form action="options.php" method="post" class="hello-haha-settings-form">
						<?php
						settings_fields( self::SETTINGS_GROUP );
						do_settings_sections( self::PAGE_SLUG );
						submit_button( __( '中央已经决定了', 'hello-haha' ) );
						?>
					</form>
				</div>

				<div class="hello-haha-settings-sidebar">
					<div class="card hello-haha-sidebar-card">
						<div class="hello-haha-sidebar-about">
							<h2 class="title"><?php esc_html_e( '插件说明', 'hello-haha' ); ?></h2>
							<p><?php esc_html_e( '这不是一个普通的插件，它象征着一代人的膜蛤精神，以及大家对长者的热情，它会在前台或者后台每个页面显示一句来自长者的经典语录，不但能够让访客变得 Excited，还能让你的博客变得更快。', 'hello-haha' ); ?></p>
							<p>
								<?php esc_html_e( '真正的粉丝，即使是一个简单的 WordPress 插件，也会感到自己的时间过得很快。', 'hello-haha' ); ?>
								+<span id="hello-haha-timer">1s</span>
							</p>
							<p><?php esc_html_e( '如果您钦定了 WP Super Cache，可能会导致前台的语录被缓存，很江硬，不会随机显示，建议关闭首页的缓存功能。', 'hello-haha' ); ?></p>
						</div>

						<hr class="hello-haha-sidebar-divider" />

						<div class="hello-haha-sidebar-meta">
							<p class="hello-haha-profile-image">
								<img src="<?php echo esc_url( 'https://i.zerodream.net/6d8ba085520d8db2ca186dbe9536cb99.jpg' ); ?>" alt="<?php esc_attr_e( '长者', 'hello-haha' ); ?>" width="140" height="140" />
							</p>
							<p><?php esc_html_e( '你看我泽个插件写的这么辛苦，资瓷一下吼不吼啊~', 'hello-haha' ); ?></p>
							<p>
								<a href="<?php echo esc_url( 'https://github.com/KasuganoSoras/wordpress-elder' ); ?>" target="_blank" rel="noopener noreferrer">
									https://github.com/KasuganoSoras/wordpress-elder
								</a>
							</p>
							<p class="description">
								<?php
								printf(
									/* translators: %s: plugin version. */
									esc_html__( 'Hello Haha v%s by KasuganoSora', 'hello-haha' ),
									esc_html( HELLO_HAHA_VERSION )
								);
								?>
							</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}
}
