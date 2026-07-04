<?php

declare(strict_types=1);

final class BPMXML_DiscoveryClassifier
{
    public static function classify(array $a): array
    {
        $label = strtolower((string)($a['label'] ?? ''));
        $unit = (string)($a['unit'] ?? '');
        $value = (string)($a['value'] ?? '');

        if (isset($a['active']) || isset($a['displayed'])) {
            return ['class' => 'alarm', 'confidence' => 95];
        }
        if (strpos($label, 'status') !== false) {
            return ['class' => 'status', 'confidence' => 90];
        }
        if (strpos($label, 'betriebsart') !== false) {
            return ['class' => 'operating_mode', 'confidence' => 90];
        }
        foreach (['out ', 'relais', 'licht', 'pumpe', 'filter', 'heizung', 'solar', 'eco', 'flock', 'salz', 'elektrolyse', 'ventil'] as $kw) {
            if (strpos($label, $kw) !== false && $unit === '' && ($value === '0' || $value === '1')) {
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
        if (in_array($unit, ['pH', 'mV', 'mg/l', 'µA', 'mA', '°C', 'C', 'V', 'l', 'min', '%', 'mS/cm'], true)) {
            return ['class' => 'measurement', 'confidence' => 75];
        }
        if ($unit === '' && is_numeric($value)) {
            return ['class' => 'numeric_status_or_config', 'confidence' => 50];
        }

        return ['class' => 'unknown', 'confidence' => 20];
    }
}
