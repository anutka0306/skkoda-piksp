<?php

namespace App\Controller;

use App\Repository\ContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use App\Repository\PriceBrandRepository;
// use App\Repository\BrandRepository;
// use App\Entity\PriceBrand;
use App\Entity\MenuTop;
use App\Repository\MenuTopRepository;
use App\Repository\MenuLeftRepository;
use App\Repository\NaschirabotyRepository;
use App\Repository\ConfigRepository;
use App\Repository\PriceModelRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(
        ContentRepository $repository,
        PriceBrandRepository $priceBrandRepository,
        MenuTopRepository $menuTopRepository,
        MenuLeftRepository $menuLeftRepository,
        NaschirabotyRepository $naschirabotyRepository,
        ConfigRepository $configRepository,
        PriceModelRepository $priceModelRepository
    )
    {
        $page = $repository->findOneBy(['path'=>'/']);
        $brand = $priceBrandRepository->findOneBy(['name' => 'Skoda']);

        $models = $priceModelRepository->findAll();
        $gallery = $this->getGalleryImages();
        $topMenu = $menuTopRepository->findBy([], ['ordering'=>'ASC']);
        $leftMenu = $menuLeftRepository->findBy([], ['ordering'=>'ASC']);
        $work = $naschirabotyRepository->findOneBy([],['id' =>'DESC']);
        $this->phone = $configRepository->findOneBy(['name' =>'phone']);
        $this->phone2 = $configRepository->findOneBy(['name' => 'phone2']);
        $this->address = $configRepository->findOneBy(['name' => 'address']);
        $this->address2 = $configRepository->findOneBy(['name'=> 'address2']);
        $this->phone3 = $configRepository->findOneBy(['name' => 'phone3']);
        $this->address3 = $configRepository->findOneBy(['name'=> 'address3']);
        $this->phone4 = $configRepository->findOneBy(['name' => 'phone4']);
        $this->address4 = $configRepository->findOneBy(['name'=> 'address4']);
        
        return $this->render('v2/pages/home/index.html.twig', [
            'page' => $page,
            'gallery'=> $gallery,
            'models' => $models,
            'topMenu' => $topMenu,
            'leftMenu' => $leftMenu,
            'pageWork' => $work,
            'phone' => $this->phone,
            'phone2' => $this->phone2,
            'address' => $this->address,
            'address2' => $this->address2,
            'phone3' => $this->phone3,
            'address3' => $this->address3,
            'phone4' => $this->phone4,
            'address4' => $this->address4,
        ]);
    }


    private function getGalleryImages(){
        $finder = new Finder();
        $filesystem = new Filesystem();
        if($filesystem->exists($_SERVER['DOCUMENT_ROOT'].'/images/gallery')){
            $finder->files()->name(['*.jpeg','*.jpg','*.png'])->in($_SERVER['DOCUMENT_ROOT'].'/images/gallery');
            $files = array();
            foreach ($finder as $file){
                $files[] = '/images/gallery/'.$file->getFilename();
            }
        }else{
            $files = null;
        }


        return $files;
    }

}
