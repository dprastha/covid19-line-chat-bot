<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LineBotController extends Controller
{
    public function index()
    {
        $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);
        $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');

        $response = $bot->replyMessage('<reply token>', $textMessageBuilder);
        if ($response->isSucceeded()) {
            echo 'Succeeded!';
            return;
        }

        // Failed
        echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
    }
}
