<?php namespace Dec\Users;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model {

    /**
     * Disable incrementing IDs
     * @var boolean
     */
    public $incrementing = false;

    /**
     * Set the primary key column
     * @var string
     */
    protected $primaryKey = 'name';

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'permissions';

    /**
     * Validation rules
     * @var array
     */
    public $rules = [
        'saving' => [
            'name'          => 'required|unique:permissions,name|between:4,32',
            'display_name'  => 'required|unique:permissions,name'
        ]
    ];

}
