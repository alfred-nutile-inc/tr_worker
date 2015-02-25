<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 2/25/15
 * Time: 3:22 PM
 */

namespace AlfredNutileInc\TRWorkerFeed;


interface FeedCallbackInterface {

    public function fire(ParseFeed $parseFeed);
}