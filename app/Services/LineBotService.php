<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use LINE\LINEBot;
use LINE\LINEBot\HTTPClient\CurlHTTPClient;
use \LINE\LINEBot\MessageBuilder\MultiMessageBuilder;
use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
use \LINE\LINEBot\MessageBuilder\StickerMessageBuilder;
use \LINE\LINEBot\MessageBuilder\AudioMessageBuilder;
use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
use \LINE\LINEBot\MessageBuilder\VideoMessageBuilder;
use \LINE\LINEBot\SignatureValidator as SignatureValidator;

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

    private $pass_signature;


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

    public function webhook(Request $request, Response $response)
    {
        $client = new CurlHTTPClient(env('LINE_BOT_CHANNEL_ACCESS_TOKEN'));
        $bot = new LINEBot($client, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);

        $body = $request->getBody();
        $signature = $request->getHeaderLine('HTTP_X_LINE_SIGNATURE');

        // log body and signature
        file_put_contents('php://stderr', 'Body: ' . $body);

        $this->pass_signature = true;

        if ($this->pass_signature === false) {
            // is LINE_SIGNATURE exists in request header?
            if (empty($signature)) {
                return $response->withStatus(400, 'Signature not set');
            }

            // is this request comes from LINE?
            if (!SignatureValidator::validateSignature($body, env('LINE_BOT_CHANNEL_SECRET'), $signature)) {
                return $response->withStatus(400, 'Invalid signature');
            }
        }

        $data = json_decode($body, true);
        if (is_array($data['events'])) {
            foreach ($data['events'] as $event) {
                $userMessage = $event['message']['text'];
                if (strtolower($userMessage) === 'Halo') {
                    $message = 'Halo juga!';
                    $textMessageBuilder = new TextMessageBuilder($message);
                    $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                    return $result->getHttpStatus() . ' ' . $result->getRawBody();
                }
            }
        }

        return $response->withStatus(400, 'No event sent!');
    }
}
