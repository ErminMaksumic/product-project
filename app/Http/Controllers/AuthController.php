<?php


namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class AuthController extends Controller
{
    public function login(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
//        dd(redirect()->to('youtube.com'));

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function logout(Request $request): string
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function redirectToProvider()
    {
        $query = http_build_query([
            'client_id' => '1',
            'redirect_uri' => 'http://localhost:3000/callback',
            'response_type' => 'code',

        ]);
        return redirect('http://localhost:8000/oauth/authorize?'.$query);
    }

    // Handle callback after authentication from OAuth provider
    public function handleProviderCallback(Request $request)
    {
        $code = $request->query('code');

        // Exchange authorization code for access token
        $response = $this->getToken($code);

        // Store the access token in the session or use it as needed
        $accessToken = $response['access_token'];

        // Example: Use the access token to make authenticated requests to your API
        $user = $this->getUserDetails($accessToken);

        // Example: Log in the user using Laravel's authentication system
        Auth::loginUsingId($user['id']);

        // Redirect the user to a protected resource or dashboard
        return redirect('/dashboard');
    }

    // Helper method to exchange authorization code for access token
    private function getToken($code)
    {
        $response = Http::post('http://your-passport-app-domain.com/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => 'your-client-id',
            'client_secret' => 'your-client-secret',
            'redirect_uri' => 'http://your-third-party-app-domain.com/auth/callback',
            'code' => $code,
        ]);

        return $response->json();
    }

    // Example: Helper method to get user details using access token
    private function getUserDetails($accessToken)
    {
        $response = Http::withToken($accessToken)->get('http://your-passport-app-domain.com/api/user');
        return $response->json();
    }
}
