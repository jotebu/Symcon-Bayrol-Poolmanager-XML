<?php

declare(strict_types=1);

final class BPMXML_SnapshotComparator
{
    public static function compare(array $a, array $b): string
    {
        $mapA = self::mapByXmlItem($a);
        $mapB = self::mapByXmlItem($b);

        $relevant = [];
        $ignored = [];
        $all = [];

        foreach ($mapB as $xmlitem => $entryB) {
            if (!isset($mapA[$xmlitem])) {
                $line = '+ ' . self::formatEntry($xmlitem, [], $entryB['attributes'] ?? []);
                $relevant[] = $line;
                $all[] = $line;
                continue;
            }

            $oldAttr = $mapA[$xmlitem]['attributes'] ?? [];
            $newAttr = $entryB['attributes'] ?? [];
            if (json_encode($oldAttr) === json_encode($newAttr)) {
                continue;
            }

            $line = '* ' . self::formatEntry($xmlitem, $oldAttr, $newAttr);
            $all[] = $line;
            if (self::isRelevantSwitchChange($oldAttr, $newAttr)) {
                $relevant[] = $line;
            } else {
                $ignored[] = $line;
            }
        }

        foreach ($mapA as $xmlitem => $entryA) {
            if (!isset($mapB[$xmlitem])) {
                $line = '- ' . self::formatEntry($xmlitem, $entryA['attributes'] ?? [], []);
                $relevant[] = $line;
                $all[] = $line;
            }
        }

        $text = [];
        $text[] = 'Relevante Status-/Schalt-Aenderungen: ' . count($relevant);
        $text[] = 'Ignorierte Messwert-Drift: ' . count($ignored);
        $text[] = 'Alle Aenderungen: ' . count($all);
        $text[] = '';

        if ($relevant) {
            $text[] = '--- RELEVANT ---';
            $text[] = implode("\n", $relevant);
        } else {
            $text[] = 'Keine relevanten Status-/Schalt-Aenderungen erkannt.';
        }

        if ($ignored) {
            $text[] = '';
            $text[] = '--- IGNORIERT: Messwert-Drift / analoge Werte ---';
            $text[] = implode("\n", array_slice($ignored, 0, 80));
            if (count($ignored) > 80) {
                $text[] = '... weitere ignorierte Aenderungen: ' . (count($ignored) - 80);
            }
        }

        return implode("\n", $text);
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

    private static function isRelevantSwitchChange(array $oldAttr, array $newAttr): bool
    {
        $label = strtolower(($newAttr['label'] ?? '') . ' ' . ($oldAttr['label'] ?? ''));
        $unit = (string)($newAttr['unit'] ?? $oldAttr['unit'] ?? '');
        $oldValue = (string)($oldAttr['value'] ?? '');
        $newValue = (string)($newAttr['value'] ?? '');

        if (isset($newAttr['active']) || isset($newAttr['displayed']) || isset($oldAttr['active']) || isset($oldAttr['displayed'])) {
            return true;
        }

        $keywords = ['status', 'betrieb', 'betriebsart', 'out', 'relais', 'licht', 'lampe', 'pumpe', 'filter', 'flock', 'heizung', 'solar', 'eco', 'salz', 'elektrolyse', 'cover', 'abdeckung', 'ventil'];
        $keywordHit = false;
        foreach ($keywords as $keyword) {
            if (strpos($label, $keyword) !== false) {
                $keywordHit = true;
                break;
            }
        }

        if ($keywordHit && $unit === '' && in_array($oldValue, ['0', '1'], true) && in_array($newValue, ['0', '1'], true) && $oldValue !== $newValue) {
            return true;
        }

        return $keywordHit && $oldValue !== $newValue && !self::isAnalogDrift($oldAttr, $newAttr);
    }

    private static function isAnalogDrift(array $oldAttr, array $newAttr): bool
    {
        $unit = (string)($newAttr['unit'] ?? $oldAttr['unit'] ?? '');
        $oldValue = (string)($oldAttr['value'] ?? '');
        $newValue = (string)($newAttr['value'] ?? '');
        $analogUnits = ['pH', 'mV', 'mg/l', 'µA', 'mA', '°C', 'C', 'V', 'l', 'min', '%', 'mS/cm'];
        return $oldValue !== $newValue && in_array($unit, $analogUnits, true) && is_numeric($oldValue) && is_numeric($newValue);
    }

    private static function formatEntry(string $xmlitem, array $oldAttr, array $newAttr): string
    {
        $label = $newAttr['label'] ?? $oldAttr['label'] ?? '';
        $unit = $newAttr['unit'] ?? $oldAttr['unit'] ?? '';
        $oldValue = $oldAttr['value'] ?? ($oldAttr['active'] ?? '');
        $newValue = $newAttr['value'] ?? ($newAttr['active'] ?? '');
        $suffix = $unit !== '' ? ' ' . $unit : '';
        return trim($xmlitem . ' ' . $label . ': ' . $oldValue . $suffix . ' -> ' . $newValue . $suffix);
    }
}
