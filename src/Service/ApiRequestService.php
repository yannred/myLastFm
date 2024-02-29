<?php

namespace App\Service;

use App\Entity\Artist;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

const API_URL = 'http://ws.audioscrobbler.com/2.0/';

const API_METHOD_USER_GET_RECENT_TRACKS = 'user.getrecenttracks';
const API_METHOD_USER_GET_INFO = 'user.getinfo';
const API_METHOD_USER_GET_ARTIST_INFO = 'artist.getinfo';

const LIMIT_RESPONSE_RECENT_TRACKS = 200;


/**
 * Call the LastFm API
 * The user must be set before calling the API
 * @package App\Service
 */
class ApiRequestService
{

  protected HttpClientInterface $request;
  protected LoggerInterface $logger;
  protected ?User $user;

  public string $apiKey = "";
  public string $apiSessionKey = "";
  public string $apiSecret = "";
  public string $apiUser = "";

  public function __construct(HttpClientInterface $request, LoggerInterface $logger, Security $security)
  {
    $this->request = $request;
    $this->logger = $logger;
    $this->user = null;

    $this->apiKey = $_ENV['LASTFM_API_KEY'];
    $this->apiSessionKey = $_ENV['LASTFM_API_SESSION_KEY'];
    $this->apiSecret = $_ENV['LASTFM_API_SECRET'];
    $this->apiUser = $_ENV['LASTFM_USERNAME'];
  }


  /**
   * Return the md5 hash of the parameters
   * @param array $parameters
   * @param string $apiScret
   * @return string
   */
  private function getSigningCall(array $parameters, string $apiScret) : string
  {
    //Sort the parameters alphabetically
    ksort($parameters);

    //Concatenate all parameters into one string
    $parametersString = '';
    foreach ($parameters as $key => $value) {
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

  /**
   * Get the last tracks of the user
   * @param int|null $from
   * @param int|null $to
   * @param int|null $page
   * @return string
   */
  public function getLastTracks(int $from = null, int $to = null, int $page = null): string
  {
    $responseContent = null;

    $parameters = array();
    $Methode = "GET";
    $Url = API_URL;
    $parameters['api_key'] = $this->apiKey;
    $parameters['sk'] = $this->apiSessionKey;
    $parameters['user'] = $this->getUser()->getLastFmUserName();
    $parameters['format'] = 'json';
    if ($from) {
      $parameters['from'] = $from;
    }
    if ($to) {
      $parameters['to'] = $to;
    }
    if ($page) {
      $parameters['page'] = $page;
    }
    $parameters['method'] = API_METHOD_USER_GET_RECENT_TRACKS;
    $parameters['limit'] = LIMIT_RESPONSE_RECENT_TRACKS;

    $parameters['api_sig'] = $this->getSigningCall($parameters, $this->getUser()->getLasFmApiSecret());

    $this->logger->info('*** Scrobble Import Info : API call parameters : ' . print_r($parameters, true));

    $response = $this->request->request($Methode, $Url, ['query' => $parameters]);
    $statusCode = $response->getStatusCode();

    if ($statusCode === 200) {
      $responseContent = $response->getContent();
    }

    return $responseContent;
  }


  /**
   * Get the user info
   * @return string
   */
  public function getLastFmUserInfo(): string
  {
    $responseContent = null;

    $parameters = array();
    $Methode = "GET";
    $Url = API_URL;
    $parameters['user'] = $this->getUser()->getLastFmUserName();
    $parameters['api_key'] = $this->apiKey;
    $parameters['format'] = 'json';
    $parameters['method'] = API_METHOD_USER_GET_INFO;

    $response = $this->request->request($Methode, $Url, ['query' => $parameters]);
    $statusCode = $response->getStatusCode();

    if ($statusCode === 200) {
      $responseContent = $response->getContent();
    }

    return $responseContent;
  }


  /**
   * Get the artist info
   * @param Artist $artist
   * @return string
   */
  public function getArtistInfo(Artist $artist): string
  {
    $responseContent = null;
    $parameters = array();
    $Methode = "GET";
    $Url = API_URL;
    $parameters['method'] = API_METHOD_USER_GET_ARTIST_INFO;

    if (!$artist->getMbid() && !$artist->getName()) {
      throw new \LogicException("Error in ApiRequestService::getArtistInfo() : mbid or artistName must be set");
    }

    if ($artist->getMbid()) {
      $parameters['mbid'] = $artist->getMbid();
    } else {
      $parameters['artist'] = $artist->getName();
    }

    $parameters['username'] = $this->getUser()->getLastFmUserName();
    $parameters['api_key'] = $this->apiKey;
    $parameters['format'] = 'json';

//    $parameters['lang'] = 'FR';
//    $parameters['autocorrect'] = "1";

    $response = $this->request->request($Methode, $Url, ['query' => $parameters]);
    $statusCode = $response->getStatusCode();

    if ($statusCode === 200) {
      $responseContent = $response->getContent();
    }

    return $responseContent;
  }




  /** ******************************************************* */
  /** ********************* GETTER/SETTER ******************* */
  /** ******************************************************* */

  public function setUser(?User $user): void
  {
    $this->user = $user;
  }

  public function getUser(): User
  {
    return $this->user;
  }

}