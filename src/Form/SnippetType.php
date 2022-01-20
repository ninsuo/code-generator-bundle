<?php

namespace Bundles\CodeGeneratorBundle\Form;

use Bundles\CodeGeneratorBundle\Entity\Snippet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SnippetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', HiddenType::class)
            ->add('name', TextType::class, [
                'label' => 'Snippet\'s name',
            ])
            ->add('context', TextareaType::class, [
                'label' => false,
                'attr'  => [
                    'rows' => 12,
                ],
            ])
            ->add('enricher', TextareaType::class, [
                'label' => false,
                'attr'  => [
                    'rows' => 12,
                ],
            ])
            ->add('files', CollectionType::class, [
                'label'         => false,
                'entry_type'    => SnippetFileType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add'     => true,
                'allow_delete'  => true,
                'prototype'     => true,
                'required'      => false,
                'delete_empty'  => true,
                'by_reference'  => false,
            ])
            ->add('export', TextareaType::class, [
                'label' => 'Export as JSON for sharing.',
                'attr'  => [
                    'readonly' => true,
                    'rows'     => 12,
                ],
            ])
            ->add('import', TextareaType::class, [
                'label'  => 'Import from a JSON string.',
                'attr'   => [
                    'rows' => 12,
                ],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Snippet::class,
        ]);
    }
}