<?php

namespace App\Http\Modules\Permission;

use App\Support\Helper;
use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
  use ApiResponser;
  
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    $permissions = Permission::query();

    return $this->showAll($permissions, Schema::getColumnListing((new Permission)->getTable()));
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \App\Http\Modules\Permission\PermissionRequest  $request
   * @return \Illuminate\Http\Response
   */
  public function store(PermissionRequest $request)
  {
    $permission = Permission::create($request->validated());

    return $this->showOne($permission, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  \Spatie\Permission\Models\Permission  $permission
   * @return \Illuminate\Http\Response
   */
  public function show(Permission $permission)
  {
    return $this->showOne($permission);
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  \App\Http\Modules\Permission\PermissionRequest  $request
   * @param  \Spatie\Permission\Models\Permission  $permission
   * @return \Illuminate\Http\Response
   */
  public function update(PermissionRequest $request, Permission $permission)
  {
    $permission->update($request->validated());

    app()['cache']->forget('spatie.permission.cache');

    return $this->showOne($permission);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  \Spatie\Permission\Models\Permission  $permission
   * @return \Illuminate\Http\Response
   */
  public function destroy(Permission $permission)
  {
    $permission->delete();

    app()['cache']->forget('spatie.permission.cache');

    return $this->showOne($permission);
  }

  /**
   * Remove all unused resources from storage.
   *
   * @return \Illuminate\Http\Response
   */
  public function clean()
  {
    Helper::getUnnecessaryPermissions()
      ->each(function ($permission) {
        $permission->delete();
      });

    app()['cache']->forget('spatie.permission.cache');

    $permissions = Permission::query();

    return $this->showAll($permissions, Schema::getColumnListing((new Permission)->getTable()));
  }
}
