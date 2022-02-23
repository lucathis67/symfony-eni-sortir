<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;



#[ORM\Entity(repositoryClass: CampusRepository::class)]
class Campus
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: "string")]
    #[Assert\Type("string")]
    #[Assert\NotBlank(message: "veuillez indiquer un nom de campus")]
    #[Assert\Length(max: 50, maxMessage: "maximum 50 caractères")]
    private $nom;

    #[ORM\OneToMany(mappedBy: "campus", targetEntity: Participant::class)]
    private $participants;

    #[ORM\OneToMany(mappedBy: "campus", targetEntity: Sortie::class)]
    private $sorties;

    /**
     * Campus constructor.
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
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
     * @return Participant[]
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param Participant $participant
     * @return $this
     */
    public function addParticipant(Participant $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
            $participant->setCampus($this);
        }

        return $this;
    }

    /**
     * @param Participant $participant
     * @return $this
     */
    public function removeParticipant(Participant $participant): self
    {
        if ($this->participants->removeElement($participant)) {
            // set the owning side to null (unless already changed)
            if ($participant->getCampus() === $this) {
                $participant->setCampus(null);
            }
        }

        return $this;
    }

    /**
     * @return Sortie[]
     */
    public function getSorties(): Sortie
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
            $sortie->setCampus($this);
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
            if ($sortie->getCampus() === $this) {
                $sortie->setCampus(null);
            }
        }

        return $this;
    }
}
