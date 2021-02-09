<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ParticipantsRepository;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantsRepository::class)
 */
class Participants implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30, unique=true)
     * @Assert\NotBlank
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=30)
     * @Assert\NotBlank
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     * @Assert\NotBlank
     */
    private $mail;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     */
    private $mot_de_passe;

    /**
     * @var
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="integer")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="integer")
     */
    private $actif;

    /**
     * @var Campus
     * @ORM\ManyToOne(targetEntity="App\Entity\Campus")
     */
    private $campus;


    /**
     * @ORM\OneToMany(targetEntity=Inscriptions::class, mappedBy="participant")
     */
    private $inscriptions;

    /**
     * @ORM\Column(type="string")
     */
    private $photoProfil;

    /**
     * @return mixed
     */
    public function getPhotoProfil()
    {
        return $this->photoProfil;
    }

    /**
     * @param mixed $photoProfil
     */
    public function setPhotoProfil($photoProfil):self
    {
        $this->photoProfil = $photoProfil;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }


    /**
     * @return ArrayCollection
     */
    public function getInscriptions(): ArrayCollection
    {
        return $this->inscriptions;
    }

    /**
     * @param ArrayCollection $inscriptions
     */
    public function setInscriptions(ArrayCollection $inscriptions): void
    {
        $this->inscriptions = $inscriptions;
    }

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
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

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->mot_de_passe;
    }

    public function setMotDePasse(string $mot_de_passe): self
    {
        $this->mot_de_passe = $mot_de_passe;

        return $this;
    }

    public function getAdministrateur(): ?int
    {
        return $this->administrateur;
    }

    public function setAdministrateur(int $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?int
    {
        return $this->actif;
    }

    public function setActif(int $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        return $this->pseudo;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return Campus
     */
    public function getCampus(): Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus $campus
     */
    public function setCampus(Campus $campus): self
    {
        $this->campus = $campus;
        return $this;
    }

}
