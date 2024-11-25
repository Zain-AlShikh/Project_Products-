<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    private $filePath;

    public function __construct()
    {
        
        $this->filePath = storage_path('app/products_list.json');
    }

    // 1. Creat 
    public function createProduct(Request $request)
    {
        // الحصول على قيم المدخلات
        $name = $request->input('name');
        $description = $request->input('description');
        $price = $request->input('price');
        $brand = $request->input('brand');

        // التحقق من أن جميع الحقول المطلوبة موجودة وصحيحة
        if (!$name || !$description || !$price || !$brand) {
            return response()->json(['error' => 'All fields (name, description, price, brand) are required.'], 400);
        }

        // التأكد أن السعر هو قيمة عددية وصحيحة
        if (!is_numeric($price) || $price < 0) {
            return response()->json(['error' => 'Price must be a positive number.'], 400);
        }

        $data = json_decode(file_get_contents($this->filePath), true);

        $newProduct = [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'brand' => $brand,
        ];

        $data[] = $newProduct;

        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Product created successfully', 'product' => $newProduct], 201);
    }


    public function listAllProducts()
    {
        $data = json_decode(file_get_contents($this->filePath), true);
        return response()->json([
            'message' => 'Retrieved successfully',
            $data
        ]);
    }


    public function deleteProductById($productId)
    {

        $data = json_decode(file_get_contents($this->filePath), true);


        if ($productId < 0 || $productId >= count($data)) {
            return response()->json(['error' => 'Invalid product ID.'], 400);
        }



        unset($data[$productId]);


        $data = array_values($data);


        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }


    
    public function updateProductName(Request $request, $index)
    {
        $data = json_decode(file_get_contents($this->filePath), true);

        if (!isset($data[$index])) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $data[$index]['name'] = $request->input('new_name');

        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT));

        return response()->json(['message' => 'Product name updated successfully', 'product' => $data[$index]], 200);
    }
}
