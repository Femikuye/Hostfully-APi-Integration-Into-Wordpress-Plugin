<?php

/**
 * @package EsPropertyListings
 */

namespace User\EsPropertyListings\Api;

class CurlRequest
{
    public function makeRequest($params, $method = "get", $decode_res = false)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $params['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $params['headers']);
        if ($method == "post") {
            // curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params['req_data']);
        }
        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
            // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        $data = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($responseCode >= 400 || $data == false) {
            curl_close($ch);
            return ['success' => false, 'msg' => "Error! ." . curl_error($ch), 'data' => $data];
        }
        if ($decode_res) {
            $data = json_decode($data, true);
        }
        curl_close($ch);
        return ['success' => true, 'data' => $data, "msg" => "Data Fetched"];
    }
}
