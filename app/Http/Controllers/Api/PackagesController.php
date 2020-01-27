<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Package as PackageResource;
use App\Package;
use Illuminate\Http\Request;

class PackagesController extends Controller
{
    public function __invoke(Request $request)
    {
        return PackageResource::collection(Package::orderBy('created_at', 'desc')->with(['author', 'tags'])->paginate(10));
    }
}
