<?php

namespace App\Controller;

use App\Utils\JsonResponse;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class BaseController
{
    public function __construct(private readonly SerializerInterface $serializer, private readonly FormFactoryInterface $formFactory)
    {
    }

    public function getJsonResponse($data, array $groups = ['Default'], $status = 200): JsonResponse
    {
        $context = new SerializationContext();
        $context->setGroups($groups);
        $context->enableMaxDepthChecks();

        return new JsonResponse($this->serializer, $data, $status, [], $context);
    }

    /**
     * Ueberschreibt den options-Default. Creates and returns a Form instance from the type of the form.
     *
     * @param string $type    The fully qualified class name of the form type
     * @param mixed  $data    The initial data for the form
     * @param array  $options Options for the form
     *
     * @throws InvalidArgumentException
     *
     * @return Form
     */
    protected function createForm(string $type, mixed $data = null, array $options = []): FormInterface
    {
        $defaultOptions = [
            'method' => 'POST',
        ];
        $options = array_replace($defaultOptions, $options);

        return $this->formFactory->create($type, $data, $options);
    }
}