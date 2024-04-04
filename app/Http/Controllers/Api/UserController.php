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
use App\Models\CategorieForUser;
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

    // public function createTransaction(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'categorie_id' => 'required',
    //         'amount' => 'required',
    //         'description' => 'required|string',
    //         'transactionDate' => 'required|string',
    //         'view' => 'string|nullable',
    //         'type_id' => 'required|string',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'Erreur de validation',
    //             'errors' => $validator->errors(),
    //         ], 422);
    //     }

    //     $transactionData = [
    //         'categorie_id' => $request->categorie_id,
    //         'amount' => $request->amount,
    //         'description' => $request->description,
    //         'transactionDate' => $request->transactionDate,
    //         'view' => $request->transactionDate,
    //         'type_id' => $request->type_id,
    //         'user_id' => auth()->id(),
    //     ];

    //     // Recherche de la ligne correspondante dans CategorieForUser
    //     $categorieForUser = CategorieForUser::where('id_user', auth()->id())
    //                                         ->where('id_categorie', $request->categorie_id)
    //                                         ->first();

    //     if ($categorieForUser) {
    //         // Mettre à jour le solde existant en ajoutant le montant de la transaction
    //         $categorieForUser->solde += $request->amount;
    //         $categorieForUser->save();
    //     } else {
    //         // Créer une nouvelle ligne si elle n'existe pas
    //         CategorieForUser::create([
    //             'id_user' => auth()->id(),
    //             'id_categorie' => $request->categorie_id,
    //             'solde' => $request->amount,
    //             'type_id' => $request->type_id,
    //         ]);
    //     }

    //     $solde = Transaction::create($transactionData);

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'Solde enregistré avec succès',
    //         'data' => $solde,
    //     ]);
    // }

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

        // Création de la transaction
        $transaction = Transaction::create($transactionData);

        // Mise à jour du solde dans la table soldes
        $solde = Solde::where('id_users', auth()->id())->first();

        if ($solde) {
            if ($request->type_id == 1) {
                $solde->solde += $request->amount;
            } elseif ($request->type_id == 2) {
                // Vérifier si le solde est suffisant pour la transaction de type 2
                if ($solde->solde < $request->amount) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Solde insuffisant pour effectuer cette transaction',
                    ], 422);
                }
                $solde->solde -= $request->amount;
            }

            $solde->save();
        } else {
            // Création du solde si l'utilisateur n'a pas encore de solde enregistré
            if ($request->type_id == 2 && $request->amount > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Solde insuffisant pour effectuer cette transaction',
                ], 422);
            }

            Solde::create([
                'id_users' => auth()->id(),
                'solde' => $request->type_id == 1 ? $request->amount : 0 - $request->amount,
            ]);
        }

        return response()->json([
            'status' => true,
            'message' => 'Transaction enregistrée avec succès',
            'data' => $transaction,
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

    public function getEntreeSoldeByCategorie()
    {
        $idUser = auth()->id();

        $entreeSolds = CategorieForUser::where('id_user', $idUser)
                                    ->where('type_id', 1)
                                    ->get();

        return response()->json([
            'status' => true,
            'data' => $entreeSolds,
        ]);
    }

    public function getSortieSoldeByCategorie()
    {
        $idUser = auth()->id();

        $entreeSolds = CategorieForUser::where('id_user', $idUser)
                                    ->where('type_id', 2)
                                    ->get();

        return response()->json([
            'status' => true,
            'data' => $entreeSolds,
        ]);
    }

}
