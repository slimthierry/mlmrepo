<?php

namespace Drewlabs\Packages\Database\Traits;

trait RoutableModel
{
    /**
     * Defines the name of the index path of the current ressource
     *
     * @var
     */
    protected $indexRoute;

    /**
     * Identifier column name for the index route
     *
     * @var [type]
     */
    protected $ressourceIdParam;

    /**
     * {@inheritDoc}
     */
    protected $routeTemplateParams;

    /**
     * Defines a model entity unique name in the data storage
     */
    protected $entityIdentifier;

    /**
     * Returns the value matching the id parameter to be passed to the ressource identifier
     *
     * @return int|string
     */
    protected function getRouteIdParam()
    {
        return $this->getKey();
    }

    /**
     * [[link]] attribute getter
     *
     * @return string
     */
    public function getLinkAttribute()
    {
        $id = $this->ressourceIdentifier();
        $idParam = $this->getRouteIdParam();
        $route =  $this->getIndexRoute();
        if (is_null($id) || is_null($idParam) || is_null($route)) {
            return null;
        }
        return route($route, array_merge(
            array($id => $idParam),
            isset($this->routeTemplateParams) &&
                is_array($this->routeTemplateParams) ?
                $this->routeTemplateParams : []
        ));
    }

    /**
     * Returns the ressource identifier parameter name for the given model
     *
     * @return string
     */
    protected function ressourceIdentifier()
    {
        return isset($this->ressourceIdParam) ? $this->ressourceIdParam : 'id';
    }

    /**
     * Returns the name of the index route for the given model
     *
     * @return string|null
     */
    protected function getIndexRoute()
    {
        return $this->indexRoute;
    }
}
