<?php

namespace App\Controller;

use SpotifyService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Service\SongFactory;



class SongController extends AbstractController
{
private SessionInterface $session;

    public function __construct(private readonly SpotifyService $spotifyService, 
                                private readonly HttpClientInterface $httpClient,
                                private readonly RequestStack $requestStack,
                                private readonly SongFactory $songFactory
    )
    {
        $this->spotifyService->auth();
        $this->session= $this->requestStack->getSession();
    }
    

    #[Route('/song', name: 'app_song')]
    public function index(): Response
    {
        
        $response = $this->httpClient->request('GET','https://api.spotify.com/vl/search?querry=papyquirush&type=track&locale=fr-FR',[
            'headers' =>[
                'Authorization' => 'Bearer ' .$this->session->get('token'),
            ],
        ] );

        $songs = $this->songFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items'] );


        return $this->render('song/index.html.twig', [
            'songs' => $songs,
        ]);
    }



    

    





}
