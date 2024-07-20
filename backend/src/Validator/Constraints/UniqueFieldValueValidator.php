<?php

namespace App\Validator\Constraints;

use App\Repository\DefaultRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validator, der prueft, ob ein Feldwert innerhalb der Entitaetsmenge einmalig ist.
 */
class UniqueFieldValueValidator extends ConstraintValidator
{
    final public const ALLOW_UNIQUE_VALIDATION_GROUP = 'AllowUniqueValidation';

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Prueft, ob der Wert des Feldes (ueber field festgelegt) einmalig innerhalb der Entitaetsmenge ist.
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (empty($value)) {
            return;
        }

        $class = !empty($constraint->entity) ? $constraint->entity : $this->context->getClassName();
        $fieldname = !empty($constraint->field) ? $constraint->field : $this->context->getPropertyName();
        $entity = $this->context->getObject();

        /* @var DefaultRepository $repo */
        $repo = $this->em->getRepository($class);
        $queryBuilder = $repo->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.' . $fieldname . ' = :value')
            ->setParameter('value', $value);

        // If the entity is not new, exclude it from the check
        if (method_exists($entity, 'getId') && $entity->getId()) {
            $queryBuilder->andWhere('e != :id')
                ->setParameter('id', $entity->getId());
        }

        $count = $queryBuilder->getQuery()->getSingleScalarResult();

        if ($count > 0) {
            $constraint->payload = $repo->findOneBy([$fieldname => $value]);
            $this->context->buildViolation($constraint->message)
                ->setParameter('%s', $value)
                ->addViolation();
        }
    }
}
