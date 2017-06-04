<?php
// src/AppBundle/Command/AppCommand.php
namespace AppBundle\Utils;

use Psr\Log\LoggerInterface;

use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Client;
use AppBundle\Entity\Post;

class Parse
{
    const BASEURL = 'http://www.viedemerde.fr/?page=';
    const POST2CRAWL = 200;
    const MAXPAGE = 12;

    public static function parseSite() {
        $nbPostCrawled = 0;
        $currentPage = 1;
        $postsArray = array();

        $months = array('01' => 'janvier', '02' => 'février', '03' => 'mars', '04' => 'avril', '05' => 'mai', 
                        '06' => 'juin', '07' => 'juillet', '08' => 'août', '09' => 'septembre', '10' => 'octobre',
                        '11' => 'novembre', '12' => 'décembre');

        while($nbPostCrawled < self::POST2CRAWL and $currentPage <=self::MAXPAGE) {
            $currentUrl = self::BASEURL.$currentPage;

            $request = new Request('GET', $currentUrl);
            $client = new Client(['base_uri' => $currentUrl]);
            $response = $client->send($request, ['timeout' => 10]);
            $crawler = new Crawler();
            $crawler->addHtmlContent((string)$response->getBody()->getContents());

            $post = $crawler->filter('div.panel-body')->each(function ($node) use( &$nbPostCrawled, $months){
                try {
                    $content = trim($node->filter('div.panel-content > p.block > a[href^="/article"]')->text());
                    $meta = $node->filter('div.text-center')->text();

                    if(!empty($content) && !empty($meta)) {
                        $pattern = '/^\s*Par (.*) [-] *\/\s\w* (\d{1,2}) ([\wé]*) (\d{4}) (\d{2}):(\d{2}) \/\s*.*/';
                        preg_match($pattern, $meta, $keywords);
                        if(!empty($keywords)) {
                            $date = $keywords[4]."-".array_search($keywords[3],$months)."-".$keywords[2]." ".$keywords[5].':'.$keywords[6].':00';
                            $author = $keywords[1];

                            $post = new Post();
                            $post->setContent(trim($content));
                            $post->setDate(new \DateTime($date));
                            $post->setAuthor($author);
                            $nbPostCrawled++;
                            return $post;
                        }
                    }
                } catch (\InvalidArgumentException $e) {
                    return;
                }
            });
            $post = array_filter($post, function($var){return !is_null($var);} );
            
            $postsArray = array_merge($postsArray, $post);
            $currentPage++;
        }
        return array_slice($postsArray, 0, self::POST2CRAWL);
    }
}