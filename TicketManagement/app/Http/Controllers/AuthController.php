<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

use GuzzleHttp\Client;
class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['message' => 'User registered successfully'], 201);
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $http = new Client;

    //     try {
    //         $response = $http->post(env('APP_URL') . '/oauth/token', [
    //             'form_params' => [
    //                 'grant_type' => 'password',
    //                 'client_id' => env('PASSPORT_CLIENT_ID'),
    //                 'client_secret' => env('PASSPORT_CLIENT_SECRET'),
    //                 'username' => $request->email,
    //                 'password' => $request->password,
    //                 'scope' => '',
    //             ],
    //         ]);

    //         return json_decode((string) $response->getBody(), true);
    //     } catch (\GuzzleHttp\Exception\BadResponseException $e) {
    //         return response()->json([
    //             'error' => 'Invalid credentials',
    //             'details' => json_decode($e->getResponse()->getBody(true), true)
    //         ], 401);
    //     }
        
    // }
    public function login(Request $request)
    {
        \Log::info('Login request started.');

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        \Log::info('Validation passed.');

        $http = new Client;

        try {
            \Log::info('Sending Guzzle request.');
            $response = $http->post(env('APP_URL') . '/oauth/token', [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('PASSPORT_CLIENT_ID'),
                    'client_secret' => env('PASSPORT_CLIENT_SECRET'),
                    'username' => $request->email,
                    'password' => $request->password,
                    'scope' => '',
                ],
            ]);

            \Log::info('Guzzle request successful.');
            return json_decode((string) $response->getBody(), true);
        } catch (\GuzzleHttp\Exception\BadResponseException $e) {
            \Log::error('Guzzle error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Invalid credentials',
                'details' => json_decode($e->getResponse()->getBody(true), true),
            ], 401);
        } catch (\Exception $e) {
            \Log::error('Unexpected error: ' . $e->getMessage());
            return response()->json(['error' => 'Unexpected error'], 500);
        }
    }

}



