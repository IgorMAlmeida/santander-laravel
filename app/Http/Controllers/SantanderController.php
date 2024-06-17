<?php

namespace App\Http\Controllers;

use App\Services\Queues;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\QueueParams;
use App\Services\BankLogin;


class SantanderController extends Controller
{
    public function Esteira(Request $request)
    {
        $retryCounter = 1;
        $maxRetries = 10;
        while($retryCounter <= $maxRetries){

            try {
                session_start();
                $params = [
                    "propostaId" => $request->input('propostaId'),
                ];

                $login = (new BankLogin)->login();
                if($login['erro']){
                    throw new \Exception($login['response']);
                }

                $queueParams = (new QueueParams())->setQueueParams([...$params, ...$login]);
                $getProposalData = (new Queues)->getQueue([...$queueParams,'finished' => true]);

                if($getProposalData['erro']){
                    $login = (new BankLogin)->login();
                    if($login['erro']){
                        throw new \Exception($login['response']);
                    }
                    $queueParams = (new QueueParams())->setQueueParams([...$params, ...$login]);
                    $getProposalData = (new Queues)->getQueue([...$queueParams, 'progress' => true]);
                }

                if($getProposalData['erro']){
                    throw new \Exception($getProposalData['response']);
                }

                // unlink($login['cookieFile']);
                return response()->json([
                    "erro"  =>  false,
                    "dados" =>  [
                        'propostas' => $getProposalData['propostas']
                    ],
                ]);

            }catch (\Exception $e) {
                if($retryCounter < $maxRetries){
                    session_destroy();
                    $retryCounter++;
                    continue;
                }
                return [
                    "erro"  =>  true,
                    "dados" =>  [
                        "mensagem" => $e->getMessage()
                    ]
                ];
            }
        }
    }
}
