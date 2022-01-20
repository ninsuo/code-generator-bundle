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

    public function save(Snippet $snippet)
    {
        $this->persist($snippet);

        foreach ($snippet->getFiles() as $file) {
            $this->persist($file);
        }

        $this->flush();
    }

    public function import(string $base64json) : Snippet
    {
        $snippet = Snippet::fromImport($base64json);

        $this->save($snippet);

        return $snippet;
    }
}
