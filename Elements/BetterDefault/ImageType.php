<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault;

use RevisionTen\CMS\Form\Types\UploadType;
use Symfony\Component\Form\FormBuilderInterface;
use RevisionTen\CMS\Form\Types\ImageType as BaseImageType;

class ImageType extends BaseImageType
{
    /**
     * {@inheritdoc}
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('image', UploadType::class, [
            'label' => 'image.label.image',
            'translation_domain' => 'cms',
            'required' => false,
            'show_file_picker' => true,
            'file_with_meta_data' => true,
        ]);
    }
}
