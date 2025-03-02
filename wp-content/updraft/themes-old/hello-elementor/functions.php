<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementor
 */

use Elementor\WPNotificationsPackage\V110\Notifications as ThemeNotifications;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '3.3.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 800; // Pixels.
}

if ( ! function_exists( 'hello_elementor_setup' ) ) {
	/**
	 * Set up theme support.
	 *
	 * @return void
	 */
	function hello_elementor_setup() {
		if ( is_admin() ) {
			hello_maybe_update_theme_version_in_db();
		}

		if ( apply_filters( 'hello_elementor_register_menus', true ) ) {
			register_nav_menus( [ 'menu-1' => esc_html__( 'Header', 'hello-elementor' ) ] );
			register_nav_menus( [ 'menu-2' => esc_html__( 'Footer', 'hello-elementor' ) ] );
		}

		if ( apply_filters( 'hello_elementor_post_type_support', true ) ) {
			add_post_type_support( 'page', 'excerpt' );
		}

		if ( apply_filters( 'hello_elementor_add_theme_support', true ) ) {
			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'automatic-feed-links' );
			add_theme_support( 'title-tag' );
			add_theme_support(
				'html5',
				[
					'search-form',
					'comment-form',
					'comment-list',
					'gallery',
					'caption',
					'script',
					'style',
				]
			);
			add_theme_support(
				'custom-logo',
				[
					'height'      => 100,
					'width'       => 350,
					'flex-height' => true,
					'flex-width'  => true,
				]
			);
			add_theme_support( 'align-wide' );
			add_theme_support( 'responsive-embeds' );

			/*
			 * Editor Styles
			 */
			add_theme_support( 'editor-styles' );
			add_editor_style( 'editor-styles.css' );

			/*
			 * WooCommerce.
			 */
			if ( apply_filters( 'hello_elementor_add_woocommerce_support', true ) ) {
				// WooCommerce in general.
				add_theme_support( 'woocommerce' );
				// Enabling WooCommerce product gallery features (are off by default since WC 3.0.0).
				// zoom.
				add_theme_support( 'wc-product-gallery-zoom' );
				// lightbox.
				add_theme_support( 'wc-product-gallery-lightbox' );
				// swipe.
				add_theme_support( 'wc-product-gallery-slider' );
			}
		}
	}
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_maybe_update_theme_version_in_db() {
	$theme_version_option_name = 'hello_theme_version';
	// The theme version saved in the database.
	$hello_theme_db_version = get_option( $theme_version_option_name );

	// If the 'hello_theme_version' option does not exist in the DB, or the version needs to be updated, do the update.
	if ( ! $hello_theme_db_version || version_compare( $hello_theme_db_version, HELLO_ELEMENTOR_VERSION, '<' ) ) {
		update_option( $theme_version_option_name, HELLO_ELEMENTOR_VERSION );
	}
}

if ( ! function_exists( 'hello_elementor_display_header_footer' ) ) {
	/**
	 * Check whether to display header footer.
	 *
	 * @return bool
	 */
	function hello_elementor_display_header_footer() {
		$hello_elementor_header_footer = true;

		return apply_filters( 'hello_elementor_header_footer', $hello_elementor_header_footer );
	}
}

if ( ! function_exists( 'hello_elementor_scripts_styles' ) ) {
	/**
	 * Theme Scripts & Styles.
	 *
	 * @return void
	 */
	function hello_elementor_scripts_styles() {
		$min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		if ( apply_filters( 'hello_elementor_enqueue_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor',
				get_template_directory_uri() . '/style' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( apply_filters( 'hello_elementor_enqueue_theme_style', true ) ) {
			wp_enqueue_style(
				'hello-elementor-theme-style',
				get_template_directory_uri() . '/theme' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}

		if ( hello_elementor_display_header_footer() ) {
			wp_enqueue_style(
				'hello-elementor-header-footer',
				get_template_directory_uri() . '/header-footer' . $min_suffix . '.css',
				[],
				HELLO_ELEMENTOR_VERSION
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

if ( ! function_exists( 'hello_elementor_register_elementor_locations' ) ) {
	/**
	 * Register Elementor Locations.
	 *
	 * @param ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $elementor_theme_manager theme manager.
	 *
	 * @return void
	 */
	function hello_elementor_register_elementor_locations( $elementor_theme_manager ) {
		if ( apply_filters( 'hello_elementor_register_elementor_locations', true ) ) {
			$elementor_theme_manager->register_all_core_location();
		}
	}
}
add_action( 'elementor/theme/register_locations', 'hello_elementor_register_elementor_locations' );

if ( ! function_exists( 'hello_elementor_content_width' ) ) {
	/**
	 * Set default content width.
	 *
	 * @return void
	 */
	function hello_elementor_content_width() {
		$GLOBALS['content_width'] = apply_filters( 'hello_elementor_content_width', 800 );
	}
}
add_action( 'after_setup_theme', 'hello_elementor_content_width', 0 );

if ( ! function_exists( 'hello_elementor_add_description_meta_tag' ) ) {
	/**
	 * Add description meta tag with excerpt text.
	 *
	 * @return void
	 */
	function hello_elementor_add_description_meta_tag() {
		if ( ! apply_filters( 'hello_elementor_description_meta_tag', true ) ) {
			return;
		}

		if ( ! is_singular() ) {
			return;
		}

		$post = get_queried_object();
		if ( empty( $post->post_excerpt ) ) {
			return;
		}

		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $post->post_excerpt ) ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'hello_elementor_add_description_meta_tag' );

// Admin notice
if ( is_admin() ) {
	require get_template_directory() . '/includes/admin-functions.php';
}

// Settings page
require get_template_directory() . '/includes/settings-functions.php';

// Header & footer styling option, inside Elementor
require get_template_directory() . '/includes/elementor-functions.php';

if ( ! function_exists( 'hello_elementor_customizer' ) ) {
	// Customizer controls
	function hello_elementor_customizer() {
		if ( ! is_customize_preview() ) {
			return;
		}

		if ( ! hello_elementor_display_header_footer() ) {
			return;
		}

		require get_template_directory() . '/includes/customizer-functions.php';
	}
}
add_action( 'init', 'hello_elementor_customizer' );

if ( ! function_exists( 'hello_elementor_check_hide_title' ) ) {
	/**
	 * Check whether to display the page title.
	 *
	 * @param bool $val default value.
	 *
	 * @return bool
	 */
	function hello_elementor_check_hide_title( $val ) {
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$current_doc = Elementor\Plugin::instance()->documents->get( get_the_ID() );
			if ( $current_doc && 'yes' === $current_doc->get_settings( 'hide_title' ) ) {
				$val = false;
			}
		}
		return $val;
	}
}
add_filter( 'hello_elementor_page_title', 'hello_elementor_check_hide_title' );

/**
 * BC:
 * In v2.7.0 the theme removed the `hello_elementor_body_open()` from `header.php` replacing it with `wp_body_open()`.
 * The following code prevents fatal errors in child themes that still use this function.
 */
if ( ! function_exists( 'hello_elementor_body_open' ) ) {
	function hello_elementor_body_open() {
		wp_body_open();
	}
}

function hello_elementor_get_theme_notifications(): ThemeNotifications {
	static $notifications = null;

	if ( null === $notifications ) {
		require get_template_directory() . '/vendor/autoload.php';

		$notifications = new ThemeNotifications(
			'hello-elementor',
			HELLO_ELEMENTOR_VERSION,
			'theme'
		);
	}

	return $notifications;
}

hello_elementor_get_theme_notifications();

function show_teachers_and_students() {
    $args = array(
        'meta_query' => array(
            array(
                'key'     => 'wp_capabilities',
                'value'   => 'um_custom_role_2', // รหัสบทบาทของอาจารย์ใน Ultimate Member
                'compare' => 'LIKE'
            )
        ),
        'orderby' => 'display_name',
        'order'   => 'ASC'
    );
    $teachers = get_users($args);

    if (!empty($teachers)) {
        $output = '<ul>';
        foreach ($teachers as $teacher) {
            $teacher_id = $teacher->ID;
            $student_count = count(get_users(array(
                'meta_query' => array(
                    array(
                        'key'     => 'advisor_id',
                        'value'   => $teacher_id,
                        'compare' => '='
                    ),
                    array(
                        'key'     => 'wp_capabilities',
                        'value'   => 'um_custom_role_1', // รหัสบทบาทของนักศึกษาใน Ultimate Member
                        'compare' => 'LIKE'
                    )
                )
            )));

            $output .= '<li>' . esc_html($teacher->display_name) . ' - ที่ปรึกษานักศึกษา: ' . $student_count . ' / 6 คน</li>';
        }
        $output .= '</ul>';
    } else {
        $output = '<p>ไม่มีอาจารย์ในระบบ</p>';
    }

    return $output;
}

add_shortcode('show_teachers_students', 'show_teachers_and_students');

function add_student_to_teacher() {
    if (!is_user_logged_in()) {
        return '<p>กรุณาเข้าสู่ระบบ</p>';
    }

    $current_user = wp_get_current_user();
    
    // เช็คว่าเป็นอาจารย์ (um_custom_role_2) หรือ ผู้ประสานงาน (um_custom_role_3) หรือไม่
    if (!in_array('um_custom_role_2', (array) $current_user->roles) && !in_array('um_custom_role_3', (array) $current_user->roles)) {
        return '<p>คุณไม่มีสิทธิ์เพิ่มนักศึกษา</p>';
    }

    $message = '';

    if (isset($_POST['student_username'])) {
        $student_username = sanitize_text_field($_POST['student_username']);
        $student = get_user_by('login', $student_username);

        if ($student) {
            $advisor_id = get_user_meta($student->ID, 'advisor_id', true);

            // เช็คว่านักศึกษามีอาจารย์ที่ปรึกษาแล้วหรือไม่
            if (!empty($advisor_id)) {
                $message = '<p style="color: red;">นักศึกษาคนนี้มีที่ปรึกษาแล้ว</p>';
            } else {
                // นับจำนวนนักศึกษาที่อาจารย์ดูแลอยู่
                $students_count = count(get_users(array(
                    'meta_query' => array(
                        array(
                            'key'     => 'advisor_id',
                            'value'   => $current_user->ID,
                            'compare' => '='
                        ),
                    )
                )));

                if ($students_count >= 6) {
                    $message = '<p style="color: red;">คุณมีนักศึกษาเต็มจำนวนแล้ว (สูงสุด 6 คน)</p>';
                } else {
                    update_user_meta($student->ID, 'advisor_id', $current_user->ID);
                    $message = '<p style="color: green;">เพิ่มนักศึกษาสำเร็จ!</p>';
                }
            }
        } else {
            $message = '<p style="color: red;">ไม่พบนักศึกษาในระบบ</p>';
        }
    }

    ob_start();
    ?>
    <form method="post">
        <label for="student_username">กรอก Username นักศึกษา:</label>
        <input type="text" name="student_username" required>
        <button type="submit">เพิ่มนักศึกษา</button>
    </form>
    <?php echo $message; ?>
    <?php
    return ob_get_clean();
}

add_shortcode('add_student_form', 'add_student_to_teacher');



function show_my_students() {
    if (!is_user_logged_in()) {
        return '<p>กรุณาเข้าสู่ระบบ</p>';
    }

    $current_user = wp_get_current_user();

    // เช็คว่าเป็นอาจารย์หรือผู้ประสานงาน
    if (!in_array('um_custom_role_2', (array) $current_user->roles) && !in_array('um_custom_role_3', (array) $current_user->roles)) {
        return '<p>คุณไม่มีสิทธิ์ดูข้อมูลนี้</p>';
    }

    // ลบนักศึกษา (ถ้ามีการส่งค่า)
    if (isset($_POST['remove_student_id'])) {
        $student_id = intval($_POST['remove_student_id']);
        $student = get_userdata($student_id);
        if ($student) {
            $advisor_id = get_user_meta($student->ID, 'advisor_id', true);
            if ($advisor_id == $current_user->ID) {
                delete_user_meta($student->ID, 'advisor_id');
                echo '<p style="color: green;">ลบนักศึกษาเรียบร้อยแล้ว</p>';
            } else {
                echo '<p style="color: red;">คุณไม่มีสิทธิ์ลบนักศึกษาคนนี้</p>';
            }
        } else {
            echo '<p style="color: red;">ไม่พบนักศึกษาในระบบ</p>';
        }
    }

    // ดึงข้อมูลนักศึกษาที่อยู่ภายใต้การดูแลของอาจารย์
    $students = get_users(array(
        'meta_query' => array(
            array(
                'key'   => 'advisor_id',
                'value' => $current_user->ID,
                'compare' => '='
            ),
            array(
                'key'   => 'wp_capabilities',
                'value' => 'um_custom_role_1',
                'compare' => 'LIKE'
            ),
        )
    ));

    ob_start();
    ?>
    <h3>รายชื่อนักศึกษาที่ปรึกษาโดย <?php echo esc_html($current_user->display_name); ?></h3>
    
    <?php if (!empty($students)) : ?>
        <ul>
            <?php foreach ($students as $student) : ?>
                <li>
                    <?php echo esc_html($student->display_name); ?> (<?php echo esc_html($student->user_email); ?>)
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="remove_student_id" value="<?php echo esc_attr($student->ID); ?>">
                        <button type="submit" style="color: red; background: none; border: none; cursor: pointer;">ลบ</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p>คุณยังไม่มีนักศึกษาในสังกัด</p>
    <?php endif; ?>

    <?php
    return ob_get_clean();
}

add_shortcode('show_my_students', 'show_my_students');



function debug_user_roles() {
    $current_user = wp_get_current_user();
    return '<pre>' . print_r($current_user->roles, true) . '</pre>';
}
add_shortcode('debug_roles', 'debug_user_roles');




