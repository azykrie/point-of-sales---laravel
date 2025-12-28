<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class PrintBarcodeController extends Controller
{
    public function __invoke(Request $request)
    {
        $ids = $request->query('ids');
        
        if (empty($ids)) {
            $products = Product::all();
        } else {
            $idArray = explode(',', $ids);
            $products = Product::whereIn('id', $idArray)->get();
        }

        return view('admin.print-barcode', compact('products'));
    }
}
