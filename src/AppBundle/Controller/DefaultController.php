<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
     * @Route("/createpost", name="action")
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
        $from = $request->getQueryString();
        $this->logger->info("FROM: ".$from);

        $repo = $this->getDoctrine()->getRepository('AppBundle:Post');
        $data = $repo->createQueryBuilder('q')
                     ->getQuery()
                     ->getArrayResult();

    return new JsonResponse($data);
    }

}
