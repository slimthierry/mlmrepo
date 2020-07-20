<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Contracts\Data\IModelFilter;
use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Packages\Identity\DefaultScopes as IdentityDefaultScopes;
use Drewlabs\Packages\Identity\Permission;

class PermissionsController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    public function __construct(BaseIlluminateModelRepository $repository, IValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository->setModel(Permission::class);
        $this->validator = $validator;
        // Middlewares definitions
        $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
        $this->middleware('policy:all,list-permissions', ['only' => ['get']]);
        $this->middleware('policy:all,create-permissions', ['only' => ['create']]);
        $this->middleware('policy:all,update-permissions', ['only' => ['update']]);
        $this->middleware('policy:all,delete-permissions', ['only' => ['delete']]);
    }

    /**
     * Handle POST /permissions
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        // validate request inputs
        $validator = $this->validator->validate(app(Permission::class), $request->all());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            return $this->respondOk(array('data' => $this->repository->insert($request->all(), true)));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /permissions/{id}
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        // validate request inputs
        $validator = $this->validator->setUpdate(true)->validate(app(Permission::class), \array_merge($request->all(), array("id" => $id)));
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            return $this->respondOk(array('data' => $this->repository->updateById($id, $request->all(), true)));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle DELETE /permissions/{id}
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        try {
            return $this->respondOk(array('data' => $this->repository->deleteById($id)));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * @route GET /application_permissions
     *
     * @return JsonResponse
     */
    public function getApplicationPermissions()
    {
        return $this->respondOk(
            array(
                // Get all item if the there is no page query parameter in the request, else call the pagination function on the repository
                'permissions' => array(
                    "data" => config('app.application_permissions', [])
                ),
            )
        );
    }

    /**
     * Handle GET /permissions
     *
     * @return JsonResponse
     */
    public function get(Request $request, $id = null)
    {
        if (isset($id)) {
            return $this->respondOk(array('data' => $this->repository->find(array(array('id', $id), array('label', '<>', IdentityDefaultScopes::SUPER_ADMIN_SCOPE)))->first()));
        }
        $filters = array(
            // Apply orderBy filters
            'where' => array(array('label', '<>', IdentityDefaultScopes::SUPER_ADMIN_SCOPE)),
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        );
        return $this->respondOk(
            $request->has('page') ?  $this->repository->pushFilter(
                app(IModelFilter::class)->setQueryFilters($filters)
            )->queryRelation(false)->paginate($request->has('per_page') ? $request->get('per_page') : null) :
                array(
                    "data" => $this->repository->pushFilter(
                        app(IModelFilter::class)->setQueryFilters($filters)
                    )->queryRelation(false)->find()
                )
        );
    }
}
