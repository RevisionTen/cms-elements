<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault;

use RevisionTen\CMS\Form\Elements\Element;
use RevisionTen\CMS\Form\Types\UploadType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class Image extends Element
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('settings', ImageSettings::class, [
            'label' => false,
            'required' => false,
        ]);

        $builder->add('title', TextType::class, [
            'label' => 'element.label.title',
            'translation_domain' => 'cms',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('alt', TextType::class, [
            'label' => 'element.label.alt',
            'help' => 'element.help.alt',
            'translation_domain' => 'cms',
            'required' => false,
        ]);

        $builder->add('image', UploadType::class, [
            'label' => 'element.label.image',
            'translation_domain' => 'cms',
            'required' => false,
            'show_file_picker' => true,
            'file_with_meta_data' => true,
        ]);

        $builder->add('lightbox', CheckboxType::class, [
            'label' => 'element.label.lightbox',
            'translation_domain' => 'cms',
            'required' => false,
        ]);
    }
}
