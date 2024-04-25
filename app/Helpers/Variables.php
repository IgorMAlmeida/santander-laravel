<?php

namespace App\Helpers;

class Variables
{
    public function setVariables($values) : string {
        
        $variables  = '__EVENTTARGET='.$values['eventTarget'];
        $variables .= "&__EVENTVALIDATION=" .  urlencode($values['eventValidation']);
        $variables .= "&__VIEWSTATE=" . urlencode($values['viewState']);
        $variables .= "&__VIEWSTATEGENERATOR=" . urlencode($values['viewStateGenerator']);
        $variables .= "&__EVENTARGUMENT=" . (isset($values['eventArgument']) ? ($values['eventArgument']) : '');
        $variables .= "&__ASYNCPOST=false";
        if(isset($values['lastFocus'])){
            $variables .= "&__LASTFOCUS=";
        }

        return $variables;
    }
}
