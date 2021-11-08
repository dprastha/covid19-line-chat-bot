<?php

namespace App\Http\Services;

use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;

class LineBotService
{
    /**
     * @var LINEBot
     */
    private $bot;
    /**
     * @var HTTPClient
     */
    private $client;


    public function replySend($formData)
    {
        $replyToken = $formData['events']['0']['replyToken'];

        $this->client = new CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $this->bot = new LINEBot($this->client, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);

        $response = $this->bot->replyText($replyToken, 'hello!');

        if ($response->isSucceeded()) {
            logger("reply success!!");
            return;
        }
    }
}
