<?php
/**
 * The Forminator_Addon_Form_Hooks_Abstract class.
 *
 * @package Forminator
 */

/**
 * Class Forminator_Addon_Abstract
 * Any change(s) to this file is subject to:
 * - Properly Written DocBlock! (what is this, why is that, how to be like those, etc, as long as you want!)
 * - Properly Written Changelog!
 *
 * If you override any of these method, please add necessary hooks in it,
 * Which you can see below, as a reference and keep the arguments signature.
 * If needed you can call these method, as parent::method_name(),
 * and add your specific hooks.
 *
 * @since 1.1
 */
abstract class Forminator_Addon_Form_Hooks_Abstract extends Forminator_Addon_Hooks_Abstract {

	/**
	 * Addon Instance
	 *
	 * @since 1.1
	 * @var Forminator_Addon_Abstract
	 */
	protected $addon;

	/**
	 * Current Form ID
	 *
	 * @since 1.1
	 * @var int
	 */
	protected $form_id;

	/**
	 * Customizable submit form error message
	 *
	 * @since 1.1
	 * @var string
	 */
	protected $_submit_form_error_message = '';

	/**
	 * Form settings instance
	 *
	 * @since 1.1
	 * @var Forminator_Addon_Form_Settings_Abstract|null
	 */
	protected $form_settings_instance;

	/**
	 * Custom Form Model
	 *
	 * @since 1.2
	 * @var Forminator_Form_Model
	 */
	protected $custom_form;

	/**
	 * Prefix for calculation element
	 *
	 * @since 1.7
	 */
	const CALCULATION_ELEMENT_PREFIX = 'calculation-';

	/**
	 * Prefix for stripe element
	 *
	 * @since 1.7
	 */
	const STRIPE_ELEMENT_PREFIX = 'stripe-';

	/**
	 * Forminator_Addon_Form_Hooks_Abstract constructor.
	 *
	 * @param Forminator_Addon_Abstract $addon Class Forminator_Addon_Abstract.
	 * @param int                       $form_id Form Id.
	 *
	 * @since 1.1
	 * @since 1.2 Add `custom_form` as class property
	 * @throws Forminator_Addon_Exception When there is an addon error.
	 */
	public function __construct( Forminator_Addon_Abstract $addon, $form_id ) {
		$this->addon       = $addon;
		$this->form_id     = $form_id;
		$this->custom_form = Forminator_Base_Form_Model::get_model( $this->form_id );
		if ( ! $this->custom_form ) {
			/* translators: Form ID */
			throw new Forminator_Addon_Exception( sprintf( esc_html__( 'Form with id %d could not be found', 'forminator' ), esc_html( $this->form_id ) ) );
		}

		$this->_submit_form_error_message = esc_html__( 'Failed to submit form because of an addon, please check your form and try again', 'forminator' );

		// get form settings instance to be available throughout cycle.
		$this->form_settings_instance = $this->addon->get_addon_settings( $this->form_id, 'form' );
	}

	/**
	 * Override this function to execute action before fields rendered
	 *
	 * If function generate output, it will output-ed,
	 * race condition between addon probably happen.
	 * Its void function, so return value will be ignored, and forminator process will always continue,
	 * unless it generates unrecoverable error, so please be careful on extending this function.
	 * If you want to `wp_enqueue_script` this might be the best place.
	 *
	 * @since 1.1
	 */
	public function on_before_render_form_fields() {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 * Fires before form fields rendered by forminator
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this action won't be triggered.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		do_action(
			'forminator_addon_' . $addon_slug . '_on_before_render_form_fields',
			$form_id,
			$form_settings_instance
		);
	}

	/**
	 * Override this function to execute action after all form fields rendered
	 *
	 * If function generate output, it will output-ed
	 * race condition between addon probably happen
	 * its void function, so return value will be ignored, and forminator process will always continue
	 * unless it generates unrecoverable error, so please be careful on extending this function
	 *
	 * @since 1.1
	 */
	public function on_after_render_form_fields() {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 * Fires when addon rendering extra output after connected form fields rendered
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this action won't be triggered.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		do_action(
			'forminator_addon_' . $addon_slug . '_on_after_render_form_fields',
			$form_id,
			$form_settings_instance
		);
	}

	/**
	 * Override this function to execute action after html markup form rendered completely
	 *
	 * If function generate output, it will output-ed
	 * race condition between addon probably happen
	 * its void function, so return value will be ignored, and forminator process will always continue
	 * unless it generates unrecoverable error, so please be careful on extending this function
	 *
	 * @since 1.1
	 */
	public function on_after_render_form() {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 * Fires when connected form completely rendered
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this action won't be triggered.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		do_action(
			'forminator_addon_' . $addon_slug . '_on_after_render_form',
			$form_id,
			$form_settings_instance
		);
	}

	/**
	 * Override this function to execute action on submit form
	 *
	 * Return true will continue forminator process,
	 * return false will stop forminator process,
	 * and display error message to user @see Forminator_Addon_Form_Hooks_Abstract::get_submit_form_error_message()
	 *
	 * @since 1.1
	 *
	 * @param array $submitted_data Submitted data.
	 *
	 * @return bool
	 */
	public function on_form_submit( $submitted_data ) {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 * Filter submitted form data to be processed by addon
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $submitted_data
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance Addon Form Settings instance.
		 */
		$submitted_data = apply_filters(
			'forminator_addon_' . $addon_slug . '_form_submitted_data',
			$submitted_data,
			$form_id,
			$form_settings_instance
		);

		$is_success = true;
		/**
		 * Filter result of form submit
		 *
		 * Return `true` if success, or **(string) error message** on fail
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param bool                                         $is_success
		 * @param int                                          $form_id                current Form ID.
		 * @param array                                        $submitted_data
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance Addon Form Settings instance.
		 */
		$is_success = apply_filters(
			'forminator_addon_' . $addon_slug . '_on_form_submit_result',
			$is_success,
			$form_id,
			$submitted_data,
			$form_settings_instance
		);

		// process filter.
		if ( true !== $is_success ) {
			// only update `_submit_form_error_message` when not empty.
			if ( ! empty( $is_success ) ) {
				$this->_submit_form_error_message = (string) $is_success;
			}

			return $is_success;
		}

		return $is_success;
	}

	/**
	 * Override this function to add another entry field to storage
	 *
	 * Return an multi array with format (at least, or it will be skipped)
	 * [
	 *  'name' => NAME,
	 *  'value' => VALUE', => can be array/object/scalar, it will serialized on storage
	 * ],
	 * [
	 *  'name' => NAME,
	 *  'value' => VALUE'
	 * ]
	 *
	 * @since          1.1
	 * @since          1.2 Add `$current_entry_fields` as optional param on inherit
	 *
	 * @param array $submitted_data Submitted data.
	 *
	 * @optional_param array $form_entry_fields default entry fields that will be saved,
	 *                                    its here for reference, this function doesnt need to return it
	 *                                    only return new entry fields.
	 *
	 * @return array
	 */
	public function add_entry_fields( $submitted_data ) {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 * Filter submitted form data to be processed by addon
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $submitted_data
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance Addon Form Settings instance.
		 */
		$submitted_data = apply_filters(
			'forminator_addon_' . $addon_slug . '_form_submitted_data',
			$submitted_data,
			$form_id,
			$form_settings_instance
		);

		// get second optional param `$form_entry_fields`.
		$form_entry_fields = array();
		$func_args         = func_get_args();
		if ( isset( $func_args[1] ) ) {
			$form_entry_fields = $func_args[1];
		}

		/**
		 * Filter current entry fields of form to be processed by addon
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.2
		 *
		 * @param array                                        $form_entry_fields
		 * @param array                                        $submitted_data
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance Addon Form Settings instance.
		 */
		$form_entry_fields = apply_filters(
			'forminator_addon_' . $addon_slug . '_form_entry_fields',
			$form_entry_fields,
			$submitted_data,
			$form_id,
			$form_settings_instance
		);

		$entry_fields = array();
		/**
		 * Filter addon entry fields to be saved to entry model
		 *
		 * @since 1.1
		 * @since 1.2 Add `$form_entry_fields` as param
		 *
		 * @param array                                        $entry_fields
		 * @param int                                          $form_id                current Form ID.
		 * @param array                                        $submitted_data
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance Addon Form Settings instance.
		 * @param array                                        $form_entry_fields      Current entry fields of the form.
		 */
		$entry_fields = apply_filters(
			'forminator_addon_' . $addon_slug . '_entry_fields',
			$entry_fields,
			$form_id,
			$submitted_data,
			$form_settings_instance,
			$form_entry_fields
		);

		return $entry_fields;
	}

	/**
	 * Override this function to execute action after entry saved
	 *
	 * Its void function, so return value will be ignored, and forminator process will always continue
	 * unless it generates unrecoverable error, so please be careful on extending this function
	 *
	 * @since 1.1
	 *
	 * @param Forminator_Form_Entry_Model $entry_model Form entry model.
	 */
	public function after_entry_saved( Forminator_Form_Entry_Model $entry_model ) {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 * Fires when entry already saved on db
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Form_Entry_Model                  $entry_model            Forminator Entry Model.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		do_action(
			'forminator_addon_' . $addon_slug . '_after_entry_saved',
			$form_id,
			$entry_model,
			$form_settings_instance
		);
	}

	/**
	 * Override this function to display another sub-row on entry detail
	 *
	 * Return a multi array with this format (at least, or it will skipped)
	 * [
	 *  'label' => LABEL,
	 *  'value' => VALUE (string) => its output is on html mode, so you can do styling, but please don't forgot to escape its html when needed
	 * ],
	 * [
	 *  'label' => LABEL,
	 *  'value' => VALUE
	 * ]
	 *
	 * @since 1.1
	 *
	 * @param Forminator_Form_Entry_Model $entry_model Form Entry Model.
	 * @param array                       $addon_meta_data Specific meta_data that added by current addon from @see: add_entry_fields().
	 *
	 * @return array
	 */
	public function on_render_entry( Forminator_Form_Entry_Model $entry_model, $addon_meta_data ) {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 *
		 * Filter addon metadata that previously saved on db to be processed
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $addon_meta_data
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Form_Entry_Model                  $entry_model            Forminator Entry Model.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		$addon_meta_data = apply_filters(
			'forminator_addon_' . $addon_slug . '_metadata',
			$addon_meta_data,
			$form_id,
			$entry_model,
			$form_settings_instance
		);

		$entry_items = array();
		/**
		 * Filter mailchimp row(s) to be displayed on entries page
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $entry_items            row(s) to be displayed on entries page.
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Form_Entry_Model                  $entry_model            Form Entry Model.
		 * @param array                                        $addon_meta_data        meta data saved by addon on entry fields.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		$entry_items = apply_filters(
			'forminator_addon_' . $addon_slug . '_entry_items',
			$entry_items,
			$form_id,
			$entry_model,
			$addon_meta_data,
			$form_settings_instance
		);

		return $entry_items;
	}

	/**
	 * Override this function to Add another Column on title Row
	 *
	 * This TITLE_ID will be referenced on @see Forminator_Addon_Form_Hooks_Abstract::on_export_render_entry_row()
	 *
	 * @example
	 * {
	 *         TITLE_ID_1 => 'TITLE 1',
	 *         TITLE_ID_2 => 'TITLE 2',
	 *         TITLE_ID_3 => 'TITLE 3',
	 * }
	 *
	 * @since 1.1
	 * @return array
	 */
	public function on_export_render_title_row() {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		$export_headers = array();
		/**
		 * Filter mailchimp headers on export file
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $export_headers         headers to be displayed on export file.
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		$export_headers = apply_filters(
			'forminator_addon_' . $addon_slug . '_export_headers',
			$export_headers,
			$form_id,
			$form_settings_instance
		);

		return $export_headers;
	}

	/**
	 * Add Additional Column on entry row,
	 *
	 * Use TITLE_ID from @see Forminator_Addon_Form_Hooks_Abstract::on_export_render_title_row()
	 *
	 * @example
	 * {
	 *   'TITLE_ID_1' => 'VALUE OF TITLE_1',
	 *   'TITLE_ID_2' => 'VALUE OF TITLE_2',
	 *   'TITLE_ID_3' => 'VALUE OF TITLE_3',
	 * }
	 *
	 * @since 1.1
	 *
	 * @param Forminator_Form_Entry_Model $entry_model Form Entry Model.
	 * @param array                       $addon_meta_data Addon Meta.
	 *
	 * @return array
	 */
	public function on_export_render_entry( Forminator_Form_Entry_Model $entry_model, $addon_meta_data ) {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 *
		 * Filter addon metadata that previously saved on db to be processed
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $addon_meta_data
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Form_Entry_Model                  $entry_model            Forminator Entry Model.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		$addon_meta_data = apply_filters(
			'forminator_addon_' . $addon_slug . '_metadata',
			$addon_meta_data,
			$form_id,
			$entry_model,
			$form_settings_instance
		);

		$export_columns = array();
		/**
		 * Filter addon columns to be displayed on export submissions
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $export_columns         column to be exported.
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Form_Entry_Model                  $entry_model            Form Entry Model.
		 * @param array                                        $addon_meta_data        meta data saved by addon on entry fields.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		$export_columns = apply_filters(
			'forminator_addon_' . $addon_slug . '_export_columns',
			$export_columns,
			$form_id,
			$entry_model,
			$addon_meta_data,
			$form_settings_instance
		);

		return $export_columns;
	}

	/**
	 * Get Submit form error message
	 *
	 * @since 1.1
	 * @return string
	 */
	public function get_submit_form_error_message() {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		$error_message = $this->_submit_form_error_message;
		/**
		 * Filter addon columns to be displayed on export submissions
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $export_columns         column to be exported.
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		$error_message = apply_filters(
			'forminator_addon_' . $addon_slug . '_submit_form_error_message',
			$error_message,
			$form_id,
			$form_settings_instance
		);

		return $error_message;
	}

	/**
	 * Override this function to execute action before submission deleted
	 *
	 * If function generate output, it will output-ed
	 * race condition between addon probably happen
	 * its void function, so return value will be ignored, and forminator process will always continue
	 * unless it generates unrecoverable error, so please be careful on extending this function
	 *
	 * @since 1.1
	 *
	 * @param Forminator_Form_Entry_Model $entry_model Form entry model.
	 * @param array                       $addon_meta_data Addon Meta.
	 */
	public function on_before_delete_entry( Forminator_Form_Entry_Model $entry_model, $addon_meta_data ) {
		$addon_slug             = $this->addon->get_slug();
		$form_id                = $this->form_id;
		$form_settings_instance = $this->form_settings_instance;

		/**
		 *
		 * Filter addon metadata that previously saved on db to be processed
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this filter probably won't be applied.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param array                                        $addon_meta_data
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Form_Entry_Model                  $entry_model            Forminator Entry Model.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		$addon_meta_data = apply_filters(
			'forminator_addon_' . $addon_slug . '_metadata',
			$addon_meta_data,
			$form_id,
			$entry_model,
			$form_settings_instance
		);

		/**
		 * Fires when connected form delete a submission
		 *
		 * Although it can be used for all addon.
		 * Please keep in mind that if the addon override this method,
		 * then this action won't be triggered.
		 * To be sure please check individual addon documentations.
		 *
		 * @since 1.1
		 *
		 * @param int                                          $form_id                current Form ID.
		 * @param Forminator_Form_Entry_Model                  $entry_model            Forminator Entry Model.
		 * @param array                                        $addon_meta_data        addon meta data.
		 * @param Forminator_Addon_Form_Settings_Abstract|null $form_settings_instance of Addon Form Settings.
		 */
		do_action(
			'forminator_addon_' . $addon_slug . '_on_before_delete_submission',
			$form_id,
			$entry_model,
			$addon_meta_data,
			$form_settings_instance
		);
	}

	/**
	 * Find Meta value from entry fields
	 *
	 * @since 1.7
	 *
	 * @param string $element_id Element Id.
	 * @param array  $form_entry_fields Form entry fields.
	 *
	 * @return array
	 */
	public static function find_meta_value_from_entry_fields( $element_id, $form_entry_fields ) {
		$meta_value = array();

		foreach ( $form_entry_fields as $form_entry_field ) {
			if ( isset( $form_entry_field['name'] ) && $form_entry_field['name'] === $element_id ) {
				$meta_value = isset( $form_entry_field['value'] ) ? $form_entry_field['value'] : array();
			}
		}

		/**
		 * Filter meta value of element_id from form entry fields
		 *
		 * @since 1.7
		 *
		 * @param array  $meta_value
		 * @param string $element_id
		 * @param array  $form_entry_fields
		 *
		 * @return array
		 */
		$meta_value = apply_filters( 'forminator_addon_meta_value_entry_fields', $meta_value, $element_id, $form_entry_fields );

		return $meta_value;
	}

	/**
	 * Check if element_id is calculation type
	 *
	 * @since 1.7
	 *
	 * @param string $element_id Element Id.
	 *
	 * @return bool
	 */
	public static function element_is_calculation( $element_id ) {
		$is_calculation = stripos( $element_id, self::CALCULATION_ELEMENT_PREFIX ) !== false;

		/**
		 * Filter calculation flag of element
		 *
		 * @since 1.7
		 *
		 * @param bool   $is_calculation
		 * @param string $element_id
		 *
		 * @return bool
		 */
		$is_calculation = apply_filters( 'forminator_addon_element_is_calculation', $is_calculation, $element_id );

		return $is_calculation;
	}

	/**
	 * Find calculations fields from entry fields
	 *
	 * @since 1.7
	 *
	 * @param array $form_entry_fields Form entry fields.
	 *
	 * @return array
	 */
	public static function find_calculation_fields_meta_from_entry_fields( $form_entry_fields ) {
		$meta_value = array();

		foreach ( $form_entry_fields as $form_entry_field ) {
			if ( isset( $form_entry_field['name'] ) ) {
				$element_id = $form_entry_field['name'];
				if ( self::element_is_calculation( $form_entry_field['name'] ) ) {
					$meta_value[ $element_id ] = isset( $form_entry_field['value'] ) ? $form_entry_field['value'] : array();
				}
			}
		}

		/**
		 * Filter calculations fields meta value form form entry fields
		 *
		 * @since 1.7
		 *
		 * @param array $meta_value
		 * @param array $form_entry_fields
		 *
		 * @return array
		 */
		$meta_value = apply_filters( 'forminator_addon_calculation_fields_entry_fields', $meta_value, $form_entry_fields );

		return $meta_value;
	}

	/**
	 * Check if element_id is stripe type
	 *
	 * @since 1.7
	 *
	 * @param string $element_id Element id.
	 *
	 * @return bool
	 */
	public static function element_is_stripe( $element_id ) {
		$is_stripe = stripos( $element_id, self::STRIPE_ELEMENT_PREFIX ) !== false;

		/**
		 * Filter stripe flag of element
		 *
		 * @since 1.7
		 *
		 * @param bool   $is_stripe
		 * @param string $element_id
		 *
		 * @return bool
		 */
		$is_stripe = apply_filters( 'forminator_addon_element_is_stripe', $is_stripe, $element_id );

		return $is_stripe;
	}

	/**
	 * Check if element_id is Datepicker
	 *
	 * @since 1.15.12
	 *
	 * @param string $element_id Element id.
	 *
	 * @return bool
	 */
	public static function element_is_datepicker( $element_id ) {
		$is_datepicker = stripos( $element_id, 'date-' ) !== false;

		/**
		 * Filter date flag of element
		 *
		 * @since 1.15.12
		 *
		 * @param bool   $is_datepicker
		 * @param string $element_id
		 *
		 * @return bool
		 */
		$is_datepicker = apply_filters( 'forminator_addon_element_is_datepicker', $is_datepicker, $element_id );

		return $is_datepicker;
	}

	/**
	 * Check if element_id is Signature
	 *
	 * @param string $element_id Field slug.
	 *
	 * @return bool
	 */
	public static function element_is_signature( $element_id ) {
		$is_signature = stripos( $element_id, 'signature-' ) !== false;

		/**
		 * Filter date flag of element
		 *
		 * @since 1.16.0
		 *
		 * @param bool   $is_signature
		 * @param string $element_id Field slug
		 *
		 * @return bool
		 */
		$is_signature = apply_filters( 'forminator_addon_element_is_signature', $is_signature, $element_id );

		return $is_signature;
	}

	/**
	 * Find stripe fields from entry fields
	 *
	 * @since 1.7
	 *
	 * @param array $form_entry_fields Form entry fields.
	 *
	 * @return array
	 */
	public static function find_stripe_fields_meta_from_entry_fields( $form_entry_fields ) {
		$meta_value = array();

		foreach ( $form_entry_fields as $form_entry_field ) {
			if ( isset( $form_entry_field['name'] ) ) {
				$element_id = $form_entry_field['name'];
				if ( self::element_is_stripe( $form_entry_field['name'] ) ) {
					$meta_value[ $element_id ] = isset( $form_entry_field['value'] ) ? $form_entry_field['value'] : array();
				}
			}
		}

		/**
		 * Filter stripe fields meta value form form entry fields
		 *
		 * @since 1.7
		 *
		 * @param array $meta_value
		 * @param array $form_entry_fields
		 *
		 * @return array
		 */
		$meta_value = apply_filters( 'forminator_addon_stripe_fields_entry_fields', $meta_value, $form_entry_fields );

		return $meta_value;
	}

	/**
	 * Check if element_id is upload
	 *
	 * @since 1.15.7
	 *
	 * @param string $element_id Element id.
	 *
	 * @return bool
	 */
	public static function element_is_upload( $element_id ) {
		$is_upload = stripos( $element_id, 'upload' ) !== false;

		/**
		 * Filter upload flag of element
		 *
		 * @since 1.15.7
		 *
		 * @param bool   $is_upload
		 * @param string $element_id
		 *
		 * @return bool
		 */
		$is_upload = apply_filters( 'forminator_addon_element_is_upload', $is_upload, $element_id );

		return $is_upload;
	}

	/**
	 * Return field data value as string
	 *
	 * @since 1.15.7
	 *
	 * @param string $element_id Element id.
	 * @param array  $element Element.
	 *
	 * @return bool
	 */
	public static function get_field_value( $element_id, $element ) {

		if ( is_array( $element ) ) {

			if ( self::element_is_upload( $element_id ) && isset( $element['file']['file_url'] ) ) {
				if ( is_array( $element['file']['file_url'] ) ) {
					$element_value = implode( ',', $element['file']['file_url'] );
				} else {
					$element_value = $element['file']['file_url'];
				}
			} else {
				$element_value = implode( ',', $element );
			}
		} else {
			$element_value = trim( $element );
		}

		/**
		 * Filter element value
		 *
		 * @since 1.15.7
		 *
		 * @param bool   $element_value
		 * @param string $element_id
		 *
		 * @return bool
		 */
		$element_value = apply_filters( 'forminator_addon_element_value', $element_value, $element_id );

		return $element_value;
	}

	/**
	 * Return date value as Unix timestamp in milliseconds
	 *
	 * @since 1.15.12
	 *
	 * @param string $element_id Element id.
	 * @param mixed  $value Form value.
	 * @param int    $form_id Form Id.
	 *
	 * @return bool
	 */
	public static function get_date_in_ms( $element_id, $value, $form_id ) {
		$field             = Forminator_API::get_form_field( $form_id, $element_id );
		$normalized_format = new Forminator_Date();
		$normalized_format = $normalized_format->normalize_date_format( $field['date_format'] );
		$date              = date_create_from_format( $normalized_format, $value );
		$date->setTimezone( timezone_open( 'UTC' ) );
		$date->modify( 'midnight' );

		return $date->getTimestamp() * 1000;
	}

	/**
	 * Prepare field value for passing to addon
	 *
	 * @param string $element_id Field slug.
	 * @param mixed  $form_entry_fields Form entry fields.
	 * @param array  $submitted_data Submitted data.
	 * @return string
	 */
	public static function prepare_field_value_for_addon( $element_id, $form_entry_fields, $submitted_data ) {
		$element_value = null;
		if ( self::element_is_calculation( $element_id ) ) {
			$meta_value    = self::find_meta_value_from_entry_fields( $element_id, $form_entry_fields );
			$element_value = Forminator_Form_Entry_Model::meta_value_to_string( 'calculation', $meta_value );
		} elseif ( self::element_is_stripe( $element_id ) ) {
			$meta_value    = self::find_meta_value_from_entry_fields( $element_id, $form_entry_fields );
			$element_value = Forminator_Form_Entry_Model::meta_value_to_string( 'stripe', $meta_value );
		} elseif ( self::element_is_signature( $element_id ) ) {
			$meta_value    = self::find_meta_value_from_entry_fields( $element_id, $form_entry_fields );
			$element_value = Forminator_Form_Entry_Model::meta_value_to_string( 'signature', $meta_value );
		} elseif ( ! empty( $submitted_data[ $element_id ] ) ) {
			$field_type    = Forminator_Core::get_field_type( $element_id );
			$element_value = Forminator_Form_Entry_Model::meta_value_to_string( $field_type, $submitted_data[ $element_id ] );
		}

		return $element_value;
	}
}
