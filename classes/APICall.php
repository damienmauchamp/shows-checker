<?php
/**
 * Created by PhpStorm.
 * User: dmauchamp
 * Date: 23/05/2018
 * Time: 15:48
 */

namespace TVShowsAPI;


class APICall
{


    /**
     * APICall constructor.
     */
    public function __construct()
    {
    }

    public static function get($url)
    {
//        var_dump("request: $url");
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $url . "?api_key=" . API_KEY,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "{}",
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return !$err ? $response : null;
    }
}