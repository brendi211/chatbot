<?php

namespace App\Http\Controllers;

use BotMan\BotMan\Messages\Outgoing\Question;
use Illuminate\Http\Request;
use BotMan\BotMan\BotMan;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;

class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');

        $botman->hears('start', function (BotMan $bot) {
            $question = Question::create('Привіт. Перед початком, потрібна Ваша згода на обробку персональних даних. Чи згодні ви, що Ваші дані будуть опрацьовані?')
                ->addButton(Button::create('Згоден')->value('start.success'))
                ->addButton(Button::create('Не згоден')->value('start.failed'));

            $bot->reply($question);
        });

        $botman->hears('start.success', function (BotMan $bot) {
            $bot->reply('Дякуємо за відповідь. Продовжуємо...');
        });

        $botman->hears('start.failed', function (BotMan $bot) {
            $bot->reply('Нажаль ми не можемо продовжити нашу розмову, оскільки нам важливо опрацьовувати Ваші персональні дані!');
        });




        $botman->listen();
    }
}
