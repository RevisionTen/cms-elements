<?php

declare(strict_types=1);

namespace RevisionTen\CmsElements\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class FormController extends AbstractController
{
    public function renderFormAction(RequestStack $requestStack, string $formUuid, array $element): Response
    {
        $request = $requestStack->getMasterRequest();

        $defaultData = [];

        $fields = $element['data']['fields'] ?? [];

        foreach ($fields as $fieldConfig) {
            $field = $fieldConfig['field'] ?? null;
            $type = $fieldConfig['type'] ?? null;
            $value = $fieldConfig['value'] ?? null;

            if (null !== $field && null !== $type) {
                if ('value' === $type) {
                    $defaultData[$field] = $value;
                } elseif ('parameter' === $type && $request) {
                    $defaultData[$field] = $request->get($value);
                }
            }
        }

        $controller = 'RevisionTen\Forms\Controller\FormController::renderFormAction';
        if (method_exists(\RevisionTen\Forms\Controller\FormController::class, 'renderCmsForm')) {
            $controller = 'RevisionTen\Forms\Controller\FormController::renderCmsForm';
        }

        return $this->forward($controller, [
            'formUuid' => $formUuid,
            'defaultData' => $defaultData,
        ]);
    }
}
