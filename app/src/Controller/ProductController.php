<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/product")
 */
class ProductController extends BaseController
{
    private $cachePool;

    public function __construct(
        TagAwareAdapter $cachePool
    )
    {
        $this->cachePool = $cachePool;
    }

    /**
     * @Route("/", name="product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $pagination = $paginator->paginate(
            $productRepository->getWithOrderQueryBuilder(), /* query NOT result */
            $request->query->getInt('page', 1)/*page number*/,
            10/*limit per page*/
        );
        $pagination->setCustomParameters([
            'align' => 'center', # center|right (for template: twitter_bootstrap_v4_pagination)
        ]);
        return $this->render('product/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/new", name="product_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            $this->cachePool->invalidateTags(['product_search']);
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET","POST"}, requirements={"id":"\d+"})
     */
    public function edit(int $id, Request $request, EntityManagerInterface $entityManager, TranslatorInterface $translator): Response
    {
        if (($product = $entityManager->getRepository(Product::class)->find($id)) === null) {
            throw $this->createNotFoundException($translator->trans('No product found for id: 0', [$id]));
        }

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->cachePool->invalidateTags(['product_search']);
            return $this->redirectToRoute('product_index');
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
            $this->cachePool->invalidateTags(['product_search']);
        }

        return $this->redirectToRoute('product_index');
    }
}
