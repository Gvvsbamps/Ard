<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProseedMailerController extends AbstractController
{
    #[Route('/proseedMailer', name: 'send_mail')]
    public function index(Request $request, MailerInterface $mailer): Response
    {
        $name = $request->get('name');
        $email = $request->get('email');
        $email2 = $request->get('email-2');
        $field = $request->get('field');

        $email = (new Email())
            ->from('proseedagency@gmail.com')
            ->to('proseedagency@gmail.com')
            ->subject('Үйлчлүүлэгч цаг авлаа.')
            ->text('Нэр: ' . $name . ' Утас: ' . $email . ' И-мейл: ' . $email2 . ' Чиглэл: ' . $field);
        $emailUser = (new Email())
            ->from('proseedagency@gmail.com')
            ->to($email2)
            ->subject('Proseed Agency')
            ->text('Амжилттай илгээгдлээ. Бид танд тохирох туршлагатай мэргэжилтнийг санал болгох болно.' . "\n" . 'Баярлалаа
            ');
        try {
            $mailer->send($email);
            $mailer->send($emailUser);
        }catch (\ErrorException $e){
            return new JsonResponse('error');
        }

        $response = new Response();
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
        $response->headers->set('Access-Control-Max-Age', '3600');
        $response->setContent('success');
        return $response;
    }
}
