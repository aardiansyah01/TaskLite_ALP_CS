<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('login.page')
                ->with(
                    'oauth_error',
                    'Login Google gagal. Periksa konfigurasi OAuth dan coba kembali.'
                );
        }

        $googleId = trim((string) $googleUser->getId());
        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $name = trim((string) $googleUser->getName());
        $raw = $googleUser->getRaw();

        if (
            $googleId === '' ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !($raw['verified_email'] ?? false)
        ) {
            return redirect()
                ->route('login.page')
                ->with(
                    'oauth_error',
                    'Akun Google tidak memiliki email yang valid dan terverifikasi.'
                );
        }

        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            $existingUser = User::where('email', $email)->first();

            if ($existingUser) {
                if (
                    !empty($existingUser->google_id) &&
                    $existingUser->google_id !== $googleId
                ) {
                    return redirect()
                        ->route('login.page')
                        ->with(
                            'oauth_error',
                            'Email sudah terhubung ke akun Google lain.'
                        );
                }

                $existingUser->google_id = $googleId;
                $existingUser->email_verified_at ??= now();
                $existingUser->save();

                $user = $existingUser;
            } else {
                $username = 'google_' . substr(
                    hash('sha256', $googleId),
                    0,
                    16
                );

                $user = User::create([
                    'name' => $name !== '' ? $name : 'Google User',
                    'username' => $username,
                    'email' => $email,
                    'password' => bcrypt(Str::random(48)),
                    'google_id' => $googleId,
                    'role' => 'member',
                    'department_id' => null,
                    'clearance_level' => 1,
                    'email_verified_at' => now(),
                ]);
            }
        }

        try {
            $token = auth('api')->login($user);
        } catch (Throwable $e) {
            report($e);

            return redirect()
                ->route('login.page')
                ->with(
                    'oauth_error',
                    'Login berhasil diverifikasi, tetapi token akses gagal dibuat.'
                );
        }

        return redirect()->to(
            url('/dashboard') . '#access_token=' . urlencode($token)
        );
    }
}
