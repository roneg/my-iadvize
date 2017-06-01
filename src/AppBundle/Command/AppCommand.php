<?php
// src/AppBundle/Command/AppCommand.php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Psr\Log\LoggerInterface;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;

class AppCommand extends ContainerAwareCommand
{
    private $logger;
    
    const BASEURL = 'http://www.viedemerde.fr/?page=';
    const POST2CRAWL = 200;
    const MAXPAGE = 12;

    protected function configure()
    {
		$this
		->setName('app:getVieDeMerde')
        ->setDescription('getVieDeMerde last 200')
        ->setHelp('Chercher les 200 derniers enregistrements du site Vie de Merde')
		;
	}

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger = $this->getContainer()->get('logger');

        $this->logger->info('Entering configure Command');

        $entityManager = $this->getContainer()->get('doctrine')->getManager();

        $entityManager->getRepository('AppBundle:Post')->getPosts();


        $nbPostCrawled = 0;
        $currentPage = 1;
        while($nbPostCrawled < self::POST2CRAWL and  $currentPage <=self::MAXPAGE) {
            $currentUrl = self::BASEURL.$currentPage;
            echo $currentUrl."\n";
            $request = new Request('GET', $currentUrl);
            $client = new Client(['base_uri' => $currentUrl]);
            $response = $client->send($request, ['timeout' => 5]);
            $crawler = new Crawler();
            $crawler->addHtmlContent((string)$response->getBody()->getContents());
            $crawler->filter('div.post.article')->each(function ($node ) {
                $content = $node->children()->first()->text();
                echo $content;
                if($nbPostCrawled < self::POST2CRAWL) {
                    echo $content." saved";
                }
                $nbPostCrawled++;
            });
            $currentPage++;
        }
        // $request = new Request('GET', self::BASEURL);
        // $client = new Client(['base_uri' => self::BASEURL]);
        // $response = $client->send($request, ['timeout' => 5]);
        // $body = $response->getBody();

        // $crawler = new Crawler($body->getContents());

        // foreach ($crawler as $domElement) {
        //     var_dump($domElement->nodeName);
        // }
        // $htmlret = $crawler->html();
        // $this->logger->debug($htmlret);

        // $items = $crawler->filter('div[class="panel-body"]');
        // echo "\n found " . count($items) . " panel-body divs\n";
        }
}