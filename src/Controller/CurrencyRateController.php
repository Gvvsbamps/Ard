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
        $sql = "Select FORMAT (c.volume, 'c', 'mn-Mong-MN') as total_volume, FORMAT ( s.Turnover, 'c', 'mn-Mong-MN') as turnover,s.trades, c.symbol,c.namemn,FORMAT (s.OpeningPrice, 'c', 'mn-Mong-MN') as today_opening,FORMAT (s.ClosingPrice, 'c', 'mn-Mong-MN') as today_closing ,FORMAT (s.PreviousClose, 'c', 'mn-Mong-MN') as prev_close, c.isin, FORMAT (s.VWAP, 'c', 'mn-Mong-MN') as today_vwap,FORMAT ( s.HighestBidPrice, 'c', 'mn-Mong-MN') as buy_highest, FORMAT (s.LowestOfferPrice, 'c', 'mn-Mong-MN') as sell_lowest , s.volume as volume_count
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
     * @Route("/detail/{symbol}/{begin}/{end}", name="app_detail")
     */
    public function detail(
        EntityManagerInterface $entityManager,
        $symbol,
        $begin,
        $end
    ): Response {
        $conn = $entityManager->getConnection();
        $sql = "SELECT companycode from companyinfo where symbol = :symbol";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue('symbol', $symbol);
        $result = $stmt->executeQuery();
        $data = $result->fetchAssociative();
        $companyCode = $data['companycode'];

        $sql1 = "SELECT h.OpeningPrice, STR_TO_DATE(h.dates, '%Y-%m-%d') as date FROM _securitytradingstatus_history h INNER JOIN companyinfo c ON c.companycode = h.companycode WHERE c.companycode = :companycode AND OpeningPrice != 0 AND STR_TO_DATE(h.dates, '%Y-%m-%d') BETWEEN :begin AND :end  LIMIT 800";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bindValue('companycode', $companyCode);
        $stmt1->bindValue('begin', $begin);
        $stmt1->bindValue('end', $end);
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
        $sql1 = "SELECT h.OpeningPrice, h.dates FROM _securitytradingstatus_history h INNER JOIN companyinfo c ON c.companycode = h.companycode WHERE c.companycode = :companycode AND MONTH(h.dates) = MONTH(CURRENT_DATE())";
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
        from _securitytradingstatus s inner join companyinfo c on c.companycode = s.companycode where MDSubOrderBookType = :type order by c.volume DESC
        LIMIT :limit OFFSET :offset ";
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
