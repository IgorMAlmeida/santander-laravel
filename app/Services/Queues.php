<?php

namespace App\Services;

use App\Services\Curl;
use App\Services\BankLogin;


class Queues extends Curl{

    public function getQueue($values):array {

        try{
            $login = (new BankLogin)->login();
            $params = $this->getProposalStatus([...$values, ...$login]);
            $consultProposal = $this->getFinished([...$params]);

            if (isset($consultProposal['mensagem'])) {
                $login = (new BankLogin)->login();
                $params = $this->getProposalStatus([...$values, ...$login]);
                $consultProposal = $this->getProgress([...$params]);
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
                "response" =>  $e->getMessage()
            ];
        }
    }

    private function getProposalStatus($values): array {
        return [
            "propostaId" => $values['propostaId'],
            "curlHandle" => $values['response'],
            "cookieFile" => $values['cookieFile']
        ];
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
                "mensagem" =>  $e->getMessage()
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