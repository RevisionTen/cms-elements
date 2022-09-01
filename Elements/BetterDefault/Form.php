<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Elements\BetterDefault;

use Doctrine\ORM\EntityManagerInterface;
use RevisionTen\CMS\Form\Elements\Element;
use RevisionTen\CmsElements\Elements\BetterDefault\Form\Field;
use RevisionTen\Forms\Model\FormRead;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class Form extends Element
{
    private array $choices = [];

    private array $forms = [];

    private ?array $config;

    public function __construct(EntityManagerInterface $entityManager, ?array $config = null)
    {
        // The config is optional, it might not be configured in the service definition (for legacy reasons).
        $this->config = $config;

        /**
         * @var FormRead[] $forms
         */
        $forms = $entityManager->getRepository(FormRead::class)->findBy(['deleted' => false]);
        if ($forms) {
            foreach ($forms as $form) {
                $this->choices[$form->getTitle()] = $form->getUuid();
                $this->forms[$form->getUuid()] = $form;
            }
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('formUuid', ChoiceType::class, [
            'label' => 'element.label.formUuid',
            'translation_domain' => 'cms',
            'required' => false,
            'choices' => $this->choices,
            'choice_translation_domain' => 'messages',
            'attr' => [
                'class' => 'custom-select',
                'data-condition' => true,
            ],
            'constraints' => new NotBlank(),
        ]);

        $formModifier = function (FormInterface $form = null, ?string $formUuid = null) {
            if ($form) {
                if (!empty($formUuid)) {
                    $fields = $this->getFields($formUuid);

                    $form->add('fields', CollectionType::class, [
                        'required' => false,
                        'label' => 'element.label.fields',
                        'translation_domain' => 'cms',
                        'entry_type' => Field::class,
                        'entry_options' => [
                            'label' => false,
                            'fields' => $fields,
                        ],
                        'allow_add' => true,
                        'allow_delete' => true,
                    ]);
                } else {
                    $form->remove('fields');
                }
            }
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $formUuid = $data['formUuid'] ?? null;
            $formModifier($event->getForm(), $formUuid);
        });

        $builder->get('formUuid')->addEventListener(FormEvents::POST_SUBMIT, static function (FormEvent $event) use ($formModifier) {
            $formUuid = $event->getForm()->getData();
            $formModifier($event->getForm()->getParent(), $formUuid);
        });
    }

    private function getFields(string $formUuid): array
    {
        /**
         * @var $form FormRead|null
         */
        $form = $this->forms[$formUuid] ?? null;
        if (empty($form)) {
            return [];
        }

        $items = $form->getPayload()['items'] ?? null;
        if (empty($items)) {
            return [];
        }

        $fields = [];

        foreach ($items as $item) {
            // Get the available data vars for this form item class.
            $vars = null;
            $itemName = $item['itemName'] ?? null;
            if ($itemName && $this->config) {
                $itemClass = $this->config['item_types'][$itemName]['class'] ?? null;
                if ($itemClass && method_exists($itemClass, 'getVariables')) {
                    $vars = $itemClass::getVariables($item['data']);
                }
            }

            // Add the data vars to the available fields.
            $name = $item['data']['name'] ?? null;
            $label = $item['data']['label'] ?? null;
            if ($name && $label) {
                if ($vars) {
                    foreach ($vars as $var) {
                        $fields[$label.' ('.$var.')'] = $var;
                    }
                } else {
                    // Fallback to simple single value variable.
                    $fields[$label.' ('.$name.')'] = $name;
                }
            }
        }

        return $fields;
    }

    public function getBlockPrefix(): string
    {
        return 'cms_form';
    }
}
