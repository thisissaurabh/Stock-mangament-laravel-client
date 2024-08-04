<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserCustomer;
use Illuminate\Support\Facades\Validator;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Models\Purchase;
use App\Models\Item;
use  Illuminate\Support\Facades\Log;
use App\Models\Barcode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use PhpParser\Builder\Function_;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function getAllSipplier(Request  $request)
    {

        $user = $request->user();
        $userRole = $request->user()->role;
        if ($userRole == 'pos') {
            return response()->json(['status' => 0, 'message' => 'Unauthorized user'], 401);
        }

        $allUserId = [];
        if (empty($user->user_added_by)) {
            $user_added_by = $user->id;
            $allUserId[] = $user->id;
        } else {
            $user_added_by = $user->user_added_by;
        }
        $allUser = User::where('user_added_by', $user_added_by)->get();
        foreach ($allUser as $user_all) {
            $allUserId[] = $user_all->id;
        }

        $supplierData = UserCustomer::where('user_customer_type', 'supplier')
            ->whereIn('user_id', $allUserId)
            ->select('id', 'first_name', 'second_name')
            ->get();

        return response()->json(['status' => 1, 'AllsupplierData' => $supplierData], 200);
    }


    public function getSipplierById(Request $request, $supplierId)
    {
        $user = $request->user();
        $userRole = $request->user()->role;
        if ($userRole == 'pos') {
            return response()->json(['status' => 0, 'message' => 'Unauthorized user'], 401);
        }
        $supplierData = UserCustomer::where('user_customer_type', 'supplier')
            ->where('id', $supplierId)
            ->first();

        if ($supplierData) {
            return response()->json(['status' => 1, 'data' => $supplierData], 200);
        } else {
            return response()->json(['status' => 0, 'message' => 'Supplier not found'], 404);
        }
    }

    public function storeItems(Request $request)
    {


        $user = $request->user();
        $user_id = $user->id;
        $userRole = $request->user()->role;

        $user_added_by = empty($user->user_added_by) ? $user->id : $user->user_added_by;
        $allUser = User::where('user_added_by', $user_added_by)->get();
        $allUserId = $allUser->pluck('id')->push($user->id)->toArray();

        if ($userRole == 'pos') {
            return response()->json(['status' => 0, 'message' => 'Unauthorized user'], 401);
        }

        $validatedData = $request->validate([
            'suppliers_id' => 'required|exists:user_customers,id',
            'group_id' => 'required|exists:groups,id',
            'brand_id' => 'required|exists:groups_brand,id',
            // 'user_add' => 'required|exists:users,id',
            'purchase_invoice_no' => [
                'required',
                Rule::unique('purchases')->where(function ($query) use ($user_id) {
                    return $query->where('user_add', $user_id);
                }),
            ],
            'challan_no' => 'required',
            'invoice_date' => 'required|date',
            'coding_type' => 'required|in:unique,lot',
            'description' => 'nullable',

            // 'code' => 'required|unique:items,code',
            'name' => 'required',
            'design_name' => 'required',
            'color' => 'nullable',
            'size' => 'nullable',
            'hsn_code' => 'required',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required',
            'purchase_price' => 'required|numeric|min:0',
            'discount' => 'numeric|min:0',
            'tax' => 'numeric|min:0',
            'sell_mrp' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'item_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

        ]);


        try {


            $invoiceDate = Carbon::createFromFormat('d-m-Y', $request->invoice_date)->format('Y-m-d');

            $purchase = new Purchase();
            $purchase->user_add = $user_id;
            $purchase->suppliers_id = $request->suppliers_id;
            $purchase->purchase_invoice_no = $request->purchase_invoice_no;
            $purchase->challan_no = $request->challan_no;
            $purchase->invoice_date =  $invoiceDate;
            $purchase->coding_type = $request->coding_type;
            $purchase->description = $request->description;
            $purchase->save();
            $lastInsertedId = $purchase->id;

            if ($request->coding_type == 'unique') {
                $item = new Item();
                $item->purchase_id = $lastInsertedId;
                $item->group_id = $request->group_id;
                $item->brand_id = $request->brand_id;
                $item->name = $request->name;
                $item->design_name = $request->design_name;
                $item->hsn_code = $request->hsn_code;
                $item->quantity = $request->quantity;
                $item->unit = $request->unit;
                $item->purchase_price = $request->purchase_price;
                $item->sell_mrp = $request->sell_mrp;
                $item->total = $request->total;
                $item->discount = $request->discount;
                $item->color = $request->color;
                $item->size = $request->size;

                if ($request->hasFile('item_image')) {
                    $image = $request->file('item_image');
                    $imageName = time()  . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('adminProfile/itemImage'), $imageName);
                    $item->item_image = '/adminProfile/itemImage/' . $imageName;
                }

                $item->save();

                for ($i = 0; $i < $request->quantity; $i++) {

                    $shortName = 'PI';
                    $code = $this->generateCode($shortName);
                    $barcode = $this->generateBarcode($code);

                    $barCodeForItem = new Barcode();
                    $barCodeForItem->item_id = $item->id;
                    $barCodeForItem->code = $code;
                    $barCodeForItem->image_url =  $barcode;
                    $barCodeForItem->save();
                }
            } else {

                $shortName = 'PI';
                $code = $this->generateCode($shortName);
                $item = new Item();
                $item->purchase_id = $lastInsertedId;
                $item->group_id = $request->group_id;
                $item->brand_id = $request->brand_id;
                $item->code =  $code;
                $item->name = $request->name;
                $item->design_name = $request->design_name;
                $item->hsn_code = $request->hsn_code;
                $item->quantity = $request->quantity;
                $item->unit = $request->unit;
                $item->purchase_price = $request->purchase_price;
                $item->sell_mrp = $request->sell_mrp;
                $item->total = $request->total;
                $item->discount = $request->discount;
                $item->color = $request->color;
                $item->size = $request->size;

                if ($request->hasFile('item_image')) {
                    $image = $request->file('item_image');
                    $imageName = time()  . '.' . $image->getClientOriginalExtension();
                    $image->move(public_path('adminProfile/itemImage'), $imageName);
                    $item->item_image = '/adminProfile/itemImage/' . $imageName;
                }
                $item->save();

                $itemInsertedId =  $item->id;
                $barcode = $this->generateBarcode($code);
                $barCodeForItem  = new Barcode();
                $barCodeForItem->item_id = $itemInsertedId;
                $barCodeForItem->code =  $code;
                $barCodeForItem->image_url =  $barcode;
                $barCodeForItem->save();
            }

            $purchase = Purchase::with('items.barcode')->findOrFail($lastInsertedId);

            return response()->json(['status' => 1, 'message' => 'Item added successfull', 'data' => $purchase], 200);
        } catch (\Exception $th) {
            return response()->json(['status' => 0, 'message' => 'Internal Server Error', 'error' => $th->getMessage()], 400);
        }
    }


    public function getPurchaseWithItemsAndBarcode($purchaseId)
    {
        try {
            $purchase = Purchase::with('items.barcode')->findOrFail($purchaseId);

            // Iterate through items and barcodes to update URLs
            foreach ($purchase->items as $item) {
                if ($item->hasFile('item_image')) {
                    $item->image_url = url($item->image_url);
                }
                $item->barcode->image_url = url($item->barcode->image_url);
            }

            return response()->json(['status' => 1, 'data' => $purchase], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'message' => 'Purchase not found', 'error' => $e->getMessage()], 404);
        }
    }



    private function generateCode($companyShortName)
    {

        $generatedCode = $companyShortName . '00000001';
        $existingBarcode = Barcode::where('code', $generatedCode)->first();
        while ($existingBarcode) {
            $serialVoice = intval(substr($generatedCode, -8));
            $serialVoice++;
            $generatedCode = $companyShortName . str_pad($serialVoice, 8, '0', STR_PAD_LEFT);
            $existingBarcode = Barcode::where('code', $generatedCode)->first();
        }
        return $generatedCode;
    }

    // private function generateBarcode($generatedCode)
    // {
    //     $sku = $generatedCode;
    //     $sku = utf8_encode($sku);
    //     $whiteColor = [255, 255, 255];
    //     $generator = new BarcodeGeneratorPNG();

    //     // Change the directory path
    //     $directoryPath = public_path('adminProfile/barcode/');

    //     // Create the directory if it doesn't exist
    //     if (!is_dir($directoryPath)) {
    //         mkdir($directoryPath, 0777, true);
    //     }

    //     $barcode = $generator->getBarcode($sku, $generator::TYPE_CODE_128, 3, 50, $whiteColor);
    //     $imagePath = $directoryPath . "$sku.png";

    //     // Use file_put_contents with the full path
    //     file_put_contents($imagePath, $barcode);

    //     Log::info('Generated SKU: ' . $sku);
    //     Log::info('Generated Barcode: ' . $barcode);

    //     return $imagePath;
    // }

    private function generateBarcode($generatedCode)
    {
        $sku = $generatedCode;
        $sku = utf8_encode($sku);
        $blackColor = [0, 0, 0];
        $generator = new BarcodeGeneratorPNG();
        $originalDirectoryPath = public_path('adminProfile/barcode/');

        if (!is_dir($originalDirectoryPath)) {
            mkdir($originalDirectoryPath, 0777, true);
        }

        $barcode = $generator->getBarcode($sku, $generator::TYPE_CODE_128, 3, 50, $blackColor);
        $originalImagePath = $originalDirectoryPath . "$sku.png";
        file_put_contents($originalImagePath, $barcode);

        Log::info('Generated SKU: ' . $sku);
        Log::info('Generated Barcode: ' . $barcode);
        $relativeImagePath = '/adminProfile/barcode/' . "$sku.png";
        rename($originalImagePath, public_path($relativeImagePath));

        return $relativeImagePath;
    }


    public function SearchSupplierCustomer(Request $request)
    {
        $search = $request->search;
        $user_customer_type = $request->user_customer_type;
        $supplier = UserCustomer::where('first_name', 'like', '%' . $search . '%')
            ->orWhere('second_name', 'like', '%' . $search . '%')
            ->orWhere('phone', 'like', '%' . $search . '%')
            ->orWhere('mail', 'like', '%' . $search . '%')
            ->orWhere('gst_no', 'like', '%' . $search . '%')
            ->where('user_customer_type', $user_customer_type)
            ->get();
        if ($supplier->isEmpty()) {
            return response()->json(['status' => 0, 'message' => 'No data found'], 404);
        }
        return response()->json(['status' => 1, 'data' => $supplier], 200);
    }


    public function getAllItems(Request $request)
    {
        $user = $request->user();
        $userRole = $user->role;

        $allUserId = [];
        if (empty($user->user_added_by)) {
            $user_added_by = $user->id;
            $allUserId[] = $user->id;
        } else {
            $user_added_by = $user->user_added_by;
        }

        $allUser = User::where('user_added_by', $user_added_by)->get();
        foreach ($allUser as $user_all) {
            $allUserId[] = $user_all->id;
        }

        $purchases = Purchase::with('items.barcode')->whereIn('user_add', $allUserId)->get();
        $response = [];
        $baseUrl = url('/');;
        foreach ($purchases as $purchase) {
            $purchaseInfo = [
                'date' => $purchase->invoice_date,
                'coding_type' => $purchase->coding_type,
            ];

            foreach ($purchase->items as $item) {
                $barcodeData = [];
                foreach ($item->barcode as $barcode) {
                    $barcodeData[] = [
                        'barcode_no' => $barcode->code,
                        'image_url' => $baseUrl . $barcode->image_url,
                    ];
                }

                $itemInfo = [
                    'item_name' => $item->name,
                    'quantity' => $item->quantity,
                    'barcodes' => $barcodeData,
                ];

                $response[] = array_merge($purchaseInfo, $itemInfo);
            }
        }



        return response()->json(['status' => 1, 'data' =>  $response], 200);
    }
}
