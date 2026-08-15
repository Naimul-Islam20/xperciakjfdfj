<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAboutPageRequest;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AboutPageController extends Controller
{
    public function index(): View
    {
        return view('admin.about.index', [
            'settings' => SiteSetting::current(),
        ]);
    }

    public function update(UpdateAboutPageRequest $request): RedirectResponse
    {
        SiteSetting::current()->update($request->validated());

        return redirect()
            ->route('admin.about.index')
            ->with('success', 'About Us page updated successfully.');
    }
}
