<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\Vehicle;

use RevisionTen\CMS\Form\Elements\Element;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

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

        $formModifier = static function (FormInterface $form = null, ?string $financingType = null) {
            if ($form) {
                if ('cash' === $financingType) {
                    $form->add('price', NumberType::class, array(
                        'label' => 'vehicle.financing.label.cashPrice',
                        'scale' => 2,
                        'constraints' => new NotBlank(),
                    ));
                } elseif ('credit' === $financingType) {
                    $form->add('price', NumberType::class, array(
                        'label' => 'vehicle.financing.label.price',
                        'scale' => 2,
                        'constraints' => new NotBlank(),
                    ));
                } elseif ('leasingPrivate' === $financingType) {
                    $form->add('price', NumberType::class, array(
                        'label' => 'vehicle.financing.label.price',
                        'scale' => 2,
                        'constraints' => new NotBlank(),
                    ));
                } elseif ('leasingBusiness' === $financingType) {
                    $form->remove('price');
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

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'cms_elements_vehicle_financing';
    }
}
