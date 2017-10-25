<?php

namespace AppBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class RepositoryRepository
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $url;

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function __toString()
    {
        return (string)$this->url;
    }
}
