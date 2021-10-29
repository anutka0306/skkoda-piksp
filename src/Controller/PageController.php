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

    protected $phone;

    /**
     * @var DiagnosticBrandRepository
     */
    protected $diagnosticBrandRepository;

    public function __construct(ContentRepository $repository, EntityManagerInterface $em, PaginatorInterface $paginator, PriceModelRepository $price_model_repository, PriceBrandRepository $priceBrandRepository, MenuTopRepository $menuTopRepository, MenuLeftRepository $menuLeftRepository, NaschirabotyRepository $naschirabotyRepository, ConfigRepository $configRepository, DiagnosticBrandRepository $diagnosticBrandRepository)
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
        $this->phone = $configRepository->findOneBy(['name' =>'phone']);
        $this->diagnosticBrandRepository = $diagnosticBrandRepository;

    }


    /**
     * @Route("/vakancies/{vakancy}", name="vakancy", requirements={"token"= "\/.+\/$"})
     */
    public function vakancy($vakancy, ContentRepository $repository):Response{
        $vakancy = '/vakancies/'.$vakancy.'/';
        if ( ! $page = $this->page_repository->findOnePublishedByToken($vakancy)) {
            throw $this->createNotFoundException(sprintf('Page %s not found',$vakancy));
        }
        return $this->render('v2/pages/vacansy/item.html.twig', [
            'page' => $page,
        ]);
    }
    
    /**
     * @Route("/{token}", name="dynamic_pages",requirements={"token"= ".+\/$"})
     */
    public function index($token, EntityManagerInterface $em, PaginatorInterface $paginator, Request $request, PriceModelRepository $priceModelRepository, PriceServiceRepository $priceServiceRepository, PriceBrandRepository $priceBrandRepository, MenuTopRepository $menuTopRepository, MenuLeftRepository $menuLeftRepository, NaschirabotyRepository $naschirabotyRepository, DiagnosticBrandRepository $diagnosticBrandRepository)
    {
        $topMenu = $menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        if ( ! $page = $this->page_repository->findOnePublishedByToken($token)) {
            throw $this->createNotFoundException(sprintf('Page %s not found',$token));
        }

        if ($page instanceof Brand) {
            return $this->brand($page, $priceModelRepository, $topMenu, $leftMenu, $naschirabotyRepository, $diagnosticBrandRepository);
        }

        if ($page instanceof Model) {
            return $this->model($page, $priceModelRepository, $priceServiceRepository, $topMenu, $leftMenu, $naschirabotyRepository, $diagnosticBrandRepository);
        }

        if ($page instanceof Service) {
            /* echo 'Page is '.$page;
            exit();*/
            return $this->service($page, $priceModelRepository, $topMenu, $leftMenu, $naschirabotyRepository, $priceServiceRepository, $diagnosticBrandRepository);
        }

        if ($page instanceof RootService) {
           /* echo 'Page is '.$page;
            exit();*/
            return $this->rootService($page, $priceBrandRepository, $topMenu, $leftMenu, $naschirabotyRepository);
        }

        if ($page instanceof Simple) {
            return $this->simple($page, $topMenu, $leftMenu);
        }

        if ($page instanceof Vacancy) {
            return $this->vacancy($page);
        }

        if($page instanceof ServiceWithout){
            return $this->service_without($page);
        }

        if($page instanceof Sitemap){
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
     * @param Sitemap $sitemap
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */


    private function service_without(ServiceWithout $service_without)
    {
        return $this->render('v2/pages/service_without.html.twig',[
           'page' => $service_without,
        ]);
    }
    
    private function brand(Brand $brand, PriceModelRepository $priceModelRepository, $topMenu, $leftMenu, NaschirabotyRepository $naschirabotyRepository, DiagnosticBrandRepository $diagnosticBrandRepository)
    {
        $brand_name = $brand->getBrandName();
        $models = $priceModelRepository->findBy(['priceBrand' => $brand->getBrandId()]);
        $work = $naschirabotyRepository->findBy(['model'=> $models], ['id' => 'DESC'], 1);
        $diagnostic = $diagnosticBrandRepository->findBy(['brand' => $brand->getPriceBrand()],[], 4 );
        if(empty($work)){
            $work = $naschirabotyRepository->findOneBy([],['id' =>'DESC']);
        }
        if($brand_name == 'Land Rover'){
            $this->phone = array('value'=>'+78129195913', 'title'=>'+7(812) 919-59-13');
        }
        return $this->render('v2/pages/brand.html.twig', [
            'page' => $brand,
            'brandName' => $brand_name,
            'models' => $models,
            'brandPath' => $brand->getPath(),
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'pageWork' => $work,
            'phone' => $this->phone,
            'diagnostic' => $diagnostic,
        ]);
    }
    
    
    private function model(Model $model, PriceModelRepository $priceModelRepository, PriceServiceRepository $priceServiceRepository, $topMenu, $leftMenu, NaschirabotyRepository $naschirabotyRepository, DiagnosticBrandRepository $diagnosticBrandRepository)
    {
        $brand_name = $model->getBrandName();
        $model_id = $model->getModelId();
        $work = $naschirabotyRepository->findOneBy(['model'=> $model_id], ['id' => 'DESC']);
        $diagnostic = $diagnosticBrandRepository->findBy(['brand' => $model->getPriceBrand()],[], 4 );
        if(empty($work)){
            $allBrandModels = $priceModelRepository->findBy(['priceBrand'=>$model->getBrandId()]);
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
        if($brand_name == 'Land Rover'){
            $this->phone = array('value'=>'+78129195913', 'title'=>'+7(812) 919-59-13');
        }
        return $this->render('v2/pages/model.html.twig', [
            'page' => $model,
            'brandName' => $brand_name,
            'modelName' => $model_name,
            'popularServices' => $popular_services,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'pageWork' => $work,
            'phone' => $this->phone,
            'diagnostic' => $diagnostic,
        ]);
    }
    
    private function service(Service $service, PriceModelRepository $priceModelRepository, $topMenu, $leftMenu, NaschirabotyRepository $naschirabotyRepository, PriceServiceRepository $priceServiceRepository, DiagnosticBrandRepository $diagnosticBrandRepository)
    {
        $popular_services = $priceServiceRepository->findBy(['is_popular' => 1, 'published'=> 1], [], 5);
        $brand_name = $service->getBrandName();
        $model_id = $service->getModelId();
        $diagnostic = $diagnosticBrandRepository->findBy(['brand' => $service->getPriceBrand()],[], 4 );


        if($model_id){
            $work = $naschirabotyRepository->findOneBy(['model' => $model_id, 'service'=> $service->getId()]);
            if(empty($work)){
                $work = $naschirabotyRepository->findOneBy(['service'=> $service->getId()]);
                if(empty($work)){
                    $work = $naschirabotyRepository->findOneBy(['model' => $model_id]);
                }
            }
           $model_name = $priceModelRepository->find($model_id)->getName();
        }else{
            $work = $naschirabotyRepository->findOneBy(['service'=> $service->getId()]);
            if(empty($work)){
                $models = $priceModelRepository->findBy(['priceBrand' => $service->getBrandId()]);
                $work = $naschirabotyRepository->findBy(['model'=> $models], ['id' => 'DESC'], 1);
                if(empty($work)){
                    $work = $naschirabotyRepository->findOneBy([],['id' =>'DESC']);
                }
            }
            $model_name = null;
        }
        $services = $this->page_repository->findOneBy(['path' => '/'.$service->getPriceCategory()->getSlug().'/']);
        $service->setName(str_replace([$brand_name.' '.$model_name, 'в Москве'], ['', ''], $service->getName() ));

        if($brand_name == 'Land Rover'){
            $this->phone = array('value'=>'+78129195913', 'title'=>'+7(812) 919-59-13');
        }

        return $this->render('v2/pages/service.html.twig', [
            'page' => $service,
            'brandName' => $brand_name,
            'modelName' => $model_name,
            'parentService' => $services,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'pageWork' => $work,
            'popularServices' => $popular_services,
            'phone' => $this->phone,
            'diagnostic' => $diagnostic,
        ]);
    }
    
    private function rootService(RootService $rootService, PriceBrandRepository $priceBrandRepository, $topMenu, $leftMenu, NaschirabotyRepository $naschirabotyRepository)
    {
        if(is_null($rootService->getAdvIcon1())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvIcon1() !== null) {
                $rootService->setAdvIcon1($rootService->getParent()->getAdvIcon1());
            }
        }
        if(is_null($rootService->getAdvIcon2())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvIcon2() !== null) {
                $rootService->setAdvIcon2($rootService->getParent()->getAdvIcon2());
            }
        }
        if(is_null($rootService->getAdvIcon3())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvIcon3() !== null) {
                $rootService->setAdvIcon3($rootService->getParent()->getAdvIcon3());
            }
        }
        if(is_null($rootService->getAdvIcon4())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvIcon4() !== null) {
                $rootService->setAdvIcon4($rootService->getParent()->getAdvIcon4());
            }
        }
        if(is_null($rootService->getAdvText1())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvText1() !== null) {
                $rootService->setAdvText1($rootService->getParent()->getAdvText1());
            }
        }
        if(is_null($rootService->getAdvText2())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvText2() !== null) {
                $rootService->setAdvText2($rootService->getParent()->getAdvText2());
            }
        }
        if(is_null($rootService->getAdvText3())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvText3() !== null) {
                $rootService->setAdvText3($rootService->getParent()->getAdvText3());
            }
        }
        if(is_null($rootService->getAdvText4())) {
            if ($rootService->getParent() !== null && $rootService->getParent()->getAdvText4() !== null) {
                $rootService->setAdvText4($rootService->getParent()->getAdvText4());
            }
        }


            $work = $naschirabotyRepository->findOneBy([],['id' =>'DESC']);


        $brands = $priceBrandRepository->findAll();

        return $this->render('v2/pages/root-service.html.twig', [
            'page' => $rootService,
            'brands' => $brands,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'pageWork' => $work,
            'phone' => $this->phone,
        ]);
    }
    
    private function simple(Simple $simple, $topMenu, $leftMenu)
    {
        return $this->render('v2/pages/simple.html.twig', [
            'page' => $simple,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'phone' => $this->phone,
        ]);
    }
    
    private function vacancy(Vacancy $vacancy)
    {
        return $this->render('v2/pages/vacansy/index.html.twig', [
            'page' => $vacancy,
        ]);
    }


}
