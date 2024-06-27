<?php

function whatsApp($number, $text, $url = '')
{
    $response = array('message' => 'Invalid Host', 'status' => 'error');
    // if ($_SERVER['SERVER_NAME'] == 'narangoda.colorbiz.org') {
        $media = '';
        if (!empty($url)) {
            $media = 'https://' . $_SERVER['SERVER_NAME'] . '/main/pages/forms/' . $url;
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://chatbiz.net/api/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        "number": "' . $number . '",
        "type": "media",
        "message": "' . $text . '",
        "media_url": "' . $media . '",
        "instance_id": "65B8745834BB2",
        "access_token": "65b8742c1285f"
        }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: stackpost_session=efk5hfs38t9mtcfe0lq2laprmohinudj'
            ),
        ));

        $response = curl_exec($curl);
        $response = json_decode($response, true);

        curl_close($curl);
        //new-662B41B331A1C
        //old-65B8745834BB2
    // }

    $response = $response['status'];
    return $response;
}
