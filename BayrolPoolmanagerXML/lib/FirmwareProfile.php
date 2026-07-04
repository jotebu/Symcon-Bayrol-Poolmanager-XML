<?php

declare(strict_types=1);

final class BPMXML_FirmwareProfile
{
    public static function create(string $firmware, array $database): array
    {
        return [
            'firmware' => $firmware !== '' ? $firmware : 'unknown',
            'created_at' => date('Y-m-d H:i:s'),
            'xml_count' => count($database),
            'classes' => self::classCounts($database),
            'items' => self::compactItems($database)
        ];
    }

    public static function compare(array $oldProfile, array $newProfile): string
    {
        $oldItems = $oldProfile['items'] ?? [];
        $newItems = $newProfile['items'] ?? [];

        $added = [];
        $removed = [];
        $classChanged = [];
        $labelChanged = [];

        foreach ($newItems as $xmlitem => $item) {
            if (!isset($oldItems[$xmlitem])) {
                $added[] = $xmlitem . ' ' . ($item['label'] ?? '');
                continue;
            }
            if (($oldItems[$xmlitem]['class'] ?? '') !== ($item['class'] ?? '')) {
                $classChanged[] = $xmlitem . ': ' . ($oldItems[$xmlitem]['class'] ?? '') . ' -> ' . ($item['class'] ?? '');
            }
            if (($oldItems[$xmlitem]['label'] ?? '') !== ($item['label'] ?? '')) {
                $labelChanged[] = $xmlitem . ': ' . ($oldItems[$xmlitem]['label'] ?? '') . ' -> ' . ($item['label'] ?? '');
            }
        }

        foreach ($oldItems as $xmlitem => $item) {
            if (!isset($newItems[$xmlitem])) {
                $removed[] = $xmlitem . ' ' . ($item['label'] ?? '');
            }
        }

        $lines = [];
        $lines[] = 'Firmware Profil Vergleich';
        $lines[] = 'Alt: ' . ($oldProfile['firmware'] ?? 'unknown') . ' (' . ($oldProfile['created_at'] ?? '-') . ')';
        $lines[] = 'Neu: ' . ($newProfile['firmware'] ?? 'unknown') . ' (' . ($newProfile['created_at'] ?? '-') . ')';
        $lines[] = 'Neu hinzugefuegt: ' . count($added);
        $lines[] = 'Entfernt: ' . count($removed);
        $lines[] = 'Klasse geaendert: ' . count($classChanged);
        $lines[] = 'Label geaendert: ' . count($labelChanged);
        $lines[] = '';

        if ($added) {
            $lines[] = '--- NEU ---';
            $lines[] = implode("\n", array_slice($added, 0, 100));
        }
        if ($removed) {
            $lines[] = '--- ENTFERNT ---';
            $lines[] = implode("\n", array_slice($removed, 0, 100));
        }
        if ($classChanged) {
            $lines[] = '--- KLASSE GEAENDERT ---';
            $lines[] = implode("\n", array_slice($classChanged, 0, 100));
        }
        if ($labelChanged) {
            $lines[] = '--- LABEL GEAENDERT ---';
            $lines[] = implode("\n", array_slice($labelChanged, 0, 100));
        }

        return implode("\n", $lines);
    }

    public static function extractFirmwareFromDiscovery(array $database): string
    {
        foreach ($database as $entry) {
            $a = $entry['attributes'] ?? [];
            $label = mb_strtolower((string)($a['label'] ?? ''));
            if (strpos($label, 'firmware') !== false || strpos($label, 'version') !== false) {
                return (string)($a['value'] ?? $a['label'] ?? '');
            }
        }
        return 'unknown';
    }

    private static function classCounts(array $database): array
    {
        $counts = [];
        foreach ($database as $entry) {
            $class = (string)($entry['class'] ?? 'unknown');
            $counts[$class] = ($counts[$class] ?? 0) + 1;
        }
        ksort($counts);
        return $counts;
    }

    private static function compactItems(array $database): array
    {
        $items = [];
        foreach ($database as $xmlitem => $entry) {
            $a = $entry['attributes'] ?? [];
            $items[(string)$xmlitem] = [
                'label' => (string)($a['label'] ?? ''),
                'unit' => (string)($a['unit'] ?? ''),
                'class' => (string)($entry['class'] ?? 'unknown'),
                'confidence' => (int)($entry['confidence'] ?? 0)
            ];
        }
        ksort($items);
        return $items;
    }
}
