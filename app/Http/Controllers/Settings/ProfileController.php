<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/Profile', [
            'user' => $request->user()->load('profilePhoto'),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        DB::transaction(function() use($request, $user, $validated) {
            if ($request->hasFile('photo')) {
                if ($user->profilePhoto?->path) {
                    $original = $user->profilePhoto->getRawOriginal('path');
                    Storage::disk('s3')->delete($original);
                }
                $newPath = $request->file('photo')->store('profile-photos','s3');
                $user->profilePhoto()->updateOrCreate(
                    ['imageable_id' => $user->id,
                    'imageable_type' => 'user',
                ],
                    ['path' => $newPath]
                );
            }
            
            $user->fill($validated);
            
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
            
            $user->save();
        });

        return to_route('profile.edit');
    }

    public function profilePhotoDestroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function() use($user) {
            if ($user->profilePhoto?->path) {
                $original = $user->profilePhoto->getRawOriginal('path');
                Storage::disk('s3')->delete($original);
            }

            $user->profilePhoto()->delete();
        });

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
