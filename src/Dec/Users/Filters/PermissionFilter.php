<?php namespace Dec\Users\Filters;

use \Auth;

class PermissionFilter {

    public function filter($route, $request, $permission)
    {
        if (Auth::guest() || !Auth::user()->can($permission))
            App::abort(401);
    }

}