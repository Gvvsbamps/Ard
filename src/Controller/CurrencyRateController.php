<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;



class CurrencyRateController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(Request $request): JsonResponse
    {
        $accessKey = 'n6BvkdPjAvX772cYZ0zrBwRWbsJn9p';
        // $apiUrl = 'https://j2me.mostmoney.mn:9097/api/fi/v1.0/getScMarketInfo';
        $apiUrl = 'https://j2me.mostmoney.mn:9097/api/fi/v1.0/getSecurityList';

        $requestData = [
            'connId' => 0,
            'brokerId' => '',
            'mostId' => '',
            'infoType' => 'A',
            'type' => 'SEC',
            'marketCode' => 'MSE',
            'wallet' => '',
            'affCustId' => '',
            'srcFiCode' => '',
            'traceNo' => '',
        ];

        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', $apiUrl, [
            'headers' => [
                'nessession' => $accessKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $requestData,
        ]);

        $statusCode = $response->getStatusCode();
        if ($statusCode >= 200 && $statusCode < 300) {
            // Get the response content as an array
            $data = $response->toArray();

            return $this->json([
                'message' => 'Data retrieved successfully!',
                'data' => $data,
            ]);
        } else {
            return $this->json([
                'error' => 'Failed to retrieve data.',
                'statusCode' => $statusCode,
            ], $statusCode);
        }
    }

    /**
     * @Route("/proseed-mailer", name="app_mailer", methods={"GET"})
     */
    public function mailer(Request $request, MailerInterface $mailer): Response
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

        $mailer->send($email);
        $mailer->send($emailUser);

        return new Response('Success');
    }
}
