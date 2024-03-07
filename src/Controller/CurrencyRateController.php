<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use GuzzleHttp\Client;

set_time_limit(300);



class CurrencyRateController extends AbstractController
{
    /**
     * @Route("/{symbol}", name="app_index")
     */
    public function index(
        EntityManagerInterface $entityManager,
        $symbol
    ): Response {
        $conn = $entityManager->getConnection();
        $client = new Client();
        if ($symbol !== 'update') {
            $sql = "SELECT namemn,nameen,companycode,logo,symbol,volume,zarlasan,segments,state_own,state_own_date,adjustmentcoef FROM companyinfo where symbol = :symbol";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue('symbol', $symbol);
            $result = $stmt->executeQuery();
            $detail = $result->fetchAssociative();
            return $this->json([
                'status' => $detail,
            ]);
        } else if ($symbol == 'update') {
            $sql = "SELECT namemn,nameen,companycode,logo,symbol,volume,zarlasan,segments,state_own,state_own_date,adjustmentcoef FROM companyinfo ";
            $stmt = $conn->prepare($sql);
            $result = $stmt->executeQuery();
            $data = $result->fetchAllAssociative();
            // foreach ($data as $record) {
            //     $client->request('POST', 'https://api.webflow.com/v2/collections/65e560e5ee90ddba8e6cd7ab/items/live', [
            //         'json' => [
            //             'isArchived' => false,
            //             'isDraft' => false,
            //             'fieldData' => [
            //                 'name' => (string)$record['namemn'] ?? '',
            //                 'slug' => $record['symbol'],
            //                 'namemn' => (string)$record['namemn'] ?? '',
            //                 'nameen' => (string)$record['nameen'] ?? '',
            //                 'logo' => (string)$record['logo'] ?? '',
            //                 'zarlasan' => (string)$record['zarlasan'] ?? '',
            //                 'volume' => (string)$record['volume'] ?? '',
            //                 'symbol' => isset($record['symbol']) ? (string)$record['symbol'] : '',
            //                 'adjustmentcoef' => isset($record['adjustmentcoef']) ? (string)$record['adjustmentcoef'] : '',
            //                 'companycode' => isset($record['companycode']) ? (string)$record['companycode'] : ''
            //             ]
            //         ],
            //         'headers' => [
            //             'accept' => 'application/json',
            //             'authorization' => 'Bearer d26b568087561b2f83e1d2e3bcd5409c2300f745b8db3facb7662f137b27e22a',
            //             'content-type' => 'application/json',
            //         ],
            //     ]);
            // }
            return $this->json([
                'status' => $data,
            ]);
        }
    }
    /**
     * @Route("/detail", name="app_detail")
     */
    public function detail(
        EntityManagerInterface $entityManager,
    ): Response {

        return $this->json([
            'status' => false,
        ]);
    }
}
