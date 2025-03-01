<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! is_admin() ) {
	/**
	 * Add dynamic profile headers
	 *
	 * @param array $sorted_menu_items
	 * @param \stdClass $args
	 *
	 * @return array
	 */
	function um_add_custom_message_to_menu( $sorted_menu_items, $args ) {
		if ( empty( $sorted_menu_items ) ) {
			return $sorted_menu_items;
		}

		if ( is_user_logged_in() ) {
			um_fetch_user( get_current_user_id() );
		}

		foreach ( $sorted_menu_items as &$menu_item ) {
			if ( ! empty( $menu_item->title ) ) {
				$menu_item->title = UM()->shortcodes()->convert_user_tags( $menu_item->title );
			}
			if ( ! empty( $menu_item->attr_title ) ) {
				$menu_item->attr_title = UM()->shortcodes()->convert_user_tags( $menu_item->attr_title );
			}
			if ( ! empty( $menu_item->description ) ) {
				$menu_item->description = UM()->shortcodes()->convert_user_tags( $menu_item->description );
			}
		}

		if ( is_user_logged_in() ) {
			um_reset_user();
		}

		return $sorted_menu_items;
	}
	add_filter( 'wp_nav_menu_objects', 'um_add_custom_message_to_menu', 9999, 2 );

	/**
	 * Conditional menu items
	 *
	 * @param $menu_items
	 * @param $args
	 *
	 * @return mixed
	 */
	function um_conditional_nav_menu( $menu_items, $args ) {
		//if empty
		if ( empty( $menu_items ) ) {
			return $menu_items;
		}

		um_fetch_user( get_current_user_id() );

		$filtered_items   = array();
		$hide_children_of = array();

		//other filter
		foreach ( $menu_items as $item ) {
			if ( empty( $item->ID ) ) {
				// Left item with empty ID for the cases like in MegaMenu when generated submenu doesn't have the menu item ID.
				if ( ! empty( $item->is_mega_menu ) ) {
					if ( isset( $item->menu_item_parent ) && in_array( absint( $item->menu_item_parent ), $hide_children_of, true ) ) {
						continue;
					}
					$filtered_items[] = $item;
				}
				continue;
			}

			$mode  = get_post_meta( $item->ID, 'menu-item-um_nav_public', true );
			$roles = get_post_meta( $item->ID, 'menu-item-um_nav_roles', true );

			$visible = true;

			// Hide any item that is the child of a hidden item.
			if ( isset( $item->menu_item_parent ) && in_array( absint( $item->menu_item_parent ), $hide_children_of, true ) ) {
				$visible            = false;
				$hide_children_of[] = absint( $item->ID ); // for nested menus
			}

			if ( isset( $mode ) && $visible ) {
				switch ( $mode ) {
					case 2:
						if ( ! empty( $roles ) && is_user_logged_in() ) {
							$current_user_roles = um_user( 'roles' );
							if ( empty( $current_user_roles ) ) {
								$visible = false;
							} else {
								$visible = ( count( array_intersect( $current_user_roles, (array) $roles ) ) > 0 ) ? true : false;
							}
						} else {
							$visible = is_user_logged_in();
						}
						break;
					case 1:
						$visible = ! is_user_logged_in();
						break;
				}
			}
			/**
			 * Filters menu item visibility base on Ultimate Member visibility settings.
			 *
			 * @since 1.3.x
			 * @hook um_nav_menu_roles_item_visibility
			 *
			 * @param {bool}   $visible Menu item visibility.
			 * @param {object} $item    Menu item instance.
			 *
			 * @return {bool} Menu item visibility.
			 *
			 * @example <caption>Make the menu item visible for some custom reason.</caption>
			 * function my_nav_menu_roles_item_visibility( $visible, $item ) {
			 *     $visible = true;
			 *     return $visible;
			 * }
			 * add_filter( 'um_nav_menu_roles_item_visibility', 'my_nav_menu_roles_item_visibility', 10, 2 );
			 */
			$visible = apply_filters( 'um_nav_menu_roles_item_visibility', $visible, $item );

			// unset non-visible item
			if ( ! $visible ) {
				$hide_children_of[] = absint( $item->ID ); // store ID of item
			} else {
				$filtered_items[] = $item;
			}
		}

		um_reset_user();

		return $filtered_items;
	}
	add_filter( 'wp_nav_menu_objects', 'um_conditional_nav_menu', 9999, 2 );

	/**
	 * Conditional menu items
	 *
	 * @param $items
	 * @param $menu
	 * @param $args
	 *
	 * @return mixed
	 */
	function um_get_nav_menu_items( $items, $menu, $args ) {
		return um_conditional_nav_menu( $items, $args );
	}
	add_filter( 'wp_get_nav_menu_items', 'um_get_nav_menu_items', 9999, 3 );
}
