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
                //$songData['isrc'],
                //$songData['spotify_url'],
                $songData['href'],
                $songData['id'],
                $songData['is_local'],
                $songData['name'],
                $songData['popularity'],
                $songData['preview_url'],
                $songData['track_number'],
                $songData['type'],
                $songData['uri'],
                //$songData['picture_link']
            );
           
            $songs[] = $song;
        }
        return $songs;
    }
}