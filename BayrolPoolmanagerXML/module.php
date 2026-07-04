<?php

class BayrolPoolmanagerXML extends IPSModule
{
    private const TIMER_NAME = 'UpdateTimer';

    private const MEASUREMENTS = [
        4001 => ['ident' => 'PH', 'label' => 'pH', 'unit' => 'pH'],
        4008 => ['ident' => 'ChlorineBromine', 'label' => 'Chlor / Brom', 'unit' => 'mg_l'],
        4022 => ['ident' => 'Redox', 'label' => 'Redox', 'unit' => 'mV'],
        4033 => ['ident' => 'Temperature1', 'label' => 'Temperatur T1', 'unit' => 'C'],
        4047 => ['ident' => 'Battery', 'label' => 'Batterie', 'unit' => 'V'],
        4069 => ['ident' => 'Temperature2', 'label' => 'Temperatur T2', 'unit' => 'C'],
        4071 => ['ident' => 'Temperature3', 'label' => 'Temperatur T3', 'unit' => 'C'],
        4077 => ['ident' => 'O2DosedAmount', 'label' => 'O2 dosierte Menge', 'unit' => 'l']
    ];

    private const SETPOINTS = [
        3001 => ['ident' => 'SetpointPH', 'label' => 'Sollwert pH', 'unit' => 'pH', 'writeProperty' => 'AllowWriteSetpointPH'],
        3002 => ['ident' => 'LowerAlarmPH', 'label' => 'Untere Alarmgrenze pH', 'unit' => 'pH'],
        3003 => ['ident' => 'UpperAlarmPH', 'label' => 'Obere Alarmgrenze pH', 'unit' => 'pH'],
        3017 => ['ident' => 'SetpointChlorineBromine', 'label' => 'Sollwert Chlor / Brom', 'unit' => 'mg_l', 'writeProperty' => 'AllowWriteSetpointChlorineBromine'],
        3018 => ['ident' => 'LowerAlarmChlorineBromine', 'label' => 'Untere Alarmgrenze Chlor / Brom', 'unit' => 'mg_l'],
        3019 => ['ident' => 'UpperAlarmChlorineBromine', 'label' => 'Obere Alarmgrenze Chlor / Brom', 'unit' => 'mg_l'],
        3049 => ['ident' => 'SetpointRedox1', 'label' => 'Sollwert Redox 1', 'unit' => 'mV', 'writeProperty' => 'AllowWriteSetpointRedox1'],
        3050 => ['ident' => 'SetpointRedox2', 'label' => 'Sollwert Redox 2', 'unit' => 'mV', 'writeProperty' => 'AllowWriteSetpointRedox2'],
        3051 => ['ident' => 'LowerAlarmRedox1', 'label' => 'Untere Alarmgrenze Redox 1', 'unit' => 'mV'],
        3052 => ['ident' => 'LowerAlarmRedox2', 'label' => 'Untere Alarmgrenze Redox 2', 'unit' => 'mV'],
        3053 => ['ident' => 'UpperAlarmRedox1', 'label' => 'Obere Alarmgrenze Redox 1', 'unit' => 'mV'],
        3054 => ['ident' => 'UpperAlarmRedox2', 'label' => 'Obere Alarmgrenze Redox 2', 'unit' => 'mV'],
        3069 => ['ident' => 'LowerAlarmT1', 'label' => 'Untere Alarmgrenze T1', 'unit' => 'C'],
        3070 => ['ident' => 'UpperAlarmT1', 'label' => 'Obere Alarmgrenze T1', 'unit' => 'C'],
        3074 => ['ident' => 'LowerAlarmT2', 'label' => 'Untere Alarmgrenze T2', 'unit' => 'C'],
        3075 => ['ident' => 'UpperAlarmT2', 'label' => 'Obere Alarmgrenze T2', 'unit' => 'C'],
        3079 => ['ident' => 'LowerAlarmT3', 'label' => 'Untere Alarmgrenze T3', 'unit' => 'C'],
        3080 => ['ident' => 'UpperAlarmT3', 'label' => 'Obere Alarmgrenze T3', 'unit' => 'C'],
        3084 => ['ident' => 'BasicDosingAmountO2', 'label' => 'Grund-Dosiermenge O2', 'unit' => 'l']
    ];

    private const ALARMS = [
        2001 => 'Sammelalarm', 2002 => 'Einschaltverzoegerung', 2003 => 'Kein Flow-Signal Eingang Flow',
        2004 => 'Kein Flow-Signal Eingang IN 1', 2005 => 'Oberer Alarm pH', 2006 => 'Unterer Alarm pH',
        2009 => 'Dosier-Alarm pH', 2010 => 'Oberer Alarm Chlor / Brom', 2011 => 'Unterer Alarm Chlor / Brom',
        2012 => 'Niveau Alarm Chlor', 2013 => 'Niveau-Warnung Chlor', 2014 => 'Dosier-Alarm Chlor / Brom',
        2019 => 'Oberer Alarm Redox', 2020 => 'Unterer Alarm Redox', 2021 => 'Niveau Alarm Redox',
        2022 => 'Niveau-Warnung Redox', 2023 => 'Dosier-Alarm Redox', 2024 => 'Niveau Alarm O2',
        2025 => 'Niveau-Warnung O2', 2028 => 'Oberer Alarm Temperatur T1', 2029 => 'Unterer Alarm Temperatur T1',
        2030 => 'Oberer Alarm Temperatur T2', 2031 => 'Unterer Alarm Temperatur T2', 2032 => 'Oberer Alarm Temperatur T3',
        2033 => 'Unterer Alarm Temperatur T3', 2034 => 'Batterie-Alarm', 2035 => 'Niveau Alarm pH+',
        2036 => 'Niveau-Warnung pH+', 2037 => 'Niveau Alarm pH-', 2038 => 'Niveau-Warnung pH-',
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
        $this->RegisterPropertyBoolean('EnableWritePreparation', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointPH', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointChlorineBromine', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointRedox1', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointRedox2', false);
        $this->RegisterTimer(self::TIMER_NAME, 0, 'BPMXML_Update($_IPS["TARGET"]);');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        $this->RegisterProfiles();
        $this->RegisterVariables();
        $this->ConfigureWritePreparation();

        if (trim($this->ReadPropertyString('Host')) === '') {
            $this->SetStatus(201);
            $this->SetTimerInterval(self::TIMER_NAME, 0);
            return;
        }

        $this->SetTimerInterval(self::TIMER_NAME, $this->ReadPropertyInteger('Interval') * 1000);
        $this->SetStatus(102);
    }

    public function Update()
    {
        if (trim($this->ReadPropertyString('Host')) === '') {
            $this->SetStatus(201);
            return false;
        }

        $ok = 0;
        $errors = [];

        foreach (self::MEASUREMENTS as $id => $meta) {
            if ($this->ReadValueItem(34, $id, $meta['ident'], $errors)) {
                $ok++;
            }
        }

        if ($this->ReadPropertyBoolean('ReadSetpoints')) {
            foreach (self::SETPOINTS as $id => $meta) {
                if ($this->ReadValueItem(34, $id, $meta['ident'], $errors)) {
                    $ok++;
                }
            }
        }

        foreach (self::ALARMS as $id => $label) {
            if ($this->ReadAlarmItem($id, $errors)) {
                $ok++;
            }
        }

        if ($this->ReadPropertyBoolean('ReadAlarmList')) {
            try {
                $this->SetValueSafe('AlarmList', $this->ReadAlarmListText());
                $ok++;
            } catch (Exception $e) {
                $errors[] = '1.1092: ' . $e->getMessage();
            }
        }

        $this->SetValueSafe('SuccessfulReads', $ok);
        $this->SetValueSafe('LastUpdate', time());
        $this->SetValueSafe('LastError', implode("\n", array_slice($errors, 0, 25)));

        if ($ok > 0) {
            $this->SetStatus(102);
            return true;
        }

        $this->SetStatus(202);
        return false;
    }

    public function RequestAction($Ident, $Value)
    {
        if (!$this->IsWritePrepared($Ident)) {
            throw new Exception('Schreibzugriff fuer ' . $Ident . ' ist nicht freigegeben.');
        }

        throw new Exception('Schreibzugriff ist sicherheitshalber noch nicht implementiert. Die Freigabe ist nur vorbereitet.');
    }

    private function ReadValueItem($type, $id, $ident, &$errors)
    {
        try {
            $item = $this->ReadItem($type, $id);
            if (!array_key_exists('value', $item)) {
                throw new Exception('Attribut value fehlt');
            }
            $this->SetValueSafe($ident, (float) $item['value']);
            return true;
        } catch (Exception $e) {
            $errors[] = $type . '.' . $id . ': ' . $e->getMessage();
            return false;
        }
    }

    private function ReadAlarmItem($id, &$errors)
    {
        try {
            $item = $this->ReadItem(44, $id);
            $this->SetValueSafe('AlarmActive' . $id, isset($item['active']) && ((int) $item['active']) === 1);
            $this->SetValueSafe('AlarmDisplayed' . $id, isset($item['displayed']) && ((int) $item['displayed']) === 1);
            return true;
        } catch (Exception $e) {
            $errors[] = '44.' . $id . ': ' . $e->getMessage();
            return false;
        }
    }

    private function RegisterVariables()
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
            $this->RegisterVariableBoolean('AlarmActive' . $id, $label . ' aktiv', 'BPMXML.Alarm', $pos++);
            $this->RegisterVariableBoolean('AlarmDisplayed' . $id, $label . ' angezeigt', 'BPMXML.Alarm', $pos++);
        }
        $this->RegisterVariableString('AlarmList', 'Alarmuebersicht', '', 900);
        $this->RegisterVariableInteger('SuccessfulReads', 'Erfolgreiche XML-Abfragen', '', 997);
        $this->RegisterVariableInteger('LastUpdate', 'Letzte Aktualisierung', '~UnixTimestamp', 998);
        $this->RegisterVariableString('LastError', 'Letzte Fehler / uebersprungene Adressen', '', 999);
    }

    private function RegisterProfiles()
    {
        $profiles = [
            ['BPMXML.pH.2', 'pH', 2], ['BPMXML.mg_l.2', 'mg/l', 2], ['BPMXML.mV.0', 'mV', 0],
            ['BPMXML.C.1', ' C', 1], ['BPMXML.V.2', 'V', 2], ['BPMXML.l.1', 'l', 1]
        ];
        foreach ($profiles as $profile) {
            if (!IPS_VariableProfileExists($profile[0])) {
                IPS_CreateVariableProfile($profile[0], 2);
            }
            IPS_SetVariableProfileDigits($profile[0], $profile[2]);
            IPS_SetVariableProfileText($profile[0], '', ' ' . $profile[1]);
        }

        if (!IPS_VariableProfileExists('BPMXML.Alarm')) {
            IPS_CreateVariableProfile('BPMXML.Alarm', 0);
        }
        IPS_SetVariableProfileAssociation('BPMXML.Alarm', false, 'OK', '', 0x00AA00);
        IPS_SetVariableProfileAssociation('BPMXML.Alarm', true, 'Alarm', '', 0xFF0000);
    }

    private function ConfigureWritePreparation()
    {
        foreach (self::SETPOINTS as $meta) {
            if (!isset($meta['writeProperty'])) {
                continue;
            }
            $id = @$this->GetIDForIdent($meta['ident']);
            if ($id === false) {
                continue;
            }
            if ($this->IsWritePrepared($meta['ident'])) {
                $this->EnableAction($meta['ident']);
            }
        }
    }

    private function IsWritePrepared($ident)
    {
        if (!$this->ReadPropertyBoolean('EnableWritePreparation')) {
            return false;
        }
        foreach (self::SETPOINTS as $meta) {
            if ($meta['ident'] === $ident && isset($meta['writeProperty'])) {
                return $this->ReadPropertyBoolean($meta['writeProperty']);
            }
        }
        return false;
    }

    private function ProfileName($unit)
    {
        $map = ['pH' => 'BPMXML.pH.2', 'mg_l' => 'BPMXML.mg_l.2', 'mV' => 'BPMXML.mV.0', 'C' => 'BPMXML.C.1', 'V' => 'BPMXML.V.2', 'l' => 'BPMXML.l.1'];
        return isset($map[$unit]) ? $map[$unit] : '';
    }

    private function ReadItem($type, $id)
    {
        $xml = $this->FetchXml($type, $id);
        if (!isset($xml->item)) {
            throw new Exception('XML enthaelt kein item');
        }
        $attributes = $xml->item->attributes();
        $result = [];
        foreach ($attributes as $key => $value) {
            $result[$key] = (string) $value;
        }
        return $result;
    }

    private function ReadAlarmListText()
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

    private function FetchXml($type, $id)
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
            throw new Exception('keine HTTP-Antwort');
        }
        $raw = trim($raw);
        $xmlStart = strpos($raw, '<');
        if ($xmlStart === false) {
            throw new Exception('Antwort ist kein XML: ' . substr($raw, 0, 100));
        }
        if ($xmlStart > 0) {
            $raw = substr($raw, $xmlStart);
        }
        $xml = @simplexml_load_string($raw);
        if (!$xml instanceof SimpleXMLElement) {
            throw new Exception('ungueltiges XML: ' . substr($raw, 0, 120));
        }
        return $xml;
    }

    private function SetValueSafe($ident, $value)
    {
        $id = @$this->GetIDForIdent($ident);
        if ($id !== false) {
            SetValue($id, $value);
        }
    }
}
