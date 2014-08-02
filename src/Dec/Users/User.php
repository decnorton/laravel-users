<?php namespace Dec\Users;

use Event;
use Hash;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\MessageBag;

class User extends Model implements UserInterface, RemindableInterface {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * Attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'password_confirmation'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rulesets = [
        'saving' => [
            'username'              => ['required', 'unique:users', 'regex:/^([a-z0-9]+(?:[-|.]?[a-z0-9]+)+)$/i'],
            'email'                 => ['required', 'unique:users', 'email'],
            'password'              => ['between:6,255', 'confirmed'],
            'password_confirmation' => ['between:6,255']
        ],

        'creating' => [
            'password'              => 'required|between:6,255|confirmed',
        ]
    ];

    protected $validationMessages = [
        'username.regex' => "Username may only contain letters, numbers, dashes and full stops."
    ];

    protected $passwordAttributes = ['password'];

    public static function boot()
    {
        parent::boot();

        Event::listen('validating.passed', function ($model)
        {
            if (is_a($model, __CLASS__))
            {
                // Prepare attributes
                foreach ($model->attributes as $key => $value)
                {
                    // Remove any confirmation fields
                    if (ends_with($key, '_confirmation'))
                    {
                        array_forget($model->attributes, $key);
                        continue;
                    }

                    // Check if this one of our password attributes and if it's been changed.
                    if (in_array($key, $model->passwordAttributes) && $value != $model->getOriginal($key))
                    {
                        // Hash it
                        $model->attributes[$key] = Hash::make($value);
                        continue;
                    }
                }
            }
        });
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the e-mail address where password reminders are sent.
     *
     * @return string
     */
    public function getReminderEmail()
    {
        return $this->email;
    }

    public function getRememberToken()
    {
        return $this->remember_token;
    }

    public function setRememberToken($value)
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Build update rules
     *
     * @param  array    $rules
     * @return array
     */
    protected function buildUpdateRules(array $rules = array())
    {
        /**
         * Make sure an unchanged or empty password doesn't trigger validation
         */
        if (isset($rules['password']))
        {
            if($this->password == $this->getOriginal('password') || empty($this->password))
            {
                unset($rules['password']);
                unset($rules['password_confirmation']);
            }
        }

        return parent::buildUpdateRules($rules);
    }

    /**
     * Relations
     */

    /**
     * Roles - belongs to many roles
     *
     * @return BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany('Role', 'user_roles', 'user_id', 'role_key');
    }

    /**
     * Alias for eloquent many-to-many sync(). Overwrites existing.
     *
     * @param  array $roles
     * @return boolean
     */
    public function syncRoles($roles)
    {
        if (!$roles || !is_array($roles))
        {
            $this->addError('roles', 'No roles');
            return false;
        }

        $sync = [];

        foreach ($roles as $role)
        {
            if (is_object($role))
                $name = $role->getKey();

            if (is_array($role))
                $name = $role['name'];

            if (is_string($name))
                $sync[] = $name;
        }

        $this->roles()->sync($sync);

        return true;
    }

    /**
     * Alias to eloquent many-to-many attach() method. Appends to existing.
     *
     * @param mixed $role
     * @return void
     */
    public function attachRole($role)
    {
        if (!$role) return;

        if (is_object($role))
            $role = $role->getKey();

        if (is_array($role))
            $role = $role['name'];

        if (!$this->hasRole($role))
            $this->roles()->attach($role);
    }

    /**
     * Attach multiple roles to a user
     *
     * @param $roles
     * @return void
     */
    public function attachRoles($roles)
    {
        foreach ($roles as $role)
        {
            $this->attachRole($role);
        }
    }

    /**
     * Alias to eloquent many-to-many relation's
     * detach() method
     *
     * @param mixed $role
     * @return void
     */
    public function detachRole($role)
    {
        if (!$role)
            return;

        if (is_object($role))
            $role = $role->getKey();

        if (is_array($role))
            $role = $role['id'];

        if ($this->hasRole($role))
            $this->roles()->detach($role);
    }

    /**
     * Detach multiple roles from a user
     *
     * @param $roles
     * @access public
     * @return void
     */
    public function detachRoles($roles)
    {
        foreach ($roles as $role)
        {
            $this->detachRole($role);
        }
    }

    /**
     * Checks if the user has a Role by its name
     *
     * @param string    $name.
     * @return boolean
     */
    public function hasRole($role)
    {
        if (!$role)
            return false;

        $name = null;

        if (is_string($role))
            $name = $role;

        if (is_object($role))
            $name = $role->getKey();

        if (!$name)
            return false;

        foreach ($this->roles as $role)
        {
            if ($role->name == $name)
                return true;
        }

        return false;
    }

    /**
     * Check if user has a permission
     *
     * @param string    $permission
     * @return boolean
     */
    public function can($permission)
    {
       $name = null;

       if (is_string($role))
           $name = $role;

       if (is_object($role))
           $name = $role->getKey();

       if (!$name)
           return false;

        foreach ($this->roles as $role)
        {
            // Check role permissions
            foreach ($role->permissions as $perm)
            {
                if ($perm->name == $permission)
                {
                    return true;
                }
            }
        }

        return false;
    }

    public function detachAllRoles()
    {
        $this->detachRoles($this->roles);
    }

    /**
     * Attributes
     */

    /**
     * Cast the ID to an integer
     *
     * @return int
     */
    public function getIdAttribute()
    {
        return (int) $this->attributes['id'];
    }

}
