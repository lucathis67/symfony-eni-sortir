<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: "string", length: 150)]
    #[Assert\Type("string")]
    #[Assert\NotBlank(message: "veuillez renseigner un nom de ville")]
    #[Assert\Length(max: 150, maxMessage: "maximum 150 caractères")]
    private $nom;

    #[ORM\Column(type: "string", length: 5)]
    #[Assert\Type("string")]
    #[Assert\NotBlank(message: "veuillez renseigner un code postal")]
    #[Assert\Regex("/[0-9]{5}/", message: "Code postal invalide")]
    #[Assert\Length(max: 5, maxMessage: "5 chiffres requis")]
    private $codePostal;

    #[ORM\OneToMany(mappedBy: "ville", targetEntity: Lieu::class)]
    private $lieux;

    /**
     * Ville constructor.
     */
    public function __construct()
    {
        $this->lieux = new ArrayCollection();
    }

    /**
     * @return ?Uuid
     */
    public function getId(): ?Uuid
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return $this
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    /**
     * @param string $codePostal
     * @return $this
     */
    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Lieu[]
     */
    public function getLieux()
    {
        return $this->lieux;
    }

    /**
     * @param Lieu $lieux
     * @return $this
     */
    public function addLieux(Lieu $lieux): self
    {
        if (!$this->lieux->contains($lieux)) {
            $this->lieux[] = $lieux;
            $lieux->setVille($this);
        }

        return $this;
    }

    /**
     * @param Lieu $lieux
     * @return $this
     */
    public function removeLieux(Lieu $lieux): self
    {
        if ($this->lieux->removeElement($lieux)) {
            // set the owning side to null (unless already changed)
            if ($lieux->getVille() === $this) {
                $lieux->setVille(null);
            }
        }

        return $this;
    }
}
