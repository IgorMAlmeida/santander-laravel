<?php

namespace App\Services;

use App\Interfaces\CNPJConsultInterface;

class CasaDosDados extends SendCurlService implements CNPJConsultInterface{

    public function consultCnpj(string $cnpj):array {

        try{

            $values = [
                "url"         => "https://casadosdados.com.br/solucao/cnpj?q=".$cnpj,
                "typeRequest" => "GET",
                "headers"     =>[
                    'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                    'accept-language: pt-BR,pt;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
                    'cache-control: no-cache',
                    'pragma: no-cache',
                    'priority: u=0, i',
                    'sec-ch-ua: "Microsoft Edge";v="125", "Chromium";v="125", "Not.A/Brand";v="24"',
                    'sec-ch-ua-mobile: ?0',
                    'sec-ch-ua-platform: "Linux"',
                    'sec-fetch-dest: document',
                    'sec-fetch-mode: navigate',
                    'sec-fetch-site: same-origin',
                    'sec-fetch-user: ?1',
                    'upgrade-insecure-requests: 1',
                    'user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36 Edg/125.0.0.0',                
                ],
                "cookie"      =>   '_ga=GA1.1.644091023.1723048908; _cc_id=77e3d9f20b0bb383cb0d160a0e6a0c72; panoramaId_expiry=1723653708052; panoramaId=d7472ee847b1e16898916b5ed25416d53938456bc28b28519e474c8db7fc1ae3; panoramaIdType=panoIndiv; tt.u=0100007F6E8E4F66BA06AD81029A4418; tt.nprf=; _pbjs_userid_consent_data=6758948062265505; cf_clearance=SRy0.gleT0Shy8.MkbjWee_e6DKcWsJXvdFYn_WEdLM-1723053892-1.0.1.1-TyzTS5uaWEz9pEOJAhzenLQjuENoWJacdHSn5Fz9IfTlA5ikcixbUf7rht..yHHulzsPFWGU0Gx3CQ708aJbbA; tt_c_vmt=1723053896; tt_c_c=direct; tt_c_s=direct; tt_c_m=direct; _ttuu.s=1723053895796; _ttdmp=E:4|X:3|G:1|LS:; __gads=ID=9a1732ee0fb81e11:T=1723048907:RT=1723054199:S=ALNI_MZ8gJP20irKp9cYPBfD2QMDAjoDjw; __gpi=UID=00000ec7dde3c128:T=1723048907:RT=1723054199:S=ALNI_Ma54ELeuv-vAzJExBpFVkQzmo17_g; __eoi=ID=7ebacded5d045d90:T=1723048907:RT=1723054199:S=AA-AfjbKUs5Fq4jdhYUhcxyOfgU9; clever-counter-76745=0-1; FCNEC=%5B%5B%22AKsRol-SBT4uPNvnytKNV8eIMACoEiIzHY9BWDKpmhfYDkUzBIBGfChGhv4kggIiWV9qfhuMzKOeprR9zRlbe-J_PcBHUITLD4tau6zBtHpgm9ZkpTb8ThrErMJD8H4PB79jqHBGQl1TGamMS9oX4F45ymcGZkAoPw%3D%3D%22%5D%5D; _ga_06Q5WY4PHL=GS1.1.1723053893.2.1.1723054379.0.0.0; cto_bundle=Ze7u-l92U3ljanFabHY0Njd1QlZ6WiUyRmpRT2dQa0NVcFFsYTF3NWlkSmMyczJ4SG95RDlDWlFOTkVFZlVJTnNEQ25DWFpoNm00QlI1SGtVMGREU1djZWpRJTJGZVdTWkZtOFNBMkVUU051bzQ2JTJGczdRUXZUUFN4bUxrZkFRWHRWS3lyMWFrSkZDNHd4JTJGOUN5S2VRbmx0NWhZZjZ0akRkTTFHN2I1YzBUVFYlMkJDMjBUUEY4JTNE; cto_bidid=7K9-PF9CaUxtdHdnams1cWtTM24zWTlUMzRYVzk4cmlMZm1xYVRsekg3bEtuQUlzOEJtdW1xNHZIOGoxMGFDbkR0SEFUTHd4elkzWXhsbFl4Wlh2eVdPNSUyRld5ZWV5ZFhCbFhzVDJPck82bWpiOTRXVHV2MnRMWEZERGZ5NzNUNTVwUzhj'
            ];

            $response = $this->send($values);

            if(!$response['status']){
                throw new \Exception($response['response']);
            }

            return [
                "status"    =>  true,
                "response"=>  $response['response']
            ];

        }catch (\Exception $e){
            return [
                "status"     =>  false,
                "response" =>  $e->getMessage(),
            ];
        }
    }

}