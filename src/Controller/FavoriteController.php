<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;


class FavoriteController extends AbstractController
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }
    #[Route('/favorite/user/{id}', name: 'app_favorite_user')]
    public function index($id): Response
    {
        $user = $this->entityManager->getRepository(User::class)->find($id);
        $songs = $user->getSongs();
        $artists = $user->getArtists();
        return $this->render('favorite/index.html.twig', [
            'songs' => $songs,
            'artists' => $artists,
        ]);
    }



}
