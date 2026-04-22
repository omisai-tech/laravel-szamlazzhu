<?php

namespace Omisai\Szamlazzhu;

trait FieldsValidationTrait
{
    /**
     * @throws SzamlaAgentException
     */
    protected function validateFields(): void
    {
        if (!isset($this->requiredFields)) {
            throw new SzamlaAgentException(sprintf('The %s::requiredFields property is not defined, but mandantory if %s::validateFields() used. ', self::class, FieldsValidationTrait::class));
        }

        foreach ($this->requiredFields as $field) {
            if (!isset($this->{$field})) {
                throw new SzamlaAgentException(sprintf('The %s::%s property is not defined, but it is set at the %s::requiredFields.', self::class, $field, self::class));
            }

            if ($this->{$field} === null || $this->{$field} === '') {
                throw new SzamlaAgentException(sprintf('The %s::%s field is mandatory and cannot be null, empty string', self::class, $field));
            }
        }
    }
}
