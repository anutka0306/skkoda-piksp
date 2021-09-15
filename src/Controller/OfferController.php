<?php

namespace App\Controller;

use App\Entity\SpecialOffer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\SpecialOfferRepository;
use App\Repository\ContentRepository;
use App\Repository\PriceBrandRepository;
use App\Repository\MenuTopRepository;

class OfferController extends AbstractController
{
    /**
     * @var SpecialOfferRepository
     */
    protected $offer_repository;

    /**
     * @var ContentRepository
     */
    protected $contentRepository;

    /**
     * @var PriceBrandRepository
     */
    protected $priceBrandRepository;

    /**
     * @var MenuTopRepository
     */
    protected $menuTopRepository;

    public function __construct(SpecialOfferRepository $offer_repository, ContentRepository $contentRepository, PriceBrandRepository $priceBrandRepository, MenuTopRepository $menuTopRepository)
    {
        $this->offer_repository = $offer_repository;
        $this->contentRepository = $contentRepository;
        $this->priceBrandRepository = $priceBrandRepository;
        $this->menuTopRepository = $menuTopRepository;
    }

    /**
     * @Route("/offers", name="offers")
     */
    public function index(): Response
    {
        $offers = $this->offer_repository->findBy(['published' => 1]);
        $page = $this->contentRepository->findOneBy(['path' => '/offers/']);
        $brands = $this->priceBrandRepository->findAll();
        $topMenu = $this->menuTopRepository->findAll();
        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
            'page' => $page,
            'brands' => $brands,
            'topMenu' => $topMenu,
        ]);
    }

    /**
     * @Route("/offer/{token}", name="dinamic_offers")
     */
    public function offer_item($token): Response{
        if ( !$offer = $this->offer_repository->findOneBy(['published'=>1, 'slug'=>$token])) {
            throw $this->createNotFoundException(sprintf('Offer %s not found',$token));
        }
        if($offer instanceof SpecialOffer){
           return $this->render('offer/offer.html.twig', ['offer'=>$offer]);
        }
    }
}
