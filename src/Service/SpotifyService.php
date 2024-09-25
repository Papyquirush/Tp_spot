<?php

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\RequestStack;


class SpotifyService{
    var HttpClientInterface $httpClient;
    var RequestStack $requestStack;
    var string $clientId;
    var string $clientSecret;
    

public function __construct(HttpClientInterface $httpClient, RequestStack $requestStack, string $clientId, string $clientSecret){
$this->httpClient = $httpClient;
$this->requestStack = $requestStack;
$this->clientId = $clientId;
$this->clientSecret = $clientSecret;

} 

    


public function auth(): void{
$newTokenNeeded = false;
$session = $this->requestStack->getSession();
if($session->has('token')||$session->get('expire')<= time()) {
    $newTokenNeeded = true;
}

if($newTokenNeeded){

    $response = $this->httpClient->request("POST","https://accounts.spotify.com/api/token",[
        'headers' =>[
            'Authorization' => 'Basic '. base64_encode($this->clientId. ':' .$this->clientSecret),
            'Content-Type' => 'applicaion/x-www-form-urlencoded',
        ],
        'body' =>[
            'grant_type' => 'client_credentials',
        ],
    ] );

    $data = $response->toArray();

} 

} 


} 