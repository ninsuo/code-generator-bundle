<?php

namespace Bundles\CodeGeneratorBundle\Entity;

use Bundles\CodeGeneratorBundle\Repository\SnippetFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @ORM\Entity(repositoryClass=SnippetFileRepository::class)
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 */
class SnippetFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $destination;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $template;

    /**
     * @ORM\ManyToOne(targetEntity=Snippet::class, inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $snippet;

    public function getId() : ?int
    {
        return $this->id;
    }

    public function getDestination() : ?string
    {
        return $this->destination;
    }

    public function setDestination(?string $destination) : self
    {
        $this->destination = $destination;

        return $this;
    }

    public function getTemplate() : ?string
    {
        return $this->template;
    }

    public function setTemplate(?string $template) : self
    {
        $this->template = $template;

        return $this;
    }

    public function getSnippet() : ?Snippet
    {
        return $this->snippet;
    }

    public function setSnippet(?Snippet $snippet) : self
    {
        $this->snippet = $snippet;

        return $this;
    }

    public function getRenderedDestination(Environment $twig, array $context, bool $escape = true) : string
    {
        return $this->render($twig, $this->destination, $context, $escape);
    }

    public function getRenderedTemplate(Environment $twig, array $context, bool $escape = true)
    {
        return $this->render($twig, $this->template, $context, $escape);
    }

    private function render(Environment $twig, ?string $template, array $context, bool $escape) : string
    {
        if (!$template) {
            return '';
        }

        $loader = $twig->getLoader();

        $twig->setLoader(
            new ArrayLoader([
                'template' => $template,
            ])
        );

        try {
            $rendered = $twig->render('template', $context);
        } catch (\Throwable $e) {
            $rendered = sprintf("<pre>%s: %s\n\n%s<pre>", get_class($e), $e->getMessage(), $e->getTraceAsString());
        } finally {
            $twig->setLoader($loader);
        }

        if ($escape) {
            return htmlentities($rendered);
        }

        return $rendered;
    }
}
