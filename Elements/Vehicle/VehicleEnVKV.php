<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CMS\Form\Elements\Element;
use Symfony\Component\Form\FormBuilderInterface;

class VehicleEnVKV extends Element
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
    }

    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_envkv';
    }
}
