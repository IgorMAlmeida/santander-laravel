<?php

namespace App\Services;

class ScrappingContentService{
   public function scrapping(string $data):array {

      try{

        $pattern = '/<strong.*?>([\d\.\/-]+)<\/strong> -\s*<strong.*?>([^<]+)<\/strong><small.*?>([^<]+)<\/small>/';

        if (preg_match($pattern, $data, $matches)) {
            $companyData = [
                'cnpj' => $matches[1],
                'name' => $matches[2],
                'status' => $matches[3]
            ];
        }

        if(!isset($companyData['cnpj']) || empty($companyData['cnpj'])) {
            throw new \Exception('Scraping content failure!');
        }

        return [
            "status"       =>  false,
            "response"   =>  $companyData,
        ];

      }catch (\Exception $e){
         return [
            "status"     =>  true,
            "response" =>  $e->getMessage()
        ];
     }
   }
}
