<?php

namespace App\Http\Controllers;

use App\Services\ConsultCNPJService;
use App\Services\ScrappingContentService;
use Illuminate\Http\Request;

class CnpjConsultController extends Controller
{

    public function index(Request $request, string $cnpj)
    {
        try{
            if(!$cnpj){
                throw new \Exception('CNPJ not found');
            }

            $consultCnpj = (new ConsultCNPJService())->consult('CasaDosDados',$cnpj);
            
            if(!$consultCnpj['status']){
                throw new \Exception($consultCnpj['response']);
            }

            $scrappingResult = (new ScrappingContentService())->scrapping($consultCnpj['response']);

            if($scrappingResult['status']){
                throw new \Exception($scrappingResult['response']);
            }

            return response()->json([
                "status"  =>  true,
                "data"    =>  $scrappingResult['response']
            ], 200);

        }catch(\Exception $e){
            return response()->json([
                "status"  =>  false,
                "data"    =>  $e->getMessage()
            ], 400);
        }
        

    }
}
