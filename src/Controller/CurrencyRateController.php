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

}
