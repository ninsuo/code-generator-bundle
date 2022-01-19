<?php

namespace Bundles\CodeGeneratorBundle\Form;

use Bundles\CodeGeneratorBundle\Entity\SnippetFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Twig\Environment;

class SnippetFileType extends AbstractType
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('destination', TextType::class, [
                'label' => 'File path',
            ])
            ->add('template', TextareaType::class, [
                'label' => false,
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['twig'] = $this->twig;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SnippetFile::class,
        ]);
    }
}