<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Identity\Role;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Packages\Identity\Services\Contracts\IRolesDataProvider;

class RolesController extends Controller
{
    /**
     *
     * @var IValidator
     */
    private $validator;

    /**
     *
     * @var IRolesDataProvider
     */
    protected $provider;

    public function __construct(IRolesDataProvider $provider, IValidator $validator)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->validator = $validator;

        // Middlewares definitions
        $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
        $this->middleware('policy:all,list-roles', ['only' => ['get']]);
        $this->middleware('policy:all,create-roles', ['only' => ['create']]);
        $this->middleware('policy:all,update-roles', ['only' => ['update']]);
        $this->middleware('policy:all,delete-roles', ['only' => ['delete']]);
    }

    /**
     * Handle POST /roles
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        // validate request inputs
        $validator = $this->validator->validate(app(Role::class), $request->all());
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            $inputs = $request->all();
            if (isset($inputs['permissions'])) {
                $inputs['permission_roles'] = array_map(function ($id) {
                    return array("permission_id" => $id);
                }, $inputs['permissions']);
            }
            // Save the current role with the provided permissions
            $result = $this->provider->create($inputs, new \Drewlabs\Core\Data\DataProviderCreateHandlerParams([
                'method' => 'insert__permission_roles',
                'upsert' => true,
                'upsert_conditions' => [
                    'label' => $request->get('label'),
                ]
            ]));
            $result = $this->provider->getById($result->getKey());
            return $this->respondOk(array('data' => $result));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /roles/{id}
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id)
    {
        // validate request inputs
        $validator = $this->validator->setUpdate(true)->validate(app(Role::class), \array_merge($request->all(), array("id" => $id)));
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            // Insert new permissions
            $inputs = $request->all();
            if (isset($inputs['permissions'])) {
                $inputs['permission_roles'] = array_map(function ($i) use ($id) {
                    return array(
                        "permission_id" => $i,
                        "role_id" => $id
                    );
                }, $inputs['permissions']);
                unset($inputs['permissions']);
            }
            $result = $this->provider->modify($id, $inputs, new \Drewlabs\Core\Data\DataProviderUpdateHandlerParams([
                'method' => 'update__permission_roles',
                'upsert' => false,
                'should_mass_update' => false
            ]));
            \drewlabs_dispatch_event(
                (new \Drewlabs\Packages\Identity\Events\Publishers\DeletePermissionRolesSoftDeletedEvent())
                    ->subscribe(new \Drewlabs\Packages\Identity\Events\Subscribers\DeletePermissionRolesSoftDeletedSubscriber())
            );
            return $this->respondOk(array('data' => $result));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle DELETE /roles/{id}
     *
     * @param Request $request
     * @param [type] $id
     * @return JsonResponse
     */
    public function delete(Request $request, $id)
    {
        try {
            $result = $this->provider->delete($id);
            \drewlabs_dispatch_event(
                (new \Drewlabs\Packages\Identity\Events\Publishers\DeletePermissionRolesSoftDeletedEvent())
                    ->subscribe(new \Drewlabs\Packages\Identity\Events\Subscribers\DeletePermissionRolesSoftDeletedSubscriber())
            );
            return $this->respondOk(array('data' => $result));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle  GET /roles/{id?}
     *
     * @return JsonResponse
     */
    public function get(Request $request, int $id = null)
    {
        if (isset($id)) {
            $filters = [
                'where' => array(array('id', $id), array('label', '<>', \config('drewlabs_identity.admin_group', \Drewlabs\Packages\Identity\IdentityDefaultPermissionGroups::SUPER_ADMIN_GROUP)))
            ];
            $result = $this->provider->get($filters, ['*'], true, false, null);
            return $this->respondOk(array('data' => $result['data']->first()));
        }
        $filters = array(
            // Apply orderBy filters
            'where' => array(array('label', '<>', \config('drewlabs_identity.admin_group', \Drewlabs\Packages\Identity\IdentityDefaultPermissionGroups::SUPER_ADMIN_GROUP))),
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        );
        $filters = array_merge($filters, app(Role::class)->parseRequestQueryFilters($request));
        return $this->respondOk(
            $this->provider->get($filters, ['*'], true, $request->has('page'), $request->get('per_page'))
        );
    }
}
