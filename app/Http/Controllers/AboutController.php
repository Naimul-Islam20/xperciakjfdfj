<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AboutController extends Controller
{
    public function show(): View
    {
        $site = \App\Models\SiteSetting::current();

        return view('about.show', [
            'site' => $site,
        ]);
    }
}
