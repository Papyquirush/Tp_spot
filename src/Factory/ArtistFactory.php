<?php
// src/Factory/ArtistFactory.php
namespace App\Factory;

use App\Entity\Artist;

class ArtistFactory
{
    public function createMultipleFromSpotifyData(array $data): array
    {
        $artists = [];
        foreach ($data as $artistData) {
            $imageUrl = $artistData['images'][0]['url'] ?? null;

            $artist = new Artist(
                $artistData['id'],
                $artistData['name'],
                $artistData['popularity'],
                $artistData['external_urls']['spotify'],
                $artistData['href'],
                $artistData['uri'],
                $imageUrl
            );

            $artists[] = $artist;
        }
        return $artists;
    }

    public function createSingleFromSpotifyData(array $data): Artist
    {
        $imageUrl = $data['images'][0]['url'] ?? null;

        return new Artist(
            $data['id'],
            $data['name'],
            $data['popularity'],
            $data['external_urls']['spotify'],
            $data['href'],
            $data['uri'],
            $imageUrl
        );
    }


}