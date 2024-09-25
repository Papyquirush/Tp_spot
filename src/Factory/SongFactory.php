<?php
// src/Factory/SongFactory.php
namespace App\Factory;

use App\Entity\Song;

class SongFactory
{
    public function createMultipleFromSpotifyData(array $data)
    {
        $songs = [];
        foreach ($data as $songData) {
            $song = new Song(
                $songData['disc_number'],
                $songData['duration_ms'],
                $songData['explicit'],
                $songData['external_ids']['isrc'],
                $songData['external_urls']['spotify'],
                $songData['href'],
                $songData['id'],
                $songData['is_local'],
                $songData['name'],
                $songData['popularity'],
                $songData['preview_url'],
                $songData['track_number'],
                $songData['type'],
                $songData['uri'],
                $songData['album']['images'][0]['url']

            );
           
            $songs[] = $song;
        }
        return $songs;
    }
}