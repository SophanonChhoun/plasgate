<?php


namespace Lyly\Plasgate;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class Plasgate
{
    public $token;
    public $senderId;
    public $base_url;
    public $client;

    public function __construct()
    {
        $this->token = config('plasgate.token');
        $this->senderId = config('plasgate.sender_id');
        $this->base_url = config('plasgate.base_url');
        $this->client = new Client();
    }

    public function send($phone, $text)
    {
        Log::info("SEND SMS TO: $phone");
        try {
            $token = $this->token;
            $senderID = $this->senderId;
            $baseUrl = $this->base_url;
            return $this->client->request("GET", "$baseUrl?token=$token&phone=$phone&senderID=$senderID&text=$text", [
                "headers" => [
                    "Content-Type" => "application/json"
                ]
            ]);
        } catch (\Exception $exception) {
            Log::info("SMS RESPONSE: ", [
                'code' => $exception->getCode(),
                'body' => $exception->getMessage(),
            ]);
            throw new \ErrorException($exception->getMessage());
        }
    }

    public function randomOtpNumber($digit)
    {
        return rand(pow(10, $digit - 1), pow(10, $digit) - 1);
    }

    public function sendArraySms($sends)
    {
        try {
            foreach ($sends as $send)
            {
                $this->send($send['phone'], $send['message']);
            }
        }catch (\Exception $exception) {
            throw new \ErrorException($exception->getMessage());
        }
    }

}
