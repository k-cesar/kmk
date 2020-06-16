<?php

namespace App\Http\Modules\User;

use App\Http\Controllers\Controller;

class UserController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $users = User::paginate();

    return $this->showAll($users);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\User\UserRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(UserRequest $request)
  {
    $user = User::create($request->validated());

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
    $user->update($request->validated());

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
    $user->secureDelete();

    return $this->showOne($user);
  }
}
