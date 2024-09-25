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


    public function createSingleFromSpotifyData(array $data)
    {
        return new Song(
            $data['disc_number'],
            $data['duration_ms'],
            $data['explicit'],
            $data['external_ids']['isrc'],
            $data['external_urls']['spotify'],
            $data['href'],
            $data['id'],
            $data['is_local'],
            $data['name'],
            $data['popularity'],
            $data['preview_url'],
            $data['track_number'],
            $data['type'],
            $data['uri'],
            $data['album']['images'][0]['url']
        );
    }


}