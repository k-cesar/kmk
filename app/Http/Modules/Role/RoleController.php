<?php

namespace App\Http\Modules\Role;

use App\Traits\ApiResponser;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use App\Http\Middleware\RoleMiddleware;

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
    $this->middleware(RoleMiddleware::class)->except('index', 'store', 'options');
  }

  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $roles = Role::query();

    return $this->showAll($roles, Schema::getColumnListing((new Role)->getTable()));
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

  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $roles = Role::select('id', 'name')
      ->where('level', '>=', auth()->user()->getMinimunRoleLevel());

    return $this->showAll($roles, Schema::getColumnListing((new Role)->getTable()));
  }
}
