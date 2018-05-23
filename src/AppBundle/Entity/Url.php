<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Url
 * @ORM\Entity
 *
 */
class Url
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="urlText", type="string", length=255)
     */
    private $urlText;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set urlText
     *
     * @param string $urlText
     *
     * @return Url
     */
    public function setUrlText($urlText)
    {
        $this->urlText = $urlText;

        return $this;
    }

    /**
     * Get urlText
     *
     * @return string
     */
    public function getUrlText()
    {
        return $this->urlText;
    }
}
