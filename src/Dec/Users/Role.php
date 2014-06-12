<?php namespace Dec\Users;

use Illuminate\Database\Eloquent\Model;

class Role extends Model {

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
    protected $table = 'roles';

    /**
     * Validation rules
     * @var array
     */
    protected $rules = [
        'saving' => [
            'name'          => 'required|unique:roles|between:4,16',
            'display_name'  => 'required|unique:roles'
        ]
    ];

    public function users()
    {
        return $this->belongsToMany('User', 'user_roles', 'role_key', 'user_id');
    }

    public function permissions()
    {
        return $this->belongsToMany('Permission', 'role_permissions', 'role_key', 'permission_key');
    }

    /**
     * Attach permission to current role
     *
     * @param $permission
     */
    public function attachPermission($permission)
    {
        if (is_object($permission))
            $permission = $permission->getKey();

        if (is_array($permission))
            $permission = $permission['name'];

        if (!is_string($permission))
            return false;

        $this->permissions()->attach($permission);

        return true;
    }

    /**
     * Attach multiple permissions to current role
     *
     * @param $permissions
     * @access public
     * @return void
     */
    public function attachPermissions($permissions)
    {
        foreach ($permissions as $permission)
        {
            $this->attachPermission($permission);
        }
    }

    /**
     * Detach permission form current role
     *
     * @param $permission
     */
    public function detachPermission($permission)
    {
        if (is_object($permission))
            $permission = $permission->getKey();

        if (is_array($permission))
            $permission = $permission['name'];

        if (!is_string($permission))
            return false;

        $this->permissions()->detach($permission);
    }

    /**
     * Detach multiple permissions from current role
     *
     * @param $permissions
     * @access public
     * @return void
     */
    public function detachPermissions($permissions)
    {
        foreach ($permissions as $permission)
        {
            $this->detachPermission($permission);
        }
    }

    /**
     * Alias for Eloquent ManyToMany::sync(). Overwrites existing.
     * @param  [type] $permissions [description]
     * @return [type]              [description]
     */
    public function syncPermissions($permissions)
    {
        if (!is_array($permissions))
        {
            $this->addError('permissions', 'Not an array');
            return false;
        }

        $sync = [];

        foreach($permissions as $permission)
        {
            if (is_object($permission))
                $name = $permission->getKey();

            if (is_array($permission))
                $name = $permission['name'];

            if (is_string($name))
                $attach[] = $name;
        }

        $this->permissions()->sync($sync);

        return true;
    }

}