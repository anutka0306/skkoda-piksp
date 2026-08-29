<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Model;
use App\Entity\RootService;
use App\Entity\Service;
use App\Entity\Simple;
use App\Entity\Vacancy;
use App\Entity\ServiceWithout;
use App\Repository\ContentRepository;
use App\Repository\PriceBrandRepository;
use App\Repository\PriceServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Sitemap;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Repository\ModelRepository;
use App\Repository\PriceModelRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use App\Entity\MenuTop;
use App\Repository\MenuTopRepository;
use App\Repository\MenuLeftRepository;
use App\Repository\NaschirabotyRepository;
use App\Repository\ConfigRepository;
use App\Repository\DiagnosticBrandRepository;
use App\Repository\PriceCategoryRepository;
use App\Repository\SpecialOfferRepository;
use App\Service\GalleryService;


class PageController extends AbstractController
{
    /**
     * @var NaschirabotyRepository
     */
    protected $naschirabotyRepository;
    /**
     * @var ContentRepository
     */
    protected $page_repository;
    /**
     * @var EntityManagerInterface
     */
    protected $em;
    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var PriceModelRepository
     */

    protected $price_model_repository;

    /**
     * @var PriceBrandRepository
     */
    protected $priceBrandRepository;

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
     * @var \App\Entity\Config|null
     */
    protected $phone;
    /**
     * @var \App\Entity\Config|null
     */
    protected  $phone2;
    /**
     * @var \App\Entity\Config|null
     */
    protected $address;
    /**
     * @var \App\Entity\Config|null
     */
    protected $address2;
    /**
     * @var \App\Entity\Config|null
     */
    protected  $phone3;
    /**
     * @var \App\Entity\Config|null
     */
    protected $address3;
    /**
     * @var \App\Entity\Config|null
     */
    protected $phone4;
    /**
     * @var \App\Entity\Config|null
     */
    protected $address4;

    /**
     * @var DiagnosticBrandRepository
     */
    protected $diagnosticBrandRepository;

    /**
     * @var PriceCategoryRepository
     */
    protected $priceCategoryRepository;

    /**
     * @var SpecialOfferRepository
     */
    protected $specialOfferRepository;

    protected $galleryService;

    /**
     * PageController constructor.
     * @param ContentRepository $repository
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param PriceModelRepository $price_model_repository
     * @param PriceBrandRepository $priceBrandRepository
     * @param MenuTopRepository $menuTopRepository
     * @param MenuLeftRepository $menuLeftRepository
     * @param NaschirabotyRepository $naschirabotyRepository
     * @param ConfigRepository $configRepository
     * @param DiagnosticBrandRepository $diagnosticBrandRepository
     */
    public function __construct(ContentRepository $repository,
                                EntityManagerInterface $em,
                                PaginatorInterface $paginator,
                                PriceModelRepository $price_model_repository,
                                PriceBrandRepository $priceBrandRepository,
                                MenuTopRepository $menuTopRepository,
                                MenuLeftRepository $menuLeftRepository,
                                NaschirabotyRepository $naschirabotyRepository,
                                ConfigRepository $configRepository,
                                DiagnosticBrandRepository $diagnosticBrandRepository,
                                PriceCategoryRepository $priceCategoryRepository,
                                SpecialOfferRepository $specialOfferRepository,
                                GalleryService $galleryService
    )
    {
        $this->page_repository = $repository;
        $this->em = $em;
        $this->paginator = $paginator;
        $this->price_model_repository = $price_model_repository;
        $this->priceBrandRepository = $priceBrandRepository;
        $this->menuTopRepository = $menuTopRepository;
        $this->menuLeftRepository = $menuLeftRepository;
        $this->naschirabotyRepository = $naschirabotyRepository;
        $this->configRepository = $configRepository;
        $this->diagnosticBrandRepository = $diagnosticBrandRepository;
        $this->priceCategoryRepository = $priceCategoryRepository;
        $this->specialOfferRepository = $specialOfferRepository;
        $this->galleryService = $galleryService;

    }


    
    /**
     * @Route("/{token}", name="dynamic_pages",requirements={"token"= ".+\/$"})
     */
    public function index($token,
                          EntityManagerInterface $em,
                          PaginatorInterface $paginator,
                          Request $request,
                          PriceModelRepository $priceModelRepository,
                          PriceServiceRepository $priceServiceRepository,
                          PriceBrandRepository $priceBrandRepository,
                          MenuTopRepository $menuTopRepository,
                          MenuLeftRepository $menuLeftRepository,
                          NaschirabotyRepository $naschirabotyRepository,
                          DiagnosticBrandRepository $diagnosticBrandRepository,
                          PriceCategoryRepository $priceCategoryRepository

    )
    {
        $topMenu = $menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        if ( ! $page = $this->page_repository->findOnePublishedByToken($token)) {
            throw $this->createNotFoundException(sprintf('Page %s not found',$token));
        }


        if ($page instanceof Model) {
            return $this->model($page,
                $priceModelRepository,
                $priceServiceRepository,
                $topMenu,
                $leftMenu,
                $naschirabotyRepository,
                $diagnosticBrandRepository,
                $priceCategoryRepository,

            );
        }

        if ($page instanceof Service) {

            return $this->service($page,
                $priceModelRepository,
                $topMenu,
                $leftMenu,
                $naschirabotyRepository,
                $priceServiceRepository,
                $diagnosticBrandRepository,
                $priceCategoryRepository
            );
        }

        if ($page instanceof RootService) {

            return $this->rootService($page,
                $priceBrandRepository,
                $topMenu,
                $leftMenu,
                $naschirabotyRepository,
                $priceModelRepository,
                $priceCategoryRepository
            );
        }

        if ($page instanceof Simple) {
            return $this->simple($page,
                $topMenu,
                $leftMenu,
                $priceCategoryRepository);
        }


        if($page instanceof ServiceWithout){
            return $this->service_without($page);
        }

        if($page instanceof Sitemap){
            // Карта сайта для людей отключена 17.08.2026 по решению заказчика:
            // страница собиралась старым шаблоном другого сайта сети (чужой логотип и телефоны).
            // Чтобы вернуть — убрать строку ниже.
            throw $this->createNotFoundException('Карта сайта для людей отключена');

            $query = $em->createQuery("SELECT a FROM App\Entity\Content as a WHERE a.published = 1 ORDER BY a.id");
            $pagination = $paginator->paginate(
                $query, /* query NOT result */
                $request->query->getInt('page', 1), /*page number*/
                100 /*limit per page*/
            );

            // parameters to template
            return $this->render('sitemap/index.html.twig', ['pagination' => $pagination,'page'=>$page, 'topMenu'=>$topMenu, 'leftMenu'=>$leftMenu]);
        }

        throw $this->createNotFoundException('Page is instance of '.get_class($page));
    }


    /**
     * @param ServiceWithout $service_without
     * @return Response
     */
    private function service_without(ServiceWithout $service_without)
    {
        return $this->render('v2/pages/service_without.html.twig',[
           'page' => $service_without,
        ]);
    }


    /**
     * @param Model $model
     * @param PriceModelRepository $priceModelRepository
     * @param PriceServiceRepository $priceServiceRepository
     * @param $topMenu
     * @param $leftMenu
     * @param NaschirabotyRepository $naschirabotyRepository
     * @param DiagnosticBrandRepository $diagnosticBrandRepository
     * @return Response
     */
    private function model(Model $model,
                           PriceModelRepository $priceModelRepository,
                           PriceServiceRepository $priceServiceRepository,
                           $topMenu,
                           $leftMenu,
                           NaschirabotyRepository $naschirabotyRepository,
                           DiagnosticBrandRepository $diagnosticBrandRepository,
                           PriceCategoryRepository $priceCategoryRepository
    )
    {
        $brand_name = $model->getBrandName();
        $model_id = $model->getModelId();
        $work = $naschirabotyRepository->findOneBy(['model'=> $model_id], ['id' => 'DESC']);
        $diagnostic = $diagnosticBrandRepository->findBy(['brand' => $model->getPriceBrand()],[], 4 );
        if(empty($work)){
            $allBrandModels = $priceModelRepository->findBy(['priceBrand'=>$model->getPriceBrand()->getId()]);
            $work = $naschirabotyRepository->findBy(['model'=> $allBrandModels], ['id' => 'DESC'], 1);
        }
        if(empty($work)){
            $work = $naschirabotyRepository->findOneBy([],['id' =>'DESC']);
        }
        $popular_services = $priceServiceRepository->findBy(['is_popular' => 1, 'published'=> 1], [], 5);
        if($model_id){
            $model_name = $priceModelRepository->find($model_id)->getName();
        }else{
            $model_name = null;
        }

        $contactTitle = $model->getBrandName();


        $categories = $priceCategoryRepository->findAll();
        $offers = $this->specialOfferRepository->findBy(['published' => 1]);
        $gallery = $this->galleryService->getImages('1');


        $context = [
            'page' => $model,
            'brandName' => $brand_name,
            'modelName' => $model_name,
            'popularServices' => $popular_services,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'pageWork' => $work,
            'contactTitle'=>$contactTitle,
            'diagnostic' => $diagnostic,
            'categories' => $categories,
            'offers' => $offers,
            'gallery' => $gallery,

        ];

        return $this->render('v2/pages/model.html.twig', $context);

    }


    /**
     * @param Service $service
     * @param PriceModelRepository $priceModelRepository
     * @param $topMenu
     * @param $leftMenu
     * @param NaschirabotyRepository $naschirabotyRepository
     * @param PriceServiceRepository $priceServiceRepository
     * @param DiagnosticBrandRepository $diagnosticBrandRepository
     * @return Response
     */
        private function service(Service $service,
                                 PriceModelRepository $priceModelRepository,
                                 $topMenu,
                                 $leftMenu,
                                 NaschirabotyRepository $naschirabotyRepository,
                                 PriceServiceRepository $priceServiceRepository,
                                 DiagnosticBrandRepository $diagnosticBrandRepository,
                                 PriceCategoryRepository $priceCategoryRepository
        )
    {
        $popular_services = $priceServiceRepository->findBy(['is_popular' => 1, 'published' => 1], [], 5);
        $brand_name = $service->getBrandName();
        $model_id = $service->getModelId();
        $brand_id = $service->getPriceBrand()
            ? $service->getPriceBrand()->getId()
            : null;
        $diagnostic = $diagnosticBrandRepository->findBy(['brand' => $service->getPriceBrand()], [], 4);

        if ($model_id) {
            $allBrandModels = $priceModelRepository->findBy(['priceBrand'=>$service->getPriceModel()->getPriceBrand()->getId()]);
            $work = $naschirabotyRepository->findOneBy(['model' => $model_id, 'service' => $service->getId()]);
            if (empty($work)) {
                $work = $naschirabotyRepository->findBy(['model'=> $allBrandModels], ['id' => 'DESC'], 1);
                if (empty($work)) {
                    $work = $naschirabotyRepository->findOneBy(['model' => $model_id]);
                }
            }
            $model_name = $priceModelRepository->find($model_id)->getName();
        }

        elseif ($brand_id){
            $allBrandModels = $priceModelRepository->findBy(['priceBrand'=>$service->getPriceBrand()->getId()]);
            $work = $naschirabotyRepository->findBy(['model'=> $allBrandModels], ['id' => 'DESC'], 1);
            if (empty($work)) {
                $work = $naschirabotyRepository->findOneBy(['service' => $service->getId()]);
            }
            $model_name = null;
        }
        else {
            $work = $naschirabotyRepository->findOneBy(['service' => $service->getId()]);
            if (empty($work)) {
                $models = $priceModelRepository->findBy(['priceBrand' => $service->getBrandId()]);
                $work = $naschirabotyRepository->findBy(['model' => $models], ['id' => 'DESC'], 1);
                if (empty($work)) {
                    $work = $naschirabotyRepository->findOneBy([], ['id' => 'DESC']);
                }
            }
            $model_name = null;
        }
        $services = $this->page_repository->findOneBy(['path' => '/' . $service->getPriceCategory()->getSlug() . '/']);

        $contactTitle = $brand_name;


        $categories = $priceCategoryRepository->findAll();

        $offers = $this->specialOfferRepository->findBy(['published' => 1]);
        $gallery = $this->galleryService->getImages('1');

        $context = [
            'page' => $service,
            'brandName' => $brand_name,
            'modelName' => $model_name,
            'parentService' => $services,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'pageWork' => $work,
            'popularServices' => $popular_services,
            'contactTitle' => $contactTitle,
            'diagnostic' => $diagnostic,
            'categories' => $categories,
            'serviceName' => $service->getName(),
            'offers' => $offers,
            'gallery' => $gallery,
        ];


        return $this->render('v2/pages/service.html.twig', $context);

        }


    /**
     * @param RootService $rootService
     * @param PriceBrandRepository $priceBrandRepository
     * @param $topMenu
     * @param $leftMenu
     * @param NaschirabotyRepository $naschirabotyRepository
     * @param PriceModelRepository $priceModelRepository
     * @return Response
     */
        private function rootService(RootService $rootService,
                                     PriceBrandRepository $priceBrandRepository,
                                     $topMenu, $leftMenu,
                                     NaschirabotyRepository $naschirabotyRepository,
                                     PriceModelRepository $priceModelRepository,
                                     PriceCategoryRepository $priceCategoryRepository
        )
        {

            $work = $naschirabotyRepository->findOneBy([], ['id' => 'DESC']);


            $models = $priceModelRepository->findAll();

            $brand = $priceBrandRepository->findOneBy(['name' => 'Skoda']);

            $categories = $priceCategoryRepository->findAll();

            $offers = $this->specialOfferRepository->findBy(['published' => 1]);
            $gallery = $this->galleryService->getImages('1');

            return $this->render('v2/pages/root-service.html.twig', [
                'page' => $rootService,
                'models' => $models,
                'brand' => $brand,
                'topMenu' => $topMenu,
                'leftMenu' => $leftMenu,
                'pageWork' => $work,
                'categories' => $categories,
                'serviceName' => $rootService->getName(),
                'offers' => $offers,
                'gallery' => $gallery,
            ]);
        }

    /**
     * @param Simple $simple
     * @param $topMenu
     * @param $leftMenu
     * @return Response
     */
    private function simple(Simple $simple,
                            $topMenu,
                            $leftMenu,
                            PriceCategoryRepository $priceCategoryRepository
    )
    {
        return $this->render('v2/pages/simple.html.twig', [
            'categories' => $priceCategoryRepository->findAll(),
            'page' => $simple,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'phone3' => $this->phone3,
            'address' => $this->address,
            'address2' => $this->address2,
            'address3' => $this->address3,
            'phone4' => $this->phone4,
            'address4' => $this->address4,

        ]);
    }




}
