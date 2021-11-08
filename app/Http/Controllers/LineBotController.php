<?php

namespace App\Http\Controllers;


use App\Http\Services\LineBotService;
use Illuminate\Http\Request;

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
}
