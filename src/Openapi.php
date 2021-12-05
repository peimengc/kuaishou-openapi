<?php


namespace Peimengc\KuaishouOpenapi;


use GuzzleHttp\Client;

class Openapi
{
    protected $guzzleOptions = [];
    protected $baseUri = 'https://open.kuaishou.com';
    protected $appid;
    protected $secret;
    protected $accessToken;

    public function __construct($appid, $secret)
    {
        $this->appid = $appid;
        $this->secret = $secret;
    }

    public function getHttpClient()
    {
        return new Client();
    }

    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
        return $this;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    //发送请求
    protected function request($method, $uri, array $options = [], $raw = false)
    {
        if ($this->accessToken && strtoupper($method) == 'POST') {
            $options['form_params']['access_token'] = $this->accessToken;
        } elseif ($this->accessToken && strtoupper($method) == 'GET') {
            $options['query']['access_token'] = $this->accessToken;
        }
        $options = array_merge(['base_uri' => $this->baseUri], $this->guzzleOptions, $options);
        $response = $this->getHttpClient()->request($method, $uri, $options);
        if ($raw) return $response;
        return json_decode($response->getBody()->getContents(), true);
    }

    protected function httpPost($uri, $data = [])
    {
        return $this->request('POST', $uri, ['form_params' => $data]);
    }

    protected function httpGet($uri, $data = [])
    {
        return $this->request('GET', $uri, ['query' => $data]);
    }

    public function getAuthUrl($scope, $redirect_uri, $state = '')
    {
        return $this->baseUri . '/oauth2/authorize?' . http_build_query([
                'app_id' => $this->appid,
                'scope' => $scope,
                'response_type' => 'code',
                'ua' => 'pc',
                'redirect_uri' => $redirect_uri,
                'state' => $state,
                'id' => '123456'
            ]);
    }

    public function getAccessToken($code)
    {
        $res = $this->httpGet('/oauth2/access_token', [
            'app_id' => $this->appid,
            'app_secret' => $this->secret,
            'code' => $code,
            'grant_type' => 'authorization_code',
        ]);
        if ($res['result'] == 1) {
            $this->setAccessToken($res['access_token']);
        }
        return $res;
    }

    public function refreshToken($refresh_token)
    {
        $res = $this->httpGet('/oauth2/refresh_token', [
            'app_id' => $this->appid,
            'app_secret' => $this->secret,
            'refresh_token' => $refresh_token,
            'grant_type' => 'refresh_token',
        ]);
        if ($res['result'] == 1) {
            $this->setAccessToken($res['access_token']);
        }
        return $res;
    }

    public function userInfo()
    {
        return $this->httpGet('openapi/user_info', [
            'app_id' => $this->appid,
        ]);
    }

    public function fansTopLiveOrderClose($ksOrderId)
    {
        return $this->httpPost('/openapi/fanstop/live/order/close', [
            'app_id' => $this->appid,
            'ksOrderId' => $ksOrderId
        ]);
    }

    public function fansTopLiveOrderInfo(array $ksOrderIds)
    {
        return $this->httpGet('/openapi/fanstop/live/order/info', [
            'app_id' => $this->appid,
            'ksOrderIds' => json_encode($ksOrderIds)
        ]);
    }

    public function fansTopLiveOrderList($liveStreamId, $startLiveTime, $endLiveTime, $start, $limit)
    {
        return $this->httpGet('openapi/fanstop/live/order/list', [
            'app_id' => $this->appid,
            'liveStreamId' => $liveStreamId,
            'startLiveTime' => $startLiveTime,
            'endLiveTime' => $endLiveTime,
            'start' => $start,
            'limit' => $limit,
        ]);
    }

    public function fansTopLiveBalanceAccount()
    {
        return $this->httpGet('openapi/fanstop/live/balanceAccount', [
            'app_id' => $this->appid,

        ]);
    }
}