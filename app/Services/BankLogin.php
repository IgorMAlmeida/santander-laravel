<?php

namespace App\Services;

use App\Services\Curl;

class BankLogin extends Curl{

   private string $login = "EX114960";
   private string $password = "CF@2030";
   
   public function login():array {

      try{

         $variables  = "txtIs_nav=false";
         $variables .= "&txtIs_moz=false";
         $variables .= "&txtIs_ie=true";
         $variables .= "&txtIs_fb=false";
         $variables .= "&txtIs_fx=false";
         $variables .= "&txtIs_major=7";
         $variables .= "&txtIs_minor=7";
         $variables .= "&txtW_width=";
         $variables .= "&txtW_height=";
         $variables .= "&autoSubmit=0";
         $variables .= "&hdnSubmit=1";
         $variables .= "&pswSenhaCript=";
         $variables .= "&txtUsuario=" . trim($this->login);
         $variables .= "&pswSenha=" . trim($this->password);
         
         // session_start();
         $params = [
            "variables" => $variables,
            "sessionId" => session_id()
         ];
         
         $login = $this->loginEsteira($params);
         
         if($login['erro']){
            throw new \Exception("Nao foi possivel fazer login.");
         }

         return [
            "erro"       =>  false,
            "response"   =>  $login['response'],
            "cookieFile" =>  $login['cookieFile']
         ];

      }catch (\Exception $e){
         return [
            "erro"     =>  true,
            "response" =>  $e->getMessage()
        ];
     }
   }
}