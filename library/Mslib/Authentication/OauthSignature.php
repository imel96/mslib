<?php

namespace Mslib\Authentication;

use Zend\Http\Request;
use Zend\Crypt\Hmac;
use Zend\Math\Rand;
use Zend\Debug\Debug;

class OauthSignature
{
    protected $signingKey;
    protected $hashAlgo;
    protected $consumerKey;
    protected $accessToken;

    public function __construct($consumerSecret, $tokenSecret = null,
        $hashAlgo = null)
    {
        $this->signingKey = rawurlencode($consumerSecret) . '&'
            . rawurlencode($tokenSecret);
        $this->hashAlgo = $hashAlgo;
    }

    public function sign(Request $request)
    {
        $params = $this->collectParameters($request);
        $uri = $request->getUri();
        $host = $uri->getHost();
        $port = $uri->getPort();

        if ($host == "0.0.0.0") {
            $host = $request->getHeader('Host')->getFieldValue();
            $port = '';
        }
        $url = $uri->getScheme() . "://$host";
        if ($port)
            $url .= ":$port";
        $url .= $uri->getPath();
        $base = $this->getSignatureBaseString($request->getMethod(), $url,
            $params);
        $params['oauth_signature'] =
            base64_encode(Hmac::compute($this->signingKey,
                $this->hashAlgo, $base, Hmac::OUTPUT_BINARY));
        return $this->getHeaderString($params);
    }

    public function setConsumerKey($key)
    {
        $this->consumerKey = $key;
        return $this;
    }

    public function setAccessToken($token)
    {
        $this->accessToken = $token;
        return $this;
    }

    // mimics oauth_get_sbs()
    protected function getSignatureBaseString($httpMethod, $uri, $params)
    {
        ksort($params);
        $ret = array();
    $ret[] = $uri;
        foreach ($params as $key => $val)
            $ret[] = "$key=$val";
        return "$httpMethod&" . rawurlencode(implode('&', $ret));
    }

    protected function collectParameters(Request $request)
    {
        $ret = array_merge($request->getQuery()->getArrayCopy(),
            $request->getUri()->getQueryAsArray());
        if ($request->getMethod() == Request::METHOD_POST)
            $ret = array_merge($ret,
                $request->getPost()->getArrayCopy());
        $oauth = self::getOauthParameterFromHeader($request);
        $ret = array_merge($ret, $oauth, array(
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_version' => '1.0'));
        if (!array_key_exists('oauth_timestamp', $ret))
            $ret['oauth_timestamp'] = time();
        if (!array_key_exists('oauth_nonce', $ret))
            $ret['oauth_nonce'] = Rand::getString(32);
        if (!array_key_exists('oauth_consumer_key', $ret))
            $ret['oauth_consumer_key'] = $this->consumerKey;
        if (!array_key_exists('oauth_token', $ret))
            $ret['oauth_token'] = $this->accessToken;
        $params = array();
        foreach ($ret as $key => $val)
            $params[rawurlencode($key)] = rawurlencode($val);
        return $params;
    }

    protected function getHeaderString($params)
    {
        ksort($params);
        $ret = array();
        foreach ($params as $key => $val)
                $ret[] = "$key=\"$val\"";
        return "OAuth " . implode(', ', $ret);
    }

    public static function getOauthParameterFromHeader($request)
    {
        $oauth = $request->getHeader('Authorization');
        if (!$oauth)
            return array();
        $oauth = $oauth->getFieldValue();
        $oauth = strstr($oauth, 'oauth_');
        $oauth = explode(',', $oauth);
        $ret = array();

        foreach ($oauth as $param) {
            parse_str($param, $tmp);
            $key = key($tmp);
            if (stripos($key, 'oauth_') !== false
                && $key != 'oauth_signature')
                $ret[$key] = trim(current($tmp), '"');
        }
        return $ret;
    }
}
