<?php
/**
 * Created by PhpStorm.
 * User: alfrednutile
 * Date: 2/25/15
 * Time: 3:11 PM
 */

namespace AlfredNutileInc\TRWorkerFeed;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Message\Response;
use Illuminate\Support\Facades\Log;

class URLCallback implements FeedCallbackInterface {

    /**
     * @var Client
     */
    protected $callback_client;

    /**
     * @var Response
     */
    public $callback_response;

    /**
     * @var ParseFeed
     */
    protected $parse_feed;

    public function fire(ParseFeed $parseFeed)
    {
        $this->parse_feed = $parseFeed;
        try
        {
            //Guzzle\Http\Message\EntityEnclosingRequest
            $this->callback_response =
                $this->getCallbackClient()
                    ->post(
                        $uri = $this->parse_feed->getDto()->callback_url,
                        $headers = [],
                        $postBody = json_encode($this->parse_feed->getDto(), true),
                        $options = ['verify' => false])
                    ->send();

            $message = sprintf("Results from post %s", $this->callback_response);
            $this->parse_feed->getDto()->process_results[] = $message;
            $this->parse_feed->getDto()->callback_status = 'OK';
        } catch(BadResponseException $e)
        {
            $response = $e->getResponse();
            if($response === null)
            {
                $message = "Null response from provider";
                $this->parse_feed->getDto()->process_results[] = $message;
            }

            $message = sprintf("Error from provider %s", $e->getResponse()->getStatusCode());
            throw new \Exception($message);

        }
        return $this->parse_feed->getDto();
    }

    /**
     * @return Response
     */
    public function getCallbackResponse()
    {
        return $this->callback_response;
    }

    /**
     * @param Response $callback_response
     */
    public function setCallbackResponse($callback_response)
    {
        $this->callback_response = $callback_response;
    }

    public function getCallbackClient()
    {
        if($this->callback_client == null)
            $this->setCallbackClient();

        return $this->callback_client;
    }

    public function setCallbackClient($client = null)
    {
        if($client == null)
        {
            $client = new Client();
        }

        $this->callback_client = $client;
        return $this;
    }

    /**
     * @return ParseFeed
     */
    public function getParseFeed()
    {
        return $this->parse_feed;
    }
}