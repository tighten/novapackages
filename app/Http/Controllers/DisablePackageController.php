<?php

namespace App\Http\Controllers;

use App\Package;

class DisablePackageController extends Controller
{
    public function __invoke(Package $package)
    {
        $package->is_disabled = true;
        $package->save();

        return back()->with([
            'package' => $package,
            'status' => 'Package disabled: ' . $package->name,
        ]);
    }
}
