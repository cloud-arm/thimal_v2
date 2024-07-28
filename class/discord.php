<?php

function discord($message, $header = "", $table = '')
{
    $response = ['message' => 'Invalid Host', 'status' => 'failed'];

    // if ($_SERVER['SERVER_NAME'] != 'localhost') {

        $server = $_SERVER['SERVER_NAME'];

        if (empty($header)) {
            $header = "Thimal";
        }

        $text = "Server => [" . $server . "]\nMessage => " . $message;

        if (!empty($table)) {
            $text = "Server => [" . $server . "]\nMessage => " . $message . "\nTable => " . $table;
        }

        $data = [
            "content" => $text,
            "username" => $header,
        ];

        $url = "https://discord.com/api/webhooks/1266975058958745613/BzUFTORGvduvbvofJDORhG3mORQ5hlDeXiTrgvJeovFBZWQ6n23FtsAYE0gsFiFQQglG";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $response['status'] = curl_exec($curl);
        $response['message'] = curl_error($curl);

        if (empty($response['status'])) {
            $response['status'] = "success";
        }

        curl_close($curl);
    // }

    return $response['status'];
}
