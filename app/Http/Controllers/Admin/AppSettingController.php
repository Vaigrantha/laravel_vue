<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppSettingController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('AppSettings/Index', [
            'settings' => AppSetting::query()->latest()->get(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('AppSettings/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:app_settings,key',
            'value' => 'nullable|string',
            'type' => 'required|string|in:string,text,json,color,number,boolean',
            'description' => 'nullable|string|max:255',
        ]);

        AppSetting::create($validated);

        return redirect()->route('admin.app-settings.index');
    }

    public function edit(AppSetting $appSetting): Response
    {
        return Inertia::render('AppSettings/Edit', [
            'setting' => $appSetting,
        ]);
    }

    public function update(Request $request, AppSetting $appSetting): RedirectResponse
    {
        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:app_settings,key,'.$appSetting->id,
            'value' => 'nullable|string',
            'type' => 'required|string|in:string,text,json,color,number,boolean',
            'description' => 'nullable|string|max:255',
        ]);

        $appSetting->update($validated);

        return redirect()->route('admin.app-settings.index');
    }

    public function destroy(AppSetting $appSetting): RedirectResponse
    {
        $appSetting->delete();

        return back();
    }
}
