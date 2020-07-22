<?php

declare(strict_types = 1);

namespace App\Web\Actions;

use App\Exceptions\Http\Http400BadRequestException;

trait FormTrait
{
    /**
     * @param array $formData
     * @param array $mandatoryFields
     * @throws Http400BadRequestException
     */
    private static function checkIfAllMandatoryFieldsArePresent(array $formData, array $mandatoryFields)
    {
        $missingFields = array_values(array_diff($mandatoryFields, array_keys($formData)));
        if (count($missingFields) != 0) {
            throw new Http400BadRequestException("POST body doesn't contains following mandatory fields: " . implode(', ', $missingFields));
        }
    }

    /**
     * @param array $formData
     * @param array $expectedFields
     * @throws Http400BadRequestException
     */
    private static function checkIfSomeFieldsUnexpected(array $formData, array $expectedFields)
    {
        $unexpectedFields = array_values(array_diff(array_keys($formData), $expectedFields));
        if (count($unexpectedFields) != 0) {
            throw new Http400BadRequestException('POST body contains following unexpected fields: ' . implode(', ', $unexpectedFields));
        }
    }

}
