<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault;

use Symfony\Component\Form\FormBuilderInterface;

class ImageSettings extends \RevisionTen\CMS\Form\Elements\ImageSettings
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

        $builder->remove('scaling');
    }
}
