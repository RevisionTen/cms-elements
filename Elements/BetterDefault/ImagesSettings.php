<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault;

use RevisionTen\CMS\Form\Elements\ElementSettings;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class ImagesSettings extends ElementSettings
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('width', NumberType::class, [
            'label' => 'element.label.width',
            'translation_domain' => 'cms',
            'required' => false,
        ]);

        $builder->add('height', NumberType::class, [
            'label' => 'element.label.height',
            'translation_domain' => 'cms',
            'required' => false,
        ]);

        $builder->add('grayscale', CheckboxType::class, [
            'label' => 'element.label.grayscale',
            'translation_domain' => 'cms',
            'required' => false,
        ]);

        parent::buildForm($builder, $options);
    }
}
