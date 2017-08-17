<?php

namespace Booster\Core\Repository\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Booster\Core\Repository\ReviewRepository")
 * @ORM\Table(name="reviews")
 * @ORM\HasLifecycleCallbacks
 */
class ReviewEntity extends BaseEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id",type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\Column(type="bigint") */
    protected $fundraiser_id;
   
    /** @ORM\Column(type="string",length=32) */
    protected $name;
    
    /** @ORM\Column(type="string",length=128) */
    protected $email;
    
    /** @ORM\Column(type="smallint") */
    protected $rating;
    
    /** @ORM\Column(type="text") */
    protected $review;
    
    /** @ORM\Column(type="datetime") */
    protected $created_at;
    
    /** @ORM\Column(type="string",length=15) */
    protected $ip;
    
    public function setFundraiserId($fundraiserId)
    {
        $this->fundraiser_id = $fundraiserId;
    }

    public function getFundraiserId()
    {
        return $this->fundraiser_id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setEmail($email)
    {
        $this->email = $email;
    }
    
    public function getEmail()
    {
        return $this->email;
    }
    
    public function setRating($rating)
    {
        $this->rating = $rating;
    }
    
    public function getRating()
    {
        return $this->rating;
    }
    
    public function setReview($review)
    {
        $this->review = $review;
    }

    public function getReview()
    {
        return $this->review;
    }
    
    public function setIp($ip)
    {
        $this->ip = $ip;
    }

    public function getIp()
    {
        return $this->ip;
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
