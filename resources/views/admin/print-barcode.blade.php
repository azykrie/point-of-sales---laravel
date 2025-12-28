<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            padding: 10px;
        }
        
        .print-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 8px;
        }
        
        .print-header h1 {
            font-size: 24px;
            color: #333;
        }
        
        .print-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-btn:hover {
            background: #2563eb;
        }
        
        .barcode-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .barcode-item {
            border: 1px dashed #ccc;
            padding: 10px;
            text-align: center;
            page-break-inside: avoid;
        }
        
        .barcode-item .product-name {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .barcode-item .product-price {
            font-size: 14px;
            font-weight: bold;
            color: #333;
            margin-top: 5px;
        }
        
        .barcode-item svg {
            max-width: 100%;
            height: 50px;
        }
        
        @media print {
            .print-header {
                display: none;
            }
            
            body {
                padding: 0;
            }
            
            .barcode-grid {
                gap: 5px;
            }
            
            .barcode-item {
                border: 1px dashed #999;
                padding: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h1>Print Barcode ({{ $products->count() }} products)</h1>
        <button class="print-btn" onclick="window.print()">
            🖨️ Print
        </button>
    </div>
    
    <div class="barcode-grid">
        @foreach ($products as $product)
            @if ($product->barcode)
                <div class="barcode-item">
                    <div class="product-name">{{ $product->name }}</div>
                    <svg id="barcode-{{ $product->id }}"></svg>
                    <div class="product-price">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</div>
                </div>
            @endif
        @endforeach
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($products as $product)
                @if ($product->barcode)
                    JsBarcode("#barcode-{{ $product->id }}", "{{ $product->barcode }}", {
                        format: "CODE128",
                        width: 1.5,
                        height: 40,
                        displayValue: true,
                        fontSize: 10,
                        margin: 5
                    });
                @endif
            @endforeach
        });
    </script>
</body>
</html>
