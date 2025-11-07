<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:mobile_users,phone', 'required_without:email'],
            'email' => ['nullable', 'email', 'max:255', 'unique:mobile_users,email', 'required_without:phone'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        /** @var \App\Models\MobileUser $mobileUser */
        $mobileUser = MobileUser::query()->create([
            'name' => $data['name'],
            'phone' => data_get($data, 'phone'),
            'email' => data_get($data, 'email'),
            'password' => $data['password'],
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Registrasi berhasil. Akun Anda menunggu persetujuan admin.',
            'user' => [
                'id' => $mobileUser->id,
                'name' => $mobileUser->name,
                'phone' => $mobileUser->phone,
                'status' => $mobileUser->status,
            ],
        ], Response::HTTP_CREATED);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'identity' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        /** @var \App\Models\MobileUser|null $user */
        $user = MobileUser::query()
            ->where(function ($query) use ($credentials) {
                $query->where('phone', $credentials['identity'])
                    ->orWhere('email', $credentials['identity']);
            })
            ->where('status', 'approved')
            ->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'Nomor atau kata sandi salah, atau akun belum disetujui.',
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('mobile-app');

        return response()->json([
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => null,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
                'status' => $user->status,
                'shift' => $user->shift,
            ],
        ]);
    }
}
