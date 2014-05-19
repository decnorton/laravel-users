<?php namespace Dec\Users\Filters;

use \Auth;

class RoleFilter {

    public function filter($route, $request, $role)
    {
        if (Auth::guest() || !Auth::user()->hasRole($role))
            App::abort(401);
    }

}