<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CMS\Form\Elements\Element;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class VehicleOffer extends Element
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('title', TextType::class, [
            'label' => 'element.label.title',
            'translation_domain' => 'cms',
            'constraints' => new NotBlank(),
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_offer';
    }
}
