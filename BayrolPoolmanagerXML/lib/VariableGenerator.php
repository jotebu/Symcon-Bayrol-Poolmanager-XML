<?php

declare(strict_types=1);

final class BPMXML_VariableGenerator
{
    public function propose(array $database, int $minConfidence = 80): array
    {
        $proposals = [];
        foreach ($database as $xmlitem => $entry) {
            $confidence = (int)($entry['confidence'] ?? 0);
            if ($confidence < $minConfidence) {
                continue;
            }

            $attributes = $entry['attributes'] ?? [];
            $class = (string)($entry['class'] ?? 'unknown');
            $label = (string)($attributes['label'] ?? $xmlitem);
            $unit = (string)($attributes['unit'] ?? '');

            $proposals[] = [
                'xml' => (string)$xmlitem,
                'ident' => $this->makeIdent($label, (string)$xmlitem),
                'name' => $label,
                'category' => $this->categoryForClass($class),
                'variable_type' => $this->variableType($class, $attributes),
                'profile' => $this->profileForUnit($unit, $class, $attributes),
                'class' => $class,
                'confidence' => $confidence,
                'write_candidate' => $this->isWriteCandidate($class),
                'enabled_by_default' => $this->enabledByDefault($class, $confidence)
            ];
        }

        usort($proposals, static fn(array $a, array $b): int => [$a['category'], $a['name']] <=> [$b['category'], $b['name']]);
        return $proposals;
    }

    public function toPhpItems(array $proposals): string
    {
        $out = "[\n";
        foreach ($proposals as $p) {
            $out .= "    ['xml' => '" . $p['xml'] . "', 'ident' => '" . $p['ident'] . "', 'name' => '" . addslashes($p['name']) . "', 'category' => '" . $p['category'] . "', 'profile' => '" . $p['profile'] . "', 'kind' => '" . $p['class'] . "'],\n";
        }
        return $out . "]";
    }

    private function categoryForClass(string $class): string
    {
        return match ($class) {
            'alarm' => 'Alarms',
            'setpoint' => 'Setpoints',
            'limit' => 'Limits',
            'status', 'output_status', 'output_or_device_state', 'digital_status_or_config', 'operating_mode' => 'Status',
            'timer_or_runtime', 'counter_or_statistic' => 'Statistics',
            'calibration' => 'Calibration',
            default => 'Measurements'
        };
    }

    private function variableType(string $class, array $attributes): string
    {
        $value = (string)($attributes['value'] ?? $attributes['active'] ?? '');
        if (in_array($class, ['alarm', 'output_status', 'digital_status_or_config'], true) && in_array($value, ['0', '1'], true)) {
            return 'boolean';
        }
        if (is_numeric($value)) {
            return strpos($value, '.') !== false ? 'float' : 'integer';
        }
        return 'string';
    }

    private function profileForUnit(string $unit, string $class, array $attributes): string
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
        if (isset($map[$unit])) {
            return $map[$unit];
        }
        if ($class === 'alarm') {
            return 'BPMXML.Alarm';
        }
        return '';
    }

    private function isWriteCandidate(string $class): bool
    {
        return in_array($class, ['setpoint', 'operating_mode', 'output_status', 'output_or_device_state'], true);
    }

    private function enabledByDefault(string $class, int $confidence): bool
    {
        return in_array($class, ['measurement', 'temperature', 'status', 'alarm', 'output_status'], true) && $confidence >= 85;
    }

    private function makeIdent(string $label, string $xmlitem): string
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
