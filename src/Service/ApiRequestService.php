<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

const API_URL = 'http://ws.audioscrobbler.com/2.0/';

const API_METHOD_USER_GET_RECENT_TRACKS = 'user.getrecenttracks';

const LIMIT_RESPONSE_RECENT_TRACKS = 200;


class ApiRequestService
{

  protected HttpClientInterface $request;

  public function __construct(HttpClientInterface $request)
  {
    $this->request = $request;
  }


  private function getSigningCall(array $p_Parameters, string $apiScret) : string
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
    $parametersString .= $apiScret;

    //Create md5 hash
    $api_sig = md5($parametersString);

    return $api_sig;
  }

  public function getLastTracks(User $user): string
  {
    $responseContent = null;

    $parameters = array();
    $Methode = "GET";
    $Url = API_URL;
    $parameters['api_key'] = $user->getLastFmApiKey();
    $parameters['sk'] = $user->getLastFmApiSessionKey();
    $parameters['user'] = $user->getLastFmUserName();
    $parameters['format'] = 'json';
    $parameters['method'] = API_METHOD_USER_GET_RECENT_TRACKS;
    $parameters['limit'] = LIMIT_RESPONSE_RECENT_TRACKS;

    $parameters['api_sig'] = $this->getSigningCall($parameters, $user->getLasFmApiSecret());

    $reponse = $this->request->request($Methode, $Url, ['query' => $parameters]);
    $statusCode = $reponse->getStatusCode();

    if ($statusCode === 200) {
      $responseContent = $reponse->getContent();
    }

    return $responseContent;
  }

}