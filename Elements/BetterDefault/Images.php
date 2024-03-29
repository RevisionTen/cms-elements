<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault;

use RevisionTen\CMS\Form\Elements\Element;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

class Images extends Element
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('settings', ImagesSettings::class, [
            'label' => false,
            'required' => false,
        ]);

        $builder->add('imagesPerRow', ChoiceType::class, [
            'label' => 'images.label.imagesPerRow',
            'required' => false,
            'choices' => [
                'images.value.imagesPerRow12' => 'col-1',
                'images.value.imagesPerRow6' => 'col-2',
                'images.value.imagesPerRow4' => 'col-3',
                'images.value.imagesPerRow3' => 'col-4',
                'images.value.imagesPerRow2' => 'col-6',
                'images.value.imagesPerRow1' => 'col-12',
            ],
            'attr' => [
                'class' => 'custom-select',
            ],
        ]);

        $builder->add('images', CollectionType::class, [
            'label' => 'element.label.images',
            'translation_domain' => 'cms',
            'entry_type' => ImageType::class,
            'entry_options' => [
                'attr' => [
                    'class' => 'well',
                ],
            ],
            'allow_add' => true,
            'allow_delete' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'cms_images';
    }
}
