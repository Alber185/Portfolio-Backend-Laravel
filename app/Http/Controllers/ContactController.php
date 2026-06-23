<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserContactResource;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response as HttpResponse;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function __construct()
    {
    }

    public function get(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }

        return response()->json(new UserContactResource($user));
    }

    public function update(Request $request)
    {
        $user = $request->user();  // Obtener usuario autenticado

        if (!$user) {
            return response()->json(['message' => 'User not found.'], HttpResponse::HTTP_NOT_FOUND);
        }

        try{
            $validatedData = $request->validate([
                'phone1' => 'nullable|string|max:20',
                'phone2' => 'nullable|string|max:20',
                'contactEmail1' => 'nullable|email|max:255',
                'contactEmail2' => 'nullable|email|max:255',
                'contactEmail3' => 'nullable|email|max:255',
                'github' => 'nullable|url|max:255',
                'linkedin' => 'nullable|url|max:255',
            ]);

            $user->update($validatedData);
            return response()->json(new UserContactResource($user), HttpResponse::HTTP_OK);
        
        }
        catch (ValidationException $e) {
            return response()->json([
                                        'message' => 'Validation failed.',
                                        'errors' => $e->errors()
                                    ],
                                    HttpResponse::HTTP_UNPROCESSABLE_ENTITY
                                );
        }
    }
}
