<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Categorie;
use App\Models\TypeTransaction;
use App\Models\Transaction;
use App\Models\Devis;
use App\Models\User;
use App\Models\Solde;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class UserController extends Controller
{
    public function getEntreeCategorie()
    {
        $categories = Categorie::where('type_id', 1)->get();
        return response()->json([
            'status' => true,
            'categorie_entree' => $categories,
        ]);
    }

    public function getSortieCategorie()
    {
        $categories = Categorie::where('type_id', 2)->get();
        return response()->json([
            'status' => true,
            'categorie_sortie' => $categories,
        ]);
    }

    public function getTypeTransaction()
    {
        $transactions = TypeTransaction::all();
        return response()->json([
            'status' => true,
            'transaction' => $transactions,
        ]);
    }

    public function getDevis()
    {
        $devis = Devis::all();
        return response()->json([
            'status' => true,
            'devis' => $devis,
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255|nullable',
            'view' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::findOrFail(auth()->id());

        $user->fill($validator->validated());

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur',
                'error' => $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Utilisateur mis à jour avec succès',
            'data' => $user,
        ]);
    }

    public function putSolde(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'solde' => 'required',
            'id_devis' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $soldeData = [
            'solde' => $request->solde,
            'id_users' => auth()->id(),
            'id_devis' =>$request->id_devis,
        ];

        $solde =  Solde::create($soldeData);

        return response()->json([
            'status' => true,
            'message' => 'Solde enregistré avec succès',
            'data' => $solde,
        ]);
    }

    public function createTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'categorie_id' => 'required',
            'amount' => 'required',
            'description' => 'required|string',
            'transactionDate' => 'required|string',
            'view' => 'string|nullable',
            'type_id' => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Erreur de validation',
                'errors' => $validator->errors(),
            ], 422);
        }

        $transactionData = [
            'categorie_id' => $request->categorie_id,
            'amount' => $request->amount,
            'description' => $request->description,
            'transactionDate' => $request->transactionDate,
            'view' => $request->transactionDate,
            'type_id' => $request->type_id,
            'user_id' => auth()->id(),
        ];

        $solde =  Transaction::create($transactionData);

        return response()->json([
            'status' => true,
            'message' => 'Solde enregistré avec succès',
            'data' => $solde,
        ]);
    }

    public function getRecentTransaction()
    {
        $lastFive = Transaction::latest()->take(5)->get();
        return $lastFive;
    }

    public function getSoldeEntree(Request $request)
    {

        $resultats = Transaction::where('user_id',auth()->id())
                            ->where('type_id', 1)
                            ->get();

        $somme = $resultats->sum('amount');

        return response()->json([
            'status' => true,
            'somme' => $somme,
        ]);
    }

    public function getSoldeSortie(Request $request)
    {

        $resultats = Transaction::where('user_id',auth()->id())
                            ->where('type_id', 2)
                            ->get();

        $somme = $resultats->sum('amount');

        return response()->json([
            'status' => true,
            'somme' => $somme,
        ]);
    }

}
