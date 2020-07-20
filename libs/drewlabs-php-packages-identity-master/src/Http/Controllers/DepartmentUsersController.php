<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Contracts\Data\IDataProvider;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Drewlabs\Packages\Identity\DepartmentUser;

class DepartmentUsersController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    /**
     * Database user table associate model instance
     *
     * @var IDataProvider
     */
    private $provider;

    public function __construct(IDataProvider $provider, IValidator $validator)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->validator = $validator;
        $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
        $this->middleware('policy:all,create-departments', ['only' => ['create']]);
        $this->middleware('policy:all,update-departments', ['only' => ['update']]);
        $this->middleware('policy:all,delete-departments', ['only' => ['delete']]);
    }

    /**
     * Handle POST /department_users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // validate request inputs
            $validator = $this->validator->validate(app(DepartmentUser::class), $request->all());

            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            return $this->respondOk(array('data' => $this->provider->create($request->all())));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /department_users/{id}
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            // validate request inputs
            $validator = $this->validator->setUpdate(true)->validate(app(DepartmentUser::class), \array_merge($request->all(), array("id" => $id)));
            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            return $this->respondOk(array('data' => $this->provider->modify($id, $request->all())));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle DELETE /department_users/{id}
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
     * Handle GET /permissions
     *
     * @return JsonResponse
     */
    public function get(Request $request)
    {
        $filters = array(
            // Apply orderBy filters
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        );
        return $this->respondOk(
            $this->provider->get($filters, array('*'), $request->has('page'), $request->get('per_page'))
        );
    }
}
