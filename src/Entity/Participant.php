<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ParticipantRepository::class)]
class Participant implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Assert\NotBlank(message: "L'email doit être renseigné.")]
    #[Assert\Email(message:"L'email {{ value }} n'est pas un email valide.")]
    #[Assert\Length(max : 180,
        maxMessage: "L'email ne peut excéder 180 caractères")]
    #[ORM\Column(type: 'string', length: 180, unique: true)]
    private $email;

    #[Assert\NotBlank(message: "Le mot de passe doit être renseigné.")]
    //TODO : CF Philippe
   // #[Assert\Type(type: ['alnum'])]
    #[Assert\Length(min : 8,
        minMessage: "Le mot de passe doit comporté au minimum 8 caractères alphanumériques")]
    #[ORM\Column(type: 'string')]
    private $password;

    #[Assert\NotBlank(message: "Le nom doit être renseigné.")]
    #[Assert\Length(min: 2, max: 50,
        minMessage: "Le nom doit avoir au moins 2 caractères",
        maxMessage: "Le nom ne peut excéder 50 caractères")]
    #[ORM\Column(type: 'string', length: 50)]
    private $nom;

    #[Assert\NotBlank(message: "Le prénom doit être renseigné.")]
    #[Assert\Length(min: 2, max: 50,
        minMessage: "Le prénom doit avoir au moins 2 caractères",
        maxMessage: "Le prénom ne peut excéder 50 caractères")]
    #[ORM\Column(type: 'string', length: 50)]
    private $prenom;

    #[Assert\NotBlank(message: "Le téléphone doit être renseigné.")]
    #[Assert\Length(max: 10,
        maxMessage: "Le téléphone ne peut excéder 10 caractères")]
    #[ORM\Column(type: 'string', length: 10)]
    private $telephone;

    #[ORM\Column(type: 'boolean')]
    private $administrateur;

    #[ORM\Column(type: 'boolean')]
    private $actif;

    #[Assert\NotBlank(message: "Le pseudo doit être renseigné.")]
    #[Assert\Length(max: 50,
        maxMessage: "Le pseudo ne peut excéder 50 caractères")]
    #[ORM\Column(type: 'string', length: 50, unique: true)]
    private $pseudo;

    #[ORM\ManyToMany(targetEntity: Sortie::class, inversedBy: 'participants')]
    private $sorties;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class)]
    private $sortiesOrganisees;

    #[ORM\ManyToOne(targetEntity: Campus::class, inversedBy: 'participants')]
    #[ORM\JoinColumn(nullable: false)]
    private $campus;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->sortiesOrganisees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        $this->sorties->removeElement($sortie);

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortiesOrganisees(): Collection
    {
        return $this->sortiesOrganisees;
    }

    public function addSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if (!$this->sortiesOrganisees->contains($sortiesOrganisee)) {
            $this->sortiesOrganisees[] = $sortiesOrganisee;
            $sortiesOrganisee->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortiesOrganisee(Sortie $sortiesOrganisee): self
    {
        if ($this->sortiesOrganisees->removeElement($sortiesOrganisee)) {
            // set the owning side to null (unless already changed)
            if ($sortiesOrganisee->getOrganisateur() === $this) {
                $sortiesOrganisee->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }
}
