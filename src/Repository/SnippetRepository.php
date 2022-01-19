<?php

namespace Bundles\CodeGeneratorBundle\Repository;

use Bundles\CodeGeneratorBundle\Base\BaseRepository;
use Bundles\CodeGeneratorBundle\Entity\Snippet;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Snippet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Snippet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Snippet[]    findAll()
 * @method Snippet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SnippetRepository extends BaseRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Snippet::class);
    }
}
