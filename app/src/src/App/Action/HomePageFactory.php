<?php

namespace App\Action;

use Doctrine\DBAL\Connection;
use Interop\Container\ContainerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageFactory
{
    public function __invoke(ContainerInterface $container) : HomePageAction
    {
        $router   = $container->get(RouterInterface::class);
        $template = ($container->has(TemplateRendererInterface::class))
            ? $container->get(TemplateRendererInterface::class)
            : null;
        $dbConnection = $container->get(Connection::class);

        return new HomePageAction($dbConnection, $router, $template);
    }
}
