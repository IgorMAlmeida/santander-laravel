<?php

namespace App\Services;

use App\Services\Curl;

class BankLogin extends Curl{

   private string $login = "EX114688";
   private string $password = "CF@5400";
   // private string $urlBase = "https://www.santandernegocios.com.br";
   // private string $urlIdentification = "https://www.santandernegocios.com.br/F6_framework_portal/asp/F6_identificacaousuario.asp";
   // private string $urlHomeConsigned = "https://www.santandernegocios.com.br/CSG_CREDITO_CONSIGNADO/ASP/CSG_LOGINMBSCSG.ASP";

   public function login():array {

      try{


         
         $login = $this->loginEsteira($this->login, $this->password);
         $consultaEsteira = $this->esteira('214699342');
         var_dump($consultaEsteira);exit;
      //    if(!$login['status']){
      //       throw new \Exception($login['response']);
      //    }

      //    return [
      //       "status"    =>  true,
      //       "response"  =>  $login['response']
      //   ];

      }catch (\Exception $e){
         return [
            "status"    =>  false,
            "response"  =>  $e->getMessage()
        ];
     }
   }
   
   // public function login():array {

   //    try{

   //       $variables  = "txtIs_nav=false";
   //       $variables .= "&txtIs_moz=false";
   //       $variables .= "&txtIs_ie=true";
   //       $variables .= "&txtIs_fb=false";
   //       $variables .= "&txtIs_fx=false";
   //       $variables .= "&txtIs_major=7";
   //       $variables .= "&txtIs_minor=7";
   //       $variables .= "&txtW_width=";
   //       $variables .= "&txtW_height=";
   //       $variables .= "&autoSubmit=0";
   //       $variables .= "&hdnSubmit=1";
   //       $variables .= "&pswSenhaCript=";
   //       $variables .= "&txtUsuario=" . trim($this->login);
   //       $variables .= "&pswSenha=" . trim($this->password);
      
   //       $params = [
   //          "login"              => true,
   //          "urlBase"            => $this->urlBase,
   //          "urlIdentification"  => $this->urlIdentification,
   //          "urlHomeConsigned"   => $this->urlHomeConsigned,
   //          "variables"          => $variables

   //       ];
         
   //       $login = $this->send($params);

   //       if(!$login['status']){
   //          throw new \Exception($login['response']);
   //       }

   //       return [
   //          "status"    =>  true,
   //          "response"  =>  $login['response']
   //      ];

   //    }catch (\Exception $e){
   //       return [
   //          "status"    =>  false,
   //          "response"  =>  $e->getMessage()
   //      ];
   //   }
   // }
}