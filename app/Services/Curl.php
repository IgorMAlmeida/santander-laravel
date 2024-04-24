<?php

namespace App\Services;

use CurlHandle;
use GuzzleHttp\Client;


class Curl
{   
    public $urlConsignado = "https://consignado.santander.com.br/";
    public $urlBase       = "https://www.santandernegocios.com.br/";
    public $urlResponse   = "";
    public $httpReceita;
    public $varConsignado = "";
    public $varProposta   = "";
    public $cookie;
    public $cookieFile;
    public $agent     = "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET4.0C; .NET4.0E)";

    public function loginEsteira($usuario, $senha)
    {
        session_start();
        // $agent     = "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET4.0C; .NET4.0E)";
        ### Arquivo que ira armazenar o COOKIE
        // $this->cookie    = tempnam("/../tmp/", "SANTANDER-CURLCOOKIE");

        $sessionId = session_id();
        $cookiePath = getcwd() . '/Cookies/';
        $this->cookieFile = $cookiePath."SANTANDER-CURLCOOKIE".'_'.$sessionId;
        // $this->cookie = '';


        ### Inicio do CURL
        $this->httpReceita = curl_init();
        ### Inicia a sessao e cookie
        curl_setopt($this->httpReceita,CURLOPT_URL, "https://www.santandernegocios.com.br");
        curl_setopt($this->httpReceita,CURLOPT_VERBOSE, true);
        curl_setopt($this->httpReceita,CURLOPT_USERAGENT, $this->agent);
        curl_setopt($this->httpReceita,CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->httpReceita,CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->httpReceita,CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($this->httpReceita,CURLOPT_COOKIEJAR, $this->cookieFile);
        // curl_setopt($this->httpReceita, CURLOPT_COOKIE,  $this->cookie);
        curl_setopt($this->httpReceita,CURLOPT_AUTOREFERER, false);
        curl_setopt($this->httpReceita,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->httpReceita,CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, null);
        $retorno = curl_exec($this->httpReceita);
        
        
        $variaveis  = "txtIs_nav=false";
        $variaveis .= "&txtIs_moz=false";
        $variaveis .= "&txtIs_ie=true";
        $variaveis .= "&txtIs_fb=false";
        $variaveis .= "&txtIs_fx=false";
        $variaveis .= "&txtIs_major=7";
        $variaveis .= "&txtIs_minor=7";
        $variaveis .= "&txtW_width=";
        $variaveis .= "&txtW_height=";
        $variaveis .= "&autoSubmit=0";
        $variaveis .= "&hdnSubmit=1";
        $variaveis .= "&pswSenhaCript=";
        $variaveis .= "&txtUsuario=" . trim($usuario);
        $variaveis .= "&pswSenha=" . trim($senha);
        curl_setopt($this->httpReceita,CURLOPT_URL, "https://www.santandernegocios.com.br/F6_framework_portal/asp/F6_identificacaousuario.asp");
        curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
        $retorno = curl_exec($this->httpReceita);
        
        
        ### Entra na parte de consignado
        curl_setopt($this->httpReceita,CURLOPT_URL, "https://www.santandernegocios.com.br/CSG_CREDITO_CONSIGNADO/ASP/CSG_LOGINMBSCSG.ASP");
        $retorno = curl_exec($this->httpReceita);
        
        preg_match_all("/janela=window.open\('(.*?)','_blank'/s", $retorno, $janela);
        
        
        if(preg_match("/Erro ao recuperar/", $retorno))
            return ['erro'=> true, 'mensagem' => 'Não foi possivel fazer login. Verifique usuário e senha e tente novamente.'];


        ### Valida no portal com o TOKEN criado
        curl_setopt($this->httpReceita,CURLOPT_URL, trim($janela[1][0]));
        $retorno = curl_exec($this->httpReceita);
        
        return $retorno;


    }

    public function replaceValue($str)
    {
        $value = str_replace('.', '', $str); // remove o ponto
        $value = str_replace(',', '.', $value); // troca a vírgula por ponto
        return $value;
    }

    public function esteira($numProposta)
    {   
            // $agent     = "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET4.0C; .NET4.0E)";

            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Portal/FI.AC.GUI.FIMENU.aspx");
            curl_setopt($this->httpReceita,CURLOPT_POST, false);
            $retorno = curl_exec($this->httpReceita);
            
            preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];

            $response = [];     
            ### Entra no menu cadastro de operação
            $variaveis  = '__EVENTTARGET=ctl00$ContentPlaceHolder1$j0$j1$DataListMenu$ctl00$LinkButton2';
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&__EVENTARGUMENT=";
            $variaveis .= "&__ASYNCPOST=false";
            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Portal/FI.AC.GUI.FIMENU.aspx");
            curl_setopt($this->httpReceita,CURLOPT_POST, true);
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
            $retorno = curl_exec($this->httpReceita);
            
            preg_match_all('/param1=(.*?)"/s', $retorno, $param1);
            $url = "https://consignado.santander.com.br/autorizador45/login/AC.UI.SAN.aspx?paramtp=TOKENCSG&param1=" . trim($param1[1][0]);
            
            curl_setopt($this->httpReceita,CURLOPT_URL, $url);
            curl_setopt($this->httpReceita,CURLOPT_POST, false);
            $retorno = curl_exec($this->httpReceita);
            
            
            ### Entra no menu "Esteira > Aprovacao de proposta"
            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Prop.aspx");
            $retornoConsulta = curl_exec($this->httpReceita);

            
            ### Entra na tela de consulta
            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_popIDProp_frameAjuda&idPback=");
            $retorno = curl_exec($this->httpReceita);
           // var_dump($retorno); exit;
            ### Consulta informacoes da esteira
            preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];
        
            $variaveis  = "__ASYNCPOST=false";
            $variaveis .= "&__EVENTARGUMENT=onclick";
            $variaveis .= "&__EVENTTARGET=ctl00\$cph\$j0\$j1\$bbPesq";
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&__LASTFOCUS=";
            $variaveis .= "&ctl00\$cph\$j0\$j1\$rblTipoPesq=nrprop";
            $variaveis .= "&ctl00\$cph\$j0\$j1\$txtPesq\$CAMPO=$numProposta";
            $variaveis .= "&ctl00\$sm=ctl00\$cph\$up1|ctl00\$cph\$j0\$j1\$grResultado\$ctl02\$lb";

            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPROP");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
            $retorno = curl_exec($this->httpReceita);
           

            ### Link da proposta
            preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];
            $variaveis  = "__ASYNCPOST=false";
            $variaveis .= "&__EVENTARGUMENT=";
            $variaveis .= "&__EVENTTARGET=ctl00%24cph%24j0%24j1%24grResultado%24ctl02%24lb";
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&__LASTFOCUS=";
            $variaveis .= "&ctl00%24cph%24j0%24j1%24rblTipoPesq=nrprop";
            $variaveis .= "&ctl00%24cph%24j0%24j1%24txtPesq%24CAMPO=$numProposta";
            $variaveis .= "&ctl00%24sm=ctl00%24cph%24up1%7Cctl00%24cph%24j0%24j1%24grResultado%24ctl02%24lb";
            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_popIDProp_frameAjuda&idPback=");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
            $retorno = curl_exec($this->httpReceita);
            
        
            ### recarrega tela anterior com as informacoes da proposta
            preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];
            $variaveis  = "__ASYNCPOST=false";
            $variaveis .= "&__EVENTARGUMENT=";
            $variaveis .= "&__EVENTTARGET=ctl00_cph_popIDProp_frameAjuda";
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&ctl00%24sm=ctl00%24cph%24popIDProp_updP%7Cctl00_cph_popIDProp_frameAjuda";
            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropAnd.aspx");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
            $retorno = curl_exec($this->httpReceita);
            
            
            ### Pegando status da proposta
            preg_match_all('/ctl00_cph_ucAprCns_j0_j1_grConsulta"(.*)<\/table>/s', $retorno, $valueStatus);

	    if(empty($valueStatus[0]) && empty($valueStatus[1])){


		curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropFin.aspx");
            $retornoConsulta = curl_exec($this->httpReceita);

            ### Entra na tela de consulta
            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPRPF");
            $retorno = curl_exec($this->httpReceita);
// var_dump($retorno); exit;
            ### Consulta informacoes da esteira
            preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];

            $variaveis  = "__ASYNCPOST=false";
            $variaveis .= "&__EVENTARGUMENT=onclick";
            $variaveis .= "&__EVENTTARGET=ctl00\$cph\$j0\$j1\$bbPesq";
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&__LASTFOCUS=";
            $variaveis .= "&ctl00\$cph\$j0\$j1\$rblTipoPesq=nrprop";
            $variaveis .= "&ctl00\$cph\$j0\$j1\$txtPesq\$CAMPO=$numProposta";
            $variaveis .= "&ctl00\$sm=ctl00\$cph\$up1|ctl00\$cph\$j0\$j1\$grResultado\$ctl02\$lb";

            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPROP");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
            $retorno = curl_exec($this->httpReceita);
            // var_dump($retorno);exit;

            
		preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];
            $variaveis  = "__ASYNCPOST=false";
            $variaveis .= "&__EVENTARGUMENT=";
            $variaveis .= "&__EVENTTARGET=ctl00%24cph%24j0%24j1%24grResultado%24ctl02%24lb";
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&__LASTFOCUS=";
            $variaveis .= "&ctl00%24cph%24j0%24j1%24rblTipoPesq=nrprop";
            $variaveis .= "&ctl00%24cph%24j0%24j1%24txtPesq%24CAMPO=$numProposta";
            $variaveis .= "&ctl00%24sm=ctl00%24cph%24up1%7Cctl00%24cph%24j0%24j1%24grResultado%24ctl02%24lb";
            curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPRPF");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
            $retorno = curl_exec($this->httpReceita);
            // var_dump($retorno);exit;
            
		preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];


            $variaveis  = "__ASYNCPOST=false";
            $variaveis .= "&__EVENTARGUMENT=";
            $variaveis .= "&__EVENTTARGET=ctl00_cph_popIDProp_frameAjuda";
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&ctl00%24sm=ctl00%24cph%24popIDProp_updP%7Cctl00_cph_popIDProp_frameAjuda";
		    curl_setopt($this->httpReceita,CURLOPT_URL, "https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropFin.aspx");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variaveis);
            $retorno = curl_exec($this->httpReceita);

            // var_dump($retorno);exit;
            // var_dump($this->httpReceita);exit;


            ### Pegando status da proposta
            preg_match_all('/ctl00_cph_ucAprCns_j0_j1_grConsulta"(.*)<\/table>/s', $retorno, $valueStatus);
	    }

            preg_match_all('/id="ctl00_cph_ucAprCns_j0_j1_grConsulta_ctl02_LkBSit"(.*)\/a>/sU', $retorno, $value);

            preg_match_all('/>(.*)</sU', isset($value[1][0]) ? $value[1][0] : '', $value);
            $response['propostas'][0]['situacao'] = isset($value[1][0]) ? $value[1][0] : null;
            
            # Proxima atividade
            preg_match_all('/id="ctl00_cph_ucAprCns_j0_j1_grConsulta_ctl02_LkBDesc"(.*)\/a>/sU', $retorno, $value);
            preg_match_all('/>(.*)</sU', isset($value[1][0]) ? $value[1][0] : '', $value);
            $response['propostas'][0]['proximaAtividade'] = isset($value[1][0]) ? $value[1][0] : null;

            

            $caminhoArquivoCookie = $this->cookieFile;
            // $cookiePath = getcwd() . '/Cookies/';
            // $this->cookieFile = $cookiePath."SANTANDER-CURLCOOKIE".'_'.$sessionId;

            // var_dump($caminhoArquivoCookie);
            // exit;

            if (file_exists($caminhoArquivoCookie)) {
                // Lê o conteúdo do arquivo de cookie
                $conteudoCookie = file_get_contents($caminhoArquivoCookie);
            
                // Verifica se o cookie ASP.NET_SessionId está presente no conteúdo do cookie
                if (preg_match('/ASP\.NET_SessionId\s+([^\s]+)/', $conteudoCookie, $matches)) {
                    $valorASPNETSessionId = $matches[1];
                } else {
                    echo "O cookie ASP.NET_SessionId não foi encontrado no arquivo de cookie.";
                }
            } else {
                echo "O arquivo de cookie não foi encontrado.";
                exit;
            }
            
            preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $retorno, $hiddens);
            $eventValidation = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewState = $hiddens[1][0];
            preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $retorno, $hiddens);
            $viewStateGenerator = $hiddens[1][0];

            $variaveis .= "&ctl00%24sm=ctl00%24cph%24popIDProp_updP%7Cctl00_cph_popIDProp_frameAjuda";

            $variaveis  = "__ASYNCPOST=false";
            $variaveis .= "&__EVENTARGUMENT=";
            $variaveis .= "&__EVENTTARGET=ctl00\$cph\$ucAprCns\$j0\$j1\$grConsulta\$ctl02\$LkBSit";
            $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
            $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
            $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
            $variaveis .= "&ctl00%24sm=ctl00%24cph%24ucAprCns%24Up1%7Cctl00%24cph%24ucAprCns%24j0%24j1%24grConsulta%24ctl02%24LkBSit";
            

            // $variaveis .= "&ctl00%24hdfName=" . urlencode($valorASPNETSessionId);
            
            // $variaveis = urlencode($variaveis);
            // var_dump($variaveis);exit;

            // $url = 'https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.DetalheSit.aspx?cod=214699342';


            curl_setopt($this->httpReceita, CURLOPT_URL, 'https://consignado.santander.com.br/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropFin.aspx');
            curl_setopt($this->httpReceita, CURLOPT_POSTFIELDS, $variaveis);
            // curl_setopt($this->httpReceita, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($this->httpReceita);
            var_dump($response);
            exit;
    
            
            
            # Cod. Proposta
            $response['propostas'][0]['codProposta'] = $numProposta;
            # Valor Liquido
            preg_match_all('/ctl00_cph_j0_j1_UcLib3_GRLIB"(.*?)<\/table>/s', $retorno, $value);
            
            #Retorno caso não encontrar proposta
            if(empty($value[1][0]) || $value[1][0] == '')
                return ['erro'=> true, 'mensagem'=> 'Nenhuma proposta foi encontrada.'];

            if (isset($value[1][0])) {
                preg_match_all('/<tr(.*?)<\/tr>/s', $value[1][0], $value, PREG_SET_ORDER, 0);
                    foreach ($value as $val) {
                    if (isset($val[1])) {
                        preg_match('/LIB CONTA CORRENTE/', $val[1], $matches, PREG_OFFSET_CAPTURE, 0);
                        if (!empty($matches)) {
                            preg_match_all('/Label1">(.*?)<\/span>/', $val[1], $matches);
                            $response['propostas'][0]['valorLiquido'] = isset($matches[1][0]) ? $matches[1][0] : null;
                            break;
                        }
                        
                        preg_match('/LIB TED/', $val[1], $matches, PREG_OFFSET_CAPTURE, 0);
                        if (!empty($matches)) {
                            preg_match_all('/Label1">(.*?)<\/span>/', $val[1], $matches);
                            $response['propostas'][0]['valorLiquido'] = isset($matches[1][0]) ? $matches[1][0] : null;
                            break;
                        }
                            
                        preg_match('/LIB CONTA POUPAN/', $val[1], $matches, PREG_OFFSET_CAPTURE, 0);
                        if (!empty($matches)) {
                            preg_match_all('/Label1">(.*?)<\/span>/', $val[1], $matches);
                            $response['propostas'][0]['valorLiquido'] = isset($matches[1][0]) ? $matches[1][0] : null;
                            break;
                        }
                    }
                }
            }
            # Valor solicitado
            preg_match_all('/lblValorSolicitado">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['valorSolicitado'] = isset($value[1][0]) ? $value[1][0] : null;
            # Nome cliente
            preg_match_all('/lblCliente">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['nomeCliente'] = isset($value[1][0]) ? $value[1][0] : null;
            # Valor Operacao
            preg_match_all('/lblValorOperacao">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['valorOperacao'] = isset($value[1][0]) ? $value[1][0] : null;
            # CPF
            preg_match_all('/lblCpf">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['cpf'] = isset($value[1][0]) ? $value[1][0] : null;
            # Valor Total
            preg_match_all('/lblValorTotal">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['valorTotal'] = isset($value[1][0]) ? $value[1][0] : null;
            # Matricula
            preg_match_all('/lbMatricula">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['matricula'] = isset($value[1][0]) ? $value[1][0] : null;
            # Margem
            preg_match_all('/lblMargem">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['margem'] = isset($value[1][0]) ? $value[1][0] : null;
            # Dt. Nascimento
            preg_match_all('/lblDataDeNascimento">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['dataNascimento'] = isset($value[1][0]) ? $value[1][0] : null;
            # Valor Parcela
            preg_match_all('/lblValorParcela">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['valorParcela'] = isset($value[1][0]) ? $value[1][0] : null;
            # Dt. Admissao
            preg_match_all('/lblDataDeAdmissao">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['dataAdmissao'] = isset($value[1][0]) ? $value[1][0] : null;
            # Qtd. Parcelas
            preg_match_all('/lblQtdParcelas">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['quantidadeParcelas'] = isset($value[1][0]) ? $value[1][0] : null;
            # Produto
            preg_match_all('/lblProduto">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['produto'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa Regra
            preg_match_all('/lblTaxaRegra">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['taxaRegra'] = isset($value[1][0]) ? $value[1][0] : null;
            # Empregador
            preg_match_all('/lblEmpregador">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['empregador'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa
            preg_match_all('/lblTaxa">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['taxa'] = isset($value[1][0]) ? $value[1][0] : null;
            # Regra
            preg_match_all('/lblRegra">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['regra'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa Contrato
            preg_match_all('/LbTxContrato">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['taxaContrato'] = isset($value[1][0]) ? $value[1][0] : null;
            # Clique Unico
            preg_match_all('/lblCliqueUnico">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['cliqueUnico'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa CET a.m
            preg_match_all('/lbTxCetAm">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['taxaCetAm'] = isset($value[1][0]) ? $value[1][0] : null;
            # Data Base
            preg_match_all('/lblDataBase">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['dataBase'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa CET a.a
            preg_match_all('/lbTxCetAa">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['taxaCetAa'] = isset($value[1][0]) ? $value[1][0] : null;
            # Vencimento Primeira Parcela
            preg_match_all('/lblVcto1Parcela">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['vencPrimeiraParcela'] = isset($value[1][0]) ? $value[1][0] : null;
            # IOF
            preg_match_all('/lblIof">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['iof'] = isset($value[1][0]) ? $value[1][0] : null;
            # Dt. Final Contrato
            preg_match_all('/lbDtFinalContrato">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['dataFinalContrato'] = isset($value[1][0]) ? $value[1][0] : null;
            # Seguro
            preg_match_all('/lblSeguro">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['seguro'] = isset($value[1][0]) ? $value[1][0] : null;
            # Limite Op. Disp. Empreg.
            preg_match_all('/lblLmteOperEmpr">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['limOpDispEmpreg'] = isset($value[1][0]) ? $value[1][0] : null;
            # Rating
            // preg_match_all('/lblRating">(.*?)<\/span>/', $retorno, $value);
            // $response['propostas'][0]['rating'] = $value[1][0];
            # Valor Total Averbado
            preg_match_all('/lbTotAverb">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['valorTotalAverbado'] = isset($value[1][0]) ? $value[1][0] : null;
            # Segmento Principal
            // preg_match_all('/lblSegtoPrincipal">(.*?)<\/span>/', $retorno, $value);
            // $response['propostas'][0]['segmentoPrincipal'] = $value[1][0];
            # Tipo Cliente
            preg_match_all('/lblTipoCliente">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['tipoCliente'] = isset($value[1][0]) ? $value[1][0] : null;
            # Tipo da Conta
            preg_match_all('/lbTpContaVlr">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['tipoConta'] = isset($value[1][0]) ? $value[1][0] : null;
            # N da PO no GARRA
            preg_match_all('/lblPOGARRA">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['poGarra'] = isset($value[1][0]) ? $value[1][0] : null;
            # Percentual Averbado
            preg_match_all('/lbPercAverb">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['percentualAverbado'] = isset($value[1][0]) ? $value[1][0] : null;
            # PropostaPreventivo
            preg_match_all('/lblNroOperacaoOY">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['propostaPreventivo'] = isset($value[1][0]) ? $value[1][0] : null;
            # Saldo OY
            preg_match_all('/lblVlrTotalSaldoOY">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['saldoOY'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa Contrato Recalculado
            preg_match_all('/lblTxJrApMes" style="color:Red;font-weight:bold;">(.*?)<\/span>/', $retorno, $value);
            $response['propostas'][0]['taxaContratoRecalculado'] = isset($value[1][0]) ? $value[1][0] : null;

            # Monta o array com o retorno
            $arrPendencias = [];
            if (trim(strtolower($response['propostas'][0]['situacao'])) == 'reprovado' || 
                trim(strtolower($response['propostas'][0]['situacao'])) == 'cancelada' || 
                trim(strtolower($response['propostas'][0]['situacao'])) == 'cancelado') {
                $arrPendencias[0]['observacao']     = 'Proposta ' . trim(strtolower($response['propostas'][0]['situacao']));
                $arrPendencias[0]['tipo_pendencia'] = 'Pendencia Santander';
            }

            $dados = [
                'codProposta'            => $response['propostas'][0]['codProposta'],
                'cpf'                    => $response['propostas'][0]['cpf'],
                'nomeCliente'            => $response['propostas'][0]['nomeCliente'],
                'dataBase'               => $response['propostas'][0]['dataBase'],
                'dataAtivo'              => null,
                'horaAtivo'              => null,
                'produto'                => $response['propostas'][0]['produto'],
                'status'                 => $response['propostas'][0]['situacao'].' '.$response['propostas'][0]['proximaAtividade'],
                'situacao'               => $response['propostas'][0]['proximaAtividade'],
                'liberacao1'             => null,
                'liberacao2'             => null,
                'convenio'               => null,
                'valorPrincipal'         => $this->replaceValue($response['propostas'][0]['valorLiquido']),
                'valorParcela'           => $this->replaceValue($response['propostas'][0]['valorParcela']),
                'promotora'              => null,
                'digitadora'             => null,
                'usuario'                => null,
                'loginDigitador'         => null,
                'valorBruto'             => $this->replaceValue($response['propostas'][0]['valorSolicitado']),
                'propostaAverbada'       => !empty($response['propostas'][0]['valorTotalAverbado']) ? 'S' : 'N',
                'valorTroco'             => null,
                'valorSeguro'            => $this->replaceValue($response['propostas'][0]['seguro']),
                'simulacaoRefin'         => null,
                'dataAverbacao'          => null,
                'dataPagamento'          => null,
                'dataPrimeiroVencimento' => $response['propostas'][0]['vencPrimeiraParcela'],
                'dataUltimoVencimento'   => null,
                'pendenciado'            => null,
                'statusId'               => null,
                'temParadinha'           => null,
                'dataSolicitacaoSaldo'   => null,
                'dataPrevistaSaldo'      => null,
                'dataRetornoSaldo'       => null,
                'saldoEnviado'           => null,
                'saldoRetornado'         => null,
                'pendencias'             => $arrPendencias,
                'obs'                    => null
            ];      
            
            return ['erro' => false, 'dados' => $dados];

    }

    // private CurlHandle $ch;
    // private string $cookie;
    // private string $agent = "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET4.0C; .NET4.0E)";

    // protected function send(array $values):array
    // {
    //     try{

    //         $this->cookie = tempnam("/../tmp/", "SANTANDER-CURLCOOKIE");
    
    //         ### Inicio do CURL
    //         $this->ch = curl_init();
    //         ### Inicia a sessao e cookie
    //         if(isset($values['queue']) && $values['queue']){
    //             $this->ch = $values['urlLogin'];
    //             var_dump($this->ch);exit;
    //         }
    //         curl_setopt($this->ch,CURLOPT_URL, $values['urlBase']);
    //         curl_setopt($this->ch,CURLOPT_VERBOSE, true);
    //         curl_setopt($this->ch,CURLOPT_USERAGENT, $this->agent);
    //         curl_setopt($this->ch,CURLOPT_SSL_VERIFYPEER, false);
    //         curl_setopt($this->ch,CURLOPT_SSL_VERIFYHOST, false);
    //         curl_setopt($this->ch,CURLOPT_COOKIEFILE, $this->cookie);
    //         curl_setopt($this->ch,CURLOPT_COOKIEJAR, $this->cookie);
    //         curl_setopt($this->ch,CURLOPT_AUTOREFERER, false);
    //         curl_setopt($this->ch,CURLOPT_RETURNTRANSFER, true);
    //         curl_setopt($this->ch,CURLOPT_FOLLOWLOCATION, true);

    //         if(isset($values['formDataString'])){
    //             curl_setopt($this->ch, CURLOPT_POST, true);
    //             curl_setopt($this->ch, CURLOPT_POSTFIELDS, $values['formDataString']);
    //         }else{
    //             curl_setopt($this->ch,CURLOPT_POSTFIELDS, null);
    //         }

    //         if(isset($values['urlRefer'])){
    //             curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
    //             curl_setopt($this->ch, CURLOPT_MAXREDIRS, 5);
    //             curl_setopt($this->ch, CURLOPT_REFERER, $values['urlRefer']);
    //         }
            
    //         $response = curl_exec($this->ch);

    //         if(isset($values['login']) && $values['login']){
    //             //Confirma autenticacao
    //             curl_setopt($this->ch,CURLOPT_URL, $values['urlIdentification']);
    //             curl_setopt($this->ch,CURLOPT_POSTFIELDS, $values['variables']);
    //             $response = curl_exec($this->ch);
               
    //             //rediireciona tela consignado
    //             curl_setopt($this->ch,CURLOPT_URL, $values['urlHomeConsigned']);
    //             $response = curl_exec($this->ch);
                
    //             preg_match_all("/janela=window.open\('(.*?)','_blank'/s", $response, $window);
            
            
    //             if(preg_match("/Erro ao recuperar/", $response))
    //                 throw new \Exception('Não foi possivel fazer login. Verifique usuário e senha e tente novamente.');
        
    //             ### Valida no portal com o TOKEN criado
    //             curl_setopt($this->ch,CURLOPT_URL, trim($window[1][0]));
    //             $response = curl_exec($this->ch);
    //         }
            
    //         if(isset($values['queue']) && $values['queue']){

    //             preg_match_all('/<input type="hidden" name="__EVENTVALIDATION" id="__EVENTVALIDATION" value="(.*?)" \/>/', $response, $hiddens);
    //             $eventValidation = $hiddens[1][0];
    //             preg_match_all('/<input type="hidden" name="__VIEWSTATE" id="__VIEWSTATE" value="(.*?)" \/>/', $response, $hiddens);
    //             $viewState = $hiddens[1][0];
    //             preg_match_all('/<input type="hidden" name="__VIEWSTATEGENERATOR" id="__VIEWSTATEGENERATOR" value="(.*?)" \/>/', $response, $hiddens);
    //             $viewStateGenerator = $hiddens[1][0];

    //             $response = [];
    //             ### Entra no menu cadastro de operação
    //             $variaveis  = '__EVENTTARGET=ctl00$ContentPlaceHolder1$j0$j1$DataListMenu$ctl00$LinkButton2';
    //             $variaveis .= "&__EVENTVALIDATION=" .  urlencode($eventValidation);
    //             $variaveis .= "&__VIEWSTATE=" . urlencode($viewState);
    //             $variaveis .= "&__VIEWSTATEGENERATOR=" . urlencode($viewStateGenerator);
    //             $variaveis .= "&__EVENTARGUMENT=";
    //             $variaveis .= "&__ASYNCPOST=false";
    //             curl_setopt($this->ch,CURLOPT_URL, $values['urlBase']);
    //             curl_setopt($this->ch,CURLOPT_POST, true);
    //             curl_setopt($this->ch,CURLOPT_POSTFIELDS, $variaveis);
    //             $response = curl_exec($this->ch);
                
    //             preg_match_all('/param1=(.*?)"/s', $response, $param1);
    //             $url = $values['urlAuthorization']. trim($param1[1][0]);
                
    //             curl_setopt($this->ch,CURLOPT_URL, $url);
    //             curl_setopt($this->ch,CURLOPT_POST, false);
    //             $response = curl_exec($this->ch);
    //             var_dump($response);exit;
    //         }

            
    //         // curl_close($this->ch);

    //         return [
    //             "status"    =>  true,
    //             "response"  =>  $response
    //         ];
            
    //     }catch(\Exception $e){
    //         return [
    //             "status"    =>  false,
    //             "response"  =>  $e->getMessage()
    //         ];
    //     }
    // }

}