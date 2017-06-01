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
    
    const BASEURL = 'http://www.viedemerde.fr/';


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


        $request = new Request('GET', self::BASEURL);
        $client = new Client(['base_uri' => self::BASEURL]);
        $response = $client->send($request, ['timeout' => 2]);
        $body = $response->getBody();
        $crawler = new Crawler((string)$body);

        foreach ($crawler as $domElement) {
            $this->logger->info($domElement->nodeName);
        }
        // echo $body;
    }
}