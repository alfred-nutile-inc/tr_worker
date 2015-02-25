# Get Feeds!

## Example using the parser I am using

~~~
<?php
		/***
		 * Example usage 
		 */
		
		/**
		 * @var ezcFeedEntryElement
		 * @var ezcFeedContentModule
		 */
		foreach($parsed->item as $item)
		{
			var_dump($item);
			var_dump($item->id->id);
			//Description
			var_dump($item->description);
			//Categories
			// can includes stores! One says Whole Foods Market
			// Food Recalls is the core of this one
			var_dump($item->category);
			//Title
			$item->title->string
			//URL to the Article
			// I would like to save this for permalink type work
			var_dump($item->link[0]->href);
			//Comments url?
			var_dump($item->comments);
		}
~~~

See the [test](/app/tests/FeedTest.php) for more info