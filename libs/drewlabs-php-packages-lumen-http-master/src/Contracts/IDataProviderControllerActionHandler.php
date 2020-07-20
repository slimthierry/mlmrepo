<?php

namespace Drewlabs\Packages\Http\Contracts;

use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Contracts\Data\IDataProvider;

/**
 * @package Drewlabs\Packages\Http
 */
interface IDataProviderControllerActionHandler
{
    /**
     * Undocumented function
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $callback
     * @param array|null $params
     * @return static
     */
    public function bindProvider($request, $callback, $params);

    /**
     * Returns the data provider bindable to the controller
     *
     * @return \Drewlabs\Contracts\Data\IDataProvider
     */
    public function getProvider();
    /**
     * Undocumented function
     *
     * @param \Closure|callable $callback
     * @param \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @param array $params
     * @return array
     */
    public function applyQueryBuilder($callback, $request, $params = []);

    /**
     * Undocumented function
     *
     * @param \Closure|callable|bool $callback
     * @param \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @param array|null $params
     * @return bool
     */
    public function applyGatePolicyHandler($callback, $request, IDataProvider $provider, $params = []);

    /**
     * Undocumented function
     *
     * @param \Closure|callable $callback
     * @param  \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @param array $params
     *
     * @return array
     */
    public function applyTransformRequestBody($callback, $request, $params = []);

    /**
     * Undocumented function
     *
     * @param \Closure|callable $callback
     * @param  \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @param IValidator $validator
     * @param array $params
     *
     * @return array|null
     */
    public function applyValidationHandler($callback, $request, IValidator $validator, $params = []);

    /**
     * Undocumented function
     *
     * @param \Closure|callable|array|null $callback
     * @param array $values
     * @param  \Illuminate\Http\Request|\Psr\Http\Message\RequestInterface $request
     * @return array|\Drewlabs\Contracts\Data\IDataProviderHandlerParams
     */
    public function applyBuildProviderHandlerParams($callback, $values = [], $request = null);

    /**
     * Apply data transformation callback to the data provider query result
     *
     * @param \Closure|callable|null $callback
     * @param array|mixed $body
     * @param array $params
     * @return array|mixed
     */
    public function applyTransformResponseBody($callback, $body, $params = []);
}
