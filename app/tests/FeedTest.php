<?php

use AlfredNutileInc\TRWorkerFeed\ParseFeed;
use AlfredNutileInc\TRWorkerFeed\ParseFeedDTO;
use Illuminate\Support\Facades\File;

class FeedTest extends TestCase {

	/**
	 * @test
	 */
	public function getMainFeed()
	{

		$url 		   = __DIR__ . '/fixtures/food_safety.xml';
		$callback_url  = '/tmp';
		$callback_type = 'file';
		$request_uuid  = 'foo-bar-foo-bar';

		$destination = $callback_url . '/' . $request_uuid . '.out';
		if(File::exists($destination))
		{
			File::delete($destination);
		}

		$dto = new ParseFeedDTO(compact('url', 'callback_url', 'callback_type', 'request_uuid'));


		$parseFeed = new ParseFeed();
		$parseFeed->fire($dto);

		$this->assertEquals('file', $parseFeed->getDto()->callback_type);
		$this->assertEquals('foo-bar-foo-bar', $parseFeed->getDto()->request_uuid);
		$this->assertEquals('/tmp', $parseFeed->getDto()->callback_url);
		$this->assertEquals($url, $parseFeed->getDto()->url);

		$this->assertFileExists($destination);
		$this->assertNotNull($parseFeed->getFeedResults());
		$this->assertNotNull(file_get_contents($destination));
	}

	/**
	 * @test
	 */
	public function should_send_to_url()
	{
		$url 		   = __DIR__ . '/fixtures/food_safety.xml';
		$callback_url  = 'https://totalrecalls.dev:443/callbacks/feed_callback';
		$callback_type = 'url';
		$request_uuid  = 'foo-bar-foo-bar';

		$dto = new ParseFeedDTO(compact('url', 'callback_url', 'callback_type', 'request_uuid'));

		$parseFeed = new ParseFeed();
		$parseFeed->fire($dto);

		$this->assertNotNull($parseFeed->getDto()->process_results);
	}

}
