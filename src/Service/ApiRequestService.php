<?php

namespace App\Service;

use App\Util\CCurlRequest;
use Symfony\Contracts\HttpClient\HttpClientInterface;

const API_URL = 'http://ws.audioscrobbler.com/2.0/';

const API_METHOD_USER_GET_RECENT_TRACKS = 'user.getrecenttracks';


class ApiRequestService
{

  protected $request;

  public function __construct(HttpClientInterface $request)
  {
    define('API_KEY', $_ENV['LASTFM_API_KEY']);
    define('API_SESSION_KEY', $_ENV['LASTFM_API_SESSION_KEY']);
    define('API_SECRET', $_ENV['LASTFM_API_SECRET']);
    define('API_USER', $_ENV['LASTFM_USERNAME']);

    $this->request = $request;
  }



  public function helloWorld(): string
  {
    $curl = new CCurlRequest();
    $tabReponseHttp = $curl->exec( "http://perdu.com");
    return $tabReponseHttp['response'];
  }

  private function getSigningCall(array $p_Parameters) : string
  {
    //Sort the parameters alphabetically
    ksort($p_Parameters);

    //Concatenate all parameters into one string
    $parametersString = '';
    foreach ($p_Parameters as $key => $value) {
      if ($key != 'format' && $key != 'callback' && $key != 'api_sig') {
        $parametersString .= $key . $value;
      }
    }

    //Append the secret to the string
    $parametersString .= API_SECRET;

    //Create md5 hash
    $api_sig = md5($parametersString);

    return $api_sig;
  }

  public function getLastTracks(): string
  {
    $responseContent = null;

    $parameters = array();
    $Methode = CCurlRequest::METHOD_GET;
    $Url = API_URL;
    $parameters['api_key'] = API_KEY;
    $parameters['format'] = 'json';
    $parameters['method'] = API_METHOD_USER_GET_RECENT_TRACKS;
    $parameters['sk'] = API_SESSION_KEY;
    $parameters['user'] = API_USER;
    $parameters['limit'] = 200;
//    $parameters['page'] = 1;

    $parameters['api_sig'] = $this->getSigningCall($parameters);

    $reponse = $this->request->request($Methode, $Url, ['query' => $parameters]);
    $statusCode = $reponse->getStatusCode();

    if ($statusCode === 200) {
      $responseContent = $reponse->getContent();
    }

    return $responseContent;
  }

}