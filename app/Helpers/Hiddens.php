<?php

namespace App\Helpers;

class Hiddens
{
    public function getHiddens($return) : array {
        
        preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $return, $hiddens);
        $eventValidation = $hiddens[1][0];
        preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $return, $hiddens);
        $viewState = $hiddens[1][0];
        preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $return, $hiddens);
        $viewStateGenerator = $hiddens[1][0];

        return [
            'eventValidation'    => $eventValidation,
            'viewState'          => $viewState,
            'viewStateGenerator' => $viewStateGenerator
        ];
    }
}
