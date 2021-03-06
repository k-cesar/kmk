<?php

namespace App\Http\Modules\User;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $users = User::visible(auth()->user());

    return $this->showAll($users, Schema::getColumnListing((new User)->getTable()));
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\User\UserRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(UserRequest $request)
  {
    $this->authorize('create', User::class);

    $user = User::create($request->validated());
    $user->syncPermissions(Permission::where('level', '>=', $user->role->level)->get());
    $user->stores()->sync($request->stores);

    return $this->showOne($user, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\User\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(User $user)
  {
    $this->authorize('manage', $user);

    $user->load('stores:id,name');

    return $this->showOne($user);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\User\UserRequest  $request
   * @param  App\Http\Modules\User\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(UserRequest $request, User $user)
  {
    $this->authorize('manage', $user);

    $user->update($request->validated());
    $user->stores()->sync($request->stores);

    return $this->showOne($user);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\User\User  $user
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(User $user)
  {
    $this->authorize('manage', $user);

    $user->secureDelete();

    return $this->showOne($user);
  }
}
