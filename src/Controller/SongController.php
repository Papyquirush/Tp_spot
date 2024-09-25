<?php

namespace App\Controller;

use App\Factory\SongFactory;
use App\Service\SpotifyService;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class SongController extends AbstractController
{

    private string $token;


    public function __construct(private readonly SpotifyService      $SpotifyService,
                                private readonly HttpClientInterface $httpClient,
                                private readonly SongFactory         $songFactory,

    )
    {
        $this->token = $this->SpotifyService->auth();
        
    }

    #[Route('/song', name: 'app_song_index')]
    public function index(Request $request ): Response
    {

        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);

        $songs = [];
         dump($form);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($form->getData());
            $searchQuery = $form->getData()['search'];
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
                'query' => [
                    'q' => $searchQuery,
                    'type' => 'track',
                    'locale' => 'fr-FR',
                ],
            ]);

            $songs = $this->songFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items']);

        }
        dump($songs);


        return $this->render('song/index.html.twig', [
            'songs' => $songs,
            'form' => $form->createView(),
        ]);
        }
    }