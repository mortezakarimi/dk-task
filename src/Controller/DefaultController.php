<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\SearchRepository;
use FOS\ElasticaBundle\Manager\RepositoryManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function index(Request $request, RepositoryManagerInterface $repository, AdapterInterface $cache, PaginatorInterface $paginator)
    {

//        $cache->clear();
        $term = trim($request->query->get('s', ''));
        $page = trim($request->query->getInt('page', 1));
        $item = $cache->getItem('product_' . md5($term . $page));
        if (!$item->isHit()) {
            $search = $repository->getRepository(Product::class)->createPaginatorAdapter($term);
            $pagination = $paginator->paginate(
                $search, /* query NOT result */
                $page/*page number*/,
                9/*limit per page*/
            );
            $item->set($pagination);
            $cache->save($item);
        }
        $pagination = $item->get();
        $pagination->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination)
        ]);
//        var_dump($pagination);
//        return new JsonResponse($pagination);

        return $this->render('default/index.html.twig', [
            'controller_name' => 'DefaultController',
            'pagination' => $pagination,
        ]);
    }
}
