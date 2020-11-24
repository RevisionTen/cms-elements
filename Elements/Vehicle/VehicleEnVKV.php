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

        $builder->add('legalDisclaimerNum', TextType::class, array(
            'label' => 'vehicle.financing.label.legalDisclaimerNum',
            'help' => 'vehicle.financing.help.legalDisclaimerNum',
            'required' => false,
        ));

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
            'constraints' => new NotBlank(),
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

        $builder->add('co2EmissionMin', NumberType::class, array(
            'label' => 'vehicle.envkv.label.co2Emission',
            'required' => false,
            'scale' => 2,
            'attr' => [
                'placeholder' => 'vehicle.envkv.label.min',
            ],
        ));

        $builder->add('co2Emission', NumberType::class, array(
            'label' => 'vehicle.envkv.label.co2Emission',
            'required' => false,
            'scale' => 2,
            'attr' => [
                'placeholder' => 'vehicle.envkv.label.max',
            ],
        ));

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

        $builder->add('power', NumberType::class, array(
            'label' => 'vehicle.envkv.label.power',
            'scale' => 0,
            'required' => false,
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
                } else {
                    $form->remove('combinedPowerConsumption');
                    $form->remove('combinedPowerConsumptionMin');
                }

                if ($hasFossilFuel) {
                    $form->add('inner', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.inner',
                        'scale' => 1,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.max',
                        ],
                    ));
                    $form->add('outer', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.outer',
                        'scale' => 1,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.max',
                        ],
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
                    $form->add('outerMin', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.outer',
                        'scale' => 1,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.min',
                        ],
                    ));
                    $form->add('innerMin', NumberType::class, array(
                        'label' => 'vehicle.envkv.label.inner',
                        'scale' => 1,
                        'required' => false,
                        'attr' => [
                            'placeholder' => 'vehicle.envkv.label.min',
                        ],
                    ));
                } else {
                    $form->remove('inner');
                    $form->remove('innerMin');
                    $form->remove('outer');
                    $form->remove('outerMin');
                    $form->remove('combined');
                    $form->remove('combinedMin');
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

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_envkv';
    }
}
