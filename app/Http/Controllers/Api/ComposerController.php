<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;


class ComposerController extends Controller
{

    public function clearAllCache()
    {

        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        // Artisan::call('view:clear');
        return response()->json(['status' => 1, 'message' => 'All caches cleared successfully!'], 200);
    }



}
