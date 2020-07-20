<?php

namespace Drewlabs\Packages\Identity\Traits;

trait AuthorizableController
{

    /**
     * Apply authorization middleware to application resources
     *
     * @param array|mixed $authorizations
     * @return void
     */
    public function applyRessourcePolicyMiddleware($authorizations)
    {
        $policyMidddleWare = \drewlabs_identity_configs('policy_middleware', 'policy');
        $allAuthorization = \drewlabs_identity_configs('all_authorization', \Drewlabs\Packages\Identity\DefaultScopes::SUPER_ADMIN_SCOPE);
        if (isset($authorizations) && is_array($authorizations)) {
            foreach ($authorizations as $value) {
                if (isset($value['authorization']) && isset($value['methods'])) {
                    $this->middleware("$policyMidddleWare:$allAuthorization," . $value['authorization'], ['only' => $value['methods']]);
                }
            }
        }
    }
}
