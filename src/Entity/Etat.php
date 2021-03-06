<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator( class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\Type("string")]
    #[Assert\NotBlank(message: "veuillez indiquer l'état")]
    #[Assert\Length(max: 50, maxMessage: "l'intitulé de l'état est trop long")]
    private $libelle;

    #[ORM\OneToMany(mappedBy: "etat", targetEntity: Sortie::class)]
    private $sorties;

    /**
     * Etat constructor.
     */
    public function __construct()
    {
        $this->sorties = new ArrayCollection();
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
    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     * @return $this
     */
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Sortie[]
     */
    public function getSorties()
    {
        return $this->sorties;
    }

    /**
     * @param Sortie $sortie
     * @return $this
     */
    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
            $sortie->setEtat($this);
        }

        return $this;
    }

    /**
     * @param Sortie $sortie
     * @return $this
     */
    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sorties->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getEtat() === $this) {
                $sortie->setEtat(null);
            }
        }

        return $this;
    }
}
