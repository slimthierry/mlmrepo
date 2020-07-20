<?php

namespace Drewlabs\Packages\MLM\Models;

use Drewlabs\Core\Validator\Contracts\Validatable;
use Drewlabs\Packages\Database\Extensions\IlluminateBaseModel as Model;

class Example extends Model implements Validatable
{
   /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // fillables properties definitions
    ];

    // Table the current model is bind to
    protected $table = 'examples';

    protected $primaryKey = 'id';

    // /**
    //  * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
    //  */
    // public function example_relation()
    // {
    //     // return $this->belongsTo('Related\\Model\\Class', 'related_table_id', 'primary_key');
    // }


    /**
     * @inheritDoc
     */
    public function rules()
    {
        // Returns a laravel validation rules definitions
        return array(
            'label' => ['required']
        );
    }

    /**
     * @inheritDoc
     */
    public function updateRules()
    {
        // Validation rules definition for example resources
        return array(
            'id' => "required|bail|exists:$this->table,$this->primaryKey"
        );
    }

    /**

    * @inheritDoc
     */
    public function messages()
    {
        return array();

   }
}
