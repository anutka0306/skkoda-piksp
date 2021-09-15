<?php

namespace App\Controller;

use App\Repository\ContentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use App\Repository\PriceBrandRepository;
use App\Entity\MenuTop;
use App\Repository\MenuTopRepository;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(ContentRepository $repository, PriceBrandRepository $priceBrandRepository, MenuTopRepository $menuTopRepository)
    {
        $page = $repository->findOneBy(['path'=>'/']);
        $brands = $priceBrandRepository->findAll();
        $gallery = $this->getGalleryImages();
        $topMenu = $menuTopRepository->findAll();
        
        return $this->render('v2/pages/home/index.html.twig', [
            'page' => $page,
            'gallery'=> $gallery,
            'brands' => $brands,
            'topMenu' => $topMenu,
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
