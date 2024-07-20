<?php

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class UniqueFieldValue extends Constraint
{
    public function __construct(
        mixed $options = null,
        array $groups = null,
        mixed $payload = null,
        public ?string $message = null,
        public ?string $field = null,
        public ?string $entity = null,
    ) {
        parent::__construct($options, $groups, $payload);
    }

    public function getDefaultOption(): string
    {
        return 'field';
    }
}
