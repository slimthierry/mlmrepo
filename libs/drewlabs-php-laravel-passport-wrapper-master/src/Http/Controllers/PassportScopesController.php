<?php

namespace Drewlabs\Packages\PassportPHPLeagueOAuth\Http\Controllers;

use Drewlabs\Contracts\Data\IModelFilter;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Packages\PassportPHPLeagueOAuth\Scope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PassportScopesController extends \Drewlabs\Packages\Http\Controllers\ApiController
{
    /**
     *
     * @var BaseIlluminateModelRepository
     */
    private $repository;

    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    public function __construct(
        \Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository $repository,
        IValidator $validator
    ) {
        parent::__construct();
        $this->repository = $repository->setModel(Scope::class);
        $this->validator = $validator;
        $this->middleware('policy:all', [ 'only' => ['create']]);
        $this->middleware('policy:all', [ 'only' => ['update']]);
        $this->middleware('policy:all', [ 'only' => ['delete']]);
        // Middlewares definitions
    }

    /**
     * Handle GET /oauth/scopes/{id?}
     *
     * @param Request $request
     * @param string|int $identifier
     * @return JsonResponse
     */
    public function get(Request $request, $id)
    {
        if (isset($id)) {
            return $this->respondOk(array(
                'data' => $this->repository->findById($id)
            ));
        }
        $filters = array(
            // Apply orderBy filters
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        );
        return $this->respondOk(
            array(
                'data' => $request->has('page') ?  $this->repository->pushFilter(
                    app(IModelFilter::class)->setQueryFilters($filters)
                )->queryRelation(false)->paginate($request->has('per_page') ? $request->get('per_page') : null) : array(
                    "data" => $this->repository->pushFilter(
                        app(IModelFilter::class)->setQueryFilters($filters)
                    )->queryRelation(false)->find()
                )
            )
        );
    }

    /**
     * Handle POST /oauth/scopes
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $validator = $this->validator->validate(app(Scope::class), $request->all());

        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            $result = $this->repository->insert($request->all(), true);
            return $this->respondOk($result);
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /oauth/scopes
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        // validate request inputs
        $validator = $this->validator->setUpdate(true)->validate(
            app(Scope::class),
            \array_merge($request->all(), array("id" => $id))
        );
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            $result = $this->repository->updateById($id, $request->all(), true);
            return $this->respondOk($result);
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle DELETE /oauth/scopes/{id}
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        try {
            $result = $this->repository->deleteById($id);
            return $this->respondOk($result);
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }
}
