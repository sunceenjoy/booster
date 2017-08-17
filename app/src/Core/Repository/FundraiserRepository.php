<?php

namespace Booster\Core\Repository;

use Booster\Core\Repository\Entity\FundraiserEntity;

class FundraiserRepository extends BaseRepository
{
    /**
     * Find all records order by rating desc
     * @return \Doctrine\ORM\Query
     */
    public function getQuery()
    {
        return $this->_em->createQuery('SELECT f FROM Booster:FundraiserEntity f ORDER BY f.rating desc');
    }
    
    /**
     * Find a record by id
     * @param int $id
     * @return []
     */
    public function getFundraiserById($id)
    {
        return $this->_em->createQuery('SELECT f FROM Booster:FundraiserEntity f WHERE f.id=:id')->setParameter('id', $id)->getResult();
    }
    
    /**
     * Add a new  record
     * @param string $name
     * @return boolean
     */
    public function addNew($name)
    {
        /** @var FundraiserEntity $fundraiserEntity */
        $fundraiserEntity = new FundraiserEntity();
        $fundraiserEntity->setName($name);
        $fundraiserEntity->setRating(0);
        $this->_em->persist($fundraiserEntity);
        $this->_em->flush();
        return true;
    }
}
