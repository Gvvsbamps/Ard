<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpClient\HttpClient;

class CurrencyRateController extends AbstractController
{
    #[Route('/', name: 'app_currency_rate')]
    public function index(Request $request): JsonResponse
    {
        // Replace 'YOUR_ACCESS_KEY' with your actual API access key
        $accessKey = 'n6BvkdPjAvX772cYZ0zrBwRWbsJn9p';
        $apiUrl = 'https://j2me.mostmoney.mn:9097/api/fi/v1.0/getScMarketInfo';

        // Make API request using Symfony HttpClient with access key in the header
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $apiUrl, [
            'headers' => [
                'nessession' => $accessKey,
                'Content-Type' => 'application/json',
            ],
        ]);

        // Check the status code and handle the response accordingly
        $statusCode = $response->getStatusCode();
        if ($statusCode >= 200 && $statusCode < 300) {
            // Get the response content as an array
            // $data = $response->toArray();

            return $this->json([
                'message' => 'Exchange rates retrieved successfully!',
                'data' => $response,
            ]);
        } else {
            return $this->json([
                'error' => 'Failed to retrieve exchange rates.',
                'statusCode' => $statusCode,
            ], $statusCode);
        }
    }
}
