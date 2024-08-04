<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Barcode; // Make sure to include this line
use Milon\Barcode\DNS1D;
use App\Models\Barcode;

use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\File;
// use Illuminate\Support\Facades\Log;
use Picqer\Barcode\BarcodeGeneratorPNG;


// use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
// use Milon\Barcode\BarcodeGeneratorPNG;



use Picqer\Barcode\BarcodeGeneratorITF;

class BarcodeController extends Controller
{

    // public function generateApi(Request $request)
    // {

    //     $sku = 'PT' . rand(1000, 99999);

    //     $redColor = [255, 0, 0];
    //     $generator = new BarcodeGeneratorPNG();
    //     $path = public_path('barcode/');

    //     !is_dir($path) &&
    //         mkdir($path, 0777, true);
    //     $barcode = $generator->getBarcode($sku, $generator::TYPE_CODE_128, 3, 50, $redColor);
    //     file_put_contents("barcode/$sku", $barcode);
    //     $data['sku'] = $sku;
    //     // Product::create($data);
    //     return response()->json(['status' => 0, 'data' =>  $barcode], 500);
    // }




    private function generateApi($name)
    {

        $sku = $name . rand(1000, 99999);
        $sku = utf8_encode($sku);
        $blackColor = [0, 0, 0];
        $generator = new BarcodeGeneratorPNG();
        $path = public_path('barcode/');
        !is_dir($path) && mkdir($path, 0777, true);
        $barcode = $generator->getBarcode($sku, $generator::TYPE_CODE_128, 3, 50, $blackColor);

        $imagePath = "barcode/$sku.png";
        file_put_contents($imagePath, $barcode);
        Log::info('Generated SKU: ' . $sku);
        Log::info('Generated Barcode: ' . $barcode);

        $data['sku'] = $sku;
        $data['barcode_link'] = asset($imagePath);
    }





    // public function generateApi(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'order_id' => 'required|numeric',
    //             'name' => 'required|string',
    //         ]);

    //         // Generate a unique SKU for the barcode
    //         $sku = 'PT' . rand(1000, 99999);

    //         // Instantiate the barcode generator for ITF
    //         $barcode = new DNS1D();
    //         $barcode->setStorPath(public_path('adminProfile/barcodeImages/'));

    //         // Concatenate order_id and name to create a unique value for the barcode
    //         $value = $request->order_id . ' - ' . $request->name;
    //         $value = preg_replace('/\D/', '', $value);

    //         $barcodeImage = $barcode->getBarcodePNG($value, 'ITF');

    //         if (!$barcodeImage) {
    //             throw new \Exception('Barcode generation failed.');
    //         }

    //         // Save the barcode image to the specified directory
    //         $imagePath = 'adminProfile/barcodeImages/';
    //         $fileName = $sku . '_itf.png';
    //         $filePath = public_path($imagePath . $fileName);
    //         file_put_contents($filePath, $barcodeImage);

    //         // Return the barcode URL in the response
    //         $url = asset($imagePath . $fileName);

    //         return response()->json(['barcode_url' => $url]);
    //     } catch (\Exception $e) {
    //         // Log the exception with details
    //         Log::error('Exception in generateApi: ' . $e->getMessage(), ['trace' => $e->getTrace()]);

    //         // Return a response with a specific error message
    //         return response()->json(['status' => 0, 'error' => $e->getMessage()], 500);
    //     }
    // }

}
