<?php

namespace App\Http\Modules\Product;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Modules\Product\Product;
use Illuminate\Support\Facades\Schema;
use App\Http\Modules\Presentation\Presentation;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('company:id,name')
            ->visibleThroughCompany(auth()->user());

        return $this->showAll($products, Schema::getColumnListing((new Product)->getTable()));
    }

    public function store(ProductRequest $request)
    {
        $this->authorize('create', Product::class);

        try {
            DB::beginTransaction();

            $product = Product::create($request->validated());
            $product->productCountries()->sync($request->countries);

            Presentation::create([
                'company_id'            => $product->company_id,
                'product_id'            => $product->id,
                'price'                 => $product->suggested_price,
                'is_grouping'           => false,
                'units'                 => 1,
                'description'           => $product->description
            ]);

            DB::commit();

            return $this->showOne($product, 201);
        } catch(Exception $exception) {
            DB::rollback();
            
            Log::error($exception);

            return $this->errorResponse(500, "Ha ocurrido un error interno");
        }
    }

    public function show(Product $product) {
        return $this->showOne($product);
    }

    public function update(ProductRequest $request, Product $product) {
        
        $this->authorize('manage', $product);

        try {
            DB::beginTransaction();
            
            $product->update($request->validated());
            $product->productCountries()->sync($request->countries);

            DB::commit();

            return $this->showOne($product);
        } catch (Exception $exception) {
            DB::rollback();
            
            Log::error($exception);

            return $this->errorResponse(500, "Ha ocurrido un error interno");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Http\Modules\Product\Product  $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Product $product) {
        
        $this->authorize('manage', $product);
        
        if ($product->presentations()->exists()) {
            return $this->errorResponse(409, 'El producto posee presentaciones activas');
        }

        $product->secureDelete();

        return $this->showOne($product);
    }

    /**
     * Display a compact list of the resource for select/combobox options.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function options() {
        $products = Product::select('id', 'description')
            ->visibleThroughCompany(auth()->user())
            ->withOut('productCategory','productSubcategory','brand','all_countries');

        return $this->showAll($products, Schema::getColumnListing((new Product)->getTable()));
    }
}
