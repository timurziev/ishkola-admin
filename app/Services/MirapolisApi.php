<?php

namespace App\Services;

class MirapolisApi {

    /**
     * Sign into Mirafox API.
     *
     * @param  int  $url
     * @param  array  $parameters
     * @return \Illuminate\Http\Response
     */
    public function signin($url, $parameters)
    {
        $appid = 'system';
        $secretkey = 'yU7RFszU';
        $ret_params = $parameters;
        ksort($ret_params);
        $ret_params['appid'] = $appid;
        $signstring = "$url?";

        foreach($ret_params as $key => $val) {
            if (($val != "")||(gettype($val) != "string"))
            {
                $signstring .= "$key=$val&";
            }
        }

        $signstring .= "secretkey=$secretkey";
        $ret_params['sign'] = strtoupper(md5($signstring));

        return $ret_params;
    }

    /**
     * Send request and return array of items from Mirafox API.
     *
     * @param  int  $url
     * @param  array  $parameters
     * @param  string  $method
     * @return \Illuminate\Http\Response
     */
    public function sendRequest($url, $parameters, $method)
    {
        $curl_data = $this->signin($url, $parameters);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_CUSTOMREQUEST,$method);
        $query = http_build_query($curl_data);

        if ($method == "POST") {
            curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
        } elseif ($method == "GET" || $method == "DELETE") {
            $url .= "?$query";
        } else {
            curl_setopt($ch,CURLOPT_POSTFIELDS,$query);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        $curl_response = curl_exec($ch);
        $response = json_decode($curl_response, true);

        return $response;
    }

    public function miraURL($module, $func, $id = null, $param = null)
    {
        return "https://room.nohchalla.com/mira/service/v2/$module/$id/$func/$param";
    }
}