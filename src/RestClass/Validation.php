<?php

namespace ZazaScandiweb\RestClass;

class Validation
{
    public static function validateFormData(array $data): array
    {
        $length = count($data);
        if ($length !== 5) {
            http_response_code(400);
            echo json_encode(["error" => "Data is not right"]);
            exit;
        }
        foreach ($data as $key => &$value) {
            if ($key === "sku") {
                $value = str_replace(' ', '', $value);
            } elseif (is_array($value)) {
                // Trim values in the specialAttribute array
                foreach ($value as &$attribute) {
                    foreach ($attribute as $attrKey => &$attrValue) {
                        $attrValue = str_replace(' ', '', $attrValue);
                    }
                }
            } else {
                // Trim regular attribute values
                $value = trim($value);
            }
        }
        return $data;
    }
    public static function validateMassDeleteFunction(array $data): void
    {
        if (is_array($data) && count(array_filter(array_keys($data), 'is_string')) === 0) {
        } else {
            // $data is not a valid numeric array
            http_response_code(400);
            echo json_encode([
                "error" => "Invalid data format. Expected a numeric array."
            ]);
            exit;
        }
    }
}
