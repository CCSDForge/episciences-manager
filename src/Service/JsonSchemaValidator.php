<?php

namespace App\Service;

use Swaggest\JsonSchema\Schema;
use Swaggest\JsonSchema\Exception as JsonSchemaException;

class JsonSchemaValidator
{
    private string $schemasDir;

    public function __construct(string $projectDir)
    {
        $this->schemasDir = $projectDir . '/config/schemas';
    }

    /**
     * Validate data against a JSON schema.
     *
     * @param array<string, mixed> $data The data to validate
     * @param string $schemaName The schema file name (without .json extension)
     * @return array{valid: bool, errors: array<string>}
     */
    public function validate(array $data, string $schemaName): array
    {
        $schemaPath = $this->schemasDir . '/' . $schemaName . '.json';

        if (!file_exists($schemaPath)) {
            return [
                'valid' => false,
                'errors' => ["Schema file not found: {$schemaName}.json"],
            ];
        }

        $schemaContent = file_get_contents($schemaPath);
        if ($schemaContent === false) {
            return [
                'valid' => false,
                'errors' => ["Unable to read schema file: {$schemaName}.json"],
            ];
        }

        $schemaObject = json_decode($schemaContent);
        if ($schemaObject === null) {
            return [
                'valid' => false,
                'errors' => ['Invalid schema JSON'],
            ];
        }

        try {
            $schema = Schema::import($schemaObject);
            // Convert array to object for validation
            $dataObject = json_decode(json_encode($data));
            $schema->in($dataObject);

            return [
                'valid' => true,
                'errors' => [],
            ];
        } catch (JsonSchemaException $e) {
            return [
                'valid' => false,
                'errors' => [$e->getMessage()],
            ];
        }
    }
}