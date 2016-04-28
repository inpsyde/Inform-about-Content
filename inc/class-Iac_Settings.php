<?php

/**
 * handles the settings for the Informer plugin
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
		'send_by_bcc'         => '0', # use strings here '1' or '0'
		'send_attachments'    => '0', # also '1' or '0'
		'attachment_max_size' => 2097152, # 2Mib
		'bcc_to_recipient'    => ''
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

		add_settings_field(
			'bcc_to_recipient',
			__( 'To: header for Bcc-option is set', Inform_About_Content::TEXTDOMAIN ),
			array( $this, 'text' ),
			$this->page,
			$this->section,
			array(
				'id'          => 'bcc_to_recipient',
				'name'        => self::OPTION_KEY . '[bcc_to_recipient]',
				'label_for'   => 'bcc_to_recipient',
				'type'        => 'email',
				'description' => __( 'The eMail needs a to-header if all addresses are set as <code>bcc</code>. Leave empty to use the <code>admin_email</code> option.', Inform_About_Content::TEXTDOMAIN )
			)
		);

		add_settings_field(
			'send_attachments',
			__( 'Attach media files to the notification email', Inform_About_Content::TEXTDOMAIN ),
			array( $this, 'checkbox' ),
			$this->page,
			$this->section,
			array(
				'id'        => 'send_attachments',
				'name'      => self::OPTION_KEY . '[send_attachments]',
				'label_for' => 'send_attachments'
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
		if ( ! empty( $attr[ 'description' ] ) ) { ?>
			<p class="description"><?php echo $attr[ 'description' ]; ?></p>
			<?php
		}
	}

	/**
	 * prints the form field
	 *
	 * @param array $attr
	 * @return void
	 */
	public function text( $attr ) {

		$id      = $attr[ 'label_for' ];
		$name    = $attr[ 'name' ];
		$current = $this->options[ $id ];
		$type    = isset( $attr[ 'type' ] )
			? $attr[ 'type' ]
			: 'text';
		$value = esc_attr( $this->options[ $id ] );
		?>
		<input
			type="<?php echo $type; ?>"
			name="<?php echo $name; ?>"
			id="<?php echo $attr[ 'label_for' ]; ?>"
			value="<?php echo $value; ?>"
		/>
		<?php
		if ( ! empty( $attr[ 'description' ] ) ) { ?>
			<p class="description"><?php echo $attr[ 'description' ]; ?></p>
			<?php
		}
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

		if ( ! empty( $request[ 'send_attachments' ] ) && '1' === $request[ 'send_attachments' ] )
			$request[ 'send_attachments' ] = '1';
		else
			$request[ 'send_attachments' ] = '0';

		return $request;
	}

	/**
	 * prints the sections description, if it were needed
	 *
	 * @return void
	 */
	public function description() {

		# monitor the current status of user-selection (opt-in or opt-out)
		$default = Inform_About_Content::default_opt_in( NULL );
		$opt_in = apply_filters( 'iac_default_opt_in', $default );

		$description = $opt_in
			? __( 'Note: Users must opt-in to e-mail notifications by default', Inform_About_Content::TEXTDOMAIN )
			: __( 'Note: Users must opt-out from e-mail notifications by default', Inform_About_Content::TEXTDOMAIN );

		printf(
			'<p class="description">%s</p>',
			$description
		);
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
		} else {
			foreach ( self::$default_options as $key => $value ) {
				if ( ! isset( $options[ $key ] ) )
					$options[ $key ] = $value;
			}
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
