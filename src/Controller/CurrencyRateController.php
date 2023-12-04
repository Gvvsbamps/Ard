<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class CurrencyRateController extends AbstractController
{
    #[Route('/currency/rate', name: 'app_currency_rate')]
    public function index(Request $request): JsonResponse
    {

        $apiUrl = 'https://j2me.mostmoney.mn:9097/api/fi/v1.0/getScMarketInfo';
        $accessToken = 'n6BvkdPjAvX772cYZ0zrBwRWbsJn9p';

        $requestData = [
            'brokerId' => '32',
            'securityCode' => 'AARD-O-0000',
            'infoType' => '',
            'mostId' => '',
            'connId' => 0,
            'marketCode' => '',
            'affCustId' => '',
            'srcFiCode' => '32',
            'traceNo' => '',
            'wallet' => '',
        ];

        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $apiUrl, [
            'headers' => [
                'nessession' => $accessToken,
                'Content-Type' => 'application/json',
            ],
            'json' => $requestData,
        ]);

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            $data = $response->toArray();

            return $this->json([
                'message' => 'Exchange rates retrieved successfully!',
                'data' => $data,
            ]);
        } else {
            return $this->json([
                'error' => 'Failed to retrieve exchange rates.',
                'statusCode' => $response->getStatusCode(),
            ], $response->getStatusCode());
        }
    }
};
