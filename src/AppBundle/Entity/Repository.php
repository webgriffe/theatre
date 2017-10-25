<?php

namespace AppBundle\Entity;

use AppBundle\Model\Package;
use AppBundle\Model\RepositoryRepository;
use AppBundle\Model\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Repository
 *
 * @ORM\Table(name="repository")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RepositoryRepository")
 */
class Repository
{
    /**
     * @var string
     *
     * @ORM\Column(name="id", type="guid")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="repositories", type="array")
     */
    private $repositories = [];

    /**
     * @var array
     *
     * @ORM\Column(name="packages", type="array")
     */
    private $packages = [];

    /**
     * @var array
     *
     * @ORM\Column(name="users", type="array")
     */
    private $users = [];

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Repository
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return RepositoryRepository[]
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param array $repositories
     *
     * @return Repository
     */
    public function setRepositories(array $repositories)
    {
        if (!empty($repositories) && $repositories === $this->repositories) {
            reset($repositories);
            $repositories[key($repositories)] = clone current($repositories);
        }
        $this->repositories = $repositories;

        return $this;
    }

    /**
     * @return Package[]
     */
    public function getPackages()
    {
        return $this->packages;
    }

    /**
     * @param array $packages
     *
     * @return $this
     */
    public function setPackages(array $packages)
    {
        if (!empty($packages) && $packages === $this->packages) {
            reset($packages);
            $packages[key($packages)] = clone current($packages);
        }
        $this->packages = $packages;

        return $this;
    }

    /**
     * @return User[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param array $users
     *
     * @return Repository
     */
    public function setUsers($users)
    {
        if (!empty($users) && $users === $this->users) {
            reset($users);
            $users[key($users)] = clone current($users);
        }
        $this->users = $users;

        return $this;
    }
}
