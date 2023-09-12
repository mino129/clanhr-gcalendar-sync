<?php

namespace App\Libs;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use League\CommonMark\Extension\CommonMark\Node\Inline\Strong;

class ClanHR
{

    private string $apiUrl;
    private string $apiKey;

    private int $perPage = 9999;
    //this are literally so made up omg they use this in their own requests
    private string $startDate = '2015-01-01';
    private string $endDate = '2999-12-31';

    public function __construct()
    {
        $this->apiUrl = env('CLANHR_API_URL');
        $this->apiKey = env('CLANHR_API_TOKEN');
    }

    public function getVacationData() : array|bool{
        $vacationDataRequest = Http::withHeaders([
            'Accept' => '*/*',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Accept-Language' => 'en-GB,en-US;q=0.9,en;q=0.8',
            'Connection' => 'keep-alive',
            'Content-Type' => 'application/json',
            'Host' => 'app.clanhr.com',
            'Referer' => 'https://app.clanhr.com/days-off',
            'Sec-Fetch-Dest' => 'empty',
            'Sec-Fetch-Mode' => 'cors',
            'Sec-Fetch-Site' => 'same-origin',
            //faking the user agent so they dont suspect we are a bot
            'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36',
            'X-Requested-With' => 'XMLHttpRequest',
            'sec-ch-ua' => '"Chromium";v="116", "Not)A;Brand";v="24", "Google Chrome";v="116"',
            'sec-ch-ua-mobile' => '?0',
            'sec-ch-ua-platform' => 'macOS',
            'x-clanhr-auth-token' => env('CLANHR_API_TOKEN'),
        ])->get($this->apiUrl, [
            'per-page' => $this->perPage,
            'start-date' => $this->startDate,
            'end-date' => $this->endDate,
            't' => time(),
        ]);
        return (!$vacationDataRequest->failed()) ? $vacationDataRequest->json() : false;
    }

    public function getVacationDataDummy():array{
        $json = Storage::disk('local')->get('vacations.json');
        return json_decode($json, true);
    }

}
