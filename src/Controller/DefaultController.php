<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Form\SearchType;
use App\Repository\SearchRepository;
use Elastica\Processor\Json;
use Elastica\Query\Term;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;
use \Elastica\Query\MatchPhrase;
use \Elastica\Query\Range;
use \Elastica\Query\Match;
use \Elastica\Query\BoolQuery;

class DefaultController extends AbstractController
{

    private $cachePool;

    public function __construct(
        TagAwareAdapter $cachePool
    )
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @Route("/", name="app_homepage")
     * @param Request $request
     * @param RepositoryManagerInterface $repository
     * @param PaginatorInterface $paginator
     * @return Response
     */
    public function index(Request $request, RepositoryManagerInterface $repository, PaginatorInterface $paginator): Response
    {
//        $cache->clear();
        $term = trim($request->query->get('s', ''));
        $page = trim($request->query->getInt('page', 1));
        $pagination = $this->cachePool->get('product_' . md5($term . $page), function (ItemInterface $item) use ($term, $page, $repository, $paginator) {
            $item->tag('product_search');
            $search = $repository->getRepository(Product::class)->createPaginatorAdapter($term);
            $pagination = $paginator->paginate(
                $search, /* query NOT result */
                $page/*page number*/,
                9/*limit per page*/
            );
            $pagination->setCustomParameters([
                'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination)
            ]);
            return $pagination;
        });


        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/{id}", name="product_user_show", methods={"GET"}, requirements={"id":"\d+"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/search", name="advance_search", methods={"GET"})
     */
    public function advanceSearch(Request $request, RepositoryManagerInterface $repository, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(SearchType::class, null, ['csrf_protection' => false]);
        $form->handleRequest($request);
        $term = @json_encode($form->getData());
        $page = trim($request->query->getInt('page', 1));
        $boolQuery = new BoolQuery();
        if ($form->isSubmitted() && $form->isValid()) {
            $this->createSearchQuery($form, $boolQuery);
        }

        $pagination = $this->cachePool->get('product_' . md5($term . $page), function (ItemInterface $item) use ($form, $page, $repository, $paginator, $boolQuery) {
            $item->tag('product_search');

            $search = $repository->getRepository(Product::class)->createPaginatorAdapter($boolQuery);
            $pagination = $paginator->paginate(
                $search, /* query NOT result */
                $page/*page number*/,
                9/*limit per page*/
            );
            $pagination->setCustomParameters([
                'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination)
            ]);
            return $pagination;
        });

        return $this->render('default/search.html.twig', [
            'searchForm' => $form->createView(),
            'pagination' => $pagination,
            'totalResult' => $pagination->getTotalItemCount()
        ]);
    }


    /**
     * Create elastic search filter for search form
     *
     * @param FormInterface $form
     * @param BoolQuery $boolQuery
     * @return BoolQuery
     */
    private function createSearchQuery(FormInterface $form, BoolQuery $boolQuery): BoolQuery
    {
        // If items not null filter result by item value

        if ($form->get('title')->getData() != null) {
            $titleQuery = new MatchPhrase();
            $titleQuery->setFieldQuery('title', $form->get('title')->getData());
            $boolQuery->addMust($titleQuery);
        }

        if ($form->get('description')->getData() != null) {
            $descriptionQuery = new Match();
            $descriptionQuery->setFieldQuery('description', $form->get('description')->getData());
            $boolQuery->addMust($descriptionQuery);
        }

        if ($form->get('color')->getData() != null) {
            $colorQuery = new Term();
            $colorQuery->setTerm('variant.color', $form->get('color')->getData());
            $boolQuery->addShould($colorQuery);
        }
        $priceRange = [];
        if ($form->get('minPrice')->getData() != null) {
            $priceRange['gte'] = $form->get('minPrice')->getData();
        }
        if ($form->get('maxPrice')->getData() != null) {
            $priceRange['lte'] = $form->get('maxPrice')->getData();
        }
        if ($form->get('maxPrice')->getData() != null || $form->get('minPrice')->getData() != null) {
            $rangeQuery = new Range();
            $rangeQuery->addField('variant.price', $priceRange);
            $boolQuery->addShould($rangeQuery);
        }

        return $boolQuery;
    }
}
