<?php
// src/AppBundle/Command/AppCommand.php
namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Psr\Log\LoggerInterface;

use AppBundle\Utils\Parse;

class AppCommand extends ContainerAwareCommand
{
    private $logger;
    
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

        $posts = Parse::parseSite();

        //clean DB before inserting
        $entityManager->getRepository('AppBundle:Post')->truncateDB();

        $entityManager->getRepository('AppBundle:Post')->savePostsArray($posts);
    }
}