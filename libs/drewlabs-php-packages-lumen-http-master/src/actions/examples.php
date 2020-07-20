<?php //

const ExampleProvider = "\\Drewlabs\\Packages\\Http\\Servvices\\Contracts\\ExampleDataProvider";
const ExampleClass = "\\Drewlabs\\Packages\\Http\\Models\\Example";

/*
|--------------------------------------------------------------------------
| Route actions definitions for forum types ressources actions
|--------------------------------------------------------------------------
|
*/
return [
    "actions" => [
        "index" => [
            /**
             * Closure for parsing request and preparing data provider query. Note this can be declare as simple array
             * <code>
             * function(\Illuminate\Http\Request $request) {
             *  return array(); // Returns a new array for applying no query parameter
             * }
             * </code>
             */
            "queryBuilder" => function (\Illuminate\Http\Request $request) {
                return [];
            },
            /**
             * Closure applying policies to the request action. Note: Closure must return a boolean value indicating authorization process result. This can be a boolean value.
             * <code>
             * function(\Illuminate\Http\Request $request) {
             *  return true;
             * }
             * </code>
             */
            "gatePolicy" => function (\Drewlabs\Contracts\Data\IDataProvider $provider, \Illuminate\Http\Request $request) {
                // Apply gate polcicy on the current routing action
                return true;
            },
            /**
             * Request validation handler. If this key is set to null | to a handler that returns null, no v alidation is applied
             * <code>
             * function (\Drewlabs\Core\Validator\Contracts\IValidator $validator, \Illuminate\Http\Request $request) {
             *  return null;
             * }
             * </code>
             */
            "validateRequestBody" => function (\Drewlabs\Core\Validator\Contracts\IValidator $validator, \Illuminate\Http\Request $request) {
                // Form request, this method return the validatable model
                return null;
            },
            // Pass an array of related table entries to load along with this ressource or true to load all relations
            "relationQuery" => true,
            /**
             * Apply data transformation to the data provider call result. If null, no transformation is applied to the provider result
             * <code>
             * function ($body, $request) {
             *  return $body;
             * }
             * </code>
             */
            "transformResponseBody" => null
        ],
        "show" => [
            /**
             * Closure applying policies to the request action. Note: Closure must return a boolean value indicating authorization process result. This can be a boolean value.
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return true;
             * }
             * </code>
             */
            "gatePolicy" => function (\Drewlabs\Contracts\Data\IDataProvider $provider, \Illuminate\Http\Request $request, $id) {
                // Apply gate polcicy on the current routing action
                return true;
            },
            /**
             * Closure for parsing request and preparing data provider query. Note: This can return array of queries| or model identifier to be applied to the model
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return $id;
             * }
             * </code>
             */
            "queryBuilder" => function ($request, $id) {
                // Build and return query to be applied to the data provider
                return $id;
            },
            /**
             * Apply data transformation to the data provider call result. If null, no transformation is applied to the provider result
             * <code>
             * function ($body, $request) {
             *  return $body;
             * }
             * </code>
             */
            "transformResponseBody" => null
        ],
        "store" => [
            /**
             * Request body transformation handler closure
             * A simple request body transformation closure
             * <code>
             * function (\Illuminate\Http\Request $request) {
             *   return $request->all();
             * }
             * </code>
             */
            "transformRequestBody" => function (\Illuminate\Http\Request $request) {
                // Apply request transformation logic in this function
                return $request->all();
            },
            /**
             * Closure applying policies to the request action. Note: Closure must return a boolean value indicating authorization process result. This can be a boolean value.
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return true;
             * }
             * </code>
             */
            "gatePolicy" => function (\Drewlabs\Contracts\Data\IDataProvider $provider, \Illuminate\Http\Request $request) {
                // Apply gate polcicy on the current routing action
                return true;
            },
            /**
             * Request validation handler. If this key is set to null | to a handler that returns null, no v alidation is applied
             * <code>
             * function (\Drewlabs\Core\Validator\Contracts\IValidator $validator, \Illuminate\Http\Request $request) {
             *  return null;
             * }
             * </code>
             */
            "validateRequestBody" => function (\Drewlabs\Core\Validator\Contracts\IValidator $validator, \Illuminate\Http\Request $request) {
                // Form request, this method return the validatable model
                $validator = $validator->validate(app(ForumTypeClazz), $request->all());
                return $validator->errors();
            },
            /**
             * Closure for parsing request and preparing data provider query. Note: This can return array of queries| or model identifier to be applied to the model
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return $id;
             * }
             * </code>
             */
            "queryBuilder" => function ($request, $id) {
                // Build and return query to be applied to the data provider
                return $id;
            },
            /**
             * Data provider DataProviderCreateHandlerParams definitions.
             * Note: It can be an array, an instance of [[\Drewlabs\Core\Data\DataProviderCreateHandlerParams]] class or a null value, or a closure returning
             * on of the preceding
             * <code>
             * array(
             *      'method' => 'insert__relationMethod1_relationMethod2' // insert // insertMany -> Default: insert,
             *      'upsert' => false, // boolean
             *      'upsert_conditions' => [] , // array / null
             * )
             *
             * function (array $attributes) {
             *     return new \Drewlabs\Core\Data\DataProviderCreateHandlerParams([
             *         'method' => 'insert',
             *         'upsert' => false,
             *         'upsert_conditions' => []
             *     ]);
             * }
             * </code>
             */
            "providerHandlerParam" => function ($attributes = []) {
                return null;
            },
            /**
             * Apply data transformation to the data provider call result. If null, no transformation is applied to the provider result
             * <code>
             * function ($body, $request) {
             *  return $body;
             * }
             * </code>
             */
            "transformResponseBody" => null
        ],
        "update" => [
            /**
             * Request body transformation handler closure
             * A simple request body transformation closure
             * <code>
             * function (\Illuminate\Http\Request $request) {
             *   return $request->all();
             * }
             * </code>
             */
            "transformRequestBody" => function (\Illuminate\Http\Request $request) {
                // Apply request transformation logic in this function
                return $request->all();
            },
            /**
             * Closure for parsing request and preparing data provider query. Note: This can return array of queries| or model identifier to be applied to the model
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return $id;
             * }
             * </code>
             */
            "queryBuilder" => function ($request, $id) {
                // Build and return query to be applied to the data provider
                return $id;
            },
            /**
             * Closure applying policies to the request action. Note: Closure must return a boolean value indicating authorization process result. This can be a boolean value.
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return true;
             * }
             * </code>
             */
            "gatePolicy" => true,
            /**
             * Request validation handler. If this key is set to null | to a handler that returns null, no v alidation is applied
             * <code>
             * function (\Drewlabs\Core\Validator\Contracts\IValidator $validator, \Illuminate\Http\Request $request) {
             *  return null;
             * }
             * </code>
             */
            "validateRequestBody" => function (\Drewlabs\Core\Validator\Contracts\IValidator $validator, \Illuminate\Http\Request $request, $id) {
                // Form request, this method return the validatable model
                $validator = $validator->setUpdate(true)->validate(app(ForumTypeClazz), array_merge($request->all(), array('id' => $id)));
                return $validator->errors();
            },
            /**
             * Data provider DataProviderUpdateHandlerParams definitions.
             * Note: It can be an array, an instance of [[\Drewlabs\Core\Data\DataProviderUpdateHandlerParams]] class or a null value, or a closure returning
             * on of the preceding
             * <code>
             * array(
             *      'method' => 'update__relationMethod1_relationMethod2' // update // -> Default: updateById,
             *      'upsert' => false, // boolean
             *      'should_mass_update' => false
             * )
             *
             * function (array $attributes) {
             *     return new \Drewlabs\Core\Data\DataProviderUpdateHandlerParams([
             *         'method' => 'update__relationMethod1_relationMethod2' // update // -> Default: updateById,
             *         'upsert' => false,
             *         'should_mass_update' => false
             *     ]);
             * }
             * </code>
             */
            "providerHandlerParam" => function (array $attributes) {
                return null;
            },
            /**
             * Apply data transformation to the data provider call result. If null, no transformation is applied to the provider result
             * <code>
             * function ($body, $request) {
             *  return $body;
             * }
             * </code>
             */
            "transformResponseBody" => null
        ],
        "destroy" => [
            /**
             * Closure for parsing request and preparing data provider query. Note: This can return array of queries| or model identifier to be applied to the model
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return $id;
             * }
             * </code>
             */
            "queryBuilder" => function ($request, $id) {
                // Build and return query to be applied to the data provider
                return $id;
            },
            /**
             * Closure applying policies to the request action. Note: Closure must return a boolean value indicating authorization process result. This can be a boolean value.
             * <code>
             * function(\Illuminate\Http\Request $request, $id) {
             *  return true;
             * }
             * </code>
             */
            "gatePolicy" => true,
            // Whether should apply mass delete action
            "massDelete" => false,
            /**
             * Apply data transformation to the data provider call result. If null, no transformation is applied to the provider result
             * <code>
             * function ($body, $request) {
             *  return $body;
             * }
             * </code>
             */
            "transformResponseBody" => null
        ]
    ],
    "provider" => ForumTypesProvider,
    "class" => ForumTypeClazz
];
