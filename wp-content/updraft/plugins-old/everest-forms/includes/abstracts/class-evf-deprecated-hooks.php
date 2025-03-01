<?php
/**
 * Abstract deprecated hooks
 *
 * @package EverestForms\Abstracts
 * @since   1.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * EVF_Deprecated_Hooks class maps old actions and filters to new ones. This is the base class for handling those deprecated hooks.
 *
 * Based on the WCS_Hook_Deprecator class by Prospress.
 */
abstract class EVF_Deprecated_Hooks {

	/**
	 * Array of deprecated hooks we need to handle.
	 *
	 * @var array
	 */
	protected $deprecated_hooks = array();

	/**
	 * Array of versions on each hook has been deprecated.
	 *
	 * @var array
	 */
	protected $deprecated_version = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$fields = evf()->form_fields->get_form_field_types();

		// Adapt dynamic fields hook deprecation with version support.
		foreach ( $this->deprecated_hooks as $new_hook => $old_hook ) {
			if ( is_string( $old_hook ) && false !== strpos( $new_hook, '{field_type}' ) ) {
				foreach ( $fields as $field ) {
					$new_dynamic_hooks = str_replace( '{field_type}', $field, $new_hook );
					$old_dynamic_hooks = str_replace( '{field_type}', $field, $old_hook );

					// Set the new deprecated field specific hooks with its version.
					$this->deprecated_hooks[ $new_dynamic_hooks ]   = $old_dynamic_hooks;
					$this->deprecated_version[ $old_dynamic_hooks ] = $this->get_deprecated_version( $old_hook );
				}

				// Remove the unused dynamic fields hooks.
				unset( $this->deprecated_hooks[ $new_hook ] );
				unset( $this->deprecated_version[ $old_hook ] );
			}
		}

		$new_hooks = array_keys( $this->deprecated_hooks );
		array_walk( $new_hooks, array( $this, 'hook_in' ) );
	}

	/**
	 * Hook into the new hook so we can handle deprecated hooks once fired.
	 *
	 * @param string $hook_name Hook name.
	 */
	abstract public function hook_in( $hook_name );

	/**
	 * Get old hooks to map to new hook.
	 *
	 * @param  string $new_hook New hook name.
	 * @return array
	 */
	public function get_old_hooks( $new_hook ) {
		$old_hooks = isset( $this->deprecated_hooks[ $new_hook ] ) ? $this->deprecated_hooks[ $new_hook ] : array();
		$old_hooks = is_array( $old_hooks ) ? $old_hooks : array( $old_hooks );

		return $old_hooks;
	}

	/**
	 * If the hook is Deprecated, call the old hooks here.
	 */
	public function maybe_handle_deprecated_hook() {
		$new_hook          = current_filter();
		$old_hooks         = $this->get_old_hooks( $new_hook );
		$new_callback_args = func_get_args();
		$return_value      = $new_callback_args[0];

		foreach ( $old_hooks as $old_hook ) {
			$return_value = $this->handle_deprecated_hook( $new_hook, $old_hook, $new_callback_args, $return_value );
		}

		return $return_value;
	}

	/**
	 * If the old hook is in-use, trigger it.
	 *
	 * @param  string $new_hook          New hook name.
	 * @param  string $old_hook          Old hook name.
	 * @param  array  $new_callback_args New callback args.
	 * @param  mixed  $return_value      Returned value.
	 * @return mixed
	 */
	abstract public function handle_deprecated_hook( $new_hook, $old_hook, $new_callback_args, $return_value );

	/**
	 * Get deprecated version.
	 *
	 * @param string $old_hook Old hook name.
	 * @return string
	 */
	protected function get_deprecated_version( $old_hook ) {
		return ! empty( $this->deprecated_version[ $old_hook ] ) ? $this->deprecated_version[ $old_hook ] : EVF_VERSION;
	}

	/**
	 * Display a deprecated notice for old hooks.
	 *
	 * @param string $old_hook Old hook.
	 * @param string $new_hook New hook.
	 */
	protected function display_notice( $old_hook, $new_hook ) {
		evf_deprecated_hook( esc_html( $old_hook ), esc_html( $this->get_deprecated_version( $old_hook ) ), esc_html( $new_hook ) );
	}

	/**
	 * Fire off a legacy hook with it's args.
	 *
	 * @param  string $old_hook          Old hook name.
	 * @param  array  $new_callback_args New callback args.
	 * @return mixed
	 */
	abstract protected function trigger_hook( $old_hook, $new_callback_args );
}
