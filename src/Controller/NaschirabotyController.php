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
use App\Repository\PriceCategoryRepository;

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

    /**
     * @var PriceCategoryRepository
     */
    protected $priceCategoryRepository;

    public function __construct(SalonManager $salon_manager,
                                ConfigRepository $configRepository,
                                TranslateService $translateService,
                                NaschirabotyRepository $naschirabotyRepository,
                                PaginatorInterface $paginator,
                                PriceCategoryRepository $priceCategoryRepository
    )
    {
        $this->salon_manager = $salon_manager;
        $this->configRepository = $configRepository;
        $this->translateService = $translateService;
        $this->naschirabotyRepository = $naschirabotyRepository;
        $this->paginator = $paginator;
        $this->priceCategoryRepository = $priceCategoryRepository;
    }

    /**
     * @Route("/blog/", name="naschiraboty_index")
     * @param ContentRepository $content_repository
     * @param  NaschirabotyRepository $naschiraboty_repository
     * @param Request $request
     * @return Response
     */
    public function index( ContentRepository $content_repository,
                           NaschirabotyRepository $naschiraboty_repository,
                           Request $request,
                           MenuTopRepository $menuTopRepository,
                           MenuLeftRepository $menuLeftRepository,
                           PriceBrandRepository $priceBrandRepository,
                           ConfigRepository $configRepository): Response
    {
        $page = $content_repository->findOneByToken('blog');
        $works = $naschiraboty_repository->findAll();
        krsort($works);
        
        $topMenu = $menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        $brands = $priceBrandRepository->findAll();


        foreach ($works as $key => $value){
            $images = $value->getAttach();
            $value->images = $images;
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
        $categories = $this->priceCategoryRepository->findAll();
        return $this->render('v2/pages/naschiraboty/index.html.twig', [
            'page' => $page,
            'form' => $form->createView(),
            'availableSalons' => $availableSalons,
            'works' => $works,
            'topMenu' => $topMenu,
            'leftMenu' =>$leftMenu,
            'brands' => $brands,
            'pagination' => $pagination,
            'categories' => $categories,
            'customH1' => 'Наши работы',
        ]);
    }

    /**
     * @Route("/blog/{alias}/", name="naschiraboty_item")
     * @param Naschiraboty $work
     * @param Request $request
     * @return Response
     */
    public function item(
        Naschiraboty $work,
        Request $request,
        PriceBrandRepository $priceBrandRepository,
        MenuTopRepository $menuTopRepository,
        MenuLeftRepository $menuLeftRepository,
        ConfigRepository $configRepository,
        ManagerRegistry $doctrine): Response
    {
        $images = $work->getAttach();
        $topMenu = $menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        $brands = $priceBrandRepository->findAll();

        $categories = $this->priceCategoryRepository->findAll();

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
            'hitspage' => $hitspage,
            'categories' => $categories,
            'customH1' => $work->getName(),
        ]);
    }
}
