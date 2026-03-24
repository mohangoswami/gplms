<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;  // keep this (your model)

class AuthController extends Controller
{
    public function versionCheck(Request $request)
    {
        $platform = strtolower((string) $request->query('platform', 'android'));
        $build = (int) $request->query('build', 0);

        if ($platform !== 'android') {
            return response()->json([
                'ok' => true,
                'forceUpdate' => false,
            ], 200);
        }

        $minBuild = (int) env('APP_MIN_ANDROID_BUILD', 10);
        $latestBuild = (int) env('APP_LATEST_ANDROID_BUILD', $minBuild);
        $latestVersion = (string) env('APP_LATEST_ANDROID_VERSION', '1.0.1');
        $updateUrl = (string) env(
            'APP_ANDROID_UPDATE_URL',
            'https://play.google.com/store/apps/details?id=com.gplmschool.app'
        );

        $forceUpdate = $build < $minBuild;

        return response()->json([
            'ok' => true,
            'platform' => 'android',
            'build' => $build,
            'minBuild' => $minBuild,
            'latestBuild' => $latestBuild,
            'latestVersion' => $latestVersion,
            'forceUpdate' => $forceUpdate,
            'message' => $forceUpdate
                ? 'A newer app version is required. Please update to continue.'
                : 'App version is supported.',
            'updateUrl' => $updateUrl,
        ], 200);
    }

    public function login(Request $request)
{
    try {
        $admission_number = $request->input('admission_number');
        $password         = $request->input('password');

        if (!$admission_number || !$password) {
            return response()->json([
                'ok' => false,
                'message' => 'Admission No & Password required'
            ], 400);
        }

        $user = User::where('admission_number', $admission_number)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json([
                'ok' => false,
                'message' => 'Invalid Admission No or Password'
            ], 401);
        }

        // Create a long-lived Sanctum token for the app.
        // Do not revoke all tokens on each login; that can force re-login on app reopen.
        $token = $user->createToken('student-app')->plainTextToken;

        return response()->json([
            'ok' => true,
            'message' => 'Login successful',
            // Keep both keys for backward compatibility with different app clients.
            'token' => $token,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'student' => [
                'name' => $user->name,
                'class' => $user->grade,
                'admission_number' => $user->admission_number,
            ],
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'ok' => false,
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
        ], 500);
    }
}


    public function logout(Request $request)
    {
        $user = $request->user();
        $deviceToken = $request->input('device_token');

        if ($user && !empty($deviceToken)) {
            DB::table('device_tokens')
                ->where('user_id', $user->id)
                ->where('token', $deviceToken)
                ->delete();
        }

        if ($request->user() && $request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();
        }

        return response()->json([
            'ok'      => true,
            'message' => 'Logged out successfully',
        ], 200);
    }

    public function deviceToken(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            $request->validate([
                'token' => ['required', 'string', 'max:1024'],
                'platform' => ['nullable', 'string', 'max:20'],
            ]);

            $incomingToken = $request->input('token');

            DB::transaction(function () use ($user, $incomingToken, $request) {
                // Keep only the latest device token for a student account.
                DB::table('device_tokens')
                    ->where('user_id', $user->id)
                    ->where('token', '!=', $incomingToken)
                    ->delete();

                DB::table('device_tokens')->updateOrInsert(
                    [
                        'user_id' => $user->id,
                        'token' => $incomingToken,
                    ],
                    [
                        'platform' => $request->input('platform', 'unknown'),
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            });

            return response()->json([
                'ok' => true,
                'message' => 'Device token saved',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'message' => 'Failed to save device token',
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
            ], 500);
        }
    }
}
