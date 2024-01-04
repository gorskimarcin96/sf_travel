<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\WeatherRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(operations: [new GetCollection()], normalizationContext: ['groups' => ['weathers']])]
#[ApiFilter(SearchFilter::class, properties: ['search' => 'exact', 'source' => 'exact'])]
#[ORM\Entity(repositoryClass: WeatherRepository::class)]
class Weather implements SourceInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('weathers')]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'weather')]
    #[Groups('weathers')]
    private City $city;

    #[ORM\Column(type: \Doctrine\DBAL\Types\Types::DATE_MUTABLE)]
    #[Groups('weathers')]
    private \DateTimeInterface $date;

    #[ORM\Column]
    #[Groups('weathers')]
    private float $temperature2mMean;

    #[ORM\Column]
    #[Groups('weathers')]
    private float $precipitationHours;

    #[ORM\Column()]
    #[Groups('weathers')]
    private float $precipitationSum;

    #[ORM\Column(length: 255)]
    #[Groups('weathers')]
    private string $source;

    #[ORM\ManyToOne(inversedBy: 'weathers')]
    private ?Search $search = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getCity(): City
    {
        return $this->city;
    }

    public function setCity(City $city): static
    {
        $this->city = $city;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getTemperature2mMean(): float
    {
        return $this->temperature2mMean;
    }

    public function setTemperature2mMean(float $temperature2mMean): static
    {
        $this->temperature2mMean = $temperature2mMean;

        return $this;
    }

    public function getPrecipitationHours(): float
    {
        return $this->precipitationHours;
    }

    public function setPrecipitationHours(float $precipitationHours): static
    {
        $this->precipitationHours = $precipitationHours;

        return $this;
    }

    public function getPrecipitationSum(): float
    {
        return $this->precipitationSum;
    }

    public function setPrecipitationSum(float $precipitationSum): static
    {
        $this->precipitationSum = $precipitationSum;

        return $this;
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setSource(string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getSearch(): ?Search
    {
        return $this->search;
    }

    public function setSearch(?Search $search): static
    {
        $this->search = $search;

        return $this;
    }
}
