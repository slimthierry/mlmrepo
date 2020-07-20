<?php

namespace Drewlabs\Packages\Http\Controllers;

use Drewlabs\Core\Validator\Contracts\IValidator;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Http\Request;
use Drewlabs\Packages\Http\Contracts\IDataProviderControllerActionHandler;
use Drewlabs\Packages\Http\Contracts\IActionResponseHandler;
use Laravel\Lumen\Routing\Controller;

/**
 * @package Drewlabs\Packages\Http
 */
class ApiDataProviderController extends Controller
{
    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    /**
     *
     * @var IDataProviderControllerActionHandler
     */
    private $actionHandler;

    /**
     *
     * @var IActionResponseHandler
     */
    private $actionResponseHandler;


    public function __construct(IValidator $validator, IDataProviderControllerActionHandler $actionHandler, IActionResponseHandler $actionResponseHandler)
    {
        $this->middleware(\config("drewlabs_http_handlers.auth_middleware", 'auth'));
        $this->validator = $validator;
        $this->actionHandler = $actionHandler;
        $this->actionResponseHandler = $actionResponseHandler;
    }

    /**
     * Display a listing of the resource.
     *
     * @route GET /{collection}[/{$id}]
     *
     * @param Request $request
     * @param string $collection
     * @param string|int|null $id
     *
     * @return Response
     */
    public function index(Request $request, $collection, ...$parameters)
    {
        $fn_params = \array_filter(func_get_args(), \filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    \config("drewlabs_http_handlers.requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply validation rules to the request body
            $errors = $this->actionHandler
                ->applyValidationHandler(\config("drewlabs_http_handlers.requests.$collection.actions.index.validateRequestBody"), $request, $this->validator, $fn_params);
            if (!is_null($errors) && count($errors) > 0) {
                return $this->actionResponseHandler->respondBadRequest($errors);
            }
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(\config("drewlabs_http_handlers.requests.$collection.actions.index.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->actionResponseHandler->unauthorized($request);
            }
            // Apply filters rules
            $filters = array(
                'orderBy' => ($request->has('order') && $request->has('by')) ?
                    array('order' => $request->get('order'), 'by' => $request->get('by')) :
                    array('order' => 'desc', 'by' => 'updated_at'),
            );
            // Parse request query parameters
            $filters = array_merge($filters, $this->actionHandler
                ->applyQueryBuilder(\config("drewlabs_http_handlers.requests.$collection.actions.index.queryBuilder"), $request, $fn_params));
            $result = $provider->get(
                $filters,
                ['*'],
                \config("drewlabs_http_handlers.requests.$collection.actions.index.relationQuery", true),
                $request->has('page')
            );
            return $this->actionResponseHandler->respondOk(
                $this->actionHandler->applyTransformResponseBody(
                    \config("drewlabs_http_handlers.requests.$collection.actions.index.transformResponseBody"),
                    $result,
                    $fn_params
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->actionResponseHandler->respondError($e);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @route POST /{collection}/
     *
     * @param Request $request
     * @param string $collection
     *
     * @return Response
     */
    public function store(Request $request, $collection)
    {
        $fn_params = \array_filter(func_get_args(), \filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    \config("drewlabs_http_handlers.requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply request body data transformation handler to the request inputs
            $data = $this->actionHandler
                ->applyTransformRequestBody(\config("drewlabs_http_handlers.requests.$collection.actions.store.transformRequestBody"), $request, $fn_params);
            // Apply validation rules to the request body
            $request  = $request->merge($data);
            $errors = $this->actionHandler
                ->applyValidationHandler(\config("drewlabs_http_handlers.requests.$collection.actions.store.validateRequestBody"), $request, $this->validator, $fn_params);
            if (!is_null($errors) && count($errors) > 0) {
                return $this->actionResponseHandler->respondBadRequest($errors);
            }
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(\config("drewlabs_http_handlers.requests.$collection.actions.store.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->actionResponseHandler->unauthorized($request);
            }
            $handlerParams = $this->actionHandler
                ->applyBuildProviderHandlerParams(\config("drewlabs_http_handlers.requests.$collection.actions.store.providerHandlerParam"), $data, $request);
            $result =  $provider->create($data, $handlerParams);
            return $this->actionResponseHandler->respondOk(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        \config("drewlabs_http_handlers.requests.$collection.actions.store.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->actionResponseHandler->respondError($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @route GET /{collection}/{$id}
     *
     * @param Request $request
     * @param string $collection
     * @param array $param
     *
     * @return Response
     */
    public function show(Request $request, $collection, ...$param)
    {
        $fn_params = \array_filter(func_get_args(), \filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    \config("drewlabs_http_handlers.requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(\config("drewlabs_http_handlers.requests.$collection.actions.show.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->actionResponseHandler->unauthorized($request);
            }
            $query = $this->actionHandler
                ->applyQueryBuilder(\config("drewlabs_http_handlers.requests.$collection.actions.show.queryBuilder"), $request, $fn_params);
            $result =  $provider->get($query);
            return $this->actionResponseHandler->respondOk(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        \config("drewlabs_http_handlers.requests.$collection.actions.show.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->actionResponseHandler->respondError($e);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @route UPDATE /{collection}/{$id}
     *
     * @param Request $request
     * @param string $collection
     * @param array $param
     *
     * @return Response
     */
    public function update(Request $request, $collection, ...$param)
    {
        $fn_params = \array_filter(func_get_args(), \filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    \config("drewlabs_http_handlers.requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply request body data transformation handler to the request inputs
            $data = $this->actionHandler
                ->applyTransformRequestBody(\config("drewlabs_http_handlers.requests.$collection.actions.update.transformRequestBody"), $request, $fn_params);
            // Apply validation rules to the request body
            $errors = $this->actionHandler
                ->applyValidationHandler(\config("drewlabs_http_handlers.requests.$collection.actions.update.validateRequestBody"), $request, $this->validator, $fn_params);
            if (!is_null($errors) && count($errors) > 0) {
                return $this->actionResponseHandler->respondBadRequest($errors);
            }
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(\config("drewlabs_http_handlers.requests.$collection.actions.update.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->actionResponseHandler->unauthorized($request);
            }
            // Return success response to user
            $query = $this->actionHandler
                ->applyQueryBuilder(
                    \config("drewlabs_http_handlers.requests.$collection.actions.update.queryBuilder"),
                    $request,
                    $fn_params
                );
            $handlerParams = $this->actionHandler
                ->applyBuildProviderHandlerParams(\config("drewlabs_http_handlers.requests.$collection.actions.update.providerHandlerParam"), $data, $request);
            $result =  $provider->modify($query, $data, $handlerParams);
            return $this->actionResponseHandler->respondOk(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        \config("drewlabs_http_handlers.requests.$collection.actions.update.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->actionResponseHandler->respondError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @route DELETE /{collection}/{$id}
     *
     * @param Request $request
     * @param string $collection
     * @param array $param
     *
     * @return Response
     */
    public function destroy(Request $request, $collection, ...$param)
    {
        $fn_params = \array_filter(func_get_args(), \filter_fn_params($collection));
        try {
            $provider = $this->actionHandler
                ->bindProvider(
                    $request,
                    \config("drewlabs_http_handlers.requests.$collection.provider"),
                    $fn_params
                )->getProvider();
            // Apply gate policy on the request actions
            if (!$this->actionHandler
                ->applyGatePolicyHandler(\config("drewlabs_http_handlers.requests.$collection.actions.destroy.gatePolicy"), $request, $provider, $fn_params)) {
                return $this->actionResponseHandler->unauthorized($request);
            }
            $query = $this->actionHandler
                ->applyQueryBuilder(\config("drewlabs_http_handlers.requests.$collection.actions.destroy.queryBuilder"), $request, $fn_params);
            $result =  $provider->delete($query, \config("drewlabs_http_handlers.requests.$collection.actions.destroy.massDelete"));
            return $this->actionResponseHandler->respondOk(
                array(
                    'data' => $this->actionHandler->applyTransformResponseBody(
                        \config("drewlabs_http_handlers.requests.$collection.actions.destroy.transformResponseBody"),
                        $result,
                        $fn_params
                    )
                )
            );
        } catch (\Exception $e) {
            return $this->actionResponseHandler->respondError($e);
        }
    }
}
