<?php

namespace App\Services;

use App\Enums\CNPJClassConsultEnum;


class ConsultCNPJService{
   public function consult(string $siteConsult, string $cnpj):array {

      try{
        $class = CNPJClassConsultEnum::chooseClass($siteConsult);
        if( !$class ){
            throw new \Exception('Class not found');
        }

        $response = $class->consultCnpj($cnpj);
        
        if(!$response['status']){
            throw new \Exception($response['response']);
        }

        return [
            "status"       =>  true,
            "response"   =>  $response['response'],
        ];

      }catch (\Exception $e){
         return [
            "status"     =>  false,
            "response"  =>  $e->getMessage()
        ];
     }
   }
}