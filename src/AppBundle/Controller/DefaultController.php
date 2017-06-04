<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Entity\Post;
use Psr\Log\LoggerInterface;

class DefaultController extends Controller
{
    
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
    
    /**
    * @Route("/", name="homepage")
    */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
    /**
     * @Route("/api/createpost", name="action")
     */
    public function createPost(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        return $em->getRepository('AppBundle:Post')->createPost();
    }
    /**
     * @Route("/api/posts", name="allposts")
     * @Method({"GET"})
     */
    public function getPosts(Request $request)
    {
        $this->logger->info("entering getPosts");

        $from = $request->query->get('from');
        $to = $request->query->get('to');
        $author = $request->query->get('author');

        $this->logger->info("Queries: ".$author." ".$from." ".$to);

        // re-root queries to internal routes
        if(!is_null($author)) {
            return $this->forward(
                        $this->get('router')->match("/api/postsauthor")['_controller'],
                        array('request' => $request)
            );
        } elseif (!is_null($from) and !is_null($to)) {
            return $this->forward(
                        $this->get('router')->match("/api/postsfromto")['_controller'],
                        array('request' => $request)
            );
        }
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:Post');
        $this->logger->info("repo");
        $posts = $repo->createQueryBuilder('q')
                    ->getQuery()
                    ->getArrayResult();
        $this->logger->info("query");

        $format = 'Y-m-d H:i:s';
        for ($i=0; $i < count($posts); $i++) { 
            $posts[$i]['date'] = $posts[$i]['date']->format($format);
        }
        
        return new Response(json_encode(['posts' => $posts,  'count' => count($posts)]),200, array('Content-Type' => 'application/json'));
    }

    /**
     * @Route("/api/postsauthor", name="postsauthor")
     * @Method({"GET"})
     */
    public function getPostsFromAuthor(Request $request) 
    {
        $author = $request->query->get('author');
        $this->logger->info("getPostsFromAuthor: ".$author);
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->getPostsFromAuthor($author);
        $format = 'Y-m-d H:i:s';
        for ($i=0; $i < count($posts); $i++) { 
            $posts[$i]['date'] = $posts[$i]['date']->format($format);
        }
        
        return new Response(json_encode(['posts' => $posts,  'count' => count($posts)]),200, array('Content-Type' => 'application/json'));
   }
    /**
     * @Route("/api/postsfromto", name="postsfromto")
     * @Method({"GET"})
     */
    public function getPostsFromTo(Request $request) 
    {
        $from = $request->query->get('from');
        $to = $request->query->get('to');

        $this->logger->info("getPostsFromTo: from : ".$from." to : ".$to);
        $em = $this->getDoctrine()->getManager();
        $posts = $em->getRepository('AppBundle:Post')->getPostsFromTo($from,$to);
        $format = 'Y-m-d H:i:s';
        for ($i=0; $i < count($posts); $i++) { 
            $posts[$i]['date'] = $posts[$i]['date']->format($format);
        }
        
        return new Response(json_encode(['posts' => $posts,  'count' => count($posts)]),200, array('Content-Type' => 'application/json'));
   }
    /**
     * @Route("/api/posts/{id}", name="postid")
     * @Method({"GET"})
     */
    public function getPostsFromId($id)
    {
        $this->logger->info("getPostsFromId: ".$id);

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->getPostsFromId($id);

        $post[0]['date'] = $this->convertDate($post);

        return new Response(json_encode(['post' => $post[0]]),200, array('Content-Type' => 'application/json'));
    }
    /**
     * @Route("/api/truncate", name="cleardb")
     * @Method({"GET"})
     */
    public function truncateDB()
    {
        $this->logger->info("truncateDB: ");

        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository('AppBundle:Post')->truncateDB();

        return new Response("done");
    }

    private function convertDate($post) {
        $format = 'Y-m-d H:i:s';
        return $post[0]['date']->format($format);
    }
}
