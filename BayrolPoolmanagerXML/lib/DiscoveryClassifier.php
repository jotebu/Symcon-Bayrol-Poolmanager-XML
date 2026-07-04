<?php

declare(strict_types=1);

final class BPMXML_DiscoveryClassifier
{
    public function classify(array $attributes): array
    {
        $label = strtolower((string)($attributes['label'] ?? ''));
        $unit = (string)($attributes['unit'] ?? '');
        $value = (string)($attributes['value'] ?? '');

        if (isset($attributes['active']) || isset($attributes['displayed'])) {
            return ['class' => 'alarm', 'confidence' => 95];
        }
        if (strpos($label, 'status') !== false) {
            return ['class' => 'status', 'confidence' => 90];
        }
        if (strpos($label, 'betriebsart') !== false) {
            return ['class' => 'operating_mode', 'confidence' => 90];
        }

        $outputKeywords = ['out ', 'relais', 'licht', 'lampe', 'pumpe', 'filter', 'heizung', 'solar', 'eco', 'flock', 'salz', 'elektrolyse', 'ventil'];
        foreach ($outputKeywords as $keyword) {
            if (strpos($label, $keyword) !== false && $unit === '' && ($value === '0' || $value === '1')) {
                return ['class' => 'output_status', 'confidence' => 88];
            }
        }

        if (strpos($label, 'alarm') !== false || strpos($label, 'grenze') !== false) {
            return ['class' => 'limit', 'confidence' => 80];
        }
        if (strpos($label, 'soll') !== false || strpos($label, 'setpoint') !== false) {
            return ['class' => 'setpoint', 'confidence' => 80];
        }
        if (strpos($label, 'kalib') !== false || preg_match('/(ph|mv|cl).*\d/', $label)) {
            return ['class' => 'calibration', 'confidence' => 70];
        }

        $analogUnits = ['pH', 'mV', 'mg/l', 'µA', 'mA', '°C', 'C', 'V', 'l', 'min', '%', 'mS/cm'];
        if (in_array($unit, $analogUnits, true)) {
            return ['class' => 'measurement', 'confidence' => 75];
        }
        if ($unit === '' && is_numeric($value)) {
            return ['class' => 'numeric_status_or_config', 'confidence' => 50];
        }

        return ['class' => 'unknown', 'confidence' => 20];
    }
}
