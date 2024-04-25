<?php

namespace App\Services;

use CurlHandle;
use App\Helpers\Hiddens;
use App\Helpers\Variables;
use App\Services\ScrappingService;

class Curl
{
    public string $urlConsignado = "https://consignado.santander.com.br/";
    public string $urlBase       = "https://www.santandernegocios.com.br/";
    public CurlHandle $httpReceita;
    public $cookie;
    public $cookieFile;
    public $agent     = "Mozilla/5.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET4.0C; .NET4.0E)";

    public function loginEsteira($params)
    {

        try{
            $sessionId = $params['sessionId'];
            $cookiePath = getcwd() . '/Cookies/';
            $this->cookieFile = $cookiePath."SANTANDER-CURLCOOKIE".'_'.$sessionId;

            ### Inicio do CURL
            $this->httpReceita = curl_init();
            ### Inicia a sessao e cookie
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlBase);
            curl_setopt($this->httpReceita,CURLOPT_VERBOSE, true);
            curl_setopt($this->httpReceita,CURLOPT_USERAGENT, $this->agent);
            curl_setopt($this->httpReceita,CURLOPT_COOKIEFILE, $this->cookieFile);
            curl_setopt($this->httpReceita,CURLOPT_COOKIEJAR, $this->cookieFile);
            curl_setopt($this->httpReceita,CURLOPT_AUTOREFERER, false);
            curl_setopt($this->httpReceita,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->httpReceita,CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, null);
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlBase."/F6_framework_portal/asp/F6_identificacaousuario.asp");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $params['variables']);
            curl_exec($this->httpReceita);
            
            ### Entra na parte de consignado
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlBase."/CSG_CREDITO_CONSIGNADO/ASP/CSG_LOGINMBSCSG.ASP");
            $return = curl_exec($this->httpReceita);
            
            preg_match_all("/janela=window.open\('(.*?)','_blank'/s", $return, $window);
            
            ### Valida se o return foi um erro
            if(preg_match("/Erro ao recuperar/", $return)){
                throw new \Exception('Não foi possivel fazer login. Verifique usuário e senha e tente novamente.');
            }

            ### Valida no portal com o TOKEN criado
            curl_setopt($this->httpReceita,CURLOPT_URL, trim($window[1][0]));
            $return = curl_exec($this->httpReceita);
        
            return [
                'erro'       => false,
                'response'   => $this->httpReceita,
                'cookieFile' => $this->cookieFile
            ];

        }catch (\Exception $e){
            return [
                'erro'=> true,
                'mensagem' => 'Não foi possivel fazer login. Verifique usuário e senha e tente novamente.'
            ];
        }

    }

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

    public function esteira($values)
    {
        try{

            $this->httpReceita = $values['curlHandle'];
            $numProposta = $values['propostaId'];
            $this->cookieFile = $values['cookieFile'];

            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Portal/FI.AC.GUI.FIMENU.aspx");
            curl_setopt($this->httpReceita,CURLOPT_POST, false);
            $return = curl_exec($this->httpReceita);

            $hiddenFields = (new Hiddens())->getHiddens($return);
            $variables = (new Variables())->setVariables(
                [...$hiddenFields,
                'eventTarget' => 'ctl00$ContentPlaceHolder1$j0$j1$DataListMenu$ctl00$LinkButton2'
            ]);

            $response = [];
            ### Entra no menu cadastro de operação
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Portal/FI.AC.GUI.FIMENU.aspx");
            curl_setopt($this->httpReceita,CURLOPT_POST, true);
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
            $return = curl_exec($this->httpReceita);
            
            preg_match_all('/param1=(.*?)"/s', $return, $param1);
            $url = $this->urlConsignado."/autorizador45/login/AC.UI.SAN.aspx?paramtp=TOKENCSG&param1=" . trim($param1[1][0]);

            curl_setopt($this->httpReceita,CURLOPT_URL, $url);
            curl_setopt($this->httpReceita,CURLOPT_POST, false);
            curl_exec($this->httpReceita);

            ### Entra no menu "Esteira > Aprovacao de proposta"
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Prop.aspx");
            $consultReturn = curl_exec($this->httpReceita);
            
            ### Entra na tela de consulta
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_popIDProp_frameAjuda&idPback=");
            curl_exec($this->httpReceita);

            $hiddenFields = (new Hiddens())->getHiddens($return);
            $variables = (new Variables())->setVariables(
                [...$hiddenFields,
                'eventTarget' => 'ctl00\$cph\$j0\$j1\$bbPesq',
                'eventArgument' => 'onclick'
            ]);

            ### Consulta informacoes da esteira
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPROP");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
            $return = curl_exec($this->httpReceita);
           
            $hiddenFields = (new Hiddens())->getHiddens($return);
            $variables = (new Variables())->setVariables(
                [...$hiddenFields,
                'eventTarget' => 'ctl00%24cph%24j0%24j1%24grResultado%24ctl02%24lb',
            ]);

            ### Link da proposta
            curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_popIDProp_frameAjuda&idPback=");
            curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
            $return = curl_exec($this->httpReceita);

            ### Pegando status da proposta
            preg_match_all('/ctl00_cph_ucAprCns_j0_j1_grConsulta"(.*)<\/table>/s', $return, $valueStatus);
            if(empty($valueStatus[0]) && empty($valueStatus[1])){

                if(isset($values['progress'])){

                    $hiddenFields = (new Hiddens())->getHiddens($return);
                    $variables = (new Variables())->setVariables(
                        [...$hiddenFields,
                        'eventTarget' => 'ctl00_cph_popIDProp_frameAjuda',
                    ]);
        
                    ### recarrega tela anterior com as informacoes da proposta
                    $variables .= "&ctl00%24sm=ctl00%24cph%24popIDProp_updP%7Cctl00_cph_popIDProp_frameAjuda";
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropAnd.aspx");
                    curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
                    $return = curl_exec($this->httpReceita);

                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPRPA");
                    $return = curl_exec($this->httpReceita);

                    $hiddenFields = (new Hiddens())->getHiddens($return);
                    $variables = (new Variables())->setVariables(
                        [...$hiddenFields,
                        'eventTarget' => "ctl00\$cph\$j0\$j1\$bbPesq",
                        'eventArgument' => 'onclick',
                    ]);
                    $variables .= "&ctl00\$cph\$j0\$j1\$rblTipoPesq=nrprop";
                    $variables .= "&ctl00\$cph\$j0\$j1\$txtPesq\$CAMPO=".$numProposta;
                    $variables .= "&ctl00\$sm=ctl00\$cph\$up1|ctl00\$cph\$j0\$j1\$grResultado\$ctl02\$lb";

                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPROP");
                    curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
                    $return = curl_exec($this->httpReceita);

                    $hiddenFields = (new Hiddens())->getHiddens($return);
                    $variables = (new Variables())->setVariables(
                        [...$hiddenFields,
                        'eventTarget' => "ctl00\$cph\$j0\$j1\$grResultado\$ctl02\$lb",
                        'eventArgument' => '',
                        'lastFocus' => ''
                    ]);
                    $variables .= "&ctl00%24cph%24j0%24j1%24rblTipoPesq=nrprop";
                    $variables .= "&ctl00%24cph%24j0%24j1%24txtPesq%24CAMPO=$numProposta";
                    $variables .= "&ctl00%24sm=ctl00\$cph\$up1|ctl00\$cph\$j0\$j1\$grResultado\$ctl02\$lb";
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPRPA");
                    curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
                    $return = curl_exec($this->httpReceita);

                    $hiddenFields = (new Hiddens())->getHiddens($return);
                    $variables = (new Variables())->setVariables(
                        [...$hiddenFields,
                        'eventTarget' => "ctl00_cph_ucAprCns_popIDProp_frameAjuda",
                        'eventArgument' => '',
                        'lastFocus' => ''
                    ]);

                    $variables .= "&ctl00%24sm=ctl00\$cph\$ucAprCns\$popIDProp_updP|ctl00_cph_ucAprCns_popIDProp_frameAjuda";
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropAnd.aspx");
                    curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
                    $return = curl_exec($this->httpReceita);
                }

                if(isset($values['finished'])){
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropFin.aspx");
                    $consultReturn = curl_exec($this->httpReceita);
    
                    ### Entra na tela de consulta
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPRPF");
                    $return = curl_exec($this->httpReceita);
    
                    $hiddenFields = (new Hiddens())->getHiddens($return);
                    $variables = (new Variables())->setVariables(
                        [...$hiddenFields,
                        'eventTarget' => "ctl00\$cph\$j0\$j1\$bbPesq",
                        'eventArgument' => 'onclick',
                        'lastFocus' => ''
                    ]);
                    $variables .= "&ctl00\$cph\$j0\$j1\$rblTipoPesq=nrprop";
                    $variables .= "&ctl00\$cph\$j0\$j1\$txtPesq\$CAMPO=$numProposta";
                    $variables .= "&ctl00\$sm=ctl00\$cph\$up1|ctl00\$cph\$j0\$j1\$grResultado\$ctl02\$lb";
    
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPROP");
                    curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
                    $return = curl_exec($this->httpReceita);
            
                    $hiddenFields = (new Hiddens())->getHiddens($return);
                    $variables = (new Variables())->setVariables(
                        [...$hiddenFields,
                        'eventTarget' => "ctl00%24cph%24j0%24j1%24grResultado%24ctl02%24lb",
                        'eventArgument' => '',
                        'lastFocus' => ''
                    ]);
                    $variables .= "&ctl00%24cph%24j0%24j1%24rblTipoPesq=nrprop";
                    $variables .= "&ctl00%24cph%24j0%24j1%24txtPesq%24CAMPO=$numProposta";
                    $variables .= "&ctl00%24sm=ctl00%24cph%24up1%7Cctl00%24cph%24j0%24j1%24grResultado%24ctl02%24lb";
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.Localiza.aspx?UpdPan=ctl00_cph_ucAprCns_popIDProp_updP&ns=CN.Prop.Localizar&frame=ctl00_cph_ucAprCns_popIDProp_frameAjuda&idPback=&rt=MPCNPRPF");
                    curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
                    $return = curl_exec($this->httpReceita);

                    $hiddenFields = (new Hiddens())->getHiddens($return);
                    $variables = (new Variables())->setVariables(
                        [...$hiddenFields,
                        'eventTarget' => "ctl00_cph_popIDProp_frameAjuda",
                        'eventArgument' => '',
                        'lastFocus' => ''
                    ]);

                    $variables .= "&ctl00%24sm=ctl00%24cph%24popIDProp_updP%7Cctl00_cph_popIDProp_frameAjuda";
                    curl_setopt($this->httpReceita,CURLOPT_URL, $this->urlConsignado."/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropFin.aspx");
                    curl_setopt($this->httpReceita,CURLOPT_POSTFIELDS, $variables);
                    $return = curl_exec($this->httpReceita);
                }

                ### Pegando status da proposta
                preg_match_all('/ctl00_cph_ucAprCns_j0_j1_grConsulta"(.*)<\/table>/s', $return, $valueStatus);
            }

            preg_match_all('/id="ctl00_cph_ucAprCns_j0_j1_grConsulta_ctl02_LkBSit"(.*)\/a>/sU', $return, $value);
            preg_match_all('/>(.*)</sU', isset($value[1][0]) ? $value[1][0] : '', $value);
            $response['propostas'][0]['situacao'] = isset($value[1][0]) ? $value[1][0] : null;
            
            # Proxima atividade
            preg_match_all('/id="ctl00_cph_ucAprCns_j0_j1_grConsulta_ctl02_LkBDesc"(.*)\/a>/sU', $return, $value);
            preg_match_all('/>(.*)</sU', isset($value[1][0]) ? $value[1][0] : '', $value);
            $response['propostas'][0]['proximaAtividade'] = isset($value[1][0]) ? $value[1][0] : null;

            $hiddenFields = (new Hiddens())->getHiddens($return);
            $variables = (new Variables())->setVariables(
                [...$hiddenFields,
                'eventTarget' => "ctl00\$cph\$ucAprCns\$j0\$j1\$grConsulta\$ctl02\$LkBSit",
                'eventArgument' => '',
                'lastFocus' => ''
            ]);
            $variables .= "&ctl00%24sm=ctl00%24cph%24ucAprCns%24Up1%7Cctl00%24cph%24ucAprCns%24j0%24j1%24grConsulta%24ctl02%24LkBSit";

            if(isset($values['progress'])){
                $url = $this->urlConsignado.'/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropAnd.aspx';
            }
            if(isset($values['finished'])){
                $url = $this->urlConsignado.'/Autorizador45/MenuWeb/Esteira/AprovacaoConsulta/UI.CN.PropFin.aspx';
            }
        
            curl_setopt($this->httpReceita, CURLOPT_URL, $url);
            curl_setopt($this->httpReceita, CURLOPT_POSTFIELDS, $variables);
            $return = curl_exec($this->httpReceita);

            # Cod. Proposta
            $response['propostas'][0]['codProposta'] = $numProposta;

            # Valor Liquido
            preg_match_all('/ctl00_cph_j0_j1_UcLib3_GRLIB"(.*?)<\/table>/s', $return, $value);

            #Retorno caso não encontrar proposta
            if( !isset($value[1][0]) ||  empty($value[1][0])) {
                $this->httpReceita = $values['curlHandle'];
                throw new \Exception("Nenhuma proposta encontrada!");
            }

            $params = [
                'response' => $response,
                'value'    => $value,
                'screen'   => $return,
            ];

            $scrappingData = (new ScrappingService())->getContent($params);

            if(isset($scrappingData['erro'])) {
                throw new \Exception("Error on scrapping data!");
            }

            return [
                'erro'  => false,
                'dados' => $scrappingData['dados']
            ];

        }catch (\Exception $e){
            return [
                'stts'=> false,
                'erro'=> true,
                'mensagem' => $e->getMessage()
            ];
        }
    }
}