<?php

namespace App\Controller;

use App\Factory\SongFactory;
use App\Service\SpotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SongController extends AbstractController
{

    private string $token;


    public function __construct(private readonly SpotifyService  $SpotifyService,
                                private readonly HttpClientInterface $httpClient,
                                private readonly SongFactory $songFactory,
    )
    {
        $this->token = $this->SpotifyService->auth();
        
    }

    #[Route('/song', name: 'app_song_index')]
    public function index(): Response
    {
        // Make the GET request to the Spotify API with kazzey as the query and the token as the Authorization header
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search?query=kazzey&type=track&locale=fr-FR', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);
        
        // Examples of how you could do this
        //        $tracks = $this->trackFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items']);

        $songs = $this->songFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items']);

        return $this->render('song/index.html.twig', [
            'songs' => $songs,
        ]);
        }
    }