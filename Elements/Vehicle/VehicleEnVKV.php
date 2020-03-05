<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CMS\Form\Elements\Element;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class VehicleEnVKV extends Element
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('title', TextType::class, [
            'label' => 'vehicle.envkv.label.title',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('energyEfficiencyClass', ChoiceType::class, array(
            'label' => 'vehicle.envkv.label.energyEfficiencyClass',
            'placeholder' => 'vehicle.envkv.label.energyEfficiencyClass',
            'choices' => array(
                'A+' => 'A+',
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
                'G' => 'G',
            ),
            'constraints' => new NotBlank(),
        ));

        $builder->add('emissionSticker', TextType::class, array(
            'label' => 'vehicle.envkv.label.emissionSticker',
            'required' => false,
        ));

        $builder->add('co2Emission', NumberType::class, array(
            'label' => 'vehicle.envkv.label.co2Emission',
            'required' => false,
            'scale' => 2,
        ));

        $builder->add('inner', NumberType::class, array(
            'label' => 'vehicle.envkv.label.inner',
            'required' => false,
            'scale' => 1,
        ));

        $builder->add('outer', NumberType::class, array(
            'label' => 'vehicle.envkv.label.outer',
            'required' => false,
            'scale' => 1,
        ));

        $builder->add('combined', NumberType::class, array(
            'label' => 'vehicle.envkv.label.combined',
            'required' => false,
            'scale' => 1,
        ));

        $builder->add('combinedPowerConsumption', NumberType::class, array(
            'label' => 'vehicle.envkv.label.combinedPowerConsumption',
            'required' => false,
            'scale' => 1,
        ));

        $builder->add('power', NumberType::class, array(
            'label' => 'vehicle.envkv.label.power',
            'required' => false,
            'scale' => 0,
        ));

        $builder->add('fuelType', ChoiceType::class, array(
            'label' => 'vehicle.envkv.label.fuelType',
            'placeholder' => 'vehicle.envkv.label.fuelType',
            'choices' => [
                'vehicle.envkv.choices.fuel.petrol' => 'petrol',
                'vehicle.envkv.choices.fuel.diesel' => 'diesel',
                'vehicle.envkv.choices.fuel.lpg' => 'lpg',
                'vehicle.envkv.choices.fuel.cng' => 'cng',
                'vehicle.envkv.choices.fuel.gas' => 'gas',
                'vehicle.envkv.choices.fuel.electricity' => 'electricity',
                'vehicle.envkv.choices.fuel.hybrid' => 'hybrid',
                'vehicle.envkv.choices.fuel.hybrid_petrol' => 'hybrid_petrol',
                'vehicle.envkv.choices.fuel.hybrid_diesel' => 'hybrid_diesel',
                'vehicle.envkv.choices.fuel.hydrogen' => 'hydrogen',
                'vehicle.envkv.choices.fuel.other' => 'other',
            ],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_envkv';
    }
}
