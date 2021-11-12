<?php


namespace Lyly\Plasgate;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

class Plasgate
{

    protected $method;
    protected $params;
    protected $key_param;

    public function sendSms($text, $phone_number)
    {
        $this->params['text'] = $text;

        if(is_string($phone_number))
            $this->params['number'] = $phone_number;
        else if(is_array($phone_number))
            $this->params['number'] = implode(',', $phone_number);

        return $this->request(true);
    }

    public function request($is_array = false)
    {
        try {
            $request = new Client([
                'headers' => [
                    'Accept' => 'application/json'
                ],
                'http_errors' => false,
                'verify' => false
            ]);

            $response = $request->post(config('plasgate.url') . 'authorize', [
                RequestOptions::JSON => [
                    'username' => config('plasgate.username'),
                    'password' => config('plasgate.password'),
                ]
            ]);

            $response = json_decode($response->getBody(), $is_array);

            if(!isset($response['status']))
                return false;

            $response = $request->post(config('plasgate.url') . 'accesstoken', [
                RequestOptions::JSON => [
                    'authorization_code' => $response['data']['authorization_code'],
                ]
            ]);

            $response = json_decode($response->getBody(), $is_array);

            if(!isset($response['status']))
                return false;

            $response = $request->post(config('plasgate.url') . 'send', [
                RequestOptions::JSON => [
                    [
                        'number' => $this->params['number'],
                        'senderID' => config('plasgate.sender_id'),
                        'type' => 'sms',
                        'text' => $this->params['text']
                    ]
                ],
                'headers' => [
                    'X-Access-Token' => $response['data']['access_token']
                ]
            ]);

            $response = json_decode($response->getBody(), $is_array);

            if(!isset($response['status']))
                return false;

            return $response;
        } catch (ConnectException $e) {
            return null;
        }
    }

}
