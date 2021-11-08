<?php

namespace App\Http\Controllers;


use App\Services\LineBotService;
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

class LineBotController extends Controller
{
    /**
     * @var GetMessageService
     */
    private $messageService;

    public function __construct(LineBotService $messageService)
    {
        $this->messageService = $messageService;
    }

    public function getMessage(Request $request)
    {
        //logger("request : ", $request->all());
        $this->messageService->replySend($request->json()->all());
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
        $httpClient = new CurlHTTPClient(env('+KLc3eFbkeHLMbaQTkME6m7gxb2To/QASocvtPUqo15MAOPn4wPCxODUcJHXUzjbrS1VVvYqLSwHn7RtaIuNrt3k75plAUU43hb5wrh7YSbXw7VsnDL6RZ1Ks7PW/pc6OesVDIECfrJWjnnbOi5gNAdB04t89/1O/w1cDnyilFU='));
        $bot = new LINEBot($httpClient, ['channelSecret' => env('LINE_BOT_CHANNEL_SECRET')]);
        $data = json_decode($body, true);
        foreach ($data['events'] as $event) {
            $userMessage = $event['message']['text'];
            if (strtolower($userMessage) == 'halo') {
                $message = "Halo juga";
                $textMessageBuilder = new TextMessageBuilder($message);
                $result = $bot->replyMessage($event['replyToken'], $textMessageBuilder);
                return $result->getHTTPStatus() . ' ' . $result->getRawBody();
            }
        }
    }
}
