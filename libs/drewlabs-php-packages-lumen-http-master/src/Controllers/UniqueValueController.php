<?php

namespace Drewlabs\Packages\Http\Controllers;

use Drewlabs\Packages\Http\Controllers\RessourcesBaseController as Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\DatabaseManager;

class UniqueValueController extends Controller
{

    /**
     * Undocumented variable
     *
     * @var DatabaseManager
     */
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = app('db');
    }

    /**
     * Handle POST /is_unique
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function get(Request $request)
    {
        $property = $request->has('property') ? $request->get('property') : null;
        $value = $request->get('value');
        $table = $request->has('entity') ? $request->get('entity') : null;
        if (is_null($property) || is_null($table)) {
            $this->respondBadRequest(array('property' => 'proterty field is required', 'entity' => 'entity field is required'));
        }
        try {
            $result = $this->db->connection()->table($table)->where($property, $value)->get();
            if ($result->isNotEmpty()) {
                return $this->respondBadRequest(array('unique' => false));
            }
            return $this->respondOk(array('unique' => true));
        } catch (\Exception $e) {
            return $this->respondError($e);
        }
    }
}
