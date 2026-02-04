<?php

namespace Tests\Core;

use PHPUnit\Framework\TestCase;
use App\Core\Router;

class RouterTest extends TestCase
{
    public function testUriSanitizationPreventsPathTraversal()
    {
        $router = new Router();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/iQMS/public/../../etc/passwd';

        ob_start();
        $router->dispatch();
        $output = ob_get_clean();

        $this->assertEquals('Bad Request', $output);
        $this->assertEquals(400, http_response_code());
    }

    public function testUriSanitizationRemovesNullBytes()
    {
        $router = new Router();
        $router->get('/test_.html', function () {
            echo 'Test page';
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = "/iQMS/public/test\x00.html";

        ob_start();
        $router->dispatch();
        $output = ob_get_clean();

        // Null bytes should be stripped, route should work
        // Note: PHP's parse_url() converts null bytes to underscores
        $this->assertEquals('Test page', $output);
    }

    public function testUriSanitizationHandlesEncodedCharacters()
    {
        $router = new Router();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/iQMS/public/%2e%2e/etc/passwd';

        ob_start();
        $router->dispatch();
        $output = ob_get_clean();

        $this->assertEquals('Bad Request', $output);
        $this->assertEquals(400, http_response_code());
    }

    public function testValidRouteWorks()
    {
        $router = new Router();
        $router->get('/test', function () {
            echo 'Test page';
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/iQMS/public/test';

        ob_start();
        $router->dispatch();
        $output = ob_get_clean();

        $this->assertEquals('Test page', $output);
    }

    public function testRootRouteWorks()
    {
        $router = new Router();
        $router->get('/', function () {
            echo 'Home page';
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/iQMS/public';

        ob_start();
        $router->dispatch();
        $output = ob_get_clean();

        $this->assertEquals('Home page', $output);
    }

    public function testRouteWithHyphensAndUnderscores()
    {
        $router = new Router();
        $router->get('/test-route_name', function () {
            echo 'Route with special chars';
        });

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/iQMS/public/test-route_name';

        ob_start();
        $router->dispatch();
        $output = ob_get_clean();

        $this->assertEquals('Route with special chars', $output);
    }
}
