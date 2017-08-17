<?php

namespace Booster\Core\Repository;

use Booster\Core\Repository\Entity\ReviewEntity;

class ReviewRepository extends BaseRepository
{
    
    /**
     * Add a new record
     * @param int $fundraiserId
     * @param string $name
     * @param string $email
     * @param int $rating
     * @param string $review
     * @param string $ip
     * @return boolean
     */
    public function addNew($fundraiserId, $name, $email, $rating, $review, $ip)
    {
        /** @var ReviewEntity $reviewEntity */
        $reviewEntity = new ReviewEntity();
        $reviewEntity->setFundraiserId($fundraiserId);
        $reviewEntity->setName($name);
        $reviewEntity->setEmail($email);
        $reviewEntity->setRating($rating);
        $reviewEntity->setReview($review);
        $reviewEntity->setIp($ip);
        $this->_em->persist($reviewEntity);
        $this->_em->flush();
        return true;
    }
    
    /**
     * Find all records by fundraiser id
     * @return \Doctrine\ORM\Query
     */
    public function getQuery($fundraiserId)
    {
        return $this->_em->createQuery('SELECT f FROM Booster:ReviewEntity f WHERE f.fundraiser_id = :f_id ORDER BY f.created_at desc')->setParameter('f_id', $fundraiserId);
    }
}
