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
        $accessKey = '6e56c316f00700490478d9c3040193bd';
        $baseUrl = 'http://api.exchangeratesapi.io/v1/latest';

        // Define parameters for the API request
        $params = [
            'access_key' => $accessKey,
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
            'data' => $data['success'],
        ]);
    }
}
