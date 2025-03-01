<?php
/**
 * Display notices in admin
 *
 * @package EverestForms/Admin
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * EVF_Admin_Notices Class.
 */
class EVF_Admin_Notices {

	/**
	 * Stores notices.
	 *
	 * @var array
	 */
	private static $notices = array();

	/**
	 * Array of notices - name => callback.
	 *
	 * @var array
	 */
	private static $core_notices = array(
		'update'          => 'update_notice',
		'review'          => 'review_notice',
		'survey'          => 'survey_notice',
		'allow_usage'     => 'allow_usage_notice',
		'php_deprecation' => 'php_deprecation_notice',
		'email_failed'    => 'email_failed_notice',
	);

	/**
	 * Constructor.
	 */
	public static function init() {
		self::$notices = get_option( 'everest_forms_admin_notices', array() );

		add_action( 'switch_theme', array( __CLASS__, 'reset_admin_notices' ) );
		add_action( 'everest_forms_installed', array( __CLASS__, 'reset_admin_notices' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'hide_notices' ) );
		add_action( 'shutdown', array( __CLASS__, 'store_notices' ) );

		if ( current_user_can( 'manage_everest_forms' ) ) {
			add_action( 'admin_print_styles', array( __CLASS__, 'add_notices' ) );
			add_action( 'in_admin_header', array( __CLASS__, 'hide_unrelated_notices' ) );
		}
	}

	/**
	 * Store notices to DB
	 */
	public static function store_notices() {
		update_option( 'everest_forms_admin_notices', self::get_notices() );
	}

	/**
	 * Get notices.
	 *
	 * @return array
	 */
	public static function get_notices() {
		return self::$notices;
	}

	/**
	 * Remove all notices.
	 */
	public static function remove_all_notices() {
		self::$notices = array();
	}

	/**
	 * Reset notices for themes when switched or a new version of EVF is installed.
	 */
	public static function reset_admin_notices() {
		if ( self::is_plugin_active( 'everest-forms-stripe/everest-forms-stripe.php' ) ) {
			self::add_notice( 'deprecated_payment_charge' );
		}
		self::add_notice( 'review' );
		self::add_notice( 'survey' );
		self::add_notice( 'allow_usage' );
		self::add_notice( 'php_deprecation' );
		self::add_notice( 'email_failed' );
	}

	/**
	 * Show a notice.
	 *
	 * @param string $name Notice name.
	 */
	public static function add_notice( $name ) {
		self::$notices = array_unique( array_merge( self::get_notices(), array( $name ) ) );
	}

	/**
	 * Remove a notice from being displayed.
	 *
	 * @param string $name Notice name.
	 */
	public static function remove_notice( $name ) {
		self::$notices = array_diff( self::get_notices(), array( $name ) );
		delete_option( 'everest_forms_admin_notice_' . $name );
	}

	/**
	 * See if a notice is being shown.
	 *
	 * @param  string $name Notice name.
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, self::get_notices(), true );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public static function hide_notices() {
		if ( isset( $_GET['evf-hide-notice'] ) && isset( $_GET['_evf_notice_nonce'] ) ) {
			if ( ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_evf_notice_nonce'] ) ), 'everest_forms_hide_notices_nonce' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'everest-forms' ) );
			}

			if ( ! current_user_can( 'manage_everest_forms' ) ) {
				wp_die( esc_html__( 'You don&#8217;t have permission to do this.', 'everest-forms' ) );
			}

			$hide_notice = sanitize_text_field( wp_unslash( $_GET['evf-hide-notice'] ) );

			self::remove_notice( $hide_notice );

			update_user_meta( get_current_user_id(), 'dismissed_' . $hide_notice . '_notice', true );

			do_action( 'everest_forms_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * Add notices + styles if needed.
	 */
	public static function add_notices() {
		$notices = self::get_notices();

		if ( empty( $notices ) ) {
			return;
		}

		$screen          = get_current_screen();
		$screen_id       = $screen ? $screen->id : '';
		$show_on_screens = array(
			'dashboard',
			'plugins',
		);

		// Notices should only show on Everest Forms screens, the main dashboard, and on the plugins screen.
		if ( ! in_array( $screen_id, evf_get_screen_ids(), true ) && ! in_array( $screen_id, $show_on_screens, true ) ) {
			return;
		}

		wp_enqueue_style( 'everest-forms-activation', plugins_url( '/assets/css/activation.css', EVF_PLUGIN_FILE ), array(), EVF_VERSION );

		// Add RTL support.
		wp_style_add_data( 'everest-forms-activation', 'rtl', 'replace' );

		foreach ( $notices as $notice ) {
			if ( ! empty( self::$core_notices[ $notice ] ) && apply_filters( 'everest_forms_show_admin_notice', true, $notice ) ) {
				add_action( 'admin_notices', array( __CLASS__, self::$core_notices[ $notice ] ) );
			} else {
				add_action( 'admin_notices', array( __CLASS__, 'output_custom_notices' ) );
			}
		}
	}

	/**
	 * Add a custom notice.
	 *
	 * @param string $name        Notice name.
	 * @param string $notice_html Notice html.
	 */
	public static function add_custom_notice( $name, $notice_html ) {
		self::add_notice( $name );
		update_option( 'everest_forms_admin_notice_' . $name, wp_kses_post( $notice_html ) );
	}

	/**
	 * Output any stored custom notices.
	 */
	public static function output_custom_notices() {
		$notices = self::get_notices();

		if ( ! empty( $notices ) ) {
			foreach ( $notices as $notice ) {
				if ( empty( self::$core_notices[ $notice ] ) ) {
					$notice_html = get_option( 'everest_forms_admin_notice_' . $notice );

					if ( $notice_html ) {
						include 'views/html-notice-custom.php';
					}
				}
			}
		}
	}

	/**
	 * If we need to update, include a message with the update button.
	 */
	public static function update_notice() {
		if ( EVF_Install::needs_db_update() ) {
			$updater = new EVF_Background_Updater();

			if ( $updater->is_updating() || ! empty( $_GET['do_update_everest_forms'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
				include 'views/html-notice-updating.php';
			} else {
				include 'views/html-notice-update.php';
			}
		} else {
			EVF_Install::update_db_version();
			include 'views/html-notice-updated.php';
		}
	}

	/**
	 * If we need reviews, include a message requesting review.
	 */
	public static function review_notice() {
		global $wpdb;

		// Check if another notice is showing.
		if ( self::survey_notice( true ) ) {
			return;
		}

		$load      = false;
		$time      = time();
		$review    = get_option( 'everest_forms_review' );
		$activated = get_option( 'everest_forms_activated' );

		// Verify for review.
		if ( ! $review ) {
			$review = array(
				'time'      => $time,
				'dismissed' => false,
			);
			update_option( 'everest_forms_review', $review );
		} else {
			// Check if it has been dismissed or not.
			if ( ( isset( $review['dismissed'] ) && ! $review['dismissed'] ) && ( isset( $review['time'] ) && ( ( $review['time'] + DAY_IN_SECONDS ) <= $time ) ) ) {
				$load = true;
			}
		}

		// Continue only if review request criteria meets.
		if ( $load && class_exists( 'EverestForms_Pro', false ) ) {
			$entries_count = $wpdb->get_var( "SELECT COUNT(entry_id) FROM {$wpdb->prefix}evf_entries WHERE `status` = 'publish'" );

			// Only continue if the site has collected at least 50 entries.
			if ( empty( $entries_count ) || $entries_count < 50 ) {
				return;
			}
		} else {
			// Only continue if plugin has been installed for at least 14 days.
			if ( ( $activated + ( WEEK_IN_SECONDS * 2 ) ) > $time ) {
				return;
			}
		}

		// Ask for some love.
		if ( $load && ( is_super_admin() || current_user_can( 'manage_everest_forms' ) ) ) {
			include 'views/html-notice-review.php';
		}
	}

	/**
	 * If we need survey, include a message requesting survey.
	 *
	 * @param boolean $status Twice notice to check.
	 * @return boolean
	 */
	public static function survey_notice( $status = false ) {

		$time        = time();
		$survey      = get_option( 'everest_forms_survey' );
		$activated   = get_option( 'everest_forms_activated' );
		$license_key = trim( get_option( 'everest-forms-pro_license_key' ) );

		if ( ! empty( $survey['dismissed'] ) ) {
			return;
		}

		// Only continue if plugin has been installed for at least 10 days.
		if ( ( $activated + ( DAY_IN_SECONDS * 10 ) ) > $time ) {
			return;
		}

		if ( ! $status && $license_key && ( is_super_admin() || current_user_can( 'manage_everest_forms' ) ) ) {
				include 'views/html-notice-survey.php';
		}

		return $status;

	}

	/**
	 * Include allow usage & discount notice.
	 */
	public static function allow_usage_notice() {

		$show_notice              = true;
		$allow_usage_notice_shown = get_option( 'everest_forms_allow_usage_notice_shown', false );
		$allow_usage_tracking     = get_option( 'everest_forms_allow_usage_tracking' );
		$activated                = get_option( 'everest_forms_activated' );

		if ( 'yes' === $allow_usage_tracking || ( $activated + DAY_IN_SECONDS > time() ) || $allow_usage_notice_shown ) {
			$show_notice = false;
		}

		if ( $show_notice && ( is_super_admin() || current_user_can( 'manage_everest_forms' ) ) ) {
			include 'views/html-notice-allow-usage.php';
		}
	}

	/**
	 * Include PHp deprecation Notice
	 */
	public static function php_deprecation_notice() {
		$php_version  = explode( '-', PHP_VERSION )[0];
		$base_version = '7.2';
		if ( version_compare( $php_version, $base_version, '<' ) ) {
			$last_prompt_date = get_option( 'everest_forms_php_deprecated_notice_last_prompt_date', '' );
			if ( empty( $last_prompt_date ) || strtotime( $last_prompt_date ) < strtotime( '-1 day' ) ) {
				$prompt_limit = 3;
				$prompt_count = get_option( 'everest_forms_php_deprecated_notice_prompt_count', 0 );

				if ( $prompt_count < $prompt_limit ) {
					include 'views/html-notice-php-deprecation.php';
				}
			}
		}
	}

	/**
	 * Email Failed  Notice
	 */
	public static function email_failed_notice() {
		$failed_data   = get_transient( 'everest_forms_mail_send_failed_count' );
		$failed_count  = isset( $failed_data['failed_count'] ) ? $failed_data['failed_count'] : 0;
		$error_message = isset( $failed_data['error_message'] ) ? $failed_data['error_message'] : '';
		$show_notice   = $failed_count > 5 ? true : false;
		if ( $show_notice ) {
			$is_dismissed = get_option( 'everest_forms_email_send_notice_dismiss', false );
		}
		if ( $show_notice && ! $is_dismissed ) {
			include 'views/html-notice-email-failed-notice.php';
		}
	}


	/**
	 * Remove non-EverestForms notices from EverestForms pages.
	 *
	 * @since 1.2.0
	 */
	public static function hide_unrelated_notices() {
		global $wp_filter;

		// Bail if we're not on a EverestForms screen or page.
		if ( empty( $_REQUEST['page'] ) || false === strpos( sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ), 'evf-' ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		foreach ( array( 'user_admin_notices', 'admin_notices', 'all_admin_notices' ) as $wp_notice ) {
			if ( ! empty( $wp_filter[ $wp_notice ]->callbacks ) && is_array( $wp_filter[ $wp_notice ]->callbacks ) ) {
				foreach ( $wp_filter[ $wp_notice ]->callbacks as $priority => $hooks ) {
					foreach ( $hooks as $name => $arr ) {
						if ( is_object( $arr['function'] ) && $arr['function'] instanceof Closure ) {
							unset( $wp_filter[ $wp_notice ]->callbacks[ $priority ][ $name ] );
							continue;
						}
						if ( ( isset( $_GET['tab'], $_GET['form_id'] ) || isset( $_GET['create-form'] ) ) && 'evf-builder' === $_REQUEST['page'] ) { // phpcs:ignore WordPress.Security.NonceVerification
							unset( $wp_filter[ $wp_notice ]->callbacks[ $priority ][ $name ] );
							continue;
						}
						if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && false !== strpos( strtolower( get_class( $arr['function'][0] ) ), 'evf_' ) ) {
							continue;
						}
						if ( ! empty( $name ) && false === strpos( strtolower( $name ), 'evf_' ) ) {
							unset( $wp_filter[ $wp_notice ]->callbacks[ $priority ][ $name ] );
						}
					}
				}
			}
		}
	}

	/**
	 * Wrapper for is_plugin_active.
	 *
	 * @param string $plugin Plugin to check.
	 * @return boolean
	 */
	protected static function is_plugin_active( $plugin ) {
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		return is_plugin_active( $plugin );
	}
}

EVF_Admin_Notices::init();
