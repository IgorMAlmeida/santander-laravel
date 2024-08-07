<?php

namespace App\Services;

class SendCurlService
{
    protected function send(array $values):array
    {
        try{

            $ch = curl_init($values['url']);

            if(isset($values['urlCaptcha'])){
                curl_close($ch);
                $ch = curl_init($values['urlCaptcha']);
            }

            if(isset($values['headers']) && !empty($values['headers'])){
                curl_setopt($ch, CURLOPT_HTTPHEADER, $values['headers']);
            }
            

            if(isset($values['formDataString'])){
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $values['formDataString']);
            }

            if(isset($values['typeRequest'])){
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $values['typeRequest']);
            }
            
            if(isset($values['cookieFile'])){
                curl_setopt($ch, CURLOPT_COOKIEFILE, $values['cookieFile']);
                curl_setopt($ch, CURLOPT_COOKIEJAR, $values['cookieFile']);
            }

            if(isset($values['cookie'])){
                curl_setopt($ch, CURLOPT_COOKIE, $values['cookie']);
            }

            if(isset($values['followLocation']) && isset($values['urlRefer'])){
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
                curl_setopt($ch, CURLOPT_REFERER, $values['urlRefer']);
            }

            if(isset($values['urlRefer'])){
                curl_setopt($ch, CURLOPT_REFERER, $values['urlRefer']);
            }
            
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            
            $response = curl_exec($ch);
            $info = curl_getinfo($ch);
            // var_dump($info);exit;
            if (curl_error($ch)) {
                throw new \Exception(curl_error($ch));
            }
            
            curl_close($ch);

            return [
                "status"    =>  true,
                "response"  =>  $response
            ];

        }catch(\Exception $e){
            return [
                "status"    =>  false,
                "response"  =>  $e->getMessage()
            ];
        }
    }
}