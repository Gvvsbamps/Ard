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
        $baseUrl = 'https://j2me.mostmoney.mn:9097/api/fi/v1.0/getScMarketInfo';

        // Define parameters for the API request
        $params = [
            'nessession' => $accessKey,
        ];

        // Build the URL with parameters
        $url = $baseUrl . '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        // Make API request using Symfony HttpClient
        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url);

        // Get the response content as an array
        $data = $response->toArray();

        // Your controller logic here
        return $this->json([
            'message' => 'Exchange rates retrieved successfully!',
            'data' => $data,
        ]);
    }
}
