<?php

namespace App\Service;

use App\Entity\Content;
use App\Repository\ContentRepository;

class ServicesMenuService
{

    /**
     * @var mixed
     */
    private $config;

    /**
     * @var ContentRepository
     */
    private $contentRepository;

    public function __construct(
       ContentRepository $contentRepository
    ) {
        $this->contentRepository = $contentRepository;
        $this->config = require dirname(__DIR__, 2) . '/config/services_menu.php';
    }

/**
 * Возвращает пункты меню для текущей модели.
 *
 * @return Content[]
 */
public function get(Content $page): array
{
    if (!$page->getModel() || !$page->getBrand()) {
        return [];
    }

    $modelAlias = mb_strtolower($page->getModel()->getAlias());

    if (!isset($this->config[$modelAlias])) {
        return [];
    }

    $pages = $this->contentRepository->findPublishedByModel(
        $page->getBrand()->getId(),
        $page->getModel()->getId()
    );

    $menu = [];

    foreach ($this->config[$modelAlias] as $menuTitle) {

        foreach ($pages as $content) {

            if ($this->contains($content->getName(), $menuTitle)) {
                $menu[] = $content;
                break;
            }
        }
    }

    return $menu;
}

private function contains(string $pageTitle, string $menuTitle): bool
{
    return str_contains(
        $this->normalize($pageTitle),
        $this->normalize($menuTitle)
    );
}

private function normalize(string $text): string
{
    $text = mb_strtolower($text);
    $text = str_replace('ё', 'е', $text);

    $text = preg_replace('/[^a-zа-я0-9]+/iu', ' ', $text);
    $text = preg_replace('/\s+/u', ' ', $text);

    return trim($text);
}

    public function getHeaderMenu(): array
    {
        $pages = $this->contentRepository->findBy(
            ['published' => true],
            ['sort' => 'ASC']
        );

        $menu = [];

        foreach ($this->config as $modelAlias => $services) {

            $items = [];

            foreach ($services as $menuTitle) {

                foreach ($pages as $page) {

                    if (
                        $page->getModel()
                        && mb_strtolower($page->getModel()->getAlias()) === '/'.$modelAlias.'/'
                        && $this->contains($page->getName(), $menuTitle)
                    ) {
                        $items[] = $page;
                        break;
                    }
                }
            }

            if (!empty($items)) {

                $menu[] = [
                    'title' => ucfirst($modelAlias),
                    'alias' => $modelAlias,
                    'items' => $items,
                ];

            }
        }

        return $menu;
    }
}