<?php

namespace App\Http\Modules\Role;

use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

class RoleController extends Controller
{
  /**
   * Display a compact list of the resource for select/combobox options.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function options()
  {
    $roles = Role::select('id', 'name')
      ->where('level', '>=', auth()->user()->role->level);

    return $this->showAll($roles, Schema::getColumnListing((new Role)->getTable()));
  }
}
