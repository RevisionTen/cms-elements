<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CMS\Form\Elements\Element;
use RevisionTen\CMS\Form\Types\TrixType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use function in_array;

class VehicleFinancing extends Element
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('financingType', ChoiceType::class, array(
            'label' => 'vehicle.financing.label.financingType',
            'placeholder' => 'vehicle.financing.label.financingType',
            'choices' => [
                'vehicle.financing.choices.financingType.cash' => 'cash',
                'vehicle.financing.choices.financingType.credit' => 'credit',
                'vehicle.financing.choices.financingType.leasingPrivate' => 'leasingPrivate',
                'vehicle.financing.choices.financingType.leasingBusiness' => 'leasingBusiness',
            ],
            'constraints' => new NotBlank(),
            'attr' => [
                'data-condition' => true,
            ],
        ));

        $builder->add('legalDisclaimer', TrixType::class, array(
            'label' => 'vehicle.financing.label.legalDisclaimer',
            'help' => 'vehicle.financing.help.legalDisclaimer',
            'required' => false,
        ));

        $formModifier = static function (FormInterface $form = null, ?string $financingType = null) {
            if ($form) {
                if ('cash' === $financingType) {
                    self::addFields($form, [
                        'msrp',
                    ]);
                    $form->add('price', NumberType::class, array(
                        'label' => 'vehicle.financing.label.cashPrice',
                        'scale' => 2,
                        'constraints' => new NotBlank(),
                    ));
                } elseif ('credit' === $financingType) {
                    self::addFields($form, [
                        'msrp',
                        'price',
                        'downpayment',
                        'netAmount',
                        'fixedInterestRate',
                        'effectiveInterest',
                        'months',
                        'kilometersCredit',
                        'lastInstalment',
                        'totalAmount',
                        'monthlyInstalment',
                    ]);
                } elseif ('leasingPrivate' === $financingType) {
                    self::addFields($form, [
                        'msrp',
                        'price',
                        'specialPayment',
                        'netAmount',
                        'fixedInterestRate',
                        'effectiveInterest',
                        'months',
                        'kilometersLeasing',
                        'totalAmount',
                        'monthlyInstalment',
                    ]);
                } elseif ('leasingBusiness' === $financingType) {
                    self::addFields($form, [
                        'specialPayment',
                        'months',
                        'kilometersLeasing',
                        'monthlyInstalment',
                    ]);
                }
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $financingType = $data['financingType'] ?? null;
            $formModifier($event->getForm(), $financingType);
        });

        $builder->get('financingType')->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event) use ($formModifier) {
            $financingType = $event->getForm()->getData();
            $formModifier($event->getForm()->getParent(), $financingType);
        });
    }

    private static function addFields(FormInterface $form, array $fields): void
    {
        if (in_array('msrp', $fields, true)) {
            $form->add('msrp', NumberType::class, array(
                'label' => 'vehicle.financing.label.msrp',
                'help' => 'vehicle.financing.help.msrp',
                'scale' => 2,
                'required' => false,
            ));
        } else {
            $form->remove('msrp');
        }

        if (in_array('price', $fields, true)) {
            $form->add('price', NumberType::class, array(
                'label' => 'vehicle.financing.label.price',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('price');
        }

        if (in_array('specialPayment', $fields, true)) {
            $form->add('specialPayment', NumberType::class, array(
                'label' => 'vehicle.financing.label.specialPayment',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('specialPayment');
        }

        if (in_array('downpayment', $fields, true)) {
            $form->add('downpayment', NumberType::class, array(
                'label' => 'vehicle.financing.label.downpayment',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('downpayment');
        }

        if (in_array('netAmount', $fields, true)) {
            $form->add('netAmount', NumberType::class, array(
                'label' => 'vehicle.financing.label.netAmount',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('netAmount');
        }

        if (in_array('fixedInterestRate', $fields, true)) {
            $form->add('fixedInterestRate', NumberType::class, array(
                'label' => 'vehicle.financing.label.fixedInterestRate',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('fixedInterestRate');
        }

        if (in_array('effectiveInterest', $fields, true)) {
            $form->add('effectiveInterest', NumberType::class, array(
                'label' => 'vehicle.financing.label.effectiveInterest',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('effectiveInterest');
        }

        if (in_array('months', $fields, true)) {
            $form->add('months', NumberType::class, array(
                'label' => 'vehicle.financing.label.months',
                'scale' => 0,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('months');
        }

        if (in_array('kilometersCredit', $fields, true)) {
            $form->add('kilometersCredit', NumberType::class, array(
                'label' => 'vehicle.financing.label.kilometersCredit',
                'help' => 'vehicle.financing.help.kilometersCredit',
                'scale' => 0,
                'required' => false,
            ));
        } else {
            $form->remove('kilometersCredit');
        }

        if (in_array('kilometersLeasing', $fields, true)) {
            $form->add('kilometersLeasing', NumberType::class, array(
                'label' => 'vehicle.financing.label.kilometersLeasing',
                'scale' => 0,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('kilometersLeasing');
        }

        if (in_array('lastInstalment', $fields, true)) {
            $form->add('lastInstalment', NumberType::class, array(
                'label' => 'vehicle.financing.label.lastInstalment',
                'scale' => 2,
                'required' => false,
            ));
            $form->add('lastInstalmentIsLastMonthlyInstalment', CheckboxType::class, array(
                'label' => 'vehicle.financing.label.lastInstalmentIsLastMonthlyInstalment',
                'help' => 'vehicle.financing.help.lastInstalmentIsLastMonthlyInstalment',
                'required' => false,
            ));
        } else {
            $form->remove('lastInstalment');
            $form->remove('lastInstalmentIsLastMonthlyInstalment');
        }

        if (in_array('totalAmount', $fields, true)) {
            $form->add('totalAmount', NumberType::class, array(
                'label' => 'vehicle.financing.label.totalAmount',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('totalAmount');
        }

        if (in_array('monthlyInstalment', $fields, true)) {
            $form->add('monthlyInstalment', NumberType::class, array(
                'label' => 'vehicle.financing.label.monthlyInstalment',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        } else {
            $form->remove('monthlyInstalment');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_financing';
    }
}
