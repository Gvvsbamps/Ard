<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use GuzzleHttp\Client;


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
        $sql = "Select FORMAT (c.volume, 'c', 'mn-Mong-MN') as total_volume,s.turnover,s.trades, c.symbol,c.namemn,FORMAT (s.OpeningPrice, 'c', 'mn-Mong-MN') as today_opening,FORMAT (s.ClosingPrice, 'c', 'mn-Mong-MN') as today_closing ,FORMAT (s.PreviousClose, 'c', 'mn-Mong-MN') as prev_close
from _securitytradingstatus s inner join companyinfo c on c.companycode = s.companycode where c.symbol = :symbol and MDSubOrderBookType = :type";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('type', 'Regular');
        $stmt->bindValue('symbol', $symbol);
        $result = $stmt->executeQuery();
        $data = $result->fetchAssociative();
        // foreach ($data as $record) {
        //     $client->request('POST', 'https://api.webflow.com/v2/collections/657764d051c342638175121e/items', [
        //         'json' => [
        //             'isArchived' => false,
        //             'isDraft' => false,
        //             'fieldData' => [
        //                 'name' => strval($record['namemn'] ?? ''),
        //                 'slug' => strval($record['symbol'] ?? ''),
        //             ]
        //         ],
        //         'headers' => [
        //             'accept' => 'application/json',
        //             'authorization' => 'Bearer d26b568087561b2f83e1d2e3bcd5409c2300f745b8db3facb7662f137b27e22a',
        //             'content-type' => 'application/json',
        //         ],
        //     ]);
        // }
        return $this->json(
            $data
        );
    }
    /**
     * @Route("/detail/{symbol}", name="app_detail")
     */
    public function detail(
        EntityManagerInterface $entityManager,
        $symbol
    ): Response {
        $conn = $entityManager->getConnection();
        $sql = "SELECT companycode from companyinfo where symbol = :symbol";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('symbol', $symbol);
        $result = $stmt->executeQuery();
        $data = $result->fetchAssociative();
        $companyCode = $data['companycode'];
        $sql1 = "select h.OpeningPrice, h.dates from _securitytradingstatus_history h inner join companyinfo c on c.companycode = h.companycode where c.companycode = :companycode and OpeningPrice != 0 order by h.dates DESC limit 800";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bindValue('companycode', $companyCode);
        $result1 = $stmt1->executeQuery();
        $detail = $result1->fetchAllAssociative();
        return $this->json([
            'data' => $detail,
        ]);
    }
    /**
     * @Route("/mini-chart/{symbol}", name="app_dmini_chart")
     */
    public function miniChart(
        EntityManagerInterface $entityManager,
        $symbol
    ): Response {
        $conn = $entityManager->getConnection();
        $sql = "SELECT companycode from companyinfo where symbol = :symbol";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('symbol', $symbol);
        $result = $stmt->executeQuery();
        $data = $result->fetchAssociative();
        $companyCode = $data['companycode'];
        $sql1 = "select h.OpeningPrice, h.dates from _securitytradingstatus_history h inner join companyinfo c on c.companycode = h.companycode where c.companycode = :companycode";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bindValue('companycode', $companyCode);
        $result1 = $stmt1->executeQuery();
        $detail = $result1->fetchAllAssociative();
        return $this->json([
            'data' => $detail,
        ]);
    }
    /**
     * @Route("list/{page}", name="app_list")
     */
    public function list(
        EntityManagerInterface $entityManager,
        $page
    ): Response {
        $conn = $entityManager->getConnection();
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $sql = "Select FORMAT (c.volume, 'c', 'mn-Mong-MN') as total_volume,s.turnover,s.trades, c.symbol,c.namemn,FORMAT (s.OpeningPrice, 'c', 'mn-Mong-MN') as today_opening,FORMAT (s.ClosingPrice, 'c', 'mn-Mong-MN') as today_closing ,FORMAT (s.PreviousClose, 'c', 'mn-Mong-MN') as prev_close
        from _securitytradingstatus s inner join companyinfo c on c.companycode = s.companycode where MDSubOrderBookType = :type
        LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('type', 'Regular');
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $result = $stmt->executeQuery();
        $data = $result->fetchAllAssociative();
        return $this->json([
            'data' => $data
        ]);
    }
}
