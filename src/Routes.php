<?php
namespace SlimRouteGroups;

use Slim\App;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use SlimRouteGroups\Exceptions\InvalidMiddlewareException;
use SlimRouteGroups\Exceptions\MiddlewareNotFoundException;
use SlimRouteGroups\Exceptions\MiddlewareArgsException;

/**
 * Base class for all Routes classes to extend.
 * @author Thomas Breese <thomasjbreese@gmail.com>
 */
class Routes
{
    /**
     * Define HTTP action constants.
     */
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PATCH = 'PATCH';
    const METHOD_OPTIONS = 'OPTIONS';
    
    /**
     * Instance of the Slim app.
     * @var App
     */
    protected $app;

    /**
     * Route middleware.
     * @var array
     */
    protected $middleware = [];
    
    /**
     * Instantiate a new instance.
     * @param App $app
     * @param array $middleware (optional) route middleware
     * @throws InvalidMiddlewareException
     */
    public function __construct(App $app, array $middleware = [])
    {
        $this->app = $app;
        if (count($middleware)) {
            foreach ($middleware as $name => $callable) {
                $this->addMiddleware($name, $callable);
            }
        }
    }
    
    /**
     * Initialization function to apply routes.
     * @param array $routes
     * @return void
     */
    public static function init(array $routes)
    {
        foreach ($routes as $route) {
            call_user_func($route);
        }
    }
    
    /**
     * Function which defines all routes.
     * @return void
     */
    public function __invoke()
    {
    }
    
    /**
     * Defines get route.
     * @param string $route
     * @param callable|string $action
     * @return Slim\Route
     */
    protected function get($route, $action)
    {
        return $this->map([self::METHOD_GET], $route, $action);
    }
    
    /**
     * Defines post route.
     * @param string $route
     * @param callable|string $action
     * @return Slim\Route
     */
    protected function post($route, $action)
    {
        return $this->map([self::METHOD_POST], $route, $action);
    }
    
    /**
     * Defines put route.
     * @param string $route
     * @param callable|string $action
     * @return Slim\Route
     */
    protected function put($route, $action)
    {
        return $this->map([self::METHOD_PUT], $route, $action);
    }
    
    /**
     * Defines delete route.
     * @param string $route
     * @param callable|string $action
     * @return Slim\Route
     */
    protected function delete($route, $action)
    {
        return $this->map([self::METHOD_DELETE], $route, $action);
    }
    
    /**
     * Defines patch route.
     * @param string $route
     * @param callable|string $action
     * @return Slim\Route
     */
    protected function patch($route, $action)
    {
        return $this->map([self::METHOD_PATCH], $route, $action);
    }
    
    /**
     * Defines options route.
     * @param string $route
     * @param callable|string $action (optional)
     * @return Slim\Route
     */
    protected function options($route, $action = null)
    {
        $routeAction = $action === null ? function(Request $request, Response $response, array $params) {
            return $response;
        } : $action;
        return $this->map([self::METHOD_OPTIONS], $route, $routeAction);
    }
    
    /**
     * Defines route accessable to all actions.
     * @param string $route
     * @param callable|string $action
     * @return Slim\Route
     */
    protected function any($route, $action)
    {
        return $this->app->any($route, $action);
    }
    
    /**
     * Defines a new route.
     * @param array $methods HTTP actions
     * @param string $route
     * @param callable|string $action
     * @return Slim\Route
     */
    protected function map(array $methods, $route, $action)
    {
        return $this->app->map($methods, $route, $action);
    }
    
    /**
     * Define a route group.
     * @param string $route
     * @param closure $subRoutes closure for route group
     * @return Slim\Route
     */
    protected function group($route, $subRoutes)
    {
        return $this->app->group($route, $subRoutes);
    }

    /**
     * Applies route middleware.
     * @param string $name name for the middleware
     * @param closure $middleware
     * @throws InvalidMiddlewareException
     * @return void
     */
    protected function addMiddleware($name, $middleware)
    {
        if (!is_callable($middleware)) {
            throw new InvalidMiddlewareException("Middleware must be callable for middleware {$name}");
        }
        $this->middleware[$name] = $middleware;
    }

    /**
     * Attaches middleware to the route/route group.
     * @param string $name
     * @param array $args parameters passed to call, in our case a slim route or route group
     * @return Slim\Route
     * @throws MiddleareArgsException
     * @throws MiddlewareNotFoundException
     */
    public function __call($name, array $args)
    {
        foreach ($this->middleware as $key => $middleware) {
            if ($key == $name) {
                if (!count($args)) {
                    throw new MiddlewareArgsException("You did not supply a resource to middleware {$name}.");
                }
                return $args[0]->add($middleware);
            }
        }
        throw new MiddlewareNotFoundException("Middleware {$name} not found.");
    }
}
