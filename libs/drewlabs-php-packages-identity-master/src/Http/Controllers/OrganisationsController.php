<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Contracts\Data\IDataProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Identity\Organisation;
use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Drewlabs\Core\Validator\Contracts\IValidator;

class OrganisationsController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    // /**
    //  * Database user table associate model instance
    //  *
    //  * @var IUserModel
    //  */
    // private $userModel;

    /**
     *
     * @var IDataProvider
     */
    private $provider;

    public function __construct(IDataProvider $provider, IValidator $validator)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->validator = $validator;
        // $this->userModel = $userModel;
        $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
        $this->middleware('policy:all,list-organisations', ['only' => ['get']]);
        $this->middleware('policy:all,create-organisations', ['only' => ['create']]);
        $this->middleware('policy:all,update-organisations', ['only' => ['update']]);
        $this->middleware('policy:all,delete-organisations', ['only' => ['delete']]);
    }

    /**
     * Handle POST /organisations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        try {
            // validate request inputs
            $request_inputs = array_merge($request->all(), array(
                'wallet_id' => null
            ));
            $validator = $this->validator->validate(app(Organisation::class), $request_inputs);

            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            return $this->respondOk(array('data' => $this->provider->create($request_inputs)));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /organisations/{id}
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            $request_inputs = array_merge($request->all(), array(
                'organisations_wallet_id' => null
            ));
            // validate request inputs
            $validator = $this->validator->setUpdate(true)->validate(app(Organisation::class), \array_merge($request_inputs, array("id" => $id)));
            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            return $this->respondOk(array('data' => $this->provider->modify($id, $request_inputs)));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle DELETE /organisations/{id}
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
        return $this->respondOk(
            $this->provider->get($filters, array('*'), true, $request->has('page'), $request->get('per_page'))
        );
    }
}
