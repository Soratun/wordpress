<?php
/**
 * This class handles all (notification) emails sent by Everest Forms.
 *
 * Heavily influenced by the great AffiliateWP plugin by Pippin Williamson.
 * https://github.com/AffiliateWP/AffiliateWP/blob/master/includes/emails/class-affwp-emails.php
 *
 * @package EverestForms\Classes\Emails
 * @version 1.2.0
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Email class.
 */
class EVF_Emails {

	/**
	 * Holds the from address.
	 *
	 * @var string
	 */
	private $from_address;

	/**
	 * Holds the from name.
	 *
	 * @var string
	 */
	private $from_name;

	/**
	 * Holds the reply-to address.
	 *
	 * @var string
	 */
	private $reply_to = false;

	/**
	 * Holds the carbon copy addresses.
	 *
	 * @var string
	 */
	private $cc = false;

	/**
	 * Holds the blind carbon copy addresses.
	 *
	 * @var string
	 */
	private $bcc = false;

	/**
	 * Holds the email content type.
	 *
	 * @var string
	 */
	private $content_type;

	/**
	 * Holds the email headers.
	 *
	 * @var string
	 */
	private $headers;

	/**
	 * Holds the email attachments.
	 *
	 * @var string
	 */
	public $attachments = '';

	/**
	 * Whether to send email in HTML.
	 *
	 * @var bool
	 */
	private $html = true;

	/**
	 * The email template to use.
	 *
	 * @var string
	 */
	private $template;

	/**
	 * Form data.
	 *
	 * @var array
	 */
	public $form_data = array();

	/**
	 * Fields, formatted, and sanitized.
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Entry ID.
	 *
	 * @var int
	 */
	public $entry_id = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		if ( 'none' === $this->get_template() ) {
			$this->html = false;
		}

		// Hooks.
		add_action( 'everest_forms_email_send_before', array( $this, 'send_before' ) );
		add_action( 'everest_forms_email_send_after', array( $this, 'send_after' ) );
	}

	/**
	 * Set a property.
	 *
	 * @param string $key   Object property key.
	 * @param mixed  $value Object property value.
	 */
	public function __set( $key, $value ) {
		$this->$key = $value;
	}

	/**
	 * Get the email from name.
	 *
	 * @return string The email from name.
	 */
	public function get_from_name() {
		if ( ! empty( $this->from_name ) ) {
			$this->from_name = $this->process_tag( $this->from_name );
		} else {
			$this->from_name = get_bloginfo( 'name' );
		}

		return apply_filters( 'everest_forms_email_from_name', wp_specialchars_decode( $this->from_name ), $this );
	}

	/**
	 * Get the email from address.
	 *
	 * @return string The email from address.
	 */
	public function get_from_address() {
		if ( ! empty( $this->from_address ) ) {
			$this->from_address = $this->process_tag( $this->from_address );
		} else {
			$this->from_address = get_option( 'admin_email' );
		}

		return apply_filters( 'everest_forms_email_from_address', $this->from_address, $this );
	}

	/**
	 * Get the email reply-to.
	 *
	 * @return string The email reply-to address.
	 */
	public function get_reply_to() {
		if ( ! empty( $this->reply_to ) ) {
			$this->reply_to = $this->process_tag( $this->reply_to );

			if ( ! is_email( $this->reply_to ) ) {
				$this->reply_to = false;
			}
		}

		return apply_filters( 'everest_forms_email_reply_to', $this->reply_to, $this );
	}

	/**
	 * Get the email carbon copy addresses.
	 *
	 * @return string The email carbon copy addresses.
	 */
	public function get_cc() {
		if ( ! empty( $this->cc ) ) {
			$this->cc  = $this->process_tag( $this->cc );
			$addresses = array_map( 'trim', explode( ',', $this->cc ) );

			foreach ( $addresses as $key => $address ) {
				if ( ! is_email( $address ) ) {
					unset( $addresses[ $key ] );
				}
			}

			$this->cc = implode( ',', $addresses );
		}

		return apply_filters( 'everest_forms_email_cc', $this->cc, $this );
	}

	/**
	 * Get the email blind carbon copy addresses.
	 *
	 * @return string The email blind carbon copy addresses.
	 */
	public function get_bcc() {
		if ( ! empty( $this->bcc ) ) {
			$this->bcc = $this->process_tag( $this->bcc );
			$addresses = array_map( 'trim', explode( ',', $this->bcc ) );

			foreach ( $addresses as $key => $address ) {
				if ( ! is_email( $address ) ) {
					unset( $addresses[ $key ] );
				}
			}

			$this->bcc = implode( ',', $addresses );
		}

		return apply_filters( 'everest_forms_email_bcc', $this->bcc, $this );
	}

	/**
	 * Get the email content type.
	 *
	 * @return string The email content type.
	 */
	public function get_content_type() {
		if ( ! $this->content_type && $this->html ) {
			$this->content_type = apply_filters( 'everest_forms_email_default_content_type', 'text/html', $this );
		} elseif ( ! $this->html ) {
			$this->content_type = 'text/plain';
		}

		return apply_filters( 'everest_forms_email_content_type', $this->content_type, $this );
	}

	/**
	 * Get the email headers.
	 *
	 * @return string The email headers.
	 */
	public function get_headers() {
		if ( ! $this->headers ) {
			$this->headers = "From: {$this->get_from_name()} <{$this->get_from_address()}>\r\n";
			if ( $this->get_reply_to() ) {
				$this->headers .= "Reply-To: {$this->get_reply_to()}\r\n";
			}
			if ( $this->get_cc() ) {
				$this->headers .= "Cc: {$this->get_cc()}\r\n";
			}
			if ( $this->get_bcc() ) {
				$this->headers .= "Bcc: {$this->get_bcc()}\r\n";
			}
			$this->headers .= "Content-Type: {$this->get_content_type()}; charset=utf-8\r\n";
		}

		return apply_filters( 'everest_forms_email_headers', $this->headers, $this );
	}

	/**
	 * Build the email.
	 *
	 * @param  string $message The email message.
	 * @return string
	 */
	public function build_email( $message ) {
		if ( false === $this->html ) {
			$message = $this->process_tag( $message, false, true );
			$message = str_replace( '{all_fields}', $this->everest_forms_html_field_value( false ), $message );

			return apply_filters( 'everest_forms_email_message', $message, $this );
		}

		ob_start();

		if ( defined( 'DOING_CRON' ) ) {
			evf_get_template( 'emails/summary-email-header-' . $this->get_template() . '.php' );

			// Hooks into the summary email header.
			do_action( 'everest_forms_summary_email_header', $this );

			evf_get_template( 'emails/summary-email-body-' . $this->get_template() . '.php' );
			// Hooks into the summary email body.
			do_action( 'everest_forms_summary_email_body', $this );

			evf_get_template( 'emails/summary-email-footer-' . $this->get_template() . '.php' );
			// Hooks inot the summary email footer.
			do_action( 'everest_forms_summary_email_footer', $this );
		} else {
			evf_get_template( 'emails/header-' . $this->get_template() . '.php' );

			// Hooks into the email header.
			do_action( 'everest_forms_email_header', $this );

			evf_get_template( 'emails/body-' . $this->get_template() . '.php' );

			// Hooks into the email body.
			do_action( 'everest_forms_email_body', $this );

			evf_get_template( 'emails/footer-' . $this->get_template() . '.php' );

			// Hooks into the email footer.
			do_action( 'everest_forms_email_footer', $this );
		}

		$message = $this->process_tag( $message, false );
		$message = nl2br( $message );

		$body    = ob_get_clean();
		$message = str_replace( '{email}', $message, $body );
		$message = str_replace( '{all_fields}', $this->everest_forms_html_field_value( true ), $message );
		$message = make_clickable( $message );

		return apply_filters( 'everest_forms_email_message', $message, $this );
	}

	/**
	 * Send the email.
	 *
	 * @param string $to The To address.
	 * @param string $subject The subject line of the email.
	 * @param string $message The body of the email.
	 * @param array  $attachments Attachments to the email.
	 * @param string $connection_id Connection ID of the email.
	 *
	 * @return bool
	 */
	public function send( $to, $subject, $message, $attachments = '', $connection_id = '' ) {
		if ( ! did_action( 'init' ) && ! did_action( 'admin_init' ) ) {
			evf_doing_it_wrong( __FUNCTION__, __( 'You cannot send emails with EVF_Emails until init/admin_init has been reached', 'everest-forms' ), null );
			return false;
		}

		// Don't send anything if emails have been disabled.
		if ( $this->is_email_disabled() ) {
			return false;
		}

		// Don't send if email address is invalid.
		if ( ! is_email( $to ) ) {
			return false;
		}

		// Hooks before email is sent.
		do_action( 'everest_forms_email_send_before', $this );

		$message = apply_filters( 'everest_forms_entry_email__message', str_replace( '{entry_id}', absint( $this->entry_id ), $message ), $this );

		// Email Template Enabled or not checked.
		$email_template_included                   = ! empty( $this->form_data['settings']['email'][ $connection_id ]['choose_template'] ) ? true : false;
		$save_and_continue_email_template_included = ! empty( $this->form_data['settings']['email']['connection_save_and_continue']['choose_template'] ) ? true : false;
		$save_and_continue_enabled                 = ! empty( $this->form_data['settings']['email']['connection_save_and_continue']['enable_email_notification'] ) ? true : false;

		if ( $email_template_included && true === $this->html ) {
			$message = apply_filters( 'everest_forms_email_template_message', $message, $this, $connection_id );
		} elseif ( ( $save_and_continue_email_template_included && true === $this->html ) && ( true === $save_and_continue_enabled ) ) {
			$message = apply_filters( 'everest_forms_email_template_message', $message, $this, 'connection_save_and_continue' );
		} else {
			$message = $this->build_email( $message );
		}
		$this->attachments = apply_filters( 'everest_forms_email_attachments', $this->attachments, $this );
		$subject           = evf_decode_string( $this->process_tag( $subject ) );

		// Let's do this.
		$sent = wp_mail( $to, $subject, $message, $this->get_headers(), $this->attachments );

		if ( ! $sent ) {
			$error_message = apply_filters( 'everest_forms_email_send_failed_message', '' );
			$failed_data  = get_transient( 'everest_forms_mail_send_failed_count' );
			$failed_count = $failed_data && isset( $failed_data['failed_count'] ) ? $failed_data['failed_count'] : 0;
			++$failed_count;
			set_transient(
				'everest_forms_mail_send_failed_count',
				array(
					'failed_count'  => $failed_count,
					'error_message' => $error_message,
				)
			);
		}
		// Hooks after the email is sent.
		do_action( 'everest_forms_email_send_after', $this );

		return $sent;
	}

	/**
	 * Add filters/actions before the email is sent.
	 */
	public function send_before() {
		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}

	/**
	 * Remove filters/actions after the email is sent.
	 */
	public function send_after() {
		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );
	}

	/**
	 * Converts text formatted HTML. This is primarily for turning line breaks
	 * into <p> and <br/> tags.
	 *
	 * @param  string $message Text to convert.
	 * @return string
	 */
	public function text_to_html( $message ) {
		if ( 'text/html' === $this->content_type || true === $this->html ) {
			$message = wpautop( $message );
		}

		return $message;
	}

	/**
	 * Processes a smart tag.
	 *
	 * @param string $string     String that may contain tags.
	 * @param bool   $sanitize   Toggle to maybe sanitize.
	 * @param bool   $linebreaks Toggle to process linebreaks.
	 *
	 * @return string
	 */
	public function process_tag( $string = '', $sanitize = true, $linebreaks = false ) {
		$tag = apply_filters( 'everest_forms_process_smart_tags', $string, $this->form_data, $this->fields, $this->entry_id );
		$tag = evf_decode_string( $tag );

		if ( $sanitize ) {
			if ( $linebreaks ) {
				$tag = evf_sanitize_textarea_field( $tag );
			} else {
				$tag = sanitize_text_field( $tag );
			}
		}

		return $tag;
	}

	/**
	 * Process the all fields smart tag if present.
	 *
	 * @param  bool $html Toggle to use HTML or plaintext.
	 * @return string
	 */
	public function everest_forms_html_field_value( $html = true ) {
		if ( empty( $this->fields ) ) {
			return '';
		}

		// Make sure we have an entry id.
		if ( ! empty( $this->entry_id ) ) {
			$this->form_data['entry_id'] = (int) $this->entry_id;
		}

		$message = '';

		if ( $html ) {

			/*
			 * HTML emails.
			 */
			ob_start();

			// Hooks into the email field.
			do_action( 'everest_forms_email_field', $this );

			evf_get_template( 'emails/field-' . $this->get_template() . '.php' );

			$field_template = ob_get_clean();
			$empty_message  = '<em>' . __( '(empty)', 'everest-forms' ) . '</em>';

			$field_iterator = 1;
			foreach ( $this->fields as $meta_id => $field ) {

				if ( isset( $this->form_data['settings']['disabled_entries'] ) && '1' === $this->form_data['settings']['disabled_entries'] ) {
					$types_to_remove = array( 'image-upload', 'file-upload', 'signature' );
					if ( isset( $field['type'] ) && in_array( $field['type'], $types_to_remove, true ) ) {
						continue;
					}
				}
				if (
					! apply_filters( 'everest_forms_email_display_empty_fields', false ) &&
					( empty( $field['value'] ) && '0' !== $field['value'] )
				) {
					continue;
				}

				// If empty value is provided for select field, don't send email.
				if ( 'select' === $field['type'] && empty( $field['value'][0] ) ) {
					continue;
				}

				if ( ( 'radio' === $field['type'] && empty( $field['value']['label'] ) ) || ( 'payment-multiple' === $field['type'] && empty( $field['value']['label'] ) ) ) {
					continue;
				}

				if ( ( 'checkbox' === $field['type'] && empty( $field['value']['label'][0] ) ) || ( 'payment-checkbox' === $field['type'] && empty( $field['value']['label'] ) ) ) {
					continue;
				}

				// If there's the export data filter, utilize that and re-loop promptly.
				if ( has_filter( "everest_forms_field_exporter_{$field['type']}" ) ) {
					$formatted_string          = apply_filters( "everest_forms_field_exporter_{$field['type']}", $field, 'email-html', 2 );
					$formatted_string['value'] = false === $formatted_string['value'] ? $empty_message : $formatted_string['value'];

					$field_item = $field_template;
					if ( 1 === $field_iterator ) {
						$field_item = str_replace( 'border-top:1px solid #dddddd;', '', $field_item );
					}

					// Inject the label and value into the email template.
					$field_item = str_replace( '{field_name}', $formatted_string['label'], $field_item );
					$field_item = str_replace( '{field_value}', $formatted_string['value'], $field_item );

					$message .= wpautop( $field_item );

					// For BW compatibility reasons.
					++$field_iterator;
					continue;
				}

				$field_val   = empty( $field['value'] ) && '0' !== $field['value'] ? $empty_message : $field['value'];
				$field_name  = isset( $field_val['name'] ) ? $field_val['name'] : $field['name'];
				$field_label = ! empty( $field_val['label'] ) ? $field_val['label'] : $field_val;
				$field_type  = $field['type'];

				// If empty label is provided for choice field, don't store their data nor send email.
				if ( in_array( $field_type, array( 'radio', 'payment-multiple' ), true ) ) {
					if ( isset( $field_val['label'] ) && '' === $field_val['label'] ) {
						continue;
					}
				} elseif ( in_array( $field_type, array( 'checkbox', 'payment-checkbox' ), true ) ) {
					if ( isset( $field_val['label'] ) && ( empty( $field_val['label'] ) || '' === current( $field_val['label'] ) ) ) {
						continue;
					}
				}

				if ( isset( $field['value'], $field['value_raw'] ) && is_string( $field['value'] ) && in_array( $field_type, array( 'image-upload', 'file-upload' ), true ) ) {
					$field['value'] = $field;
				}

				if ( isset( $field_val['type'] ) && in_array( $field['type'], array( 'image-upload', 'file-upload', 'rating' ), true ) ) {
					if ( 'rating' === $field_val['type'] ) {
						$value           = ! empty( $field_val['value'] ) ? $field_val['value'] : 0;
						$number_of_stars = ! empty( $field_val['number_of_rating'] ) ? $field_val['number_of_rating'] : 5;
						$field_val       = $value . '/' . $number_of_stars;
					} else {
						$field_val = empty( $field_val['file_url'] ) ? $empty_message : $field_val;
					}
				}

				if ( 'rating' !== $field_type ) {
					if ( is_array( $field_label ) ) {
						$field_html = array();
						foreach ( $field_label as $meta_val ) {
							$field_html[] = esc_html( $meta_val );
						}
						$field_val = implode( ', ', $field_html );
					} else {
						$field_val = esc_html( $field_label );
					}
				}

				if ( empty( $field_name ) ) {
					$field_name = sprintf(
						/* translators: %d - field ID. */
						esc_html__( 'Field ID #%d', 'everest-forms' ),
						absint( $field['id'] )
					);
				}

				$field_item = $field_template;
				if ( 1 === $field_iterator ) {
					$field_item = str_replace( 'border-top:1px solid #dddddd;', '', $field_item );
				}

				$field_item  = str_replace( '{field_name}', $field_name, $field_item );
				$field_value = apply_filters( 'everest_forms_html_field_value', evf_decode_string( $field_val ), $field['value'], $this->form_data, 'email-html', $field );
				$field_item  = str_replace( '{field_value}', $field_value, $field_item );

				$message .= wpautop( $field_item );
				++$field_iterator;
			}
		} else {
			/*
			 * Plain Text emails.
			 */
			foreach ( $this->fields as $field ) {

				if ( isset( $this->form_data['settings']['disabled_entries'] ) && '1' === $this->form_data['settings']['disabled_entries'] ) {
					$types_to_remove = array( 'image-upload', 'file-upload', 'signature' );
					if ( isset( $field['type'] ) && in_array( $field['type'], $types_to_remove, true ) ) {
						continue;
					}
				}

				if ( ! apply_filters( 'everest_forms_email_display_empty_fields', false ) && ( empty( $field['value'] ) && '0' !== $field['value'] ) ) {
					continue;
				}

				$field_val  = empty( $field['value'] ) && '0' !== $field['value'] ? esc_html__( '(empty)', 'everest-forms' ) : $field['value'];
				$field_name = isset( $field['name'] ) ? $field['name'] : '';

				if ( is_array( $field_val ) ) {
					$field_html = array();

					foreach ( $field_val as $meta_val ) {
						$field_html[] = $meta_val;
					}

					if ( ! empty( $field_html ) && is_array( $field_html ) ) {
						$field_val = implode( ', ', $field_html );
					} else {
						$field_val = '';
					}
				}

				if ( empty( $field_name ) ) {
					$field_name = sprintf(
						/* translators: %d - field ID. */
						esc_html__( 'Field ID #%d', 'everest-forms' ),
						absint( $field['id'] )
					);
				}

				$message    .= '--- ' . evf_decode_string( $field_name ) . " ---\r\n\r\n";
				$field_value = evf_decode_string( $field_val ) . "\r\n\r\n";
				$message    .= apply_filters( 'everest_forms_plaintext_field_value', $field_value, $field['value'], $this->form_data, 'email-plain' );
			}
		}

		if ( empty( $message ) ) {
			$empty_message = esc_html__( 'An empty form was submitted.', 'everest-forms' );
			$message       = $html ? wpautop( $empty_message ) : $empty_message;
		}

		return $message;
	}

	/**
	 * Email kill switch if needed.
	 *
	 * @return bool
	 */
	public function is_email_disabled() {
		return (bool) apply_filters( 'everest_forms_disable_all_emails', false, $this );
	}

	/**
	 * Get the enabled email template.
	 *
	 * @todo Email template.
	 *
	 * @return string When filtering return 'none' to switch to text/plain email.
	 */
	public function get_template() {
		if ( ! $this->template ) {
			$this->template = get_option( 'everest_forms_email_template', 'default' );
		}

		return apply_filters( 'everest_forms_email_template', $this->template );
	}
}
