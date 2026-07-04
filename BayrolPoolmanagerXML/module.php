<?php

class BayrolPoolmanagerXML extends IPSModule
{
    private const TIMER_NAME = 'UpdateTimer';

    private const MEASUREMENTS = [
        4001 => ['ident' => 'PH', 'label' => 'pH', 'unit' => 'pH', 'digits' => 2],
        4008 => ['ident' => 'ChlorineBromine', 'label' => 'Chlor / Brom', 'unit' => 'mg/l', 'digits' => 2],
        4022 => ['ident' => 'Redox', 'label' => 'Redox', 'unit' => 'mV', 'digits' => 0],
        4033 => ['ident' => 'Temperature1', 'label' => 'Temperatur T1', 'unit' => 'C', 'digits' => 1],
        4047 => ['ident' => 'Battery', 'label' => 'Batterie', 'unit' => 'V', 'digits' => 2],
        4069 => ['ident' => 'Temperature2', 'label' => 'Temperatur T2', 'unit' => 'C', 'digits' => 1],
        4071 => ['ident' => 'Temperature3', 'label' => 'Temperatur T3', 'unit' => 'C', 'digits' => 1],
        4077 => ['ident' => 'O2DosedAmount', 'label' => 'O2 dosierte Menge', 'unit' => 'l', 'digits' => 1],
    ];

    private const SETPOINTS = [
        3001 => ['ident' => 'SetpointPH', 'label' => 'Sollwert pH', 'unit' => 'pH', 'digits' => 2],
        3002 => ['ident' => 'LowerAlarmPH', 'label' => 'Untere Alarmgrenze pH', 'unit' => 'pH', 'digits' => 2],
        3003 => ['ident' => 'UpperAlarmPH', 'label' => 'Obere Alarmgrenze pH', 'unit' => 'pH', 'digits' => 2],
        3017 => ['ident' => 'SetpointChlorineBromine', 'label' => 'Sollwert Chlor / Brom', 'unit' => 'mg/l', 'digits' => 2],
        3018 => ['ident' => 'LowerAlarmChlorineBromine', 'label' => 'Untere Alarmgrenze Chlor / Brom', 'unit' => 'mg/l', 'digits' => 2],
        3019 => ['ident' => 'UpperAlarmChlorineBromine', 'label' => 'Obere Alarmgrenze Chlor / Brom', 'unit' => 'mg/l', 'digits' => 2],
        3049 => ['ident' => 'SetpointRedox1', 'label' => 'Sollwert Redox 1', 'unit' => 'mV', 'digits' => 0],
        3050 => ['ident' => 'SetpointRedox2', 'label' => 'Sollwert Redox 2', 'unit' => 'mV', 'digits' => 0],
        3051 => ['ident' => 'LowerAlarmRedox1', 'label' => 'Untere Alarmgrenze Redox 1', 'unit' => 'mV', 'digits' => 0],
        3052 => ['ident' => 'LowerAlarmRedox2', 'label' => 'Untere Alarmgrenze Redox 2', 'unit' => 'mV', 'digits' => 0],
        3053 => ['ident' => 'UpperAlarmRedox1', 'label' => 'Obere Alarmgrenze Redox 1', 'unit' => 'mV', 'digits' => 0],
        3054 => ['ident' => 'UpperAlarmRedox2', 'label' => 'Obere Alarmgrenze Redox 2', 'unit' => 'mV', 'digits' => 0],
        3069 => ['ident' => 'LowerAlarmT1', 'label' => 'Untere Alarmgrenze T1', 'unit' => 'C', 'digits' => 1],
        3070 => ['ident' => 'UpperAlarmT1', 'label' => 'Obere Alarmgrenze T1', 'unit' => 'C', 'digits' => 1],
        3074 => ['ident' => 'LowerAlarmT2', 'label' => 'Untere Alarmgrenze T2', 'unit' => 'C', 'digits' => 1],
        3075 => ['ident' => 'UpperAlarmT2', 'label' => 'Obere Alarmgrenze T2', 'unit' => 'C', 'digits' => 1],
        3079 => ['ident' => 'LowerAlarmT3', 'label' => 'Untere Alarmgrenze T3', 'unit' => 'C', 'digits' => 1],
        3080 => ['ident' => 'UpperAlarmT3', 'label' => 'Obere Alarmgrenze T3', 'unit' => 'C', 'digits' => 1],
        3084 => ['ident' => 'BasicDosingAmountO2', 'label' => 'Grund-Dosiermenge O2', 'unit' => 'l', 'digits' => 1],
    ];

    private const ALARMS = [
        2001 => 'Sammelalarm',
        2002 => 'Einschaltverzoegerung',
        2003 => 'Kein Flow-Signal Eingang Flow',
        2004 => 'Kein Flow-Signal Eingang IN 1',
        2005 => 'Oberer Alarm pH',
        2006 => 'Unterer Alarm pH',
        2009 => 'Dosier-Alarm pH',
        2010 => 'Oberer Alarm Chlor / Brom',
        2011 => 'Unterer Alarm Chlor / Brom',
        2012 => 'Niveau Alarm Chlor',
        2013 => 'Niveau-Warnung Chlor',
        2014 => 'Dosier-Alarm Chlor / Brom',
        2019 => 'Oberer Alarm Redox',
        2020 => 'Unterer Alarm Redox',
        2021 => 'Niveau Alarm Redox',
        2022 => 'Niveau-Warnung Redox',
        2023 => 'Dosier-Alarm Redox',
        2024 => 'Niveau Alarm O2',
        2025 => 'Niveau-Warnung O2',
        2028 => 'Oberer Alarm Temperatur T1',
        2029 => 'Unterer Alarm Temperatur T1',
        2030 => 'Oberer Alarm Temperatur T2',
        2031 => 'Unterer Alarm Temperatur T2',
        2032 => 'Oberer Alarm Temperatur T3',
        2033 => 'Unterer Alarm Temperatur T3',
        2034 => 'Batterie-Alarm',
        2035 => 'Niveau Alarm pH+',
        2036 => 'Niveau-Warnung pH+',
        2037 => 'Niveau Alarm pH-',
        2038 => 'Niveau-Warnung pH-',
        2039 => 'Niveau Alarm Flockmatic'
    ];

    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyString('Host', '');
        $this->RegisterPropertyInteger('Interval', 60);
        $this->RegisterPropertyInteger('Timeout', 5);
        $this->RegisterPropertyBoolean('ReadSetpoints', true);
        $this->RegisterPropertyBoolean('ReadAlarmList', true);
        $this->RegisterTimer(self::TIMER_NAME, 0, 'BPMXML_Update($_IPS["TARGET"]);');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        $this->RegisterProfiles();
        $this->RegisterVariables();

        $host = trim($this->ReadPropertyString('Host'));
        if ($host === '') {
            $this->SetStatus(201);
            $this->SetTimerInterval(self::TIMER_NAME, 0);
            return;
        }

        $this->SetTimerInterval(self::TIMER_NAME, $this->ReadPropertyInteger('Interval') * 1000);
        $this->SetStatus(102);
    }

    public function Update(): bool
    {
        $host = trim($this->ReadPropertyString('Host'));
        if ($host === '') {
            $this->SetStatus(201);
            return false;
        }

        try {
            foreach (self::MEASUREMENTS as $id => $meta) {
                $item = $this->ReadItem(34, $id);
                $this->SetValueSafe($meta['ident'], (float) $item['value']);
            }

            if ($this->ReadPropertyBoolean('ReadSetpoints')) {
                foreach (self::SETPOINTS as $id => $meta) {
                    $item = $this->ReadItem(34, $id);
                    $this->SetValueSafe($meta['ident'], (float) $item['value']);
                }
            }

            foreach (self::ALARMS as $id => $label) {
                $item = $this->ReadItem(44, $id);
                $this->SetValueSafe('AlarmActive' . $id, ((int) $item['active']) === 1);
                $this->SetValueSafe('AlarmDisplayed' . $id, ((int) $item['displayed']) === 1);
            }

            if ($this->ReadPropertyBoolean('ReadAlarmList')) {
                $this->SetValueSafe('AlarmList', $this->ReadAlarmListText());
            }

            $this->SetValueSafe('LastUpdate', time());
            $this->SetValueSafe('LastError', '');
            $this->SetStatus(102);
            return true;
        } catch (Throwable $e) {
            $this->SetValueSafe('LastError', $e->getMessage());
            $this->SetStatus(202);
            $this->SendDebug('Update failed', $e->getMessage(), 0);
            return false;
        }
    }

    private function RegisterVariables(): void
    {
        $pos = 10;
        foreach (self::MEASUREMENTS as $meta) {
            $this->RegisterVariableFloat($meta['ident'], $meta['label'], $this->ProfileName($meta['unit']), $pos++);
        }
        if ($this->ReadPropertyBoolean('ReadSetpoints')) {
            foreach (self::SETPOINTS as $meta) {
                $this->RegisterVariableFloat($meta['ident'], $meta['label'], $this->ProfileName($meta['unit']), $pos++);
            }
        }
        foreach (self::ALARMS as $id => $label) {
            $this->RegisterVariableBoolean('AlarmActive' . $id, $label . ' aktiv', '~Alert', $pos++);
            $this->RegisterVariableBoolean('AlarmDisplayed' . $id, $label . ' angezeigt', '~Alert', $pos++);
        }
        $this->RegisterVariableString('AlarmList', 'Alarmuebersicht', '', 900);
        $this->RegisterVariableInteger('LastUpdate', 'Letzte Aktualisierung', '~UnixTimestamp', 998);
        $this->RegisterVariableString('LastError', 'Letzter Fehler', '', 999);
    }

    private function RegisterProfiles(): void
    {
        $profiles = [
            ['BPMXML.pH.2', 'pH', 2],
            ['BPMXML.mg_l.2', 'mg/l', 2],
            ['BPMXML.mV.0', 'mV', 0],
            ['BPMXML.C.1', ' C', 1],
            ['BPMXML.V.2', 'V', 2],
            ['BPMXML.l.1', 'l', 1],
        ];
        foreach ($profiles as $profile) {
            [$name, $suffix, $digits] = $profile;
            if (!IPS_VariableProfileExists($name)) {
                IPS_CreateVariableProfile($name, 2);
            }
            IPS_SetVariableProfileDigits($name, $digits);
            IPS_SetVariableProfileText($name, '', ' ' . $suffix);
        }
    }

    private function ProfileName(string $unit): string
    {
        return match ($unit) {
            'pH' => 'BPMXML.pH.2',
            'mg/l' => 'BPMXML.mg_l.2',
            'mV' => 'BPMXML.mV.0',
            'C' => 'BPMXML.C.1',
            'V' => 'BPMXML.V.2',
            'l' => 'BPMXML.l.1',
            default => ''
        };
    }

    private function ReadItem(int $type, int $id): array
    {
        $xml = $this->FetchXml($type, $id);
        if (!isset($xml->item)) {
            throw new Exception('XML enthaelt kein item fuer ' . $type . '.' . $id);
        }
        $attributes = $xml->item->attributes();
        $result = [];
        foreach ($attributes as $key => $value) {
            $result[$key] = (string) $value;
        }
        return $result;
    }

    private function ReadAlarmListText(): string
    {
        $xml = $this->FetchXml(1, 1092);
        $lines = [];
        if (isset($xml->item->item)) {
            foreach ($xml->item->item as $alarm) {
                $a = $alarm->attributes();
                $lines[] = sprintf('%s: aktiv=%s, angezeigt=%s', (string) $a['label'], (string) $a['active'], (string) $a['displayed']);
            }
        }
        return implode("\n", $lines);
    }

    private function FetchXml(int $type, int $id): SimpleXMLElement
    {
        $host = trim($this->ReadPropertyString('Host'));
        $host = preg_replace('#^https?://#', '', $host);
        $url = 'http://' . $host . '/cgi-bin/webgui.fcgi?xmlitem=' . $type . '.' . $id;
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $this->ReadPropertyInteger('Timeout'),
                'ignore_errors' => true,
                'header' => "Connection: close\r\n"
            ]
        ]);
        $raw = @file_get_contents($url, false, $context);
        if ($raw === false || trim($raw) === '') {
            throw new Exception('Keine Antwort von ' . $url);
        }
        $xml = @simplexml_load_string($raw);
        if (!$xml instanceof SimpleXMLElement) {
            throw new Exception('Ungueltige XML-Antwort von ' . $url . ': ' . substr($raw, 0, 120));
        }
        return $xml;
    }

    private function SetValueSafe(string $ident, mixed $value): void
    {
        $id = @$this->GetIDForIdent($ident);
        if ($id !== false) {
            SetValue($id, $value);
        }
    }
}
