<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ContentRepository;
use App\Repository\MenuTopRepository;
use App\Repository\MenuLeftRepository;
use App\Repository\PriceBrandRepository;
use App\Repository\ConfigRepository;
use App\Repository\PriceCategoryRepository;

class ContactController extends AbstractController
{
    /**
     * @var PriceBrandRepository
     */
    protected $priceBrandRepository;
    /**
     * @var ContentRepository
     */
    protected $page_repository;

    /**
     * @var MenuTopRepository
     */
    protected $menuTopRepository;

    /**
     * @var MenuLeftRepository
     */
    protected $menuLeftRepository;

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var PriceCategoryRepository
     */
    protected $priceCategoryRepository;

    public function __construct(ContentRepository $repository,
                                MenuTopRepository $menuTopRepository,
                                MenuLeftRepository $menuLeftRepository,
                                PriceBrandRepository $priceBrandRepository,
                                ConfigRepository $configRepository,
                                PriceCategoryRepository $priceCategoryRepository
    )
    {
        $this->page_repository = $repository;
        $this->menuTopRepository = $menuTopRepository;
        $this->menuLeftRepository = $menuLeftRepository;
        $this->priceBrandRepository = $priceBrandRepository;
        $this->configRepository = $configRepository;
        $this->priceCategoryRepository = $priceCategoryRepository;
    }

    /**
     * @Route("/contacts/", name="contacts")
     */
    public function index(): Response
    {
        if(! $page = $this->page_repository->findOneBy(['path'=>'/contacts/'])){
            throw $this->createNotFoundException('Page /contacts/ not found');
        }
        $topMenu = $this->menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $this->menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        $categories = $this->priceCategoryRepository->findAll();


        return $this->render('v2/pages/contact/index.html.twig', [
            'page' => $page,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'categories' => $categories,
            'customH1' => 'Контакты'

        ]);
    }
}
