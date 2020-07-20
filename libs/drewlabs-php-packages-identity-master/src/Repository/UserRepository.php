<?php

namespace Drewlabs\Packages\Identity\Repository;

use Drewlabs\Packages\Database\Extensions\IlluminateModelRepository as ModelRepository;
use Drewlabs\Packages\Database\Extensions\CustomQueryCriteria;
use Drewlabs\Packages\Identity\RoleUser;
use Drewlabs\Contracts\Auth\IUserModel;
use Drewlabs\Packages\Database\Extensions\BaseIlluminateModelRepository;
use Drewlabs\Packages\Identity\Role;

class UserRepository extends ModelRepository
{
    /**
     * Insert new user entry to the data storage with the related role
     *
     * @param array $values
     * @param string|null $label
     * @param bool $from_state_map
     * @return void
     */
    public function insertWithRole(array $values, ?string $label = null, bool $from_state_map = false)
    {
        $created_entry = $this->insert($values, $from_state_map);
        if (isset($label)) {
            $query_filters = new CustomQueryCriteria(
                array(
                    'where' => array(array('label', $label))
                )
            );
            $r = app(BaseIlluminateModelRepository::class)->setModel(Role::class);
            $entry = $r->pushFilter($query_filters)->find()->first();
            $r->insert(array(
                RoleUser::getUserIdFieldName() => $created_entry->{$this->modelPrimaryKey()},
                RoleUser::roleIdFieldName() => $entry->{$entry->getPrimaryKey()}
            ));
        }
        return $created_entry;
    }

    /**
     * Convert a list of IUserModel to an iterator of IAuthenticatable objects
     *
     * @param \ArrayAccess|array|Collection $list
     * @return \Traversable
     */
    public function authenticatableGenerator($list)
    {
        foreach ($list as $value) {
            # code...
            yield $value->toAuthenticatable();
        }
    }

    /**
     * @inheritDoc
     */
    public function getModel()
    {
        return IUserModel::class;
    }
}
