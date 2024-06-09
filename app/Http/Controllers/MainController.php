<?php

namespace App\Http\Controllers;

use App\Conversations\OnboardingConversation;
use BotMan\BotMan\BotMan;

class MainController extends Controller
{
    public function __invoke()
    {
        $messages = json_encode([
            [
                'text'     => 'Привіт. Для початку розмови напишіть start або букву s',
                'type'     => 'text',
                'isMine'   => 1,
                'original' => []
            ]
        ]);

        return view('welcome', compact('messages'));
    }
}
