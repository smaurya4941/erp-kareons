<?php

namespace App\Http\Controllers\Api\Mr;

use App\Http\Controllers\Api\BaseApiController;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    /**
     * Read-only catalogue of active products for MRs.
     *
     * Used by the mobile app to populate product pickers when recording
     * doctor visits, discussed products, sample distributions and orders.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->where('status', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('product_code', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('strength', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $query->orderBy('name', 'asc');

        // Support both a full list (per_page=all) and paginated fetches.
        if ($request->input('per_page') === 'all') {
            return $this->successResponse(
                ProductResource::collection($query->get()),
                'Products retrieved successfully.'
            );
        }

        $products = $query->paginate($request->input('per_page', 50));

        return $this->successResponse(
            ProductResource::collection($products)->response()->getData(true),
            'Products retrieved successfully.'
        );
    }
}
