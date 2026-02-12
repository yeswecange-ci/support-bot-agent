<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Services\Chatwoot\ChatwootClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * AJAX — Toggle availability (online/offline/busy)
     */
    public function toggleAvailability(Request $request): JsonResponse
    {
        $request->validate(['availability' => 'required|in:online,offline,busy']);

        try {
            $chatwoot = app(ChatwootClient::class);
            $result = $chatwoot->updateAvailability($request->availability);
            return response()->json(['success' => true, 'availability' => $request->availability]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * AJAX — Get current availability
     */
    public function getAvailability(): JsonResponse
    {
        try {
            $chatwoot = app(ChatwootClient::class);
            // Get agents list and find current user
            $agents = $chatwoot->listAgents();
            $userEmail = auth()->user()->email ?? '';
            $current = collect($agents)->firstWhere('email', $userEmail);
            return response()->json([
                'availability' => $current['availability_status'] ?? 'offline',
            ]);
        } catch (\Exception $e) {
            return response()->json(['availability' => 'offline']);
        }
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
