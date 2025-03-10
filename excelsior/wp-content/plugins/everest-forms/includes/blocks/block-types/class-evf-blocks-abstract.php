<?php
/**
 * Everest Forms block abstract.
 *
 * @since 2.0.9
 * @package everest-forms
 */

defined( 'ABSPATH' ) || exit;
/**
 * Abstract class.
 */
abstract class EVF_Blocks_Abstract {
	/**
	 * Block namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'everest-forms';
	/**
	 * Block name.
	 *
	 * @var string
	 */
	protected $block_name = '';

	/**
	 * Attributes.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Block content.
	 *
	 * @var string
	 */
	protected $content = '';

	/**
	 * Block instance.
	 *
	 * @var \WP_Block
	 */
	protected $block;

	/**
	 * Constructor.
	 *
	 * @param string $block_name Block name.
	 */
	public function __construct( $block_name = '' ) {
		$this->block_name = empty( $block_name ) ? $this->block_name : $block_name;
		$this->register();
	}

	/**
	 * Register.
	 *
	 * @return void
	 */
	protected function register() {

		if ( version_compare( get_bloginfo( 'version' ), '5.5', '<' ) ) {
			return;
		}

		if ( empty( $this->block_name ) ) {
			_doing_it_wrong( __CLASS__, esc_html__( 'Block name is not set.', 'everest-forms' ), '2.0.9' );
			return;
		}
		$metadata = $this->get_metadata_base_dir() . "/$this->block_name/block.json";

		if ( ! file_exists( $metadata ) ) {
			_doing_it_wrong(
				__CLASS__,
				/* Translators: 1: Block name */
				esc_html( sprintf( __( 'Metadata file for %s block does not exist.', 'everest-forms' ), $this->block_name ) ),
				'2.0.9'
			);
			return;
		}
		register_block_type_from_metadata(
			$metadata,
			array(
				'render_callback' => array( $this, 'render' ),
			)
		);
	}

	/**
	 * Get base metadata path.
	 * npm
	 *
	 * @return string
	 */
	protected function get_metadata_base_dir() {
		return dirname( EVF_PLUGIN_FILE ) . '/dist';
	}

	/**
	 * Get block type.
	 *
	 * @return string
	 */
	protected function get_block_type() {
		return "$this->namespace/$this->block_name";
	}

	/**
	 * Render callback.
	 *
	 * @param array     $attributes Block attributes.
	 * @param string    $content Block content.
	 * @param \WP_Block $block Block object.
	 *
	 * @return string
	 */
	public function render( $attributes, $content, $block ) {
		$this->attributes = $attributes;
		$this->block      = $block;
		$this->content    = $content;
		$content          = apply_filters(
			"everest_forms_{$this->block_name}_content",
			$this->build_html( $this->content ),
			$this
		);
		return $content;
	}

	/**
	 * Build html.
	 *
	 * @param string $content Build html content.
	 * @return string
	 */
	protected function build_html( $content ) {
		return $content;
	}
}
