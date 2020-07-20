<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Contracts\Data\IDataProvider;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Identity\Department;

class DepartmentsController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    /**
     * Undocumented variable
     *
     * @var IDataProvider
     */
    protected $provider;

    public function __construct(
        IDataProvider $provider,
        IValidator $validator
    ) {
        parent::__construct();
        $this->provider = $provider;
        $this->validator = $validator;
        $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
        $this->middleware('policy:all,list-departments', ['only' => ['get']]);
        $this->middleware('policy:all,create-departments', ['only' => ['create']]);
        $this->middleware('policy:all,update-departments', ['only' => ['update']]);
        $this->middleware('policy:all,delete-departments', ['only' => ['delete']]);
    }

    /**
     * Handle POST /departments
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $validator = $this->validator->validate(app(Department::class), $request->all());

        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            $request_inputs = $request->all();
            if (isset($request_inputs['roles'])) {
                $inputs['department_roles'] = array_map(function ($item) {
                    return array(
                        'role_id' => $item
                    );
                }, $request_inputs['roles']);
            }
            return $this->respondOk(array('data' => $this->provider->create($request_inputs, [
                'method' => 'insert__department_roles'
            ])));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /departments/{id}
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // validate request inputs
            $validator = $this->validator->setUpdate(true)->validate(app(Department::class), \array_merge($request->all(), array("id" => $id)));
            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            $request_inputs = $request->all();
            if (isset($request_inputs['roles'])) {
                $request_inputs['department_roles'] = array_map(function ($item) {
                    return array('role_id' => $item);
                }, $request_inputs['roles']);
            }
            return $this->respondOk(array(
                'data' => $this->provider->modify($id, $request_inputs, [
                    'method' => 'update__department_roles',
                    'upsert'  => false
                ])
            ));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle DELETE /departments/{id}
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function delete(Request $request, int $id)
    {
        try {
            return $this->respondOk(array('data' => $this->provider->delete($id)));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle GET /permissions/{id?}
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function get(Request $request, $id = null)
    {
        if (isset($id)) {
            return $this->respondOk(array('data' => $this->provider->getById($id)));
        }
        $filters = array(
            // Apply orderBy filters
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        );
        $filters = array_merge($filters, app(Department::class)->parseRequestQueryFilters($request));
        return $this->respondOk(
            $this->provider->get($filters, array('*'), true, $request->has('page'), $request->get('per_page'))
        );
    }
}
