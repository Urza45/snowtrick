<?php

namespace App\Services;

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

    /**
     * __construct
     *
     * @param  string          $mailFrom
     * @param  MailerInterface $mailer
     * @param  Environment     $twig
     * @return void
     */
    public function __construct($mailFrom, MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->mailFrom = $mailFrom;
    }

    /**
     * send
     *
     * @param  array $array
     * @return void
     */
    public function send(array $array): void
    {
        $from = $this->mailFrom;
        if ($array['from']) {
            $from = $array['from'];
        }

        $email = (new Email())
            ->from($from)
            ->to($array['to'])
            ->subject($array['subject'])
            ->html(
                $this->twig->render($array['template'], $array['parameters']),
                'text/html'
            );

        $this->mailer->send($email);
    }
}
