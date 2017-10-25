<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Package
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $version;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return Package
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     *
     * @return Package
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }
}
