<?php

namespace App\Http\Controllers;


use App\Services\LineBotService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
        $this->messageService->webHook($request, $response);
    }
}
