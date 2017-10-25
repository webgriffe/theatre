<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class User
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }
}
