<?php

declare(strict_types=1);

final class BPMXML_DiscoveryExporter
{
    public static function toCsv(array $results, bool $includeError = true, bool $includeClassAndConfidence = true): string
    {
        $csv = $includeClassAndConfidence
            ? "xmlitem;class;confidence;valid;label;unit;value;active;displayed;attributes;duration_ms"
            : "xmlitem;valid;label;unit;value;active;displayed;attributes;duration_ms";
        if ($includeError) {
            $csv .= ";error";
        }
        $csv .= "\n";

        foreach ($results as $entry) {
            $a = $entry['attributes'] ?? [];
            $csv .= self::csv($entry['xmlitem'] ?? '') . ';';
            if ($includeClassAndConfidence) {
                $csv .= self::csv($entry['class'] ?? '') . ';'
                    . self::csv((string)($entry['confidence'] ?? '')) . ';';
            }
            $csv .= (!empty($entry['valid']) ? '1' : '0') . ';'
                . self::csv($a['label'] ?? '') . ';'
                . self::csv($a['unit'] ?? '') . ';'
                . self::csv($a['value'] ?? '') . ';'
                . self::csv($a['active'] ?? '') . ';'
                . self::csv($a['displayed'] ?? '') . ';'
                . self::csv(json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . ';'
                . self::csv((string)($entry['duration_ms'] ?? ''));
            if ($includeError) {
                $csv .= ';' . self::csv($entry['error'] ?? '');
            }
            $csv .= "\n";
        }

        return $csv;
    }

    public static function toJson(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    public static function toPhpDefinition(array $results): string
    {
        $out = "[\n";
        foreach ($results as $entry) {
            if (empty($entry['valid']) || empty($entry['attributes'])) {
                continue;
            }
            $a = $entry['attributes'];
            $label = $a['label'] ?? ($entry['xmlitem'] ?? 'XML');
            $ident = self::makeIdent($label, (string)($entry['xmlitem'] ?? ''));
            $profile = self::suggestProfile($a['unit'] ?? '', $a);
            $category = isset($a['active']) || isset($a['displayed']) ? 'Alarms' : 'Measurements';
            $out .= "    ['xml' => '" . ($entry['xmlitem'] ?? '') . "', 'ident' => '" . $ident . "', 'name' => '" . addslashes((string)$label) . "', 'category' => '" . $category . "', 'profile' => '" . $profile . "', 'kind' => 'explored'],\n";
        }
        return $out . "]";
    }

    public static function toMarkdown(array $results): string
    {
        $out = "| XML | Class | Label | Unit | Value | Confidence |\n|---|---|---|---|---|---|\n";
        foreach ($results as $entry) {
            if (empty($entry['valid'])) {
                continue;
            }
            $a = $entry['attributes'] ?? [];
            $out .= '| ' . ($entry['xmlitem'] ?? '')
                . ' | ' . ($entry['class'] ?? '')
                . ' | ' . str_replace('|', '/', (string)($a['label'] ?? ''))
                . ' | ' . ($a['unit'] ?? '')
                . ' | ' . ($a['value'] ?? ($a['active'] ?? ''))
                . ' | ' . ($entry['confidence'] ?? '')
                . " |\n";
        }
        return $out;
    }

    private static function csv($value): string
    {
        return '"' . str_replace('"', '""', (string)$value) . '"';
    }

    private static function suggestProfile(string $unit, array $attributes): string
    {
        $map = [
            'pH' => 'BPMXML.pH',
            'mg/l' => 'BPMXML.mg_l',
            'mV' => 'BPMXML.mV',
            'C' => 'BPMXML.C',
            '°C' => 'BPMXML.C',
            'V' => 'BPMXML.V',
            'l' => 'BPMXML.l',
            '%' => 'BPMXML.percent',
            'min' => 'BPMXML.min',
            'µA' => 'BPMXML.microA',
            'mA' => 'BPMXML.mA',
            'mS/cm' => 'BPMXML.mScm'
        ];
        return $map[$unit] ?? (isset($attributes['value']) ? '' : 'BPMXML.Alarm');
    }

    private static function makeIdent(string $label, string $xmlitem): string
    {
        $base = preg_replace('/[^A-Za-z0-9]/', '', ucwords($label));
        if ($base === '') {
            $base = 'XML' . str_replace('.', '_', $xmlitem);
        }
        if (preg_match('/^[0-9]/', $base)) {
            $base = 'XML' . $base;
        }
        return $base;
    }
}
