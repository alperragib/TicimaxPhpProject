<?php

/**
 * Simple console helper class for test output formatting
 */
class ConsoleHelper
{
    /**
     * Print text to console
     * @param string $text Text to print
     */
    public static function print(string $text): void
    {
        echo $text;
    }

    /**
     * Display data in a formatted way
     * @param string $label Data label
     * @param mixed $value Data value
     */
    public static function displayData(string $label, $value): void
    {
        $displayValue = $value ?? 'N/A';
        if (is_bool($value)) {
            $displayValue = $value ? 'Yes' : 'No';
        } elseif (is_array($value)) {
            $displayValue = json_encode($value, JSON_PRETTY_PRINT);
        } elseif (is_object($value)) {
            $displayValue = json_encode($value, JSON_PRETTY_PRINT);
        }
        
        echo "  - $label: $displayValue\n";
    }

    /**
     * Print a section header
     * @param string $title Section title
     */
    public static function printHeader(string $title): void
    {
        echo "\n" . str_repeat("=", strlen($title)) . "\n";
        echo $title . "\n";
        echo str_repeat("=", strlen($title)) . "\n\n";
    }

    /**
     * Print a separator line
     * @param int $length Length of separator
     */
    public static function printSeparator(int $length = 50): void
    {
        echo str_repeat("-", $length) . "\n";
    }
} 