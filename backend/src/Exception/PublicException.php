<?php

namespace App\Exception;

/**
 * Exception, deren Message oeffentlich ist = Benutzer angezeigt werden darf/soll.
 */
class PublicException extends \Exception
{
    public const DEFAULT_MESSAGE = '';

    /** @var string[] Name der betroffenen Entity-Felder (optional). */
    private ?array $fields = null;

    public function __construct(?string $msg = null, int $code = 0, ?\Throwable $prevException = null)
    {
        parent::__construct($msg ?? static::DEFAULT_MESSAGE, $code, $prevException);
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function setFields(array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }
}
