<?php

declare(strict_types=1);

namespace Core\Components\Models\Components;

class SQLComponent extends AbstractComponent
{
    #[\Override]
    public function fill(string $name, mixed $value, array $options = []): self
    {
        if (!array_key_exists($name, $this->variableMap)) {
            $options = implode(',', array_keys($this->variableMap));

            throw new \Exception("Cannot fill non-existent value '{$name}' options are '{$options}'");
        }

        if (!$value instanceof ComponentInterface && !is_scalar($value)) {
            throw new \Exception('You can only fill HTMLComponents with scalar values or other components');
        }

        $pseudonyms = $this->variableMap[$name];
        foreach ($pseudonyms as $pseudonym) {
            $this->sockets[$pseudonym] = $value; // TODO: This needs changed to be actually useful for SQL
        }

        return $this;
    }
}
