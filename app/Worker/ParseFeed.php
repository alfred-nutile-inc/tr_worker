<?php namespace AlfredNutileInc\TRWorkerFeed;

use ezcFeed;
use Illuminate\Support\Facades\File;
use Symfony\Component\Yaml\Yaml;

class ParseFeed {
    protected $feed_results;

    /**
     * @var FeedParserInterface
     */
    protected $feed_parser;

    /**
     * @var ParseFeedDTO
     */
    private $dto;

    public function __construct($feed_parser = null)
    {
        $this->feed_parser = $feed_parser;
    }

    public function fire(ParseFeedDTO $dto)
    {
        try {
            $this->setDto($dto);
            $this->getFeed();
            $this->callCallback();
            return $this->getDto();
        }
        catch(\Exception $e)
        {
            //@TODO throw new exception with dto info for back trace
            $this->getDto()->process_results[] = sprintf("Error with feed %s", $e->getMessage());
            return $this->getDto();
        }
    }

    /**
     * @return ParseFeedDTO
     */
    public function getDto()
    {
        return $this->dto;
    }

    /**
     * @param ParseFeedDTO $dto
     */
    public function setDto($dto)
    {
        $this->dto = $dto;
    }

    private function getFeed()
    {
        try
        {
            $this->feed_results = file_get_contents($this->getDto()->url);
            $this->parseFeed();
            $this->getDto()->process_results[] = sprintf("Got feed %s", $this->getDto()->url);

        } catch(\Exception $e)
        {
            $this->getDto()->process_results[] = sprintf("Issue getting feed %s", $e->getMessage());
        }
    }

    /**
     * @return mixed
     */
    public function getFeedResults()
    {
        return $this->feed_results;
    }

    /**
     * @param mixed $feed_results
     */
    public function setFeedResults($feed_results)
    {
        $this->feed_results = $feed_results;
    }

    private function callCallback()
    {
        if($this->getDto()->callback_type == 'file')
        {
            $this->getDto()->process_results[] = "File Callback";
            $this->fileCallback();
        } else {
            $this->getDto()->process_results[] = "URL Callback";
            $this->urlCallback();
        }
    }

    protected function fileCallback()
    {
        try
        {
            $callback = new FileCallback();
            /**
             * @var ParseFeedDTO
             */
            $results = $callback->fire($this);
            $this->getDto()->process_results = $results->process_results;
        } catch(\Exception $e)
        {
            $this->getDto()->process_results[] = sprintf("Error with File callback %s", $e->getMessage());
        }
    }

    public function getSerializedResults()
    {
        return serialize($this->getFeedResults());
    }

    private function parseFeed()
    {
        try
        {
            $this->feed_results = $this->processFeed($this->feed_results);
            $this->getDto()->feed_results = $this->feed_results;
        } catch(\Exception $e)
        {
            $this->getDto()->process_results[] = sprintf("Error parsing feed %s", $e->getMessage());
        }
    }

    protected function processFeed($results)
    {
        if($this->feed_parser == null)
        {
            return ezcFeed::parseContent($results);
        }

        return $this->getFeedParser()->parse($results);
    }

    private function getFeedParser()
    {
        return $this->feed_parser;
    }

    public function setFeedParser(FeedParserInterface $parser)
    {
        $this->feed_parser = $parser;
    }

    private function urlCallback()
    {
        try
        {
            $callback = new URLCallback();
            /**
             * @var ParseFeedDTO
             */
            $results = $callback->fire($this);
            $this->getDto()->process_results = $results->process_results;
        } catch(\Exception $e)
        {
            $this->getDto()->process_results[] = sprintf("Error with url callback %s", $e->getMessage());
        }
    }

}