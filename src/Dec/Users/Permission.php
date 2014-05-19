<?php namespace Dec\Users;

class Permission extends \Dec\Validation\Model {

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
    public static $rules = [
        'name'          => 'required|unique:permissions|between:4,32',
        'display_name'  => 'required|unique:permissions'
    ];

}
