<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=150)
     *
     * @Assert\Type("string")
     * @Assert\NotBlank(message = "Veuillez indiquer le nom de la lieu.")
     * @Assert\Length(
     *          max = 150
     *          maxMessage = "Le nom du lieu est trop long (150 max)."
     *      )
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     *
     * @Assert\Type("string")
     * @Assert\NotBlank(message = "Veuillez indiquer la rue.")
     * @Assert\Length(
     *          max = 255
     *          maxMessage = "Le nom du lieu est trop long (255 max)."
     *      )
     */
    private $rue;

    /**
     * @var float
     *
     * @ORM\Column(type = "float")
     *
     * @Assert\Type(type = "float", message = "La valeur n'est pas valide")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(type = "float")
     *
     * @Assert\Type(type = "float", message = "La valeur n'est pas valide")
     */
    private $longitude;

    /**
     * @var Sortie[]
     *
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="lieu")
     */
    private $sorties;

    /**
     * @var Ville
     *
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="lieux")
     * @ORM\JoinColumn(nullable: false)
     */
    private $ville;

    /**
     * Lieu constructor.
     */
    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
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
    public function getRue(): ?string
    {
        return $this->rue;
    }

    /**
     * @param string $rue
     * @return $this
     */
    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     * @return $this
     */
    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     * @return $this
     */
    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

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
            $sortie->setLieu($this);
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
            if ($sortie->getLieu() === $this) {
                $sortie->setLieu(null);
            }
        }

        return $this;
    }

    /**
     * @return Ville|null
     */
    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    /**
     * @param Ville|null $ville
     * @return $this
     */
    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }
}
