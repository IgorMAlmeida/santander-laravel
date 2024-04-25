<?php

namespace App\Services;

use App\Services\Curl;
use App\Adapters\ReplaceValues;

class ScrappingService extends Curl{

    public function getContent($params) : array {
        try{
            $value = $params['value'];
            $page = $params['screen'];
            $response = $params['response'];

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
            preg_match_all('/lblValorSolicitado">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['valorSolicitado'] = isset($value[1][0]) ? $value[1][0] : null;
            # Nome cliente
            preg_match_all('/lblCliente">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['nomeCliente'] = isset($value[1][0]) ? $value[1][0] : null;
            # Valor Operacao
            preg_match_all('/lblValorOperacao">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['valorOperacao'] = isset($value[1][0]) ? $value[1][0] : null;
            # CPF
            preg_match_all('/lblCpf">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['cpf'] = isset($value[1][0]) ? $value[1][0] : null;
            # Valor Total
            preg_match_all('/lblValorTotal">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['valorTotal'] = isset($value[1][0]) ? $value[1][0] : null;
            # Matricula
            preg_match_all('/lbMatricula">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['matricula'] = isset($value[1][0]) ? $value[1][0] : null;
            # Margem
            preg_match_all('/lblMargem">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['margem'] = isset($value[1][0]) ? $value[1][0] : null;
            # Dt. Nascimento
            preg_match_all('/lblDataDeNascimento">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['dataNascimento'] = isset($value[1][0]) ? $value[1][0] : null;
            # Valor Parcela
            preg_match_all('/lblValorParcela">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['valorParcela'] = isset($value[1][0]) ? $value[1][0] : null;
            # Dt. Admissao
            preg_match_all('/lblDataDeAdmissao">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['dataAdmissao'] = isset($value[1][0]) ? $value[1][0] : null;
            # Qtd. Parcelas
            preg_match_all('/lblQtdParcelas">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['quantidadeParcelas'] = isset($value[1][0]) ? $value[1][0] : null;
            # Produto
            preg_match_all('/lblProduto">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['produto'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa Regra
            preg_match_all('/lblTaxaRegra">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['taxaRegra'] = isset($value[1][0]) ? $value[1][0] : null;
            # Empregador
            preg_match_all('/lblEmpregador">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['empregador'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa
            preg_match_all('/lblTaxa">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['taxa'] = isset($value[1][0]) ? $value[1][0] : null;
            # Regra
            preg_match_all('/lblRegra">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['regra'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa Contrato
            preg_match_all('/LbTxContrato">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['taxaContrato'] = isset($value[1][0]) ? $value[1][0] : null;
            # Clique Unico
            preg_match_all('/lblCliqueUnico">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['cliqueUnico'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa CET a.m
            preg_match_all('/lbTxCetAm">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['taxaCetAm'] = isset($value[1][0]) ? $value[1][0] : null;
            # Data Base
            preg_match_all('/lblDataBase">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['dataBase'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa CET a.a
            preg_match_all('/lbTxCetAa">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['taxaCetAa'] = isset($value[1][0]) ? $value[1][0] : null;
            # Vencimento Primeira Parcela
            preg_match_all('/lblVcto1Parcela">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['vencPrimeiraParcela'] = isset($value[1][0]) ? $value[1][0] : null;
            # IOF
            preg_match_all('/lblIof">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['iof'] = isset($value[1][0]) ? $value[1][0] : null;
            # Dt. Final Contrato
            preg_match_all('/lbDtFinalContrato">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['dataFinalContrato'] = isset($value[1][0]) ? $value[1][0] : null;
            # Seguro
            preg_match_all('/lblSeguro">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['seguro'] = isset($value[1][0]) ? $value[1][0] : null;
            # Limite Op. Disp. Empreg.
            preg_match_all('/lblLmteOperEmpr">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['limOpDispEmpreg'] = isset($value[1][0]) ? $value[1][0] : null;
            # Valor Total Averbado
            preg_match_all('/lbTotAverb">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['valorTotalAverbado'] = isset($value[1][0]) ? $value[1][0] : null;
            # Tipo Cliente
            preg_match_all('/lblTipoCliente">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['tipoCliente'] = isset($value[1][0]) ? $value[1][0] : null;
            # Tipo da Conta
            preg_match_all('/lbTpContaVlr">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['tipoConta'] = isset($value[1][0]) ? $value[1][0] : null;
            # N da PO no GARRA
            preg_match_all('/lblPOGARRA">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['poGarra'] = isset($value[1][0]) ? $value[1][0] : null;
            # Percentual Averbado
            preg_match_all('/lbPercAverb">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['percentualAverbado'] = isset($value[1][0]) ? $value[1][0] : null;
            # PropostaPreventivo
            preg_match_all('/lblNroOperacaoOY">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['propostaPreventivo'] = isset($value[1][0]) ? $value[1][0] : null;
            # Saldo OY
            preg_match_all('/lblVlrTotalSaldoOY">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['saldoOY'] = isset($value[1][0]) ? $value[1][0] : null;
            # Taxa Contrato Recalculado
            preg_match_all('/lblTxJrApMes" style="color:Red;font-weight:bold;">(.*?)<\/span>/', $page, $value);
            $response['propostas'][0]['taxaContratoRecalculado'] = isset($value[1][0]) ? $value[1][0] : null;

            # Monta o array com o retorno
            $arrPendencias = [];
            if (trim(strtolower($response['propostas'][0]['situacao'])) == 'reprovado' || 
                trim(strtolower($response['propostas'][0]['situacao'])) == 'cancelada' || 
                trim(strtolower($response['propostas'][0]['situacao'])) == 'cancelado') {
                $arrPendencias[0]['observacao']     = 'Proposta ' . trim(strtolower($response['propostas'][0]['situacao']));
                $arrPendencias[0]['tipo_pendencia'] = 'Pendencia Santander';
            }

            return [
                "status"    =>  true,
                "dados"     => [
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
                    'valorPrincipal'         => (new ReplaceValues())->replaceComma($response['propostas'][0]['valorLiquido']),
                    'valorParcela'           => (new ReplaceValues())->replaceComma($response['propostas'][0]['valorParcela']),
                    'promotora'              => null,
                    'digitadora'             => null,
                    'usuario'                => null,
                    'loginDigitador'         => null,
                    'valorBruto'             => (new ReplaceValues())->replaceComma($response['propostas'][0]['valorSolicitado']),
                    'propostaAverbada'       => !empty($response['propostas'][0]['valorTotalAverbado']) ? 'S' : 'N',
                    'valorTroco'             => null,
                    'valorSeguro'            => (new ReplaceValues())->replaceComma($response['propostas'][0]['seguro']),
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
                ]
            ];
        
        }catch (\Exception $e){
            return [
                'erro'=> true,
                'mensagem' => $e->getMessage()
            ];
        }
    }

}