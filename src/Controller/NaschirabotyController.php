<?php

namespace App\Controller;

use App\Form\SalonFilterType;
use App\Repository\MenuTopRepository;
use  App\Repository\MenuLeftRepository;
use App\Repository\PriceBrandRepository;
use App\Service\SalonManager;
use App\Entity\Naschiraboty;
use App\Repository\ContentRepository;
use App\Repository\NaschirabotyRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ConfigRepository;
use App\Service\TranslateService;
use Knp\Component\Pager\PaginatorInterface;

class NaschirabotyController extends AbstractController
{
    /**
     * @var SalonManager
     */
    protected $salon_manager;

    /**
     * @var ConfigRepository
     */
    protected $configRepository;

    /**
     * @var TranslateService
     */
    protected $translateService;
    /**
     * @var NaschirabotyRepository
     */
    protected $naschirabotyRepository;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    public function __construct(SalonManager $salon_manager, ConfigRepository $configRepository, TranslateService $translateService, NaschirabotyRepository $naschirabotyRepository, PaginatorInterface $paginator)
    {
        $this->salon_manager = $salon_manager;
        $this->configRepository = $configRepository;
        $this->translateService = $translateService;
        $this->naschirabotyRepository = $naschirabotyRepository;
        $this->paginator = $paginator;
    }

    /**
     * @Route("/blog/", name="naschiraboty_index")
     * @param ContentRepository $content_repository
     * @param  NaschirabotyRepository $naschiraboty_repository
     * @param Request $request
     * @return Response
     */
    public function index( ContentRepository $content_repository, NaschirabotyRepository $naschiraboty_repository,Request $request, MenuTopRepository $menuTopRepository, MenuLeftRepository $menuLeftRepository, PriceBrandRepository $priceBrandRepository, ConfigRepository $configRepository): Response
    {
        $page = $content_repository->findOneByToken('blog');
        $works = $naschiraboty_repository->findAll();
        //$works = $naschiraboty_repository->findAll(array('id' => 'DESC'));
        krsort($works);
        
        $topMenu = $menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        $brands = $priceBrandRepository->findAll();

        $this->phone = $this->configRepository->findOneBy(['name' =>'phone']);
        $this->phone2 = $this->configRepository->findOneBy(['name' => 'phone2']);
        $this->address = $this->configRepository->findOneBy(['name' => 'address']);
        $this->address2 = $this->configRepository->findOneBy(['name'=> 'address2']);
        $this->phone3 = $this->configRepository->findOneBy(['name' =>'phone3']);
        $this->address3 = $this->configRepository->findOneBy(['name'=> 'address3']);
        $this->phone4 = $this->configRepository->findOneBy(['name' =>'phone4']);
        $this->address4 = $this->configRepository->findOneBy(['name'=> 'address4']);

        foreach ($works as $key => $value){
            $images = $value->getAttach();
            $value->images = $images;
            //$value->alias = $this->translateService->transliterate($value->getName());
        }

        $form = $this->createForm(
            SalonFilterType::class,
            null,
            ['method' => 'GET', 'priceBrand' => null]
        );
        $form->handleRequest($request);

        $availableSalons = $this->salon_manager->getSalonsByFilterForm($form, null);

        $pagination = $this->paginator->paginate(
            $works,
            $request->query->getInt('page', 1),
            8
        );
        return $this->render('v2/pages/naschiraboty/index.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'availableSalons' => $availableSalons,
            'works' => $works,
            'topMenu' => $topMenu,
            'leftMenu' =>$leftMenu,
            'brands' => $brands,
            'markServiceImgs' => [
                'japan' => ['Toyota','Infiniti','Lexus','Nissan','Mazda','Mitsubishi'],
                'china' => ['Chery','Geely','Haval'],
                'vag' => ['Audi','Bentley','Jaguar','Lamborghini','Land Rover','Porsche','Seat','Skoda','Volkswagen']
            ], // TODO надо из бд подтягивать
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'phone3' => $this->phone3,
            'address' => $this->address,
            'address2' => $this->address2,
            'address3' => $this->address3,
            'phone4' => $this->phone4,
            'address4' => $this->address4,
            'pagination' => $pagination,
            /*'hitspage' => $hitspage,*/
        ]);
    }

    /**
     * @Route("/blog/{alias}/", name="naschiraboty_item")
     * @param Naschiraboty $work
     * @param Request $request
     * @return Response
     */
    public function item(Naschiraboty $work, Request $request, PriceBrandRepository $priceBrandRepository, MenuTopRepository $menuTopRepository, MenuLeftRepository $menuLeftRepository, ConfigRepository $configRepository, ManagerRegistry $doctrine): Response
    {
        $images = $work->getAttach();
        $topMenu = $menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        $brands = $priceBrandRepository->findAll();

        $this->phone = $this->configRepository->findOneBy(['name' =>'phone']);
        $this->phone2 = $this->configRepository->findOneBy(['name' => 'phone2']);
        $this->address = $this->configRepository->findOneBy(['name' => 'address']);
        $this->address2 = $this->configRepository->findOneBy(['name'=> 'address2']);
        $this->phone3 = $this->configRepository->findOneBy(['name' =>'phone3']);
        $this->address3 = $this->configRepository->findOneBy(['name'=> 'address3']);
        $this->phone4 = $this->configRepository->findOneBy(['name' =>'phone4']);
        $this->address4 = $this->configRepository->findOneBy(['name'=> 'address4']);

        $entityManager = $doctrine->getManager();
        if (isset($_COOKIE[$work->getId() . 'HitsPage'])) {
        } else {
            $this->naschirabotyRepository->find($work->getId())->setHitsPage($work->getHitsPage()+1);
            setcookie($work->getId() . 'HitsPage', 'HitsPage', time() + (3600 * 12));
        }
        $entityManager->flush();
        $hitspage = $work->getHitsPage();

        $form = $this->createForm(
            SalonFilterType::class,
            null,
            ['method' => 'GET', 'priceBrand' => null]
        );
        $form->handleRequest($request);

        $availableSalons = $this->salon_manager->getSalonsByFilterForm($form, null);

        return $this->render('v2/pages/naschiraboty/item.html.twig', [
            'page' => $work,
            'item' => $work,
            'form' => $form->createView(),
            'availableSalons' => $availableSalons,
            'images' => $images,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'brands' => $brands,
            'markServiceImgs' => [
                'japan' => ['Toyota','Infiniti','Lexus','Nissan','Mazda','Mitsubishi'],
                'china' => ['Chery','Geely','Haval'],
                'vag' => ['Audi','Bentley','Jaguar','Lamborghini','Land Rover','Porsche','Seat','Skoda','Volkswagen']
            ], // TODO надо из бд подтягивать
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'phone3' => $this->phone3,
            'address' => $this->address,
            'address2' => $this->address2,
            'address3' => $this->address3,
            'phone4' => $this->phone4,
            'address4' => $this->address4,
            'hitspage' => $hitspage,
        ]);
    }
}
