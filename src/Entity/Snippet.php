<?php

namespace Bundles\CodeGeneratorBundle\Entity;

use Bundles\CodeGeneratorBundle\Repository\SnippetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

/**
 * @ORM\Entity(repositoryClass=SnippetRepository::class)
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class Snippet
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $context;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $enricher;

    /**
     * @var SnippetFile[]
     *
     * @ORM\OneToMany(targetEntity=SnippetFile::class, mappedBy="snippet", orphanRemoval=true)
     */
    private $files;

    public function __construct()
    {
        $this->files = new ArrayCollection();
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : self
    {
        $this->name = $name;

        return $this;
    }

    public function getContext() : ?string
    {
        return $this->context;
    }

    public function setContext(?string $context) : self
    {
        $this->context = $context;

        return $this;
    }

    public function getEnricher() : ?string
    {
        return $this->enricher;
    }

    public function setEnricher(?string $enricher) : self
    {
        $this->enricher = $enricher;

        return $this;
    }

    /**
     * @return Collection|SnippetFile[]
     */
    public function getFiles() : Collection
    {
        return $this->files;
    }

    public function addFile(SnippetFile $file) : self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setSnippet($this);
        }

        return $this;
    }

    public function removeFile(SnippetFile $file) : self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getSnippet() === $this) {
                $file->setSnippet(null);
            }
        }

        return $this;
    }

    public function dumpContext()
    {
        ob_start();

        $handler = VarDumper::setHandler(function ($var) {
            $cloner = new VarCloner();
            $dumper = new HtmlDumper();

            return $dumper->dump($cloner->cloneVar($var));
        });

        try {
            echo VarDumper::dump(
                $this->computeContext()
            );
        } catch (\Throwable $e) {
            echo sprintf("<pre>%s: %s\n\n%s<pre>", get_class($e), $e->getMessage(), $e->getTraceAsString());
        } finally {
            VarDumper::setHandler($handler);
        }

        return ob_get_clean();
    }

    public function createContext() : array
    {
        try {
            return $this->computeContext();
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function dumpTemplates(Environment $twig, bool $escape = true) : array
    {
        $context = $this->createContext();

        $templates = [];
        foreach ($this->files as $index => $file) {
            $templates[$index] = [
                'destination' => $file->getRenderedDestination($twig, $context, $escape),
                'template' => $file->getRenderedTemplate($twig, $context, $escape),
            ];
        }

        return $templates;
    }

    public function getContextAsArray() : array
    {
        return Yaml::parse($this->context);
    }

    public function setContextAsArray(array $context)
    {
        $this->context = Yaml::dump($context);
    }

    private function computeContext()
    {
        if (!$this->context) {
            return [];
        }

        $context = $this->getContextAsArray();

        eval($this->enricher);

        return $context;
    }
}
