<?php

namespace App\Twig;

use App\Service\ServicesMenuService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ServicesMenuExtension extends AbstractExtension
{
    /**
     * @var ServicesMenuService
     */
    private $servicesMenuService;

    public function __construct(
        ServicesMenuService $servicesMenuService
    ) {
        $this->servicesMenuService = $servicesMenuService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('services_header_menu', [$this, 'servicesHeaderMenu']),
        ];
    }

    public function servicesHeaderMenu(): array
    {
        return $this->servicesMenuService->getHeaderMenu();
    }
}