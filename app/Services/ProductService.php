<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class ProductService extends BaseService
{
    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        // Automatically generate Product Code (e.g. KRN-001) if not provided
        if (empty($data['product_code'])) {
            $data['product_code'] = $this->generateProductCode();
        }

        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            $path = $data['image']->store('products', 'public');
            $data['image'] = $path;
        }

        $product = Product::create($data);

        // Audit Log Placeholder
        // Log::info('Admin created Product', ['product_id' => $product->id]);

        return $product;
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        // Don't update product code after creation
        unset($data['product_code']);

        // Handle image upload
        if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $path = $data['image']->store('products', 'public');
            $data['image'] = $path;
        }

        $product->update($data);

        // Audit Log Placeholder
        // Log::info('Admin updated Product', ['product_id' => $product->id]);

        return $product;
    }

    /**
     * Toggle the active status of a product.
     *
     * @param Product $product
     * @return Product
     */
    public function toggleStatus(Product $product): Product
    {
        $product->update(['status' => !$product->status]);
        
        // Audit Log Placeholder
        // Log::info('Admin toggled Product status', ['product_id' => $product->id, 'new_status' => $product->status]);

        return $product;
    }

    /**
     * Soft delete a product.
     * In the future, we should block deletion if the product is used in orders/visits.
     *
     * @param Product $product
     * @return bool
     */
    public function deleteProduct(Product $product): bool
    {
        // Check if used in other tables (Placeholder for Phase 3/4)
        // if ($product->orders()->exists() || $product->visits()->exists()) {
        //     throw new \Exception("Cannot delete product as it is already in use.");
        // }

        // Audit Log Placeholder
        // Log::info('Admin soft deleted Product', ['product_id' => $product->id]);

        return $product->delete();
    }

    /**
     * Generate a unique Product Code (e.g. KRN-001)
     *
     * @return string
     */
    private function generateProductCode(): string
    {
        $prefix = 'KRN-';
        
        // Find the last product with this prefix
        $lastProduct = Product::where('product_code', 'LIKE', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastProduct) {
            return $prefix . '001';
        }

        // Extract the number part
        $lastNumber = (int) substr($lastProduct->product_code, strlen($prefix));
        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
