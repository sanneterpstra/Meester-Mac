<?php

namespace App\MonologHandlers;

use Monolog\Handler\SymfonyMailerHandler;
use Monolog\Level;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class MailLogHandler extends SymfonyMailerHandler
{
    public function __construct($to, $subject, $from, $level = Level::Error, bool $bubble = true)
    {
        $mailer = new Mailer(Transport::fromDsn('smtp://'.urlencode(config('mail.mailers.smtp.username')).':'.
            urlencode(config('mail.mailers.smtp.password')).'@'.
            config('mail.mailers.smtp.host').
            ':'.config('mail.mailers.smtp.port')
        ));

        $email = (new Email())
            ->subject($subject)
            ->from(...$from)
            ->to(...$to);

        parent::__construct($mailer, $email, $level, $bubble);
    }
}
