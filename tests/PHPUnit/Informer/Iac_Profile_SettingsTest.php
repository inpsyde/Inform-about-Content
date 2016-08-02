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

	/**
	 * @see Iac_Profile_Settings::save_user_settings
	 * @dataProvider save_user_settings_test_data
	 *
	 * @param array $current_settings
	 * @param array $arguments Arguments for the method call save_user_settings()
	 * @param bool  $default_opt_in
	 * @param array $expected_settings
	 */
	public function test_save_user_settings( array $current_settings, array $arguments, $default_opt_in, array $expected_settings ) {

		$user_id  = 42;
		$user_can = TRUE;

		// user_can() mock
		Monkey::functions()
			->expect( 'current_user_can' )
			->with( 'edit_user', $user_id )
			->andReturn( $user_can );

		// apply_filters mock
		Monkey::functions()
			->expect( 'apply_filters' )
			->with( 'iac_default_opt_in', FALSE )
			->andReturn( $default_opt_in );

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
			->andReturn( $current_settings[ 'comment_subscription' ] );

		// update_user_meta_mock
		if ( isset( $expected_settings[ 'post_subscription' ] ) ) {
			Monkey::functions()
				->expect( 'update_user_meta' )
				->once()
				->with( $user_id, 'post_subscription', $expected_settings[ 'post_subscription' ] );
		}
		if ( isset( $expected_settings[ 'comment_subscription' ] ) ) {
			Monkey::functions()
				->expect( 'update_user_meta' )
				->once()
				->with( $user_id, 'comment_subscription', $expected_settings[ 'comment_subscription' ] );
		}

		$testee = $this->get_testee();
		$this->assertTrue(
			$testee->save_user_settings(
				$user_id,
				$arguments[ 'inform_about_posts' ],
				$arguments[ 'inform_about_comments' ]
			)
		);
	}

	/**
	 * @see test_save_user_settings
	 *
	 * @return array
	 */
	public function save_user_settings_test_data() {

		return array(
			/**
			 * - default_opt_in is set to TRUE (users are subscribed by default)
			 * - user didn't make any changes before (user meta values doesn't exist yet)
			 * - user un-checks the subscription checkboxes
			 */
			'unsubscribe_post_and_comments_from_default_opt_in' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '',
					'comment_subscription' => ''
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => NULL,
					'inform_about_comments' => NULL
				),
				// 3. parameter $default_opt_in
				TRUE,
				// 4. parameter $expected_settings
				array(
					'post_subscription'    => '0',
					'comment_subscription' => '0'
				)
			),
			/**
			 * - default_opt_in is set to FALSE (users are not subscribed by default)
			 * - user didn't make any changes before (user meta values doesn't exist yet)
			 * - subscription checkboxes remain unchecked
			 */
			'unsubscribe_post_and_comments_from_default' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '',
					'comment_subscription' => ''
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => NULL,
					'inform_about_comments' => NULL
				),
				// 3. parameter $default_opt_in
				FALSE,
				// 4. parameter $expected_settings
				array(
					// nothing, no update_user_meta() invocation expected
				)
			),
			/**
			 * - default_opt_in is set to FALSE (users are not subscribed by default)
			 * - user didn't make any changes before (user meta values doesn't exist yet)
			 * - user checks the subscription checkboxes
			 */
			'subscribe_post_and_comments_from_default' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '',
					'comment_subscription' => ''
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => '1',
					'inform_about_comments' => '1'
				),
				// 3. parameter $default_opt_in
				FALSE,
				// 4. parameter $expected_settings
				array(
					'post_subscription'    => '1',
					'comment_subscription' => '1'
				)
			),
			/**
			 * - default_opt_in is set to TRUE (users are subscribed by default)
			 * - user didn't make any changes before (user meta values doesn't exist yet)
			 * - subscription checkboxes remain checked
			 */
			'subscribe_post_and_comments_from_default_opt_in' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '',
					'comment_subscription' => ''
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => '1',
					'inform_about_comments' => '1'
				),
				// 3. parameter $default_opt_in
				TRUE,
				// 4. parameter $expected_settings
				array(
					// no update_user_meta() invocation expected
				)
			),
			/**
			 * - default_opt_in is set to TRUE (users are subscribed by default)
			 * - user unsubscribed before
			 * - user subscribes explicitly
			 */
			'explicit_subscribe_post_and_comments_from_default_opt_in' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '0',
					'comment_subscription' => '0'
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => '1',
					'inform_about_comments' => '1'
				),
				// 3. parameter $default_opt_in
				TRUE,
				// 4. parameter $expected_settings
				array(
					'post_subscription'    => '1',
					'comment_subscription' => '1'
				)
			),
			/**
			 * - default_opt_in is set to FALSE (users are not subscribed by default)
			 * - user unsubscribed before
			 * - user subscribes explicitly
			 */
			'explicit_subscribe_post_and_comments_from_default' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '0',
					'comment_subscription' => '0'
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => '1',
					'inform_about_comments' => '1'
				),
				// 3. parameter $default_opt_in
				FALSE,
				// 4. parameter $expected_settings
				array(
					'post_subscription'    => '1',
					'comment_subscription' => '1'
				)
			),
			/**
			 * - default_opt_in is set to TRUE (users are subscribed by default)
			 * - user subscribed before
			 * - user unsubscribes explicitly
			 */
			'explicit_unsubscribe_post_and_comments_from_default_opt_in' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '1',
					'comment_subscription' => '1'
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => NULL,
					'inform_about_comments' => NULL
				),
				// 3. parameter $default_opt_in
				TRUE,
				// 4. parameter $expected_settings
				array(
					'post_subscription'    => '0',
					'comment_subscription' => '0'
				)
			),
			/**
			 * - default_opt_in is set to FALSE (users are not subscribed by default)
			 * - user subscribed before
			 * - user unsubscribes explicitly
			 */
			'explicit_unsubscribe_post_and_comments_from_default' => array(
				// 1. parameter $current_settings
				array(
					'post_subscription'    => '1',
					'comment_subscription' => '1'
				),
				// 2. parameter $arguments
				array(
					'inform_about_posts'    => NULL,
					'inform_about_comments' => NULL
				),
				// 3. parameter $default_opt_in
				FALSE,
				// 4. parameter $expected_settings
				array(
					'post_subscription'    => '0',
					'comment_subscription' => '0'
				)
			),
		);
	}
}
