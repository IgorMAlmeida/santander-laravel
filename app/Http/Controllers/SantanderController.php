<?php

namespace App\Http\Controllers;

use App\Services\BankLogin;
use App\Services\Queues;
use Illuminate\Http\Request;

class SantanderController extends Controller
{
    public function Esteira()
    {

        try {
            $login = (new BankLogin)->login();
            var_dump($login);exit;

            // $getProposalData = (new Queues)->getQueue($login['response']);

            // var_dump($getProposalData);exit;
            
            if(!$login['status']){
               throw new \Exception($login['response']);
            }

            return [
                "status"    =>  true,
                "response"  =>  $login['response']
            ];

        }catch (\Exception $e) {
            //a forma de retorno padrao acho q nao é essa// conferir antes de termianr
            return [
                "status"    =>  false,
                "response"  =>  $e->getMessage()
            ];
        }
    }
}
