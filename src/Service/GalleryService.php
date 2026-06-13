<?php


namespace App\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class GalleryService
{

    private string $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function getImages(string $folder): array
    {
        $path = $this->projectDir . '/public/v3/assets/gallery/' . $folder;

        $finder = Finder::create()
            ->files()
            ->in($path)
            ->name(['*.jpg', '*.jpeg', '*.png', '*.webp']);

        $images = [];

        foreach ($finder as $file) {
            $images[] = '/v3/assets/gallery/' . $folder . '/' . $file->getFilename();
        }

        natsort($images);

        return array_values($images);
    }


}