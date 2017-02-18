<?php

namespace AppTest\Action;

use App\Action\HomePageAction;
use Doctrine\DBAL\Connection;
use Prophecy\Argument;
use Zend\Diactoros\Response;
use Zend\Diactoros\ServerRequest;
use Zend\Expressive\Router\RouterInterface;

class HomePageActionTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouterInterface */
    protected $router;
    private $dbConnection;

    protected function setUp()
    {
        $this->router = $this->prophesize(RouterInterface::class);
        $this->dbConnection = $this->prophesize(Connection::class);
    }

    public function testResponse()
    {
        $this->dbConnection->isConnected()->willReturn(true);
        $this->dbConnection->fetchAll(Argument::type('string'))->willReturn([['value' => 'Hello World']]);
        $homePage = new HomePageAction($this->dbConnection->reveal(), $this->router->reveal(), null);
        $response = $homePage(new ServerRequest(['/']), new Response(), function () {
        });

        $this->assertTrue($response instanceof Response);
    }
}
