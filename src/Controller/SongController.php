<?php

namespace App\Controller;

use App\Entity\User;
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

        }else {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search?query=kazzey&type=track&locale=fr-FR', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $songs = $this->songFactory->createMultipleFromSpotifyData($response->toArray()['tracks']['items']);
         }


        $user = $this->getUser();
        $favoriteSongs = [];
        if ($user instanceof User) {
            foreach ($songs as $song) {
                if ($this->isFavorite($user, $song)) {
                    $favoriteSongs[] = $song->getId();
                }
            }
        }

        return $this->render('song/index.html.twig', [
            'songs' => $songs,
            'form' => $form->createView(),
            'favoriteSongs' => $favoriteSongs,
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
        $favoriteSongs = [];



        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/recommendations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'query' => [
                'seed_tracks' => $id,
            ],
        ]);

        $recommandations = $this->songFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);

        $user = $this->getUser();
        $favoriteSongs = [];
        if ($user instanceof User) {

            if ($this->isFavorite($user, $song)) {
                $favoriteSongs[] = $song->getId();
            }

        }

        return $this->render('song/show.html.twig', [
            'song' => $song,
            'recommandations' => $recommandations,
            'favoriteSongs' => $favoriteSongs,
        ]);
    }


    #[Route('/song/add/{id}', name: 'app_song_add')]
    public function add(string $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to add a song.');
        }

        $song = $this->entityManager->getRepository(Song::class)->find($id);
        if (!$song) {
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/tracks/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);

            $songData = $response->toArray();
            $song = $this->songFactory->createSingleFromSpotifyData($songData);

            $this->entityManager->persist($song);
            $this->entityManager->flush();
        }

        $user->addSong($song);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_favorite_user', ['id' => $user->getId()]);
    }

    #[Route('/song/remove/{id}', name: 'app_song_remove')]
    public function remove(string $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to remove a song.');
        }

        $song = $this->entityManager->getRepository(Song::class)->find($id);
        if (!$song) {
            throw $this->createNotFoundException('Song not found.');
        }

        if ($user->getSongs()->contains($song)) {
            $user->removeSong($song);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_favorite_user', ['id' => $user->getId()]);
    }

    public function isFavorite(User $user, Song $song): bool
    {
        foreach ($user->getSongs() as $favoriteSong) {
            if ($favoriteSong->getId() === $song->getId()) {
                return true;
            }
        }
        return false;
    }
}


