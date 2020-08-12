<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as QueryBuilder;
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
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection|array $data
   * @param array $allowedFieldsToSearchAndSort
   * @param int $code
   * 
   * @return \Illuminate\Http\JsonResponse
   */
  protected function showAll($data, array $allowedFieldsToSearchAndSort = [], $code = 200)
  {
    $data = $this->normalizeData($data);
    $data = $this->filterData($data, $allowedFieldsToSearchAndSort);
    $data = $this->sortData($data, $allowedFieldsToSearchAndSort);
    $data = $this->paginateData($data);
    $data = $this->extractData($data);

    return $this->successResponse($data, $code);
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
   * Returns a normalized data from data given, casting array given in a collecction,
   * otherwise the data is not altered.
   *
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection|array $data
   * 
   * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection
   */
  private function normalizeData($data)
  {
    $data = is_array($data) ? collect($data) : $data;

    return $data;
  }

  /**
   * Returns a filtered data from data given, based on fields present in the request.
   *
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection $data
   * @param array $allowedFieldsToSearchAndSort
   * 
   * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection
   */
  private function filterData($data, array $allowedFieldsToSearchAndSort)
  {
    Validator::validate(request()->all(), [
      'strict_search' => 'sometimes|integer|in:0,1',
    ]);
      
    $strict_search = request()->get('strict_search', 0);
      
    $operator = $strict_search ? '=' : 'ilike';
    
    foreach (request()->query() as $field => $value) {
      if (in_array($field, $allowedFieldsToSearchAndSort)) {
        if ($data instanceof Builder || $data instanceof QueryBuilder) {
          $data = $data->where($field, $operator, $value);
        } else {
          $data = $data->reject(function($element) use ($field, $value, $strict_search) {
            $storedValue = is_array($element) ? $element[$field] : $element->{$field};
            
            if ($strict_search) {
              return $storedValue != $value;
            } else {
              return mb_strpos(strtolower($storedValue), strtolower($value)) === false;
            }
          });
        }
      }
    }

    if ($data instanceof Collection || $data instanceof EloquentCollection) {
      $data = $data->values();
    }

    return $data;
  }

  /**
   * Returns a sortered data from data given, based on field present in the request.
   *
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection $data
   * @param array $allowedFieldsToSearchAndSort
   * 
   * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection
   */
  private function sortData($data, array $allowedFieldsToSearchAndSort, string $sortType = 'asc')
  {
    Validator::validate(request()->all(), [
      'sort_type' => 'sometimes|string|in:asc,desc',
    ]);
    
    $field = request()->get('sort_by');
    $sortType = request()->sort_type ?: $sortType;

    if (in_array($field, $allowedFieldsToSearchAndSort)) {
      if ($data instanceof Collection || $data instanceof EloquentCollection) {
        $data = $data->sortBy($field, SORT_REGULAR, $sortType=='desc');
        $data = $data->values();
      } else {
        $data = $data->orderBy($field, $sortType);
      }
    }

    return $data;
  }

  /**
   * Returns a paginated data from data given
   *
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection $data
   * 
   * @return \Illuminate\Pagination\LengthAwarePaginator
   */
  private function paginateData($data, $perPage = 15, $page = null, $options = [])
  {
    Validator::validate(request()->all(), [
      'per_page' => 'sometimes|integer|min:1|max:100',
      'paginate' => 'sometimes|integer|in:0,1',
    ]);

    if (!request()->get('paginate', 1)) {
      return $data;
    }

    $perPage = request()->per_page ?: $perPage;

    if ($data instanceof Collection || $data instanceof EloquentCollection) {
      $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
      $options['path'] = LengthAwarePaginator::resolveCurrentPath();
      
      $data = new LengthAwarePaginator($data->forPage($page, $perPage), $data->count(), $perPage, $page, $options);

    } else {
      $data = $data->paginate($perPage);
    }

    $data->appends(request()->all());
    
    return $data;
  }

  /**
   * Returns a extracted data from data given
   *
   * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\Collection|\Illuminate\Pagination\LengthAwarePaginator $data
   * 
   * @return \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Support\Collection
   */
  private function extractData($data)
  {
    if ($data instanceof Builder || $data instanceof QueryBuilder) {
      $data = $data->get();
    }

    if (!isset($data->data)){
      $data = collect(['data' => $data]);
    }
    
    return $data;
  }

}