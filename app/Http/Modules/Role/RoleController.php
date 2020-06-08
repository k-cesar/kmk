<?php

namespace App\Http\Modules\Role;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use App\Http\Middleware\RoleMiddleware;
use App\Traits\ApiResponser;

class RoleController extends Controller
{
    use ApiResponser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(RoleMiddleware::class)->except('index', 'store');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return $this->showAll($roles);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Modules\Role\RoleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = Role::create($request->validated());

        return $this->showOne($role, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return $this->showOne($role);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Modules\Role\RoleRequest  $request
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, Role $role)
    {
        $role->update($request->validated());

        app()['cache']->forget('spatie.permission.cache');

        return $this->showOne($role);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Spatie\Permission\Models\Role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        $role->delete();

        app()['cache']->forget('spatie.permission.cache');

        return $this->showOne($role);
    }
}
