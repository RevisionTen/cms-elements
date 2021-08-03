<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CMS\Form\Elements\Element;
use RevisionTen\CMS\Form\Types\CKEditorType;
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
                'class' => 'custom-select',
            ],
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
                        'required' => false,
                    ));
                } elseif ('credit' === $financingType) {
                    self::addFields($form, [
                        'msrp',
                        'price',
                        'downpayment',
                        'downpaymentDisclaimerNum',
                        'netAmount',
                        'fixedInterestRate',
                        //'fixedInterestRateDisclaimer',
                        'fixedInterestRateDisclaimerNum',
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
                        //'specialPaymentDisclaimer',
                        'specialPaymentDisclaimerNum',
                        //'fixedInterestRateDisclaimer',
                        'fixedInterestRateDisclaimerNum',
                        'effectiveInterest',
                        'months',
                        'kilometersLeasing',
                        'totalAmount',
                        'monthlyInstalment',
                    ]);

                    /*
                    // Net amount is optional.
                    $form->add('netAmount', NumberType::class, array(
                        'label' => 'vehicle.financing.label.netAmount',
                        'scale' => 2,
                        'required' => false,
                    ));*/
                } elseif ('leasingBusiness' === $financingType) {
                    self::addFields($form, [
                        'specialPayment',
                        //'specialPaymentDisclaimer',
                        'specialPaymentDisclaimerNum',
                        'months',
                        'kilometersLeasing',
                        'monthlyInstalment',
                    ]);
                }
            }
        };

        // Migrate legalDisclaimer input to disclaimer field.
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            // New field.
            $disclaimer = $data['disclaimer'] ?? '';

            // Old fields.
            $legalDisclaimer = $data['legalDisclaimer'] ?? null;
            $specialPaymentDisclaimer = $data['specialPaymentDisclaimer'] ?? null;
            $fixedInterestRateDisclaimer = $data['fixedInterestRateDisclaimer'] ?? null;

            // Move data.
            if (!empty($legalDisclaimer)) {
                $data['disclaimer'] = ($data['disclaimer'] ?? '').$legalDisclaimer;
                $data['legalDisclaimer'] = null;
            }
            if (!empty($specialPaymentDisclaimer)) {
                $data['disclaimer'] = ($data['disclaimer'] ?? '').$specialPaymentDisclaimer;
                $data['specialPaymentDisclaimer'] = null;
            }
            if (!empty($fixedInterestRateDisclaimer)) {
                $data['disclaimer'] = ($data['disclaimer'] ?? '').$fixedInterestRateDisclaimer;
                $data['fixedInterestRateDisclaimer'] = null;
            }

            $event->setData($data);
        });

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
        $form->remove('msrp');
        $form->remove('price');
        $form->remove('specialPayment');
        $form->remove('downpayment');
        $form->remove('netAmount');
        $form->remove('fixedInterestRate');
        $form->remove('effectiveInterest');
        $form->remove('months');
        $form->remove('kilometersCredit');
        $form->remove('kilometersLeasing');
        $form->remove('lastInstalment');
        $form->remove('lastInstalmentIsLastMonthlyInstalment');
        $form->remove('totalAmount');
        $form->remove('monthlyInstalment');
        $form->remove('legalDisclaimerNum');
        $form->remove('specialPaymentDisclaimerNum');
        $form->remove('fixedInterestRateDisclaimerNum');
        $form->remove('legalDisclaimer');
        $form->remove('downpaymentDisclaimerNum');
        //$form->remove('specialPaymentDisclaimer');
        //$form->remove('fixedInterestRateDisclaimer');

        if (in_array('msrp', $fields, true)) {
            $form->add('msrp', NumberType::class, array(
                'label' => 'vehicle.financing.label.msrp',
                'help' => 'vehicle.financing.help.msrp',
                'scale' => 2,
                'required' => false,
            ));
        }

        if (in_array('price', $fields, true)) {
            $form->add('price', NumberType::class, array(
                'label' => 'vehicle.financing.label.price',
                'scale' => 2,
                'required' => false,
            ));
        }

        if (in_array('specialPayment', $fields, true)) {
            $form->add('specialPayment', NumberType::class, array(
                'label' => 'vehicle.financing.label.specialPayment',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        }

        if (in_array('downpayment', $fields, true)) {
            $form->add('downpayment', NumberType::class, array(
                'label' => 'vehicle.financing.label.downpayment',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        }

        if (in_array('netAmount', $fields, true)) {
            $form->add('netAmount', NumberType::class, array(
                'label' => 'vehicle.financing.label.netAmount',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        }

        if (in_array('fixedInterestRate', $fields, true)) {
            $form->add('fixedInterestRate', NumberType::class, array(
                'label' => 'vehicle.financing.label.fixedInterestRate',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        }

        if (in_array('effectiveInterest', $fields, true)) {
            $form->add('effectiveInterest', NumberType::class, array(
                'label' => 'vehicle.financing.label.effectiveInterest',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        }

        if (in_array('months', $fields, true)) {
            $form->add('months', NumberType::class, array(
                'label' => 'vehicle.financing.label.months',
                'scale' => 0,
                'constraints' => new NotBlank(),
            ));
        }

        if (in_array('kilometersCredit', $fields, true)) {
            $form->add('kilometersCredit', NumberType::class, array(
                'label' => 'vehicle.financing.label.kilometersCredit',
                'help' => 'vehicle.financing.help.kilometersCredit',
                'scale' => 0,
                'required' => false,
            ));
        }

        if (in_array('kilometersLeasing', $fields, true)) {
            $form->add('kilometersLeasing', NumberType::class, array(
                'label' => 'vehicle.financing.label.kilometersLeasing',
                'scale' => 0,
                'constraints' => new NotBlank(),
            ));
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
        }

        if (in_array('totalAmount', $fields, true)) {
            $form->add('totalAmount', NumberType::class, array(
                'label' => 'vehicle.financing.label.totalAmount',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        }

        if (in_array('monthlyInstalment', $fields, true)) {
            $form->add('monthlyInstalment', NumberType::class, array(
                'label' => 'vehicle.financing.label.monthlyInstalment',
                'scale' => 2,
                'constraints' => new NotBlank(),
            ));
        }


        // Legal.
        $form->add('legalDisclaimerNum', TextType::class, array(
            'label' => 'vehicle.financing.label.legalDisclaimerNum',
            'required' => false,
        ));
        if (in_array('specialPaymentDisclaimerNum', $fields, true)) {
            $form->add('specialPaymentDisclaimerNum', TextType::class, array(
                'label' => 'vehicle.financing.label.specialPaymentDisclaimerNum',
                'required' => false,
            ));
        }
        if (in_array('fixedInterestRateDisclaimerNum', $fields, true)) {
            $form->add('fixedInterestRateDisclaimerNum', TextType::class, array(
                'label' => 'vehicle.financing.label.fixedInterestRateDisclaimerNum',
                'required' => false,
            ));
        }
        if (in_array('downpaymentDisclaimerNum', $fields, true)) {
            $form->add('downpaymentDisclaimerNum', TextType::class, array(
                'label' => 'vehicle.financing.label.downpaymentDisclaimerNum',
                'required' => false,
            ));
        }

        $form->add('disclaimer', CKEditorType::class, array(
            'label' => 'vehicle.financing.label.disclaimer',
            'help' => 'vehicle.financing.help.legalDisclaimer',
            'required' => false,
        ));

        /*
        $form->add('disclaimer', CKEditorType::class, array(
            'label' => 'vehicle.financing.label.disclaimer',
            'required' => false,
        ));
        $form->add('legalDisclaimer', TrixType::class, array(
            'label' => 'vehicle.financing.label.legalDisclaimer',
            'help' => 'vehicle.financing.help.legalDisclaimer',
            'required' => false,
        ));
        if (in_array('specialPaymentDisclaimer', $fields, true)) {
            $form->add('specialPaymentDisclaimer', TrixType::class, array(
                'label' => 'vehicle.financing.label.specialPaymentDisclaimer',
                'help' => 'vehicle.financing.help.specialPaymentDisclaimer',
                'required' => false,
            ));
        }
        if (in_array('fixedInterestRateDisclaimer', $fields, true)) {
            $form->add('fixedInterestRateDisclaimer', TrixType::class, array(
                'label' => 'vehicle.financing.label.fixedInterestRateDisclaimer',
                'help' => 'vehicle.financing.help.fixedInterestRateDisclaimer',
                'required' => false,
            ));
        }*/
    }

    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_financing';
    }
}
