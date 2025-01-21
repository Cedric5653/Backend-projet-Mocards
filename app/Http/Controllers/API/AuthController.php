<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // Ajoutez cet import
use Illuminate\Support\Facades\DB; // Ajoutez aussi cet import si pas déjà fait

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|unique:users',
            'prenom' => 'required|string|unique:users',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,role_id'
        ]);

        $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'email' => $request->email,
            // 'password' => Hash::make($request->password),
            'password_hash' => Hash::make($request->password),
            'role_id' => $request->role_id
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'User successfully registered',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }

    /**
     * User login
     */

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            // Vérifiez d'abord si l'utilisateur existe
            $user = User::where('email', $request->email)->first();
            
            if (!$user || !Hash::check($request->password, $user->password_hash)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Identifiants incorrects'
                ], 401);
            }

            // Crée le token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => 'success',
                'message' => 'Connexion réussie',
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur serveur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * User logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    /**
     * Get authenticated user details
     */
    public function user(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }


    public function getRegistrationRoles()
    {
        // Ne retourner que les rôles autorisés pour l'inscription
        $allowedRoles = Role::whereIn('role_name', ['Médecin', 'Infirmier', 'Secouriste','Admin'])
            ->get(['role_id', 'role_name']);
            
        return response()->json([
            'status' => 'success',
            'data' => $allowedRoles
        ]);
    }


    /**
     * Get the profile of the authenticated user
     */
    public function getUserProfile(Request $request)
    {
        try {
            $user = $request->user();
            dd($user);

            return response()->json([
                'status' => 'success',
                'message' => 'User profile retrieved successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error retrieving user profile: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the profile of the authenticated user
     */
    public function updateUserProfile(Request $request)
    {
        try {
            $user = $request->user(); // Utilisateur authentifié

            // Validation des données
            $request->validate([
                'nom' => 'sometimes|string|unique:users,nom,' . $user->id,
                'prenom' => 'sometimes|string|unique:users,prenom,' . $user->id,
                'email' => 'sometimes|string|email|unique:users,email,' . $user->id,
                'password' => 'nullable|string|min:8|confirmed', // Optionnel
            ]);

            // Mise à jour des champs
            if ($request->has('nom')) {
                $user->nom = $request->nom;
            }
            if ($request->has('prenom')) {
                $user->prenom = $request->prenom;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password_hash = Hash::make($request->password);
            }

            // Sauvegarde des modifications
            $user->save();

            return response()->json([
                'status' => 'success',
                'message' => 'User profile updated successfully',
                'data' => $user
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error updating user profile: ' . $e->getMessage()
            ], 500);
        }
    }



    // ...  méthodes mdp oublier ...


    public function forgotPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email'
            ]);

            $user = User::where('email', $request->email)->first();
            
            if ($user) {
                $token = Str::random(60);
                
                // Mise à jour avec la nouvelle structure
                DB::table('password_resets')->insertOrIgnore([
                    'email' => $user->email,
                    'token' => Hash::make($token),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $resetUrl = config('front_url') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Si votre email existe, vous recevrez un lien de réinitialisation.',
                    'debug_token' => $token,
                    'resetUrl' => $resetUrl
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Si votre email existe, vous recevrez un lien de réinitialisation.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'token' => 'required|string',
                'password' => 'required|string|min:8|confirmed'
            ]);

            // Vérifier le token
            $reset = DB::table('password_resets')
                ->where('email', $request->email)
                ->first();

            if (!$reset || !Hash::check($request->token, $reset->token)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Token invalide'
                ], 400);
            }

            // Mise à jour du mot de passe
            $user = User::where('email', $request->email)->first();
            $user->password_hash = Hash::make($request->password);
            $user->save();

            // Supprimer le token utilisé
            DB::table('password_resets')
                ->where('email', $request->email)
                ->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Mot de passe réinitialisé avec succès'
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors de la réinitialisation'
            ], 500);
        }
    }
}

