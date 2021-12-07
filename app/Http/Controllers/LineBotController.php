<?php

namespace App\Http\Controllers;

use App\Services\DataCovidService;
use App\Services\LineBotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;
use Illuminate\Support\Str;

class LineBotController extends Controller
{
    private $dataCovidService;

    public function __construct(DataCovidService $dataCovidService)
    {
        $this->dataCovidService = $dataCovidService;
    }

    public function webhook(Request $request, Response $response)
    {
        // get request body and line signature header
        $body        = file_get_contents('php://input');
        $signature = $_SERVER['HTTP_X_LINE_SIGNATURE'];

        // log body and signature
        file_put_contents('php://stderr', 'Body: ' . $body);

        // is LINE_SIGNATURE exists in request header?
        if (empty($signature)) {
            return $response->withStatus(400, 'Signature not set');
        }

        // is this request comes from LINE?
        if (env('PASS_SIGNATURE') == false && !SignatureValidator::validateSignature($body, env('LINE_BOT_CHANNEL_SECRET'), $signature)) {
            return $response->withStatus(400, 'Invalid signature');
        }

        // init bot
        $httpClient = new CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);
        $data = json_decode($body, true);
        foreach ($data['events'] as $event) {
            $userMessage = $event['message']['text'];
            if (strtolower(Str::of($userMessage)->trim()) == 'halo') {
                $message = "Halo juga";
                $textMessageBuilder = new TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else if (strtolower(Str::of($userMessage)->trim()) == 'indonesia') {
                $textMessageBuilder = new TextMessageBuilder($this->dataCovidService->index());
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            } else {
                $textMessageBuilder = new TextMessageBuilder("Command : \n - Indonesia : to get all covid data in Indonesia");
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
        }
    }
}
