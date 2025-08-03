<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProfileService;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(Request $request)
    {
        return view('user.profile', [
            'user' => $request->user()
        ]);
    }

    public function edit(Request $request)
    {
        return view('user.profile-edit', [
            'user' => $request->user()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama' => ['required', 'max:100'],
            'username' => ['required', 'unique:users,username,' . $request->user()->id],
            'password_baru' => ['nullable', 'max:100', 'confirmed']
        ]);

        $this->profileService->updateProfile($request->user(), $request->all());

        return redirect()->route('profile.show')->with('update', 'success');
    }
}
