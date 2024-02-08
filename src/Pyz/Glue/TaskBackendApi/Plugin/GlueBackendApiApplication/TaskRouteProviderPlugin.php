<?php

/**
 * This file is part of the Spryker Commerce OS.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Glue\TaskBackendApi\Plugin\GlueBackendApiApplication;

use Pyz\Glue\TaskBackendApi\Controller\TaskBackendApiController;
use Spryker\Glue\GlueApplicationExtension\Dependency\Plugin\RouteProviderPluginInterface;
use Spryker\Glue\Kernel\Backend\AbstractPlugin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class TaskRouteProviderPlugin extends AbstractPlugin implements RouteProviderPluginInterface
{
    /**
     * @var string
     */
    protected const ROUTE_PATH = '/tasks';

    /**
     * @var string
     */
    protected const ROUTE_PATH_WITH_ID = '%s/{id}';

    /**
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    public function addRoutes(RouteCollection $routeCollection): RouteCollection
    {
        $route = $this->buildRoute('getCollectionAction', Request::METHOD_GET, self::ROUTE_PATH);
        $routeCollection = $this->addToRouteCollection($routeCollection, $route, $this->formatName('taskCollection', Request::METHOD_GET));

        $route = $this->buildRoute('getAction', Request::METHOD_GET, $this->getRoutePathWithId());
        $routeCollection = $this->addToRouteCollection($routeCollection, $route, $this->formatName('task', Request::METHOD_GET));

        $route = $this->buildRoute('postAction', strtolower(Request::METHOD_POST), self::ROUTE_PATH);
        $routeCollection = $this->addToRouteCollection($routeCollection, $route, $this->formatName('task', Request::METHOD_POST));

        $route = $this->buildRoute('patchAction', strtolower(Request::METHOD_PATCH), $this->getRoutePathWithId());
        $routeCollection = $this->addToRouteCollection($routeCollection, $route, $this->formatName('task', Request::METHOD_PATCH));

        $route = $this->buildRoute('deleteAction', strtolower(Request::METHOD_DELETE), $this->getRoutePathWithId());
        $routeCollection = $this->addToRouteCollection($routeCollection, $route, $this->formatName('task', Request::METHOD_DELETE));

        return $routeCollection;
    }

    /**
     * @param \Symfony\Component\Routing\RouteCollection $routeCollection
     * @param \Symfony\Component\Routing\Route $route
     * @param string $name
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    protected function addToRouteCollection(RouteCollection $routeCollection, Route $route, string $name): RouteCollection
    {
        $routeCollection->add($name, $route);

        return $routeCollection;
    }

    /**
     * @param string $action
     * @param string $method
     * @param string $path
     *
     * @return \Symfony\Component\Routing\Route
     */
    protected function buildRoute(string $action, string $method, string $path): Route
    {
        $route = new Route($path);
        $route->setDefault('_controller', [TaskBackendApiController::class, $action])
            ->setDefault('_method', $method)
            ->setDefault('_authorization_strategies', ['ApiKey'])
            ->setMethods($method);

        return $route;
    }

    /**
     * @return string
     */
    protected function getRoutePathWithId(): string
    {
        return sprintf(static::ROUTE_PATH_WITH_ID, static::ROUTE_PATH);
    }

    /**
     * @param string $name
     * @param string $method
     *
     * @return string
     */
    protected function formatName(string $name, string $method): string
    {
        return sprintf('%s-%s', $name, $method);
    }
}
