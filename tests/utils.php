<?php
// Helper for test scripts to load project context and print values succinctly
require_once __DIR__ . '/../include.php';

function printLine(string $text): void
{
    echo $text . "<br/>\n";
}

function stringifyValue(mixed $value): string
{
    if (is_null($value)) {
        return 'null';
    }
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_scalar($value)) {
        $text = (string)$value;
        return strlen($text) > 120 ? substr($text, 0, 117) . '...' : $text;
    }
    if (is_array($value)) {
        return 'array(count=' . count($value) . ')';
    }
    if (is_object($value)) {
        return 'object(' . get_class($value) . ')';
    }
    return gettype($value);
}

function dumpObjectAttributes(object $object): void
{
    $methods = get_class_methods($object) ?: [];
    foreach ($methods as $method) {
        if (!preg_match('/^get[A-Z]/', $method)) {
            continue;
        }
        try {
            $ref = new ReflectionMethod($object, $method);
            if ($ref->getNumberOfRequiredParameters() > 0) {
                continue;
            }
            $value = $object->$method();
            printLine(' - ' . $method . ' = ' . stringifyValue($value));
        } catch (Throwable $e) {
            printLine(' - ' . $method . ' = <error ' . $e->getMessage() . '>');
        }
    }
}

function dumpValue(string $label, mixed $value): void
{
    printLine("=== $label ===");

    if (is_array($value)) {
        printLine('count=' . count($value));
        $idx = 0;
        foreach ($value as $item) {
            if ($idx >= 3) {
                printLine(' ... (trimmed)');
                break;
            }
            printLine('#'.$idx.' -> ' . stringifyValue($item));
            if (is_object($item)) {
                dumpObjectAttributes($item);
            }
            $idx++;
        }
    } elseif (is_object($value)) {
        printLine(stringifyValue($value));
        dumpObjectAttributes($value);
    } else {
        printLine(stringifyValue($value));
    }

    printLine('');
}
