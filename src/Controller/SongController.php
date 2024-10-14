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
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Song;


class SongController extends AbstractController
{

    private string $token;


    public function __construct(private readonly SpotifyService         $SpotifyService,
                                private readonly HttpClientInterface    $httpClient,
                                private readonly SongFactory            $songFactory,
                                private readonly EntityManagerInterface $entityManager,


    )
    {
        $this->token = $this->SpotifyService->auth();

    }

    #[Route('/song', name: 'app_song_index')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);

        $songs = [];

        if ($form->isSubmitted() && $form->isValid()) {

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


        return $this->render('song/index.html.twig', [
            'songs' => $songs,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/song/{id}', name: 'app_song_show')]
    public function show(string $id): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $song = $this->songFactory->createSingleFromSpotifyData($response->toArray());


        $recommandations = [];

        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/recommendations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'query' => [
                'seed_tracks' => $id,
            ],
        ]);

        $recommandations = $this->songFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);


        return $this->render('song/show.html.twig', [
            'song' => $song,
            'recommandations' => $recommandations,
        ]);
    }


    #[Route('/song/add/{id}', name: 'app_song_add')]
    public function add(string $id): Response
    {
        $existingSong = $this->entityManager->getRepository(Song::class)->find($id);
        if($existingSong){
            $this->entityManager->remove($existingSong);
            $this->entityManager->flush();
        }


        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $songData = $response->toArray();
        $song = $this->songFactory->createSingleFromSpotifyData($songData);


        $this->entityManager->persist($song);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_song_show', ['id' => $song->getId()]);


    }
}


