<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CMS\Form\Elements\Element;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class VehicleWLTP extends Element
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('title', TextType::class, [
            'label' => 'vehicle.envkv.label.title',
            'constraints' => new NotBlank(),
        ]);

        $builder->add('hideNEFZ', CheckboxType::class, array(
            'label' => 'vehicle.wltp.label.hideNEFZ',
            'help' => 'vehicle.wltp.help.hideNEFZ',
            'required' => false,
        ));

        $builder->add('unofficial', CheckboxType::class, array(
            'label' => 'vehicle.wltp.label.unofficial',
            'required' => false,
        ));

        /*
        $builder->add('energyEfficiencyClassMin', ChoiceType::class, array(
            'label' => 'vehicle.envkv.label.energyEfficiencyClass',
            'placeholder' => 'vehicle.envkv.label.min',
            'choices' => [
                'A+' => 'A+',
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
                'G' => 'G',
            ],
            'required' => false,
            'attr' => [
                'class' => 'custom-select',
            ],
        ));

        $builder->add('energyEfficiencyClass', ChoiceType::class, array(
            'label' => 'vehicle.envkv.label.energyEfficiencyClass',
            'placeholder' => 'vehicle.envkv.label.max',
            'choices' => [
                'A+' => 'A+',
                'A' => 'A',
                'B' => 'B',
                'C' => 'C',
                'D' => 'D',
                'E' => 'E',
                'F' => 'F',
                'G' => 'G',
            ],
            'required' => false,
            'attr' => [
                'class' => 'custom-select',
            ],
        ));

        $builder->add('emissionStickerMin', ChoiceType::class, array(
            'label' => 'vehicle.envkv.label.emissionSticker',
            'placeholder' => 'vehicle.envkv.label.min',
            'required' => false,
            'choices' => [
                'vehicle.envkv.choices.emissionSticker.green' => 'green',
                'vehicle.envkv.choices.emissionSticker.yellow' => 'yellow',
                'vehicle.envkv.choices.emissionSticker.red' => 'red',
                'vehicle.envkv.choices.emissionSticker.blue' => 'blue',
            ],
            'attr' => [
                'class' => 'custom-select',
            ],
        ));

        $builder->add('emissionSticker', ChoiceType::class, array(
            'label' => 'vehicle.envkv.label.emissionSticker',
            'placeholder' => 'vehicle.envkv.label.max',
            'required' => false,
            'choices' => [
                'vehicle.envkv.choices.emissionSticker.green' => 'green',
                'vehicle.envkv.choices.emissionSticker.yellow' => 'yellow',
                'vehicle.envkv.choices.emissionSticker.red' => 'red',
                'vehicle.envkv.choices.emissionSticker.blue' => 'blue',
            ],
            'attr' => [
                'class' => 'custom-select',
            ],
        ));

        $builder->add('emissionClassMin', TextType::class, array(
            'label' => 'vehicle.envkv.label.emissionClass',
            'attr' => [
                'placeholder' => 'vehicle.envkv.label.min',
            ],
            'required' => false,
        ));

        $builder->add('emissionClass', TextType::class, array(
            'label' => 'vehicle.envkv.label.emissionClass',
            'attr' => [
                'placeholder' => 'vehicle.envkv.label.max',
            ],
            'required' => false,
        ));

        */

        $builder->add('motor', TextType::class, array(
            'label' => 'vehicle.envkv.label.motor',
            'attr' => [
                'placeholder' => 'vehicle.envkv.placeholder.motor',
            ],
            'required' => false,
        ));

        $builder->add('gearbox', TextType::class, array(
            'label' => 'vehicle.envkv.label.gearbox',
            'attr' => [
                'placeholder' => 'vehicle.envkv.placeholder.gearbox',
            ],
            'required' => false,
        ));

        // Power (KW) and horsepower (PS) describes the total power of the vehicle.
        $builder->add('power', NumberType::class, array(
            'label' => 'vehicle.wltp.form.power',
            'scale' => 0,
            'required' => false,
        ));
        $builder->add('horsepower', NumberType::class, array(
            'label' => 'vehicle.wltp.form.horsepower',
            'scale' => 0,
            'required' => false,
        ));

        $builder->add('weight', NumberType::class, array(
            'label' => 'vehicle.envkv.label.weight',
            'scale' => 0,
            'required' => false,
        ));

        $builder->add('fuelType', ChoiceType::class, array(
            'label' => 'vehicle.wltp.label.fuelType',
            'placeholder' => 'vehicle.wltp.label.fuelType',
            'choices' => [
                'vehicle.envkv.choices.fuel.petrol' => 'petrol',
                'vehicle.envkv.choices.fuel.diesel' => 'diesel',
                #'vehicle.envkv.choices.fuel.lpg' => 'lpg',
                #'vehicle.envkv.choices.fuel.cng' => 'cng',
                #'vehicle.envkv.choices.fuel.gas' => 'gas',
                'vehicle.envkv.choices.fuel.electricity' => 'electricity',
                'vehicle.envkv.choices.fuel.hybrid' => 'hybrid',
                'vehicle.envkv.choices.fuel.hybrid_petrol' => 'hybrid_petrol',
                'vehicle.envkv.choices.fuel.hybrid_diesel' => 'hybrid_diesel',
                #'vehicle.envkv.choices.fuel.hydrogen' => 'hydrogen',
                #'vehicle.envkv.choices.fuel.other' => 'other',
            ],
            'constraints' => new NotBlank(),
            'attr' => [
                'data-condition' => true,
                'class' => 'custom-select',
            ],
        ));

        $formModifier = static function (FormInterface $form = null, ?string $fuelType = null) {
            if ($form) {
                $hasFossilFuel = 'electricity' !== $fuelType && 'hydrogen' !== $fuelType;
                $hasBattery = 'electricity' === $fuelType || 'hydrogen' === $fuelType || 'hybrid' === $fuelType || 'hybrid_petrol' === $fuelType || 'hybrid_diesel' === $fuelType;

                // Add additional power specs for hybrid vehicles.
                if ($hasFossilFuel && $hasBattery) {
                    $form->add('fuelPower', NumberType::class, array(
                        'label' => 'vehicle.wltp.form.fuelPower',
                        'scale' => 0,
                        'required' => false,
                    ));
                    $form->add('fuelHorsepower', NumberType::class, array(
                        'label' => 'vehicle.wltp.form.fuelHorsepower',
                        'scale' => 0,
                        'required' => false,
                    ));
                    $form->add('electricPower', NumberType::class, array(
                        'label' => 'vehicle.wltp.form.electricPower',
                        'scale' => 0,
                        'required' => false,
                    ));
                    $form->add('electricHorsepower', NumberType::class, array(
                        'label' => 'vehicle.wltp.form.electricHorsepower',
                        'scale' => 0,
                        'required' => false,
                    ));
                    $form->add('co2EmissionWeightedMin', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.co2EmissionWeighted',
                        'required' => false,
                        'scale' => 2,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.min',
                        ],
                    ));
                    $form->add('co2EmissionWeighted', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.co2EmissionWeighted',
                        'required' => false,
                        'scale' => 2,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.max',
                        ],
                    ));
                } else {
                    $form->remove('fuelPower');
                    $form->remove('fuelHorsepower');
                    $form->remove('electricPower');
                    $form->remove('electricHorsepower');
                    $form->remove('co2EmissionWeightedMin');
                    $form->remove('co2EmissionWeighted');
                }

                if ($hasBattery) {
                    $form->add('combinedPowerConsumptionMin', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.combinedPowerConsumption',
                        'scale' => 1,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.min',
                        ],
                    ));
                    $form->add('combinedPowerConsumption', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.combinedPowerConsumption',
                        'scale' => 1,
                        'constraints' => new NotBlank(),
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.max',
                        ],
                    ));
                    $form->add('rangeMin', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.rangeMin',
                        'scale' => 1,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.min',
                        ],
                    ));
                    $form->add('range', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.range',
                        'scale' => 1,
                        'constraints' => new NotBlank(),
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.max',
                        ],
                    ));
                } else {
                    $form->remove('combinedPowerConsumption');
                    $form->remove('combinedPowerConsumptionMin');
                    $form->remove('range');
                    $form->remove('rangeMin');
                }

                if ($hasFossilFuel) {
                    $form->add('fuel', TextType::class, array(
                        'label' => 'vehicle.envkv.label.fuel',
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.placeholder.fuel',
                        ],
                        'required' => false,
                    ));
                    $form->add('cubicCapacity', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.cubicCapacity',
                        'scale' => 0,
                        'required' => false,
                    ));
                    $form->add('combined', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.combined',
                        'scale' => 1,
                        'constraints' => new NotBlank(),
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.max',
                        ],
                    ));
                    $form->add('combinedMin', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.combined',
                        'scale' => 1,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.min',
                        ],
                    ));
                    $form->add('co2EmissionMin', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.co2Emission',
                        'required' => false,
                        'scale' => 2,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.min',
                        ],
                    ));
                    $form->add('co2Emission', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.co2Emission',
                        'required' => false,
                        'scale' => 2,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.max',
                        ],
                    ));
                } else {
                    $form->remove('combined');
                    $form->remove('combinedMin');
                    $form->remove('co2EmissionMin');
                    $form->remove('co2Emission');
                    $form->remove('fuel');
                    $form->remove('cubicCapacity');
                }
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $fuelType = $data['fuelType'] ?? null;
            $formModifier($event->getForm(), $fuelType);
        });

        $builder->get('fuelType')->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event) use ($formModifier) {
            $fuelType = $event->getForm()->getData();
            $formModifier($event->getForm()->getParent(), $fuelType);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_wltp';
    }
}
