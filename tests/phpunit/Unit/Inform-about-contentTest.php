<?php
namespace Inpsyde\Informer\Tests;

use Brain;
use MonkeryTestCase;

/**
 * Class ExecutionTime - set the service time out up to 0
 *
 * @package Inpsyde\SearchReplace\Service
 */
class Inform_About_ContentTest extends MonkeryTestCase\TestCase{

	/**
	 * @dataProvider default_test_data
	 */
	public function test_logger( $line, $log_content = FALSE  ){

		#test

	}

	/**
	 * @return array
	 */
	public function default_test_data() {

		$data = [ ];

		# 1. Set timeout to 0
		$data[ 'test_1' ] = [ (string) 0, (string) 40 ];

		# 2. restore timeout
		$data[ 'test_2' ] = [ (string) 40, (string) 0 ];

		return $data;
	}

}