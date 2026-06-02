<?php
namespace App\EventListener;

use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ExceptionListener
{
    private RouterInterface $router;
    private RequestStack $requestStack;

    public function __construct(RouterInterface $router, RequestStack $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Ловим удалённую сущность
        if ($exception instanceof EntityNotFoundException) {
            $request = $this->requestStack->getCurrentRequest();
            $session = $request ? $request->getSession() : null;

            // Добавим flash-сообщение
            if ($session && $session->isStarted()) {
                $session->getFlashBag()->add('warning', 'Запись не найдена. Возможно, она была удалена.');
            }

            // Перенаправим на дашборд
            $url = $this->router->generate('admin');
            $event->setResponse(new RedirectResponse($url));
        }
    }
}
