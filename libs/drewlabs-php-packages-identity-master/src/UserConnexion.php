<?php

namespace Drewlabs\Packages\Identity;

use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

class UserConnexion extends Model
{
    protected $table = "user_connexions";
    protected $entityIdentifier = "usr_connexions";
    protected $primaryKey = "user_connexion_id";

    protected $fillable = [
        "identifier",
        "user_connexion_status",
        "user_connexion_ip_address",
    ];

    protected $model_states = [
        "usr_connexions_identifier" => "identifier",
        "usr_connexions_user_connexion_status" => "user_connexion_status",
        "usr_connexions_user_connexion_ip_address" => "user_connexion_ip_address",
    ];

    protected $relations = [];
}
