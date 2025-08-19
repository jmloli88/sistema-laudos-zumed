<?php

namespace App\Http\Controllers;

use Google\Cloud\Firestore\FirestoreClient;
use Kreait\Firebase\Factory;
use App\Services\FirebaseService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = $this->firebaseService->validateUser(
            $credentials['username'],
            $credentials['password']
        );

        if ($user) {
            session([
                'nombres' => $user['NOMBRES'],
                'apellidos' => $user['APELLIDOS'],
                'id_clinica' => $user['ID_CLINICA'],
                'nombre_clinica' => ''
            ]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'username' => 'Credenciais incorretas.'
        ]);
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login.show');
    }
}
