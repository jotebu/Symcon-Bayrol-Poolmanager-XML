<?php

declare(strict_types=1);

final class BPMXML_DiscoveryHistory
{
    public static function append(string $historyJson, array $run, int $maxRuns = 20): array
    {
        $history = json_decode($historyJson, true);
        if (!is_array($history)) {
            $history = [];
        }

        $history[] = [
            'time' => date('Y-m-d H:i:s'),
            'summary' => $run['summary'] ?? '',
            'range' => $run['range'] ?? [],
            'stats' => $run['stats'] ?? [],
            'result_count' => isset($run['results']) && is_array($run['results']) ? count($run['results']) : 0,
            'results' => $run['results'] ?? []
        ];

        if (count($history) > $maxRuns) {
            $history = array_slice($history, -$maxRuns);
        }

        return $history;
    }

    public static function compareRuns(array $olderRun, array $newerRun): string
    {
        $old = self::mapByXmlItem($olderRun['results'] ?? []);
        $new = self::mapByXmlItem($newerRun['results'] ?? []);

        $added = [];
        $removed = [];
        $changed = [];

        foreach ($new as $xmlitem => $entry) {
            if (!isset($old[$xmlitem])) {
                $added[] = $xmlitem;
                continue;
            }
            $oldAttr = $old[$xmlitem]['attributes'] ?? [];
            $newAttr = $entry['attributes'] ?? [];
            if (json_encode($oldAttr) !== json_encode($newAttr)) {
                $changed[] = $xmlitem;
            }
        }

        foreach ($old as $xmlitem => $entry) {
            if (!isset($new[$xmlitem])) {
                $removed[] = $xmlitem;
            }
        }

        $lines = [];
        $lines[] = 'Discovery History Vergleich';
        $lines[] = 'Aelterer Lauf: ' . ($olderRun['time'] ?? '-');
        $lines[] = 'Neuerer Lauf: ' . ($newerRun['time'] ?? '-');
        $lines[] = 'Neu: ' . count($added);
        $lines[] = 'Entfernt: ' . count($removed);
        $lines[] = 'Geaendert: ' . count($changed);
        $lines[] = '';

        if ($added) {
            $lines[] = '--- NEU ---';
            $lines[] = implode("\n", array_slice($added, 0, 100));
        }
        if ($removed) {
            $lines[] = '--- ENTFERNT ---';
            $lines[] = implode("\n", array_slice($removed, 0, 100));
        }
        if ($changed) {
            $lines[] = '--- GEAENDERT ---';
            $lines[] = implode("\n", array_slice($changed, 0, 100));
        }

        return implode("\n", $lines);
    }

    public static function summarize(array $history): string
    {
        $lines = [];
        $lines[] = 'Discovery History';
        $lines[] = 'Gespeicherte Laeufe: ' . count($history);
        foreach ($history as $idx => $run) {
            $lines[] = ($idx + 1) . '. ' . ($run['time'] ?? '-') . ' - ' . ($run['summary'] ?? '') . ' - Ergebnisse: ' . ($run['result_count'] ?? 0);
        }
        return implode("\n", $lines);
    }

    private static function mapByXmlItem(array $data): array
    {
        $map = [];
        foreach ($data as $entry) {
            if (isset($entry['xmlitem'])) {
                $map[(string)$entry['xmlitem']] = $entry;
            }
        }
        return $map;
    }
}
