<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MailerService extends AbstractController
{
    /**
     * mailer
     *
     * @var MailerInterface
     */
    private $mailer;

    /**
     * twig
     *
     * @var Environnement 
     */
    private $twig;

    /**
     * mailFrom
     *
     * @var string
     */
    private $mailFrom;

    public function __construct($mailFrom, MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;
    }

    public function send(
        string $subject,
        string $from = '',
        string $to,
        string $template,
        array $parameters
    ): void {
        if ($from == '') {
            $from = $this->mailFrom;
        }
        $email = (new Email())
            ->from($from)
            ->to($to)
            ->subject($subject)
            ->html(
                $this->twig->render($template, $parameters),
                'text/html'
            );

        $this->mailer->send($email);
    }
}
