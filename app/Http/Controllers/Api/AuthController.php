<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Mail;
use App\Mail\EmailverifyMail;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|string|max:255|unique:users,email',
                'name' => 'required|string|max:255',
                'password' => 'required|string|min:6',
                'view' => 'nullable|image|max:4096',

            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }


            $fileNameToStore = null;
            $fileNameToStore1 = null;

            if ($request->hasFile('view')) {
                $fileNameWithExt = $request->file('view')->getClientOriginalName();

                $fileName = pathinfo($fileNameWithExt, PATHINFO_FILENAME);

                $extension = $request->file('view')->getClientOriginalExtension();

                $fileNameToStore = $fileName.'_'.time().'.'.$extension;

                $path = $request->file('view')->storeAs('public/view', $fileNameToStore);
            }

            $validation_code = str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

            $userData = [
                'email' => $request->email,
                'name' => $request->name,
                'view' => $fileNameToStore,
                'password' => Hash::make($request->password,['rounds' => 12,]) ,
                'validation_code' => $validation_code,
            ];

            $user = User::create($userData);

            $email = $user->email;

            $subject = "Verification Adress Email";
            $body =  $user->validation_code;
            // $mailinfo = [
            //     "subject"=> "Verification Adress Email",
            //     "body"=> $user->confirm_code,
            // ];

            Mail::to($email)->send( new EmailverifyMail($subject,$body));

            return response()->json([
                'status_message' => "Reste à valider",
                'status_code' => 200,
                'user' => $user,
            ]);
            } catch (Exception $e) {
                return response()->json($e);
            }
    }

    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vos identifiants ne correspondent pas',
                ], 401);
            }

            $user = Auth::user();


            if (!$user->email_verified_at) {
                Auth::logout();
                return response()->json([
                    'status' => false,
                    'message' => 'Vous n\'êtes pas autorisé à vous connecter.',
                ], 401);
            }

        $token = $user->createToken('Bearer Token')->plainTextToken;


            return response()->json([
                'status' => true,
                'user' => $user,
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }

    }

    public function emailVerify(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
                'email' => 'required|string|email',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $user = User::where('email', $request->email)
            ->where('validation_code', $request->code)
            ->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Code invalide',
                ]);
            }

            if ($user->email_verified_at !== null) {
                return response()->json([
                    'status' => false,
                    'message' => 'Votre adresse email a déjà été vérifiée',
                ]);
            }

            $user->email_verified_at = now();
            $user->save();

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Votre adresse email a été vérifiée avec succès',
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }
    }

    public function logout()
    {
        try {
            $user = Auth::user();
            $user->tokens()->delete();
            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
            ]);
        } catch (Exception $e) {
            return response()->json($e);
        }

    }
}
