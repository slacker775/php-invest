<?php

namespace App\Entity;

use App\Repository\CountryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CountryRepository::class)
 */
class Country
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=2, options={"fixed":true, "comment":"ISO 3166-1 Alpha-2 code"})
     * @Assert\Country
     */
    private $Code;

    public function __construct($code)
    {
        $this->Code = $code;
    }

    public function getCode(): ?string
    {
        return $this->Code;
    }

    public function setCode(string $code): self
    {
        $this->Code = $code;
        return $this;
    }

    public function __toString(): string
    {
        return $this->Code;
    }
}
