<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class Field extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('field', ChoiceType::class, [
            'label' => 'element.label.formField',
            'translation_domain' => 'cms',
            'choices' => $options['fields'],
            'required' => false,
            'choice_translation_domain' => 'messages',
            'attr' => [
                'class' => 'custom-select',
            ],
            'constraints' => new NotBlank(),
        ]);

        $builder->add('type', ChoiceType::class, [
            'label' => 'element.label.formType',
            'translation_domain' => 'cms',
            'choices' => [
                'element.value.formTypeParameter' => 'parameter',
                'element.value.formTypeValue' => 'value',
                'element.value.formTypeUrl' => 'url',
            ],
            'required' => false,
            'attr' => [
                'class' => 'custom-select',
            ],
            'constraints' => new NotBlank(),
        ]);

        $builder->add('value', TextType::class, [
            'required' => false,
            'label' => 'element.label.formValue',
            'translation_domain' => 'cms',
            'constraints' => new NotBlank(),
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'compound' => true,
            'fields' => [],
        ]);
    }
}
