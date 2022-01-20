<?php

namespace Bundles\CodeGeneratorBundle\Controller;

use Bundles\CodeGeneratorBundle\Entity\Snippet;
use Bundles\CodeGeneratorBundle\Entity\SnippetFile;
use Bundles\CodeGeneratorBundle\Form\SnippetType;
use Bundles\CodeGeneratorBundle\Repository\SnippetFileRepository;
use Bundles\CodeGeneratorBundle\Repository\SnippetRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormErrorIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/snippet", name="snippet_")
 */
class SnippetController extends AbstractController
{
    private SnippetRepository     $snippetRepository;
    private SnippetFileRepository $snippetFileRepository;

    public function __construct(SnippetRepository $snippetRepository, SnippetFileRepository $snippetFileRepository)
    {
        $this->snippetRepository     = $snippetRepository;
        $this->snippetFileRepository = $snippetFileRepository;
    }

    /**
     * @Route(path="/{id}", name="index", defaults={"id"=null}, requirements={"id": "\d+"})
     */
    public function index(Request $request, ?Snippet $snippet = null) : Response
    {
        $twig = $this->container->get('twig');

        if (null === $snippet) {
            if ($id = $request->request->all('snippet')['id'] ?? null) {
                $snippet = $this->snippetRepository->find($id);
            } else {
                $snippet = new Snippet();
                $file    = new SnippetFile();
                $snippet->addFile($file);
            }
        }

        $form = $this
            ->createForm(SnippetType::class, $snippet)
            ->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $imported = false;
                if ($base64json = $form->get('import')->getData()) {
                    if ($array = json_decode(base64_decode($base64json), true)) {
                        if (is_array($array)) {
                            $snippet->fromArray($array, $snippet);
                            $imported = true;
                        }
                    }
                }

                $this->save($snippet);

                return $this->json([
                    'snippet'   => [
                        'id'   => $snippet->getId(),
                        'name' => $snippet->getName(),
                    ],
                    'context'   => $snippet->dumpContext(),
                    'templates' => $snippet->dumpTemplates($twig),
                    'export'    => $snippet->getExport(),
                    'imported'  => $imported,
                ]);
            }

            return $this->json([
                'context' => nl2br(str_replace(FormErrorIterator::INDENTATION, '- ', (string) $form->getErrors(true, false))),
            ]);
        }

        return $this->render('@CodeGenerator/snippet/index.html.twig', [
            'twig'    => $twig,
            'list'    => $this->snippetRepository->findAll(),
            'current' => $snippet,
            'form'    => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/remove/{token}/{id}", name="remove")
     */
    public function remove(Snippet $snippet, string $token)
    {
        if (!$this->isCsrfTokenValid('token', $token)) {
            throw $this->createNotFoundException();
        }

        $this->snippetRepository->remove($snippet);

        $this->snippetRepository->flush();

        return $this->redirectToRoute('snippet_index');
    }

    private function save(Snippet $snippet)
    {
        $this->snippetRepository->save($snippet);
    }
}
