<?php 
namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailService
{
    private $mailer;    

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail(
         string $from,
        string $subject, 
        string $htmlTemplate,
        array $context, 
        string $to='admin@example.com',
       
     ): void
    {
         $email = (new TemplatedEmail())
                        ->from($from)
                        ->to($to)
                        ->subject($subject)
                        ->htmlTemplate($htmlTemplate)
                        ->context($context);

                    

        $this->mailer->send($email);
    }
}


?>