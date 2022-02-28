<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: SortieRepository::class)]
class Sortie
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: "string", length: 50)]
    #[Assert\Type("string")]
    #[Assert\NotBlank(message: "Veuillez renseigner un nom de sortie")]
    #[Assert\Length(max: 50, maxMessage: "Maximum 50 caractères")]
    private $nom;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(type: "\Datetime", message: "La valeur n'est pas valide")]
    #[Assert\NotBlank(message: "Veuillez renseigner la date de la sortie")]
    #[Assert\GreaterThan(value: "today", message: "La date renseignée est passée")]
    private $dateHeureDebut;

    #[ORM\Column(type: "integer")]
    #[Assert\Type(type: "integer", message: "La valeur n'est pas valide")]
    private $duree;

    #[ORM\Column(type: "datetime")]
    #[Assert\Type(type: "\Datetime", message: "La valeur n'est pas valide")]
    #[Assert\NotBlank(message: "Veuillez renseigner la date limite d'inscription")]
    #[Assert\GreaterThan(value: "today", message: "La date renseignée est passée")]
    #[Assert\LessThan(propertyPath: "dateHeureDebut", message: "Veuillez renseigner une date antérieure à celle de la sortie")]
    private $dateLimiteInscription;

    #[ORM\Column(type: "integer")]
    #[Assert\Type(type: "integer", message: "La valeur n'est pas valide")]
    private $nbInscriptionsMax;

    #[ORM\Column(type: "string", length: 150, nullable: true)]
    private $infosSortie;

    #[ORM\ManyToMany(targetEntity: Participant::class, mappedBy: "sorties")]
    private $participants;

    #[ORM\ManyToOne(targetEntity: Participant::class, inversedBy: "sortiesOrganisees")]
    #[ORM\JoinColumn(nullable: false)]
    private $organisateur;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: "sorties")]
    #[ORM\JoinColumn(nullable: false)]
    private $campus;

    #[ORM\ManyToOne(targetEntity: Etat::class, inversedBy: "sorties")]
    #[ORM\JoinColumn(nullable: false)]
    private $etat;

    #[ORM\ManyToOne(targetEntity: Lieu::class, inversedBy: "sorties")]
    #[ORM\JoinColumn(nullable: false)]
    private $lieu;

    /**
     * Sortie constructor.
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
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
     * @return \DateTime|null
     */
    public function getDateHeureDebut(): ?\DateTime
    {
        return $this->dateHeureDebut;
    }

    /**
     * @param \DateTime $dateHeureDebut
     * @return $this
     */
    public function setDateHeureDebut(\DateTime $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getDuree(): ?int
    {
        return $this->duree;
    }

    /**
     * @param int $duree
     * @return $this
     */
    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    /**
     * @param \DateTimeInterface $dateLimiteInscription
     * @return $this
     */
    public function setDateLimiteInscription(\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    /**
     * @param int $nbInscriptionsMax
     * @return $this
     */
    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    /**
     * @param string|null $infosSortie
     * @return $this
     */
    public function setInfosSortie(?string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    /**
     * @return ArrayCollection
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
            $participant->addSortie($this);
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
            $participant->removeSortie($this);
        }

        return $this;
    }

    /**
     * @return Participant|null
     */
    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    /**
     * @param Participant|null $organisateur
     * @return $this
     */
    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     * @return $this
     */
    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }

    /**
     * @return Etat|null
     */
    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    /**
     * @param Etat|null $etat
     * @return $this
     */
    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Lieu|null
     */
    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    /**
     * @param Lieu|null $lieu
     * @return $this
     */
    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

}
