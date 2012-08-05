<?php

/**
 * handles the settings for the Inform About Content plugin
 *
 * @since 0.0.5
 */

class Iac_Settings {

	/**
	 * option key
	 *
	 * @const string
	 */
	const OPTION_KEY = 'iac_options';

	/**
	 * default options
	 *
	 * @var array
	 */
	protected static $default_options = array(
		'send_by_bcc' => '0' # use strings here '1' or '0'
	);

	/**
	 * current options
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * the settings page
	 *
	 * @var string
	 */
	public $page = 'reading';

	/**
	 * section identifyer
	 *
	 * @var string
	 */
	public $section = 'iac_reading';

	/**
	 * constructor
	 *
	 * @return Authenticator_Settings
	 */
	public function __construct() {

		register_uninstall_hook(
			__FILE__,
			array( __CLASS__, 'uninstall' )
		);

		$default_options = apply_filters(
			'iac_default_options',
			self::$default_options
		);
		self::$default_options = wp_parse_args( $default_options, self::$default_options );

		$this->load_options();
		add_action( 'admin_init', array( $this, 'init_settings' ) );
	}

	/**
	 * @return void
	 */
	public function init_settings() {

		register_setting(
			$this->page,
			self::OPTION_KEY,
			array( $this, 'validate' )
		);

		add_settings_section(
			$this->section,
			__( 'Inform About Content Settings', Inform_About_Content::TEXTDOMAIN ),
			array( $this, 'description' ),
			$this->page
		);

		add_settings_field(
			'send_by_bcc',
			__( 'Hide E-Mail addresses to other recipients (BCC)', Inform_About_Content::TEXTDOMAIN ),
			array( $this, 'checkbox' ),
			$this->page,
			$this->section,
			array(
				'id'        => 'send_by_bcc',
				'name'      => self::OPTION_KEY . '[send_by_bcc]',
				'label_for' => 'send_by_bcc'
			)
		);
	}

	/**
	 * prints the form field
	 *
	 * @param array $attr
	 * @return void
	 */
	public function checkbox( $attr ) {

		$id      = $attr[ 'label_for' ];
		$name    = $attr[ 'name' ];
		$current = $this->options[ $id ];
		?>
		<input
			type="checkbox"
			name="<?php echo $name; ?>"
			id="<?php echo $attr[ 'label_for' ]; ?>"
			value="1"
			<?php checked( $current, '1' ); ?>
		/>
		<?php
	}

	/**
	 * validate the input
	 *
	 * @param array $request
	 * @return array
	 */
	public function validate( $request ) {

		if ( ! empty( $request[ 'send_by_bcc' ] ) && '1' === $request[ 'send_by_bcc' ] )
			$request[ 'send_by_bcc' ] = '1';
		else
			$request[ 'send_by_bcc' ] = '0';

		return $request;
	}

	/**
	 * prints the sections description, if it were needed
	 *
	 * @return void
	 */
	public function description() {

		return;
	}

	/**
	 * load options and set defaults if necessary
	 *
	 * @return void
	 */
	public function load_options() {

		$options = get_option( self::OPTION_KEY, '' );

		if ( ! is_array( $options ) ) {
			$options = self::$default_options;
			update_option( self::OPTION_KEY, $options );
		}
		$this->options = $options;
	}

	/**
	 * clean up
	 *
	 * @return void
	 */
	public static function uninstall() {

		delete_option( self::OPTION_KEY );
	}

}
