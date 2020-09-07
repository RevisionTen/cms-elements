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


        return $this->forward('RevisionTen\Forms\Controller\FormController::renderFormAction', [
            'formUuid' => $formUuid,
            'defaultData' => $defaultData,
        ]);
    }
}
