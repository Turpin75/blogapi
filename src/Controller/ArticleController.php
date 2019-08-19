<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use App\ArticlesRepresentation\Articles;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Swagger\Annotations as SWG;

class ArticleController extends AbstractFOSRestController
{
    private $manager;
    private $articleRepo;
    private $serializer;
    
    public function __construct(ObjectManager $manager, ArticleRepository $articleRepo, 
                                    SerializerInterface $serializer)
    {
        $this->manager = $manager;
        $this->articleRepo = $articleRepo;
        $this->serializer = $serializer;
    }
    
    /**
     * @Rest\Get("/articles/{id}", name="articles_show", requirements={"id"="\d+"})
     * @Rest\View(statusCode=202)
     * 
     * @SWG\Response(
     *  response=200,
     *  description="Get one article"
     * )
     * 
     * @SWG\Parameter(
     *  name="id",
     *  in="query",
     *  type="integer",
     *  description="The article unique identifier"
     * )
     * 
     */
    public function show(Article $article)
    {   
        return $article;
    }

    /**
     * @Rest\Get("/articles", name="articles_list")
     * @Rest\QueryParam(name="term", requirements={"[a-zA-Z0-9]"}, nullable=true, 
     *  description="The term to search for")
     * @Rest\QueryParam(name="order", requirements={"ASC|DESC"}, default="ASC", description="Sort order")
     * @Rest\QueryParam(name="offset", requirements={"\d+"}, default="1", 
     *  description="The pagination offset")
     * @Rest\QueryParam(name="limit", requirements={"\d+"}, default="10", 
     *  description="Max number of elements per page")
     * @Rest\View()
     * 
     *@SWG\Response(
     *     response=200,
     *     description="Returns the list of artciles"
     * )
     */
    public function list($term, $order, $offset, $limit)
    {
        $pager = $this->articleRepo->search($term, $order, $offset, $limit);

        return new Articles($pager);
    }

    /**
     * @Rest\Post("/articles", name ="articles_create")
     * @ParamConverter("article", converter="fos_rest.request_body")
     * @Rest\View(statusCode=201)
     */
    public function create(Article $article, ConstraintViolationListInterface $validationErrors)
    {
        if(count($validationErrors) > 0){
            return $this->view($validationErrors, Response::HTTP_BAD_REQUEST);
        }
        
        $this->manager->persist(($article));
        $this->manager->flush();

        return $article;
    }

    /**
     * @Rest\Put("/articles/{id}", name="articles_update", requirements={"id"="\d+"})
     * @ParamConverter("articleUpdated", converter="fos_rest.request_body")
     * @Rest\View()
     */
    public function update(Article $articleUpdated, Article $article,
     ConstraintViolationListInterface $validationErrors)
    {
        if(count($validationErrors) > 0){
            return $this->view($validationErrors, Response::HTTP_BAD_REQUEST);
        }

        $article->setTitle($articleUpdated->getTitle())
                ->setContent($articleUpdated->getContent());

        $this->manager->flush();

        return $article;

    }

    /**
     * @Rest\Delete("/articles/{id}", name="articles_delete", requirements={"id"="\d+"})
     * @Rest\View()
     */
    public function delete(Article $article)
    {
        $this->manager->remove($article);
        $this->manager->flush();

        $message = ["code" => 200, "message" => "Article deleted !"];

        return $message;
    }
}
