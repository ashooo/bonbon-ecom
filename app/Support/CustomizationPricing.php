<?php

namespace App\Support;

class CustomizationPricing
{
    public static function defaults(): array
    {
        return [
            'size' => ['6' => 450, '8' => 700, '10' => 980, '12' => 1280],
            'layers' => ['1' => 0, '2' => 240, '3' => 420, '4' => 620],
            'sponge' => [
                'Vanilla' => 0,
                'Chocolate' => 0,
                'Red Velvet' => 30,
                'Lemon' => 20,
                'Strawberry' => 20,
                'Funfetti' => 30,
            ],
            'filling' => [
                'Chocolate Mousse' => 0,
                'Strawberry Jam' => 10,
                'Vanilla Cream' => 0,
                'Nutella' => 30,
                'Cookies & Cream' => 20,
                'Buttercream' => 0,
            ],
            'frosting' => [
                'white' => 10,
                'ivory' => 10,
                'blush' => 10,
                'sage' => 10,
                'powder_blue' => 10,
                'chocolate' => 10,
                'mocha' => 10,
                'lavender' => 10,
                'custom' => 10,
            ],
            'drip' => ['none' => 0, 'chocolate' => 70, 'white_chocolate' => 80, 'pink' => 80, 'caramel' => 90],
            'topper' => ['none' => 0, 'name' => 120, 'acrylic' => 200, 'edible_print' => 180],
            'rush' => ['no' => 0, 'yes' => 350],
            'toppings' => [
                'per_piece' => 0,
            ],
        ];
    }

    public static function mergeWithDefaults(?array $saved): array
    {
        $defaults = self::defaults();
        if (!is_array($saved)) {
            return $defaults;
        }

        $merged = $defaults;
        foreach ($defaults as $group => $values) {
            if (!isset($saved[$group]) || !is_array($saved[$group])) {
                continue;
            }
            foreach ($values as $key => $defaultValue) {
                $candidate = $saved[$group][$key] ?? null;
                if (!is_numeric($candidate)) {
                    continue;
                }
                $merged[$group][$key] = (float) $candidate;
            }
        }

        return $merged;
    }
}

