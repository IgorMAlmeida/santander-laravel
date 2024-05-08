<?php

namespace App\Services;

use App\Helpers\QueueParams;
use App\Services\Curl;
use App\Services\BankLogin;

class Queues extends Curl{

    public function getQueue($values):array {

        try{
            $consultProposal = '';

            if (isset($values['finished'])) {
                $consultProposal = $this->getFinished([...$values]);
            }

            if(isset($values['progress'])) {
                $consultProposal = $this->getProgress([...$values]);
            }

            if($consultProposal['erro']) {
                throw new \Exception($consultProposal['mensagem']);
            }

            return [
                "erro"    =>  false,
                "propostas"=>  $consultProposal['dados']
            ];

        }catch (\Exception $e){
            return [
                "erro"     =>  true,
                "response" =>  $e->getMessage(),
            ];
        }
    }

    private function getFinished($values):array {

        try{
            $consultProposal = $this->esteira([...$values, 'finished' => true]);
            if($consultProposal['erro']) {
                throw new \Exception($consultProposal['mensagem']);
            }

            return [
                "erro" =>  false,
                "dados"=>  $consultProposal['dados']
            ];

        }catch (\Exception $e){
            return [
                "erro"     =>  true,
                "mensagem" =>  $e->getMessage(),
            ];
        }
    }


    private function getProgress($values):array {

        try{

            $consultProposal = $this->esteira([...$values, 'progress' => true]);

            if($consultProposal['erro']) {
                throw new \Exception($consultProposal['mensagem']);
            }

            return [
                "erro"    =>  false,
                "dados"=>  $consultProposal['dados']
            ];

        }catch (\Exception $e){
            return [
                "erro"     =>  true,
                "mensagem" =>  $e->getMessage()
            ];
        }
    }

}