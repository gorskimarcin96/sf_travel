<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/** @codeCoverageIgnore */
#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue('SEQUENCE')]
    #[ORM\Column]
    #[Groups('weathers')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups('weathers')]
    private string $namePl;

    #[ORM\Column(length: 255)]
    private string $nameEn;

    #[ORM\Column(length: 255)]
    private string $countryCode;

    #[ORM\Column(length: 255)]
    private string $country;

    #[ORM\Column]
    private float $latitude;

    #[ORM\Column]
    private float $longitude;

    /**
     * @var \Doctrine\Common\Collections\Collection<int, \App\Entity\Weather>|\App\Entity\Weather[]
     */
    #[ORM\OneToMany(mappedBy: 'city', targetEntity: Weather::class)]
    private Collection $weather;

    public function __construct()
    {
        $this->weather = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamePl(): string
    {
        return $this->namePl;
    }

    public function setNamePl(string $namePl): static
    {
        $this->namePl = $namePl;

        return $this;
    }

    public function getNameEn(): string
    {
        return $this->nameEn;
    }

    public function setNameEn(string $nameEn): static
    {
        $this->nameEn = $nameEn;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): static
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Collection<int, Weather>
     */
    public function getWeather(): Collection
    {
        return $this->weather;
    }

    public function addWeather(Weather $weather): static
    {
        if (!$this->weather->contains($weather)) {
            $this->weather->add($weather);
            $weather->setCity($this);
        }

        return $this;
    }
}
