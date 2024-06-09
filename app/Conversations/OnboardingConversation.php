<?php

namespace App\Conversations;

use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Outgoing\Question;

class OnboardingConversation extends Conversation
{
    public function run(): void
    {
        $this->askStart();
    }

    public function askStart(): void
    {
        $question = Question::create('Привіт. Перед початком, потрібна Ваша згода на обробку персональних даних. Чи згодні ви, що Ваші дані будуть опрацьовані?')
            ->fallback('Unable to create a new database')
            ->callbackId('create_database')
            ->addButtons([
                Button::create('Згоден')->value('start.success'),
                Button::create('Не згоден')->value('start.failed'),
            ]);

        $this->ask($question, function (Answer $answer) use ($question) {
            if ($answer->isInteractiveMessageReply()) {
                $selectedValue = $answer->getValue();
                $selectedText = $answer->getText();
                if ($selectedValue == 'start.success') {
                    $this->success();
                }
                if ($selectedValue == 'start.failed') {
                    $this->failed();
                    $this->repeat($question);
                }
            }
        });
    }

    public function success(): void
    {
        $this->say('Дякуємо за відповідь. Продовжуємо...');
    }

    public function failed(): void
    {
        $this->say('Нажаль ми не можемо продовжити нашу розмову, оскільки нам важливо опрацьовувати Ваші персональні дані!');
    }

}
