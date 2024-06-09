<?php

namespace App\Http\Controllers;

use App\Conversations\OnboardingConversation;
use BotMan\BotMan\BotMan;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('s|start', function (BotMan $bot) {
            $bot->startConversation(new OnboardingConversation);
        });

        $botman->fallback(function (BotMan $bot) {
            $bot->reply('Вибачте, я Вас не розумію');
        });

        $botman->listen();
    }
}
