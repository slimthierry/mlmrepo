<?php

namespace Drewlabs\Packages\Identity\Http\Controllers;

use Drewlabs\Contracts\Data\IModelFilter;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Core\Validator\Contracts\IValidator;
use Drewlabs\Packages\Identity\UserInfo as IdentityUserInfo;

class UserInfosController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var IValidator
     */
    private $viewmodel_validator;

    /**
     * Database user table associate model instance
     *
     * @var IUserModel
     */
    private $userModel;

    public function __construct(BaseIlluminateModelRepository $repository, IUserModel $userModel, IValidator $validator)
    {
        parent::__construct();
        $this->repository = $repository->setModel(IdentityUserInfo::class);
        $this->viewmodel_validator = $validator;
        $this->userModel = $userModel;
        $this->middleware("scope:" . \config('passport.first_party_clients_scope'));
    }

    /**
     * Handle POST /organisations
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        // validate request inputs
        $request_inputs = $request->all();
        $validator = $this->viewmodel_validator->validate(app(IdentityUserInfo::class), $request_inputs);
        if ($validator->fails()) {
            return $this->respondBadRequest($validator->errors());
        }
        try {
            $result = $this->repository->insert($request_inputs, true);
            return $this->respondOk($result);
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }

    /**
     * Handle PUT /user_infos[/{id}]
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function update(Request $request, $id = null)
    {
        $id = is_null($id) ? $this->userModel->fromAuthenticatable($request->user())->user_info->getKey() : $id;
        // validate request inputs
        $validator = $this->viewmodel_validator->setUpdate(true)->validate(app(IdentityUserInfo::class), \array_merge($request->all(), array("id" => $id)));
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
     * Handle DELETE /user_infos/{id}
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

    /**
     * Handle GET /user_infos
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
            array(
                // Get all item if the there is no page query parameter in the request, else call the pagination function on the repository
                'user_infos' => $request->has('page') ?  $this->repository->pushFilter(
                    app(IModelFilter::class)->setQueryFilters($filters)
                )->queryRelation(true)->paginate($request->has('per_page') ? $request->get('per_page') : null) :
                array(
                    "data" => $this->repository->pushFilter(
                        app(IModelFilter::class)->setQueryFilters($filters)
                    )->queryRelation(true)->find()
                ),
            )
        );
    }
}
