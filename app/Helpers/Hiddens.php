<?php

namespace App\Helpers;

class Hiddens
{
    public function getHiddens($return) : array {
        
        try{
            preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $return, $hiddens);
            if(!isset($hiddens[1][0]) || empty($hiddens[1][0])){
                throw new \Exception('Nenhuma proposta encontrada!');
            }
            $eventValidation = $hiddens[1][0];

            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $return, $hiddens);
            if(!isset($hiddens[1][0]) || empty($hiddens[1][0])){
                throw new \Exception('Nenhuma proposta encontrada!');
            }
            $viewState = $hiddens[1][0];

            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $return, $hiddens);
            if(!isset($hiddens[1][0]) || empty($hiddens[1][0])){
                throw new \Exception('Nenhuma proposta encontrada!');
            }
            $viewStateGenerator = $hiddens[1][0];

            return [
                'erro' => false,
                'eventValidation'    => $eventValidation,
                'viewState'          => $viewState,
                'viewStateGenerator' => $viewStateGenerator
            ];
        }catch(\Exception $e){
            return [
                'erro' => true,
                'response' => $e->getMessage()
            ];
        }

    }
}
