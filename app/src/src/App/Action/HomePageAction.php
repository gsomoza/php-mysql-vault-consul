<?php

namespace App\Action;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;
use Zend\Expressive\Router;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Plates\PlatesRenderer;
use Zend\Expressive\Template\TemplateRendererInterface;

class HomePageAction
{
    private $router;

    private $template;

    private $db;

    public function __construct(Connection $dbConnection, RouterInterface $router, TemplateRendererInterface $template = null)
    {
        $this->router   = $router;
        $this->template = $template;
        $this->db = $dbConnection;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next = null)
    {
        $data = [];

        // this should actually be a middleware, but here for simplicity
        try {
            if (!$this->db->isConnected()) {
                $this->db->connect();
            }
        } catch (DriverException $e) {
            // nothing
        }
        $data['isDbConnected'] = $this->db->isConnected();

        // boilerplate:

        if ($this->router instanceof Router\ZendRouter) {
            $data['routerName'] = 'Zend Router';
            $data['routerDocs'] = 'http://framework.zend.com/manual/current/en/modules/zend.mvc.routing.html';
        }

        if ($this->template instanceof PlatesRenderer) {
            $data['templateName'] = 'Plates';
            $data['templateDocs'] = 'http://platesphp.com/';
        }

        if (!$this->template) {
            return new JsonResponse([
                'welcome' => 'Congratulations! You have installed the zend-expressive skeleton application.',
                'docsUrl' => 'zend-expressive.readthedocs.org',
            ]);
        }

        return new HtmlResponse($this->template->render('app::home-page', $data));
    }
}
