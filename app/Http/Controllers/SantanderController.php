<?php

namespace App\Http\Controllers;

use App\Services\Queues;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Services\BankLogin;


class SantanderController extends Controller
{
    public function Esteira(Request $request)
    {
        try {

            session_start();
            $params = [
                "propostaId" => $request->input('propostaId')
            ];

            $getProposalData = (new Queues)->getQueue($params);

            if($getProposalData['erro']){
               throw new \Exception($getProposalData['response']);
            }

            return response()->json([
                "erro"  =>  false,
                "dados" =>  [
                    'propostas' => $getProposalData['propostas']
            ]
            ]);

        }catch (\Exception $e) {
            return [
                "erro"  =>  true,
                "dados" =>  $e->getMessage()
            ];
        }
    }
}
