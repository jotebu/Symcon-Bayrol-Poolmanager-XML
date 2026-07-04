<?php

declare(strict_types=1);

final class BPMXML_DiscoveryClassifier
{
    public function classify(array $attributes): array
    {
        $labelRaw = (string)($attributes['label'] ?? '');
        $label = mb_strtolower($labelRaw);
        $unit = (string)($attributes['unit'] ?? '');
        $value = (string)($attributes['value'] ?? '');

        if (isset($attributes['active']) || isset($attributes['displayed'])) {
            return $this->result('alarm', 95, 'active/displayed attributes present');
        }

        if ($this->containsAny($label, ['status', 'zustand', 'state'])) {
            if ($this->isBooleanLike($value) || $unit === '') {
                return $this->result('status', 92, 'status keyword and digital-like value');
            }
            return $this->result('status', 82, 'status keyword');
        }

        if ($this->containsAny($label, ['betriebsart', 'mode', 'operation mode'])) {
            return $this->result('operating_mode', 92, 'operating mode keyword');
        }

        if ($this->containsAny($label, ['out ', 'out1', 'out2', 'out3', 'out4', 'relais', 'relay', 'licht', 'light', 'lampe', 'pumpe', 'pump', 'filter', 'heizung', 'heater', 'solar', 'eco', 'flock', 'salz', 'elektrolyse', 'ventil', 'valve'])) {
            if ($unit === '' && $this->isBooleanLike($value)) {
                return $this->result('output_status', 94, 'output keyword and boolean-like value');
            }
            return $this->result('output_or_device_state', 78, 'output/device keyword');
        }

        if ($this->containsAny($label, ['timer', 'zeit', 'laufzeit', 'duration'])) {
            return $this->result('timer_or_runtime', 82, 'timer/runtime keyword');
        }

        if ($this->containsAny($label, ['zaehler', 'zähler', 'counter', 'count', 'menge', 'amount', 'runtime', 'hours'])) {
            return $this->result('counter_or_statistic', 80, 'counter/statistic keyword');
        }

        if ($this->containsAny($label, ['alarm', 'grenze', 'limit', 'warnung', 'warning'])) {
            return $this->result('limit', 82, 'alarm/limit keyword');
        }

        if ($this->containsAny($label, ['soll', 'setpoint', 'target'])) {
            return $this->result('setpoint', 84, 'setpoint keyword');
        }

        if ($this->containsAny($label, ['kalib', 'calib']) || preg_match('/(ph|mv|cl).*\d/', $label)) {
            return $this->result('calibration', 76, 'calibration pattern');
        }

        if ($this->containsAny($label, ['temperatur', 'temperature', 'temp']) || in_array($unit, ['°C', 'C'], true)) {
            return $this->result('temperature', 85, 'temperature keyword/unit');
        }

        $analogUnits = ['pH', 'mV', 'mg/l', 'µA', 'mA', 'V', 'l', 'min', '%', 'mS/cm'];
        if (in_array($unit, $analogUnits, true)) {
            return $this->result('measurement', 78, 'analog unit');
        }

        if ($unit === '' && $this->isBooleanLike($value)) {
            return $this->result('digital_status_or_config', 62, 'boolean-like numeric value without unit');
        }

        if ($unit === '' && is_numeric($value)) {
            return $this->result('numeric_status_or_config', 52, 'numeric value without unit');
        }

        return $this->result('unknown', 20, 'no strong classification signal');
    }

    private function containsAny(string $text, array $needles): bool
    {
        foreach ($needles as $needle) {
            if (strpos($text, mb_strtolower($needle)) !== false) {
                return true;
            }
        }
        return false;
    }

    private function isBooleanLike(string $value): bool
    {
        return in_array($value, ['0', '1', 'true', 'false', 'on', 'off'], true);
    }

    private function result(string $class, int $confidence, string $reason): array
    {
        return [
            'class' => $class,
            'confidence' => $confidence,
            'reason' => $reason
        ];
    }
}
