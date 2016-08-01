<?php # -*- coding: utf-8 -*-

use
	MonkeryTestCase\BrainMonkeyWpTestCase,
	Brain\Monkey;

class Iac_Profile_SettingsTest extends BrainMonkeyWpTestCase {

	/**
	 * @return Iac_Profile_Settings
	 */
	public function get_testee() {

		// this function is called by the constructor
		Monkey::functions()
			->expect( 'register_uninstall_hook' );

		$testee = new Iac_Profile_Settings;

		return $testee;
	}

	public function test_save_user_settings() {

		$user_id          = 42;
		$current_settings = array(
			'post_subscription'    => '',
			'comment_subscription' => ''
		);
		$user_can         = TRUE;
		$default_opt_in   = TRUE;

		// user_can() mock
		Monkey::functions()
			->expect( 'current_user_can' )
			->with( 'edit_user', $user_id )
			->andReturn( $user_can );

		// apply_filters mock
		add_filter(
			'iac_default_opt_in',
			function() use ( $default_opt_in ) {

				return $default_opt_in;
			}
		);

		// get_user_meta mock
		Monkey::functions()
			->expect( 'get_user_meta' )
			->once()
			->with( $user_id, 'post_subscription', TRUE )
			->andReturn( $current_settings[ 'post_subscription' ] );
		Monkey::functions()
			->expect( 'get_user_meta' )
			->once()
			->with( $user_id, 'comment_subscription', TRUE )
			->andReturn( $current_settings[ 'comment_subscription' ]  );

		// update_user_meta_mock
		Monkey::functions()
			->expect( 'update_user_meta' );

		$testee = $this->get_testee();
		$testee->save_user_settings( $user_id, NULL, NULL );
		
		$this->markTestIncomplete( "Under construction" );
	}
}
