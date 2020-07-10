<?php

namespace App\Http\Modules\SocioeconomicLevel;

use App\Http\Controllers\Controller;
use App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel;

class SocioeconomicLevelController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $socioeconomicLevels = SocioeconomicLevel::paginate();

    return $this->showAll($socioeconomicLevels);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\SocioeconomicLevel\SocioeconomicLevelRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(SocioeconomicLevelRequest $request)
  {
    $socioeconomicLevel = SocioeconomicLevel::create($request->validated());

    return $this->showOne($socioeconomicLevel, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel  $socioeconomicLevel
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(SocioeconomicLevel $socioeconomicLevel)
  {
    return $this->showOne($socioeconomicLevel);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\SocioeconomicLevel\SocioeconomicLevelRequest  $request
   * @param  App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel  $socioeconomicLevel
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(SocioeconomicLevelRequest $request, SocioeconomicLevel $socioeconomicLevel)
  {
    $socioeconomicLevel->update($request->validated());

    return $this->showOne($socioeconomicLevel);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\SocioeconomicLevel\SocioeconomicLevel  $socioeconomicLevel
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(SocioeconomicLevel $socioeconomicLevel)
  {
    $socioeconomicLevel->secureDelete();

    return $this->showOne($socioeconomicLevel);
  }
}
