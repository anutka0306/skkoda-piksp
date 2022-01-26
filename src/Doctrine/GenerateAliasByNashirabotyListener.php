<?php


namespace App\Doctrine;
use App\Service\PageGeneratorService;
use App\Entity\Naschiraboty;


class GenerateAliasByNashirabotyListener
{
    /**
     * @var PageGeneratorService
     */
    protected $page_generator_service;

    public function __construct(PageGeneratorService $page_generator_service){
        $this->page_generator_service = $page_generator_service;
    }

    public function postPersist(Naschiraboty $naschiraboty){
        $this->page_generator_service->generateNashiraboryAlias($naschiraboty);
    }
}