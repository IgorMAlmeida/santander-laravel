<?php

namespace App\Helpers;

class QueueParams {

    public function setQueueParams($values): array {
        return [
            "propostaId" => $values['propostaId'],
            "curlHandle" => $values['response'],
            "cookieFile" => $values['cookieFile']
        ];
    }

}