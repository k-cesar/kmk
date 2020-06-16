<?php

namespace App\Http\Modules\Company;

use App\Http\Controllers\Controller;

class CompanyController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\JsonResponse
   */
  public function index()
  {
    $companys = Company::paginate();

    return $this->showAll($companys);
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  App\Http\Modules\Company\CompanyRequest  $request
   * @return \Illuminate\Http\JsonResponse
   */
  public function store(CompanyRequest $request)
  {
    $company = Company::create($request->validated());

    return $this->showOne($company, 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  App\Http\Modules\Company\Company  $company
   * @return \Illuminate\Http\JsonResponse
   */
  public function show(Company $company)
  {
    return $this->showOne($company);
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  App\Http\Modules\Company\CompanyRequest  $request
   * @param  App\Http\Modules\Company\Company  $company
   * @return \Illuminate\Http\JsonResponse
   */
  public function update(CompanyRequest $request, Company $company)
  {
    $company->update($request->validated());

    return $this->showOne($company);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  App\Http\Modules\Company\Company  $company
   * @return \Illuminate\Http\JsonResponse
   */
  public function destroy(Company $company)
  {
    $company->secureDelete();

    return $this->showOne($company);
  }
}
