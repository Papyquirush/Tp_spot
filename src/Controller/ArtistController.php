<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\User;
use App\Factory\ArtistFactory;
use App\Service\SpotifyService;
use App\Form\SearchType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ArtistController extends AbstractController
{

    private string $token;


    public function __construct(private readonly SpotifyService      $SpotifyService,
                                private readonly HttpClientInterface $httpClient,
                                private readonly ArtistFactory       $artistFactory,
                                private readonly EntityManagerInterface $entityManager,

    )
    {
        $this->token = $this->SpotifyService->auth();

    }


    #[Route('/artist', name: 'app_artist_index')]
    public function index(Request $request): Response
    {

        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);

        $artists = [];

        if ($form->isSubmitted() && $form->isValid()) {

            $searchQuery = $form->getData()['search'];
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/search', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
                'query' => [
                    'q' => $searchQuery,
                    'type' => 'artist',
                    'locale' => 'fr-FR',
                ],
            ]);

            $artists = $this->artistFactory->createMultipleFromSpotifyData($response->toArray()['artists']['items']);

        }


        return $this->render('artist/index.html.twig', [
            'artists' => $artists,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/artist/{id}', name: 'app_artist_show')]
    public function show(string $id): Response
    {
        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
        ]);

        $artist = $this->artistFactory->createSingleFromSpotifyData($response->toArray());



        $recommandations = [];

        $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/recommendations', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token,
            ],
            'query' => [
                'seed_artists' => $id,
            ],
        ]);


        $recommandations = $this->artistFactory->createMultipleFromSpotifyData($response->toArray()['tracks']);

        dump($response->toArray()['tracks'][0]['album']['images'][0]['url']);

        $imagesUrls = [];
        foreach ($response->toArray()['tracks'] as $track) {
            $imagesUrls[] = $track['album']['images'][0]['url'];
        }


        return $this->render('artist/show.html.twig', [
            'artist' => $artist,
            'recommandations' => $recommandations,
            'imagesUrls' => $imagesUrls,
        ]);
    }

    #[Route('/artist/add/{id}', name: 'app_artist_add')]
    public function add(string $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to add a artist.');
        }

        $artist = $this->entityManager->getRepository(Artist::class)->find($id);
        if (!$artist) {
            $response = $this->httpClient->request('GET', 'https://api.spotify.com/v1/artists/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                ],
            ]);

            $artistData = $response->toArray();
            $artist = $this->artistFactory->createSingleFromSpotifyData($artistData);

            $this->entityManager->persist($artist);
            $this->entityManager->flush();
        }

        $user->addArtist($artist);
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $this->redirectToRoute('app_favorite_user', ['id' => $user->getId()]);
    }


    #[Route('/artist/remove/{id}', name: 'app_artist_remove')]
    public function remove(string $id): Response
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('You must be logged in to remove a artist.');
        }

        $artist = $this->entityManager->getRepository(Artist::class)->find($id);
        if (!$artist) {
            throw $this->createNotFoundException('Artist not found.');
        }

        if ($user->getArtists()->contains($artist)) {
            $user->removeSong($artist);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
        }

        return $this->redirectToRoute('app_favorite_user', ['id' => $user->getId()]);
    }


}






