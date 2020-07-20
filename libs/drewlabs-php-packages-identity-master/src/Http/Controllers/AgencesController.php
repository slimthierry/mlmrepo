<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Contracts\Data\IDataProvider;
use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Illuminate\Http\JsonResponse as Response;
use Illuminate\Http\Request;

class AgencesController extends Controller
{
    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $validator;

    /**
     *
     * @var IDataProvider
     */
    protected $provider;

    public function __construct(IDataProvider $provider, IValidator $validator)
    {
        parent::__construct();
        $this->provider = $provider;
        $this->validator = $validator;
        // $this->middleware('policy:all,create-agences', ['only' => 'store']);
        // $this->middleware('policy:all,update-agences', ['only' => 'update']);
        // $this->middleware('policy:all,delete-agences', ['only' => 'destroy']);
    }

    /**
     * Display a listing of the resource.
     *
     * @route GET /agences[/{$id}]
     *
     * @return Response
     */
    public function index(Request $request, $id = null)
    {
        if (!is_null($id)) {
            return $this->show($request, $id);
        }
        $filters = array(
            // Apply orderBy filters
            'orderBy' => ($request->has('order') && $request->has('by')) ? array('order' => $request->get('order'), 'by' => $request->get('by')) : array('order' => 'desc', 'by' => 'updated_at'),
        );
        return $this->respondOk(
            $this->provider->get($filters, array('*'), true, $request->has('page'), $request->get('per_page'))
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @route POST /agences
     *
     * @param Request $request
     * @return void
     */
    public function store(Request $request)
    {
        try {
            // validate request inputs
            $validator = $this->validator->validate(app(\config('drewlabs_identity.models.agence.class', '\\Drewlabs\\Packages\\Identity\\Agence')), $request->all());
            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            return $this->respondOk(array('data' => $this->provider->create($request->all())));
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->respondError($e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @route GET /agences/{$id}
     *
     * @param Request $request
     * @param mixed $id
     * @return Response
     */
    public function show(Request $request, $id)
    {
        return $this->respondOk(array('data' => $this->provider->getById($id)));
    }


    /**
     * Update the specified resource in storage.
     *
     * @route UPDATE /agences/{$id}
     *
     * @param Request $request
     * @param mixed $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            // validate request inputs
            $validator = $this->validator->setUpdate(true)->validate(app(\config('drewlabs_identity.models.agence.class', '\\Drewlabs\\Packages\\Identity\\Agence')), \array_merge($request->all(), array("id" => $id)));
            if ($validator->fails()) {
                return $this->respondBadRequest($validator->errors());
            }
            return $this->respondOk(array('data' => $this->provider->modify($id, $request->all())));
        } catch (\Exception $e) {
            // Return failure response to request client
            return $this->respondError($e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @route DELETE /agences/{$id}
     *
     * @param Request $request
     * @param mixed $id
     * @return Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            return $this->respondOk(array('data' => $this->provider->delete($id)));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }
}
