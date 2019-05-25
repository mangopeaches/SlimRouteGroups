<?php
namespace SlimRouteGroupsTests\Unit;

use Slim\App;
use PHPUnit\Framework\TestCase;
use SlimRouteGroups\Routes;
use SlimRouteGroups\Exceptions\InvalidMiddlewareException;
use SlimRouteGroups\Exceptions\MiddlewareNotFoundException;
use SlimRouteGroups\Exceptions\MiddlewareArgsException;

/**
 * Mock class for tests.
 */
class MockRoutes extends Routes
{
    public function __invoke() {}

    // helper methods to give us external access so we can test
    // individual methods

    public function applyGet($route, $action)
    {
        return $this->get($route, $action);
    }

    public function applyPost($route, $action)
    {
        return $this->post($route, $action);
    }

    public function applyPut($route, $action)
    {
        return $this->put($route, $action);
    }

    public function applyDelete($route, $action)
    {
        return $this->delete($route, $action);
    }

    public function applyPatch($route, $action)
    {
        return $this->patch($route, $action);
    }

    public function applyOptions($route, $action)
    {
        return $this->options($route, $action);
    }

    public function applyAny($route, $action)
    {
        return $this->any($route, $action);
    }

    public function applyMap(array $methods, $route, $action)
    {
        return $this->map($methods, $route, $action);
    }

    public function applyGroup($route, $subRoutes)
    {
        return $this->group($route, $subRoutes);
    }
}

/**
 * Mock class to test trying to call middleware which is not applied.
 */
class MiddlewareNoFound extends Routes
{
    public function __invoke()
    {
        $this->invalidMiddleware($this->get('/', function() { echo 'hi'; }));
    }
}

/**
 * Mock class to test calling middleware.
 */
class MiddlewareFound extends Routes
{
    public function __invoke()
    {
        $this->banana($this->get('/', function() { echo 'hi'; }));
    }
}

/**
 * Mock class to test calling middleware on route group.
 */
class MiddlewareGroup extends Routes
{
    public function __invoke()
    {
        $self = $this;
        $this->banana($this->group('/base', function($app) use ($self) {
            $self->get('/get', function() { echo 'hi'; });
        }));
    }
}

/**
 * Mock class to test defining multiple routes.
 */
class MultiRoutes extends Routes
{
    public function __invoke()
    {
        $self = $this;
        $this->group('/base', function($app) use ($self) {
            $self->get('', function() { echo 'hi'; });
            $self->group('/inner', function($app) use ($self) {
                $self->get('/one', function() { echo 'hello'; });
                $self->post('/two', function() { echo 'hello'; });
            });
        });
    }
}

/**
 * Mock class to test calling middleware with invalid args.
 */
class MiddlewareArgs extends Routes
{
    public function __invoke()
    {
        $this->banana();
    }
}

/**
 * Test cases for Routes class.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class RoutesTest extends TestCase
{
    /**
     * Tests creating a new instance.
     */
    public function testControlGroup()
    {
        $app = new App();
        $routes = new MockRoutes($app);
        $this->assertTrue($routes instanceof Routes);
    }

    /**
     * Tests adding a GET route.
     */
    public function testGet()
    {
        $route = '/get';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyGet($route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['GET']);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests adding a POST route.
     */
    public function testPost()
    {
        $route = '/post';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyPost($route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['POST']);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests adding a PUT route.
     */
    public function testPut()
    {
        $route = '/put';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyPut($route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['PUT']);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests adding a DELETE route.
     */
    public function testDelete()
    {
        $route = '/delete';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyDelete($route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['DELETE']);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests adding a PATCH route.
     */
    public function testPatch()
    {
        $route = '/patch';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyPatch($route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['PATCH']);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests adding a OPTIONS route.
     */
    public function testOptions()
    {
        $route = '/options';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyOptions($route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['OPTIONS']);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests adding an any route.
     */
    public function testAny()
    {
        $route = '/any';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyAny($route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS']);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests adding a map route.
     */
    public function testMap()
    {
        $route = '/map';
        $methods = ['GET', 'OPTIONS'];
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $routes->applyMap($methods, $route, $callable);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === $methods);
        $this->assertTrue($firstRoute->getPattern() === $route);
    }

    /**
     * Tests route groups.
     */
    public function testGroup()
    {
        $groupBase = '/group';
        $route = '/get';
        $callable = function() { echo 'hi'; };
        $app = new App();
        $routes = new MockRoutes($app);
        $self = $routes;
        $routes->applyGroup($groupBase, function() use ($self, $route, $callable) {
            $self->applyGet($route, $callable);
        });
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['GET']);
        $this->assertTrue($firstRoute->getPattern() === "{$groupBase}{$route}");
    }

    /**
     * Tests where middleware is not callable.
     */
    public function testInvalidMiddlewareException()
    {
        $this->expectException(InvalidMiddlewareException::class);
        $app = new App();
        $routes = new MockRoutes($app, [
            'banana' => 'apple'
        ]);
    }

    /**
     * Tests middleware is callable in the constructor.
     */
    public function testValidMiddleware()
    {
        $app = new App();
        $routes = new MockRoutes($app, [
            'banana' => function($request, $response, $next) {
                return $response;    
            }
        ]);
        $this->assertTrue($routes instanceof Routes);
    }

    /**
     * Tests accessing middleware that's not applied.
     */
    public function testMiddlewareNotFoundExeption()
    {
        $this->expectException(MiddlewareNotFoundException::class);
        $app = new App();
        $routes = new MiddlewareNoFound($app, [
            'banana' => function($request, $response, $next) {
                return $response;    
            }
        ]);
        Routes::init([$routes]);
    }

    /**
     * Tests accessing middleware without parameters.
     */
    public function testMiddlewareArgsExeption()
    {
        $this->expectException(MiddlewareArgsException::class);
        $app = new App();
        $routes = new MiddlewareArgs($app, [
            'banana' => function($request, $response, $next) {
                return $response;    
            }
        ]);
        Routes::init([$routes]);
    }

    /**
     * Tests applying middleware to a single route.
     */
    public function testMiddlewareSingle()
    {
        $app = new App();
        $routes = new MiddlewareFound($app, [
            'banana' => function($request, $response, $next) {
                return $response;    
            }
        ]);
        Routes::init([$routes]);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['GET']);
        $this->assertTrue($firstRoute->getPattern() === '/');
    }

    /**
     * Tests applying middleware to a route group.
     */
    public function testMiddlewareGroup()
    {
        $app = new App();
        $routes = new MiddlewareGroup($app, [
            'banana' => function($request, $response, $next) {
                return $response;    
            }
        ]);
        Routes::init([$routes]);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['GET']);
        $this->assertTrue($firstRoute->getPattern() === '/base/get');
    }

    /**
     * Tests init function with multiple routes.
     */
    public function testInit()
    {
        $app = new App();
        $routes = new MultiRoutes($app);
        Routes::init([$routes]);
        $slimRoutes = $app->getContainer()->router->getRoutes();
        $firstRoute = array_pop($slimRoutes);
        $this->assertTrue($firstRoute->getMethods() === ['POST']);
        $this->assertTrue($firstRoute->getPattern() === '/base/inner/two');
        $secondRoute = array_pop($slimRoutes);
        $this->assertTrue($secondRoute->getMethods() === ['GET']);
        $this->assertTrue($secondRoute->getPattern() === '/base/inner/one');
        $thirdRoute = array_pop($slimRoutes);
        $this->assertTrue($thirdRoute->getMethods() === ['GET']);
        $this->assertTrue($thirdRoute->getPattern() === '/base');
    }
}
