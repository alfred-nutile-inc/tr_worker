<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 2/25/15
 * Time: 3:23 PM
 */

namespace AlfredNutileInc\TRWorkerFeed;


use Illuminate\Support\Facades\File;

class FileCallback implements FeedCallbackInterface {


    /**
     * @var ParseFeed;
     */
    protected $parse_feed;

    public function fire(ParseFeed $parseFeed)
    {
        $this->parse_feed = $parseFeed;
        try {
            File::put(
                $this->parse_feed->getDto()->callback_url . '/' . $this->parse_feed->getDto()->request_uuid . '.out',
                $this->parse_feed->getSerializedResults());
        } catch (\Exception $e)
        {
            $this->parse_feed->getDto()->process_results[] = sprintf("error writing file %s", $e->getMessage());
        }
        return $this->parse_feed->getDto();
    }
}