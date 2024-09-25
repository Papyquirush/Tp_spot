<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'artists')]
class Artist
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $popularity;

    #[ORM\Column(type: 'string', length: 255)]
    private string $spotifyUrl;

    #[ORM\Column(type: 'string', length: 255)]
    private string $href;

    #[ORM\Column(type: 'string', length: 255)]
    private string $uri;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $imageUrl;

    public function __construct(
        string $id,
        string $name,
        int $popularity,
        string $spotifyUrl,
        string $href,
        string $uri,
        ?string $imageUrl
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->popularity = $popularity;
        $this->spotifyUrl = $spotifyUrl;
        $this->href = $href;
        $this->uri = $uri;
        $this->imageUrl = $imageUrl;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPopularity(): int
    {
        return $this->popularity;
    }

    public function getSpotifyUrl(): string
    {
        return $this->spotifyUrl;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }





}