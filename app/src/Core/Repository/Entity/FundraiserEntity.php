<?php

namespace Booster\Core\Repository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Booster\Core\Repository\FundraiserRepository")
 * @ORM\Table(name="fundraisers")
 * @ORM\HasLifecycleCallbacks
 */
class FundraiserEntity extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="string",length=32) */
    protected $name;

    /** @ORM\Column(type="decimal") */
    protected $rating;

    /** @ORM\Column(type="datetime") */
    protected $created_at;
    
    public function getId()
    {
        return $this->id;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setRating($rating)
    {
        $this->rating = $rating;
    }

    public function getRating()
    {
        return $this->rating;
    }
   
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }
    
    /**
     * @ORM\PreFlush
     * see http://symfony.com/doc/current/cookbook/doctrine/file_uploads.html
     * see http://doctrine-orm.readthedocs.org/en/latest/reference/events.html#lifecycle-events
     */
    public function preFlush()
    {
        $this->created_at = new \Datetime('now');
    }
}
