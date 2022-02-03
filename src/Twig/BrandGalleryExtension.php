<?php

namespace App\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Filesystem;

class BrandGalleryExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getBrandGallery', [$this, 'getBrandGallery'], ['needs_environment'=> true, 'is_safe' => ['html']]),
        ];
    }

    public function getBrandGallery(Environment $twig, $brand = null)
    {
        $finder = new Finder();
        $filesystem = new Filesystem();
        if (is_null($brand)){
            $files = $this->getImagesFromGeneral();
            if(!is_null($files) and !empty($files)){
                return $twig->render('v2/extensions/brandGallery.html.twig', compact('files'));
            }else{
                return '';
            }
        }else {
            if($brand == 'Land Rover'){
                return  '';
            }

            if ($filesystem->exists($_SERVER['DOCUMENT_ROOT'] . '/img/brandGalleries/' . strtolower($brand))) {
                $finder->files()->name(['*.jpeg', '*.jpg', '*.png'])->in($_SERVER['DOCUMENT_ROOT'] . '/img/brandGalleries/' . strtolower($brand));
                $files = array();
                foreach ($finder as $file) {
                    $files[] = '/img/brandGalleries/' . strtolower($brand) . '/' . $file->getFilename();
                }
                if(empty($files)){
                    $files = $this->getImagesFromGeneral();
                }
            } else {
                $files = $this->getImagesFromGeneral();
            }
            if(!is_null($files) and !empty($files)){
                return $twig->render('v2/extensions/brandGallery.html.twig', compact('files'));
            }else{
                return '';
            }
        }



    }

    private function getImagesFromGeneral(){
        $finder = new Finder();
        $filesystem = new Filesystem();
        if($filesystem->exists($_SERVER['DOCUMENT_ROOT'].'/img/brandGalleries/general')){
            $finder->files()->name(['*.jpeg','*.jpg','*.png'])->in($_SERVER['DOCUMENT_ROOT'].'/img/brandGalleries/general');
            $files = array();
            foreach ($finder as $file){
                $files[] = '/img/brandGalleries/general/'.$file->getFilename();
            }
        }else{
            $files = null;
        }
        return $files;
    }
}
