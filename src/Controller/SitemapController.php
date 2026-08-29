<?php

namespace App\Controller;

use App\Entity\Content;
use App\Entity\Naschiraboty;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    /** Сколько URL в одном файле карты (лимит стандарта — 50 000) */
    private const CHUNK_SIZE = 10000;

    /**
     * priority / changefreq для отдельных адресов.
     * Остальные страницы получают вес по глубине пути (см. weightFor).
     */
    private const SPECIAL_PAGES = [
        '/'          => ['1.0', 'daily'],
        '/contacts/' => ['0.8', 'monthly'],
        '/offers'    => ['0.6', 'weekly'],
        '/blog/'     => ['0.5', 'weekly'],
        '/sitemap/'  => ['0.3', 'monthly'],
        '/politika/' => ['0.3', 'yearly'],
        '/cookies/'  => ['0.3', 'yearly'],
    ];

    /**
     * @Route("/sitemap.xml", name="sitemap")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        $chunks = array_chunk($this->collectUrls($request->getSchemeAndHttpHost()), self::CHUNK_SIZE);

        $sitemaps = array();
        foreach ($chunks as $i => $chunk) {
            $sitemaps[] = array(
                'loc'     => $request->getSchemeAndHttpHost().'/sitemap_'.($i + 1).'.xml',
                'lastmod' => $this->lastmodOf($chunk),
            );
        }

        return $this->xmlResponse('sitemap/sitemap.html.twig', array('sitemaps' => $sitemaps));
    }

    /**
     * @Route("/sitemap_{token}.xml", name="sitemap_part", requirements={"token"="\d+"})
     * @param Request $request
     * @param $token
     * @return Response
     */
    public function sitemap_part(Request $request, $token): Response
    {
        $chunks = array_chunk($this->collectUrls($request->getSchemeAndHttpHost()), self::CHUNK_SIZE);
        $index = (int) $token - 1;

        if ($index < 0 || !isset($chunks[$index])) {
            throw $this->createNotFoundException('Такой части карты сайта нет');
        }

        return $this->xmlResponse('sitemap/sitemap_part.html.twig', array('urls' => $chunks[$index]));
    }

    /**
     * Единый список адресов карты: страницы из content + статьи блога.
     * Порядок фиксирован (по id), иначе индексный файл и части разъедутся.
     *
     * @param string $hostname
     * @return array
     */
    private function collectUrls(string $hostname): array
    {
        $em = $this->getDoctrine()->getManager();
        $urls = array();

        $pages = $em->createQuery(
            'SELECT c.path AS path, c.modifyDate AS date FROM '.Content::class.' c'
            .' WHERE c.published = 1 ORDER BY c.id ASC'
        )->getArrayResult();

        foreach ($pages as $page) {
            $path = (string) $page['path'];
            if ('' === $path || '/' !== $path[0]) {
                continue;
            }
            // Карта сайта для людей отключена 17.08.2026 — в XML-карту её не отдаём
            if ('/sitemap/' === $path) {
                continue;
            }
            // «Акции» живут по роуту /offers без слэша, а в БД путь записан со слэшем —
            // в карту отдаём конечный URL, чтобы не вести поисковик через 301.
            if ('/offers/' === $path) {
                $path = '/offers';
            }
            $urls[$path] = $this->makeUrl($hostname, $path, $page['date']);
        }

        // Статьи блога — сущность Naschiraboty, роут /blog/{alias}/. В таблице content их нет.
        $posts = $em->createQuery(
            'SELECT n.alias AS alias, n.modifyDate AS date FROM '.Naschiraboty::class.' n ORDER BY n.id ASC'
        )->getArrayResult();

        foreach ($posts as $post) {
            $alias = trim((string) $post['alias'], '/');
            if ('' === $alias) {
                continue;
            }
            $path = '/blog/'.$alias.'/';
            $urls[$path] = $this->makeUrl($hostname, $path, $post['date']);
        }

        return array_values($urls);
    }

    /**
     * @param string $hostname
     * @param string $path
     * @param \DateTimeInterface|null $date
     * @return array
     */
    private function makeUrl(string $hostname, string $path, $date): array
    {
        list($priority, $changefreq) = $this->weightFor($path);

        return array(
            'loc' => $hostname.$path,
            // Дату форматируем здесь, а не в шаблоне: в Twig обратный слэш перед T
            // теряется, и вместо разделителя даты в карту попадает название часового пояса.
            'lastmod'    => $this->atom($date),
            'changefreq' => $changefreq,
            'priority'   => $priority,
        );
    }

    /**
     * priority и changefreq по типу страницы (ТЗ от 25.07.2026).
     *
     * @param string $path
     * @return array
     */
    private function weightFor(string $path): array
    {
        if (isset(self::SPECIAL_PAGES[$path])) {
            return self::SPECIAL_PAGES[$path];
        }
        if (0 === strpos($path, '/blog/')) {
            return array('0.5', 'monthly');   // статья блога
        }

        $depth = count(array_filter(explode('/', $path), 'strlen'));

        return $depth <= 1
            ? array('0.8', 'weekly')          // категория услуг или страница модели
            : array('0.6', 'monthly');        // конечная услуга: модель + услуга + операция
    }

    /**
     * Дата в формате W3C (2026-08-11T12:31:52+03:00) — как требует стандарт sitemap.
     *
     * @param \DateTimeInterface|null $date
     * @return string
     */
    private function atom($date): string
    {
        if (!$date instanceof \DateTimeInterface) {
            $date = new \DateTime();
        }

        return $date->format(\DateTime::ATOM);
    }

    /**
     * Самая свежая дата изменения среди страниц части карты.
     *
     * @param array $urls
     * @return string
     */
    private function lastmodOf(array $urls): string
    {
        $max = '';
        foreach ($urls as $url) {
            if ($url['lastmod'] > $max) {
                $max = $url['lastmod'];
            }
        }

        return '' !== $max ? $max : $this->atom(null);
    }

    /**
     * @param string $template
     * @param array $params
     * @return Response
     */
    private function xmlResponse(string $template, array $params): Response
    {
        $response = new Response($this->renderView($template, $params), 200);
        $response->headers->set('Content-Type', 'text/xml; charset=UTF-8');

        return $response;
    }
}
