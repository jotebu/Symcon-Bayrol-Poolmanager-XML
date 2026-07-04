<?php

declare(strict_types=1);

final class BPMXML_DiscoveryDatabase
{
    public static function merge(string $existingJson, array $results): array
    {
        $db = json_decode($existingJson, true);
        if (!is_array($db)) {
            $db = [];
        }

        $now = date('Y-m-d H:i:s');
        foreach ($results as $entry) {
            if (empty($entry['valid'])) {
                continue;
            }

            $key = (string)$entry['xmlitem'];
            if (!isset($db[$key])) {
                $db[$key] = [
                    'xmlitem' => $key,
                    'first_seen' => $now,
                    'scan_count' => 0,
                    'notes' => '',
                    'user_mapping' => ''
                ];
            }

            $db[$key]['last_seen'] = $now;
            $db[$key]['scan_count'] = (int)($db[$key]['scan_count'] ?? 0) + 1;
            $db[$key]['class'] = $entry['class'] ?? 'unknown';
            $db[$key]['confidence'] = $entry['confidence'] ?? 0;
            $db[$key]['attributes'] = $entry['attributes'] ?? [];
        }

        ksort($db);
        return $db;
    }

    public static function report(array $results, array $database, int $scanned, int $valid, int $errors): string
    {
        $classes = [];
        foreach ($results as $entry) {
            $class = $entry['class'] ?? 'unknown';
            $classes[$class] = ($classes[$class] ?? 0) + 1;
        }
        ksort($classes);

        $lines = [];
        $lines[] = 'Bayrol PM5 Discovery Report';
        $lines[] = 'Zeit: ' . date('Y-m-d H:i:s');
        $lines[] = 'Abfragen: ' . $scanned;
        $lines[] = 'Gueltig: ' . $valid;
        $lines[] = 'Fehler/ungueltig: ' . $errors;
        $lines[] = 'Discovery-DB Eintraege gesamt: ' . count($database);
        $lines[] = '';
        $lines[] = 'Klassen im aktuellen Lauf:';

        foreach ($classes as $class => $count) {
            $lines[] = '- ' . $class . ': ' . $count;
        }

        return implode("\n", $lines);
    }
}
