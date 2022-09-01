<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault;

use RevisionTen\CMS\Form\Types\UploadType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use RevisionTen\CMS\Form\Types\ImageType as BaseImageType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ImageType extends BaseImageType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('title', TextType::class, [
            'label' => 'image.label.title',
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
            'label' => 'image.label.image',
            'translation_domain' => 'cms',
            'required' => false,
            'show_file_picker' => true,
            'file_with_meta_data' => true,
        ]);
    }
}
