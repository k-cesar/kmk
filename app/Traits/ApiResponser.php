<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait ApiResponser
{
  /**
   * Returns a success a response in json format
   *
   * @param mixed $data
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  private function successResponse($data, $code)
  {
    return response()->json($data, $code);
  }

  /**
   * Returns a error response in json format
   *
   * @param int $code
   * @param string $message
   * @param string|array $errors
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  protected function errorResponse($code, $message, $errors=null)
  {
    return response()->json(['code' => $code, 'message' => $message, 'errors' => $errors], $code);
  }

  /**
   * Returns a response with the collection given in json format 
   *
   * @param \Illuminate\Support\Collection $collection
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  protected function showAll(Collection $collection, $code = 200)
  {
    return $this->successResponse(['data' => $collection], $code);
  }

  /**
   * Returns a response with the instance of Model given in json format 
   *
   * @param \Illuminate\Database\Eloquent\Model $model
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  protected function showOne(Model $instance, $code = 200)
  {
    return $this->successResponse($instance, $code);
  }

  /**
   * Returns a response with the message given in json format 
   *
   * @param string $message
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  protected function showMessage($message, $code = 200)
  {
    return $this->successResponse(['message' => $message], $code);
  }

}