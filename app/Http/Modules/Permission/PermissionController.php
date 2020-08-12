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
    $permissions = Permission::all()
      ->map(function (Permission $permission) {
        $permission->name = explode(' ', $permission->name)[0];

        return $permission;
      });

    return $this->showAll($permissions, Schema::getColumnListing((new Permission)->getTable()));
  }
}