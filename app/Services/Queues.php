<?php

namespace App\Services;

use App\Services\Curl;

class Queues extends Curl{

   private string $urlBase = "https://consignado.santander.com.br/Portal/FI.AC.GUI.FIMENU.aspx";
   private string $urlAuthorization = "https://consignado.santander.com.br/autorizador45/login/AC.UI.SAN.aspx?paramtp=TOKENCSG&param1=";
   private string $urlHomeConsigned = "https://www.santandernegocios.com.br/CSG_CREDITO_CONSIGNADO/ASP/CSG_LOGINMBSCSG.ASP";


   public function getQueue($loginScreen):array {

    try{

        //request para o menu principal
        $params = [
            "urlBase"          => $this->urlBase,
            "urlAuthorization" => $this->urlAuthorization,
            "queue"            => true,
            "urlLogin"         => $loginScreen
        ];

        $queue = $this->send($params);
        var_dump($queue);exit;


        return [
            "status"    =>  true,
            "response"  =>  $queue['response']
        ];

        }catch (\Exception $e){
            return [
                "status"    =>  false,
                "response"  =>  $e->getMessage()
            ];
        }
    }

    private function getQueueFinished():array {

        try{
    
        
            return [
                "status"    =>  true,
                "response"  =>  "response"
            ];
    
        }catch (\Exception $e){
            return [
                "status"    =>  false,
                "response"  =>  $e->getMessage()
            ];
        }
    }

    private function getQueueProgress():array {

        try{
    
        
            return [
                "status"    =>  true,
                "response"  =>  "response"
            ];
    
        }catch (\Exception $e){
            return [
                "status"    =>  false,
                "response"  =>  $e->getMessage()
            ];
        }
    }
}   