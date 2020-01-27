<?php

namespace App\Http\Controllers;

use App\Package;

class EnablePackageController extends Controller
{
    public function __invoke(Package $package)
    {
        $package->is_disabled = false;
        $package->save();

        return back()->with([
            'package' => $package,
            'status' => 'Package enabled: '.$package->name,
        ]);
    }
}
