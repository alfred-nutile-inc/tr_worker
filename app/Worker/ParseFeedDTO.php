<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 2/25/15
 * Time: 2:17 PM
 */

namespace AlfredNutileInc\TRWorkerFeed;


class ParseFeedDTO {

    public $url;
    public $callback_url;
    public $callback_type = 'url'; //file
    public $request_uuid;
    public $process_results = [];
    public $feed_results;
    public $callback_status;

    /**
     * Items are a key value store of the above fields
     * @param array $items
     */
    public function __construct(array $items)
    {
        foreach($items as $key => $value)
        {
            $this->setItem($key, $value);
        }
    }

    protected function setItem($key, $value)
    {
        $this->{$key} = $value;
    }



}