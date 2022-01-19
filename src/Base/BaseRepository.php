<?php

namespace Bundles\CodeGeneratorBundle\Base;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function persist($entity)
    {
        $this->_em->persist($entity);
    }

    public function remove($entity)
    {
        $this->_em->remove($entity);
    }

    public function flush()
    {
        $this->_em->flush();
    }
}