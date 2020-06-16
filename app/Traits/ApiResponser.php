<?php

namespace App\Traits;

use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Pagination\LengthAwarePaginator;

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
   * @param \Illuminate\Pagination\LengthAwarePaginator $paginator
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  protected function showAll(LengthAwarePaginator $paginator, $code = 200)
  {
    return $this->successResponse($paginator, $code);
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

  /**
   * Returns a LengthAwarePaginator created from data given
   *
   * @param mixed $items
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  protected function paginate($items, $perPage = 15, $page = null, $options = [])
  {
    Validator::validate(request()->all(), [
      "per_page" => "sometimes|integer|min:1|max:100",
    ]);

    $perPage = request()->per_page ?: $perPage;
    
    $items = $items instanceof Collection ? $items : Collection::make($items);

    $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

    $options['path'] = LengthAwarePaginator::resolveCurrentPath();
    
    return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
  }

}