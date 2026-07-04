<?php

class BayrolPoolmanagerXML extends IPSModule
{
    private const TIMER_MEASUREMENTS = 'UpdateTimer';
    private const TIMER_STATIC = 'StaticUpdateTimer';

    private const CATEGORIES = [
        'Info' => ['name' => 'Informationen', 'pos' => 10],
        'Measurements' => ['name' => 'Messwerte', 'pos' => 20],
        'Setpoints' => ['name' => 'Sollwerte', 'pos' => 30],
        'Limits' => ['name' => 'Alarmgrenzen', 'pos' => 40],
        'Alarms' => ['name' => 'Alarme', 'pos' => 50],
        'Explorer' => ['name' => 'XML Explorer', 'pos' => 800],
        'Service' => ['name' => 'Service', 'pos' => 900]
    ];

    private const ITEMS = [
        ['xml' => '34.4001', 'ident' => 'PH', 'name' => 'pH', 'category' => 'Measurements', 'profile' => 'BPMXML.pH', 'kind' => 'measurement'],
        ['xml' => '34.4008', 'ident' => 'ChlorineBromine', 'name' => 'Chlor / Brom', 'category' => 'Measurements', 'profile' => 'BPMXML.mg_l', 'kind' => 'measurement'],
        ['xml' => '34.4022', 'ident' => 'Redox', 'name' => 'Redox', 'category' => 'Measurements', 'profile' => 'BPMXML.mV', 'kind' => 'measurement'],
        ['xml' => '34.4033', 'ident' => 'Temperature1', 'name' => 'Temperatur T1', 'category' => 'Measurements', 'profile' => 'BPMXML.C', 'kind' => 'measurement'],
        ['xml' => '34.4047', 'ident' => 'Battery', 'name' => 'Batterie', 'category' => 'Measurements', 'profile' => 'BPMXML.V', 'kind' => 'measurement'],
        ['xml' => '34.4069', 'ident' => 'Temperature2', 'name' => 'Temperatur T2', 'category' => 'Measurements', 'profile' => 'BPMXML.C', 'kind' => 'measurement'],
        ['xml' => '34.4071', 'ident' => 'Temperature3', 'name' => 'Temperatur T3', 'category' => 'Measurements', 'profile' => 'BPMXML.C', 'kind' => 'measurement'],
        ['xml' => '34.4077', 'ident' => 'O2DosedAmount', 'name' => 'O2 dosierte Menge', 'category' => 'Measurements', 'profile' => 'BPMXML.l', 'kind' => 'measurement'],
        ['xml' => '34.3001', 'ident' => 'SetpointPH', 'name' => 'Sollwert pH', 'category' => 'Setpoints', 'profile' => 'BPMXML.pH', 'kind' => 'setpoint', 'writeProperty' => 'AllowWriteSetpointPH'],
        ['xml' => '34.3017', 'ident' => 'SetpointChlorineBromine', 'name' => 'Sollwert Chlor / Brom', 'category' => 'Setpoints', 'profile' => 'BPMXML.mg_l', 'kind' => 'setpoint', 'writeProperty' => 'AllowWriteSetpointChlorineBromine'],
        ['xml' => '34.3049', 'ident' => 'SetpointRedox1', 'name' => 'Sollwert Redox 1', 'category' => 'Setpoints', 'profile' => 'BPMXML.mV', 'kind' => 'setpoint', 'writeProperty' => 'AllowWriteSetpointRedox1'],
        ['xml' => '34.3050', 'ident' => 'SetpointRedox2', 'name' => 'Sollwert Redox 2', 'category' => 'Setpoints', 'profile' => 'BPMXML.mV', 'kind' => 'setpoint', 'writeProperty' => 'AllowWriteSetpointRedox2'],
        ['xml' => '34.3002', 'ident' => 'LowerAlarmPH', 'name' => 'Untere Alarmgrenze pH', 'category' => 'Limits', 'profile' => 'BPMXML.pH', 'kind' => 'setpoint'],
        ['xml' => '34.3003', 'ident' => 'UpperAlarmPH', 'name' => 'Obere Alarmgrenze pH', 'category' => 'Limits', 'profile' => 'BPMXML.pH', 'kind' => 'setpoint'],
        ['xml' => '34.3018', 'ident' => 'LowerAlarmChlorineBromine', 'name' => 'Untere Alarmgrenze Chlor / Brom', 'category' => 'Limits', 'profile' => 'BPMXML.mg_l', 'kind' => 'setpoint'],
        ['xml' => '34.3019', 'ident' => 'UpperAlarmChlorineBromine', 'name' => 'Obere Alarmgrenze Chlor / Brom', 'category' => 'Limits', 'profile' => 'BPMXML.mg_l', 'kind' => 'setpoint'],
        ['xml' => '34.3051', 'ident' => 'LowerAlarmRedox1', 'name' => 'Untere Alarmgrenze Redox 1', 'category' => 'Limits', 'profile' => 'BPMXML.mV', 'kind' => 'setpoint'],
        ['xml' => '34.3052', 'ident' => 'LowerAlarmRedox2', 'name' => 'Untere Alarmgrenze Redox 2', 'category' => 'Limits', 'profile' => 'BPMXML.mV', 'kind' => 'setpoint'],
        ['xml' => '34.3053', 'ident' => 'UpperAlarmRedox1', 'name' => 'Obere Alarmgrenze Redox 1', 'category' => 'Limits', 'profile' => 'BPMXML.mV', 'kind' => 'setpoint'],
        ['xml' => '34.3054', 'ident' => 'UpperAlarmRedox2', 'name' => 'Obere Alarmgrenze Redox 2', 'category' => 'Limits', 'profile' => 'BPMXML.mV', 'kind' => 'setpoint'],
        ['xml' => '34.3069', 'ident' => 'LowerAlarmT1', 'name' => 'Untere Alarmgrenze T1', 'category' => 'Limits', 'profile' => 'BPMXML.C', 'kind' => 'setpoint'],
        ['xml' => '34.3070', 'ident' => 'UpperAlarmT1', 'name' => 'Obere Alarmgrenze T1', 'category' => 'Limits', 'profile' => 'BPMXML.C', 'kind' => 'setpoint'],
        ['xml' => '34.3074', 'ident' => 'LowerAlarmT2', 'name' => 'Untere Alarmgrenze T2', 'category' => 'Limits', 'profile' => 'BPMXML.C', 'kind' => 'setpoint'],
        ['xml' => '34.3075', 'ident' => 'UpperAlarmT2', 'name' => 'Obere Alarmgrenze T2', 'category' => 'Limits', 'profile' => 'BPMXML.C', 'kind' => 'setpoint'],
        ['xml' => '34.3079', 'ident' => 'LowerAlarmT3', 'name' => 'Untere Alarmgrenze T3', 'category' => 'Limits', 'profile' => 'BPMXML.C', 'kind' => 'setpoint'],
        ['xml' => '34.3080', 'ident' => 'UpperAlarmT3', 'name' => 'Obere Alarmgrenze T3', 'category' => 'Limits', 'profile' => 'BPMXML.C', 'kind' => 'setpoint'],
        ['xml' => '34.3084', 'ident' => 'BasicDosingAmountO2', 'name' => 'Grund-Dosiermenge O2', 'category' => 'Limits', 'profile' => 'BPMXML.l', 'kind' => 'setpoint']
    ];

    private const ALARMS = [
        2001 => 'Sammelalarm', 2002 => 'Einschaltverzoegerung', 2003 => 'Kein Flow-Signal Eingang Flow', 2004 => 'Kein Flow-Signal Eingang IN 1',
        2005 => 'Oberer Alarm pH', 2006 => 'Unterer Alarm pH', 2009 => 'Dosier-Alarm pH', 2010 => 'Oberer Alarm Chlor / Brom',
        2011 => 'Unterer Alarm Chlor / Brom', 2012 => 'Niveau Alarm Chlor', 2013 => 'Niveau-Warnung Chlor', 2014 => 'Dosier-Alarm Chlor / Brom',
        2019 => 'Oberer Alarm Redox', 2020 => 'Unterer Alarm Redox', 2021 => 'Niveau Alarm Redox', 2022 => 'Niveau-Warnung Redox',
        2023 => 'Dosier-Alarm Redox', 2024 => 'Niveau Alarm O2', 2025 => 'Niveau-Warnung O2', 2028 => 'Oberer Alarm Temperatur T1',
        2029 => 'Unterer Alarm Temperatur T1', 2030 => 'Oberer Alarm Temperatur T2', 2031 => 'Unterer Alarm Temperatur T2', 2032 => 'Oberer Alarm Temperatur T3',
        2033 => 'Unterer Alarm Temperatur T3', 2034 => 'Batterie-Alarm', 2035 => 'Niveau Alarm pH+', 2036 => 'Niveau-Warnung pH+',
        2037 => 'Niveau Alarm pH-', 2038 => 'Niveau-Warnung pH-', 2039 => 'Niveau Alarm Flockmatic'
    ];

    public function Create()
    {
        parent::Create();
        $this->RegisterPropertyString('Host', '');
        $this->RegisterPropertyInteger('Interval', 60);
        $this->RegisterPropertyInteger('StaticInterval', 10);
        $this->RegisterPropertyInteger('Timeout', 5);
        $this->RegisterPropertyBoolean('ReadSetpoints', true);
        $this->RegisterPropertyBoolean('ReadAlarms', true);
        $this->RegisterPropertyBoolean('ReadAlarmList', false);
        $this->RegisterPropertyBoolean('AutoUpdateAfterApply', false);
        $this->RegisterPropertyBoolean('DebugXml', false);
        $this->RegisterPropertyInteger('ExplorerType', 34);
        $this->RegisterPropertyInteger('ExplorerStart', 4000);
        $this->RegisterPropertyInteger('ExplorerEnd', 4100);
        $this->RegisterPropertyInteger('ExplorerMaxPerRun', 150);
        $this->RegisterPropertyBoolean('ExplorerOnlyValid', true);
        $this->RegisterPropertyBoolean('ExplorerStoreRaw', true);
        $this->RegisterPropertyBoolean('EnableWritePreparation', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointPH', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointChlorineBromine', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointRedox1', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointRedox2', false);
        $this->RegisterTimer(self::TIMER_MEASUREMENTS, 0, 'BPMXML_Update($_IPS["TARGET"]);');
        $this->RegisterTimer(self::TIMER_STATIC, 0, 'BPMXML_UpdateStatic($_IPS["TARGET"]);');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        $this->RegisterProfiles();
        $this->CreateStructure();
        $this->RegisterServiceVariables();
        $this->RegisterExplorerVariables();
        $this->RegisterDataVariables();
        $this->ConfigureWritePreparation();

        if (trim($this->ReadPropertyString('Host')) === '') {
            $this->SetStatus(201);
            $this->SetTimerInterval(self::TIMER_MEASUREMENTS, 0);
            $this->SetTimerInterval(self::TIMER_STATIC, 0);
            return;
        }

        $this->SetTimerInterval(self::TIMER_MEASUREMENTS, max(0, $this->ReadPropertyInteger('Interval')) * 1000);
        $this->SetTimerInterval(self::TIMER_STATIC, max(0, $this->ReadPropertyInteger('StaticInterval')) * 60 * 1000);
        $this->SetStatus(102);
        if ($this->ReadPropertyBoolean('AutoUpdateAfterApply')) {
            $this->Update();
        }
    }

    public function Update() { return $this->UpdateInternal(false); }
    public function UpdateStatic() { return $this->UpdateInternal(true); }

    public function RunExplorer()
    {
        return $this->RunExplorerRange($this->ReadPropertyInteger('ExplorerType'), $this->ReadPropertyInteger('ExplorerStart'), $this->ReadPropertyInteger('ExplorerEnd'), 'Freier Scan');
    }

    public function RunExplorerMeasurements() { return $this->RunExplorerRange(34, 4000, 4100, 'Preset Messwerte 34.4000-4100'); }
    public function RunExplorerSetpoints() { return $this->RunExplorerRange(34, 3000, 3100, 'Preset Sollwerte 34.3000-3100'); }
    public function RunExplorerAlarms() { return $this->RunExplorerRange(44, 2000, 2050, 'Preset Alarme 44.2000-2050'); }

    public function StoreSnapshotA()
    {
        $this->RunExplorer();
        $this->SetValueByIdent('ExplorerSnapshotA', $this->GetValueByIdent('ExplorerJSON'));
        $this->SetValueByIdent('ExplorerCompare', 'Snapshot A gespeichert: ' . date('Y-m-d H:i:s'));
        return true;
    }

    public function StoreSnapshotB()
    {
        $this->RunExplorer();
        $this->SetValueByIdent('ExplorerSnapshotB', $this->GetValueByIdent('ExplorerJSON'));
        $this->SetValueByIdent('ExplorerCompare', 'Snapshot B gespeichert: ' . date('Y-m-d H:i:s'));
        return true;
    }

    public function CompareSnapshots()
    {
        $a = json_decode($this->GetValueByIdent('ExplorerSnapshotA'), true);
        $b = json_decode($this->GetValueByIdent('ExplorerSnapshotB'), true);
        if (!is_array($a) || !is_array($b)) {
            $this->SetValueByIdent('ExplorerCompare', 'Snapshot A oder B fehlt / ist kein gueltiges JSON.');
            return false;
        }
        $ma = $this->MapExplorerResult($a);
        $mb = $this->MapExplorerResult($b);
        $lines = [];
        foreach ($mb as $xmlitem => $entryB) {
            if (!isset($ma[$xmlitem])) {
                $lines[] = '+ ' . $xmlitem . ' neu: ' . json_encode($entryB['attributes'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                continue;
            }
            $old = json_encode($ma[$xmlitem]['attributes'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $new = json_encode($entryB['attributes'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($old !== $new) {
                $lines[] = '* ' . $xmlitem . ' geaendert: ' . $old . ' -> ' . $new;
            }
        }
        foreach ($ma as $xmlitem => $entryA) {
            if (!isset($mb[$xmlitem])) {
                $lines[] = '- ' . $xmlitem . ' fehlt in B';
            }
        }
        $this->SetValueByIdent('ExplorerCompare', count($lines) ? implode("\n", $lines) : 'Keine Unterschiede gefunden.');
        return true;
    }

    public function GenerateDefinitionFromExplorer()
    {
        $data = json_decode($this->GetValueByIdent('ExplorerJSON'), true);
        if (!is_array($data)) {
            $this->SetValueByIdent('ExplorerDefinition', 'Kein gueltiges Explorer-JSON vorhanden.');
            return false;
        }
        $out = "[\n";
        foreach ($data as $entry) {
            if (empty($entry['valid']) || empty($entry['attributes'])) {
                continue;
            }
            $a = $entry['attributes'];
            $label = isset($a['label']) ? $a['label'] : $entry['xmlitem'];
            $unit = isset($a['unit']) ? $a['unit'] : '';
            $profile = $this->SuggestProfile($unit, $a);
            $ident = $this->MakeIdent($label, $entry['xmlitem']);
            $category = isset($a['active']) || isset($a['displayed']) ? 'Alarms' : 'Measurements';
            $out .= "    ['xml' => '" . $entry['xmlitem'] . "', 'ident' => '" . $ident . "', 'name' => '" . addslashes($label) . "', 'category' => '" . $category . "', 'profile' => '" . $profile . "', 'kind' => 'explored'],\n";
        }
        $out .= "]";
        $this->SetValueByIdent('ExplorerDefinition', $out);
        return true;
    }

    public function ClearExplorer()
    {
        foreach (['ExplorerSummary', 'ExplorerCSV', 'ExplorerJSON', 'ExplorerLastRun', 'ExplorerSnapshotA', 'ExplorerSnapshotB', 'ExplorerCompare', 'ExplorerDefinition'] as $ident) {
            $this->SetValueByIdent($ident, $ident === 'ExplorerLastRun' ? 0 : '');
        }
    }

    public function RequestAction($Ident, $Value)
    {
        if (!$this->IsWritePrepared($Ident)) {
            throw new Exception('Schreibzugriff fuer ' . $Ident . ' ist nicht freigegeben.');
        }
        throw new Exception('Schreiben ist absichtlich noch gesperrt. Die Schreibfreigaben sind nur vorbereitet, bis die PM5-Write-URLs sicher getestet wurden.');
    }

    private function RunExplorerRange($type, $start, $end, $title)
    {
        if (trim($this->ReadPropertyString('Host')) === '') {
            $this->SetStatus(201);
            return false;
        }
        if ($end < $start) { $tmp = $start; $start = $end; $end = $tmp; }
        $max = max(1, min($this->ReadPropertyInteger('ExplorerMaxPerRun'), 1000));
        $onlyValid = $this->ReadPropertyBoolean('ExplorerOnlyValid');
        $storeRaw = $this->ReadPropertyBoolean('ExplorerStoreRaw');
        $results = [];
        $csv = "xmlitem;valid;label;unit;value;active;displayed;attributes;duration_ms;error\n";
        $scanned = 0; $valid = 0;
        for ($id = $start; $id <= $end && $scanned < $max; $id++) {
            $scanned++;
            $started = microtime(true);
            $entry = ['xmlitem' => $type . '.' . $id, 'valid' => false, 'attributes' => [], 'duration_ms' => 0, 'error' => ''];
            try {
                $raw = $this->FetchRawXml($type, $id);
                $entry['duration_ms'] = round((microtime(true) - $started) * 1000, 1);
                $xml = $this->ParseXmlString($raw);
                if (isset($xml->item)) {
                    foreach ($xml->item->attributes() as $key => $value) { $entry['attributes'][(string)$key] = (string)$value; }
                    $entry['valid'] = true; $valid++;
                    if ($storeRaw) { $entry['raw'] = substr(trim($raw), 0, 2000); }
                } else { $entry['error'] = 'XML enthaelt kein item'; }
            } catch (Exception $e) {
                $entry['duration_ms'] = round((microtime(true) - $started) * 1000, 1);
                $entry['error'] = $e->getMessage();
            }
            if (!$onlyValid || $entry['valid']) {
                $results[] = $entry;
                $a = $entry['attributes'];
                $csv .= $this->Csv($entry['xmlitem']) . ';' . ($entry['valid'] ? '1' : '0') . ';' . $this->Csv($a['label'] ?? '') . ';' . $this->Csv($a['unit'] ?? '') . ';' . $this->Csv($a['value'] ?? '') . ';' . $this->Csv($a['active'] ?? '') . ';' . $this->Csv($a['displayed'] ?? '') . ';' . $this->Csv(json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . ';' . $this->Csv((string)$entry['duration_ms']) . ';' . $this->Csv($entry['error']) . "\n";
            }
        }
        $summary = $title . ': Typ ' . $type . ', Bereich ' . $start . '-' . $end . ', abgefragt ' . $scanned . ', gueltig ' . $valid . ', gespeichert ' . count($results) . ', Zeit ' . date('Y-m-d H:i:s');
        $this->SetValueByIdent('ExplorerSummary', $summary);
        $this->SetValueByIdent('ExplorerCSV', $csv);
        $this->SetValueByIdent('ExplorerJSON', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->SetValueByIdent('ExplorerLastRun', time());
        return true;
    }

    private function UpdateInternal($staticOnly)
    {
        if (trim($this->ReadPropertyString('Host')) === '') { $this->SetStatus(201); return false; }
        $ok = 0; $errors = [];
        if (!$staticOnly) {
            foreach (self::ITEMS as $item) {
                if ($item['kind'] === 'setpoint' && !$this->ReadPropertyBoolean('ReadSetpoints')) { continue; }
                if ($this->ReadValueItem($item, $errors)) { $ok++; }
            }
            if ($this->ReadPropertyBoolean('ReadAlarms')) {
                foreach (self::ALARMS as $id => $label) { if ($this->ReadAlarmItem($id, $label, $errors)) { $ok++; } }
            }
            if ($this->ReadPropertyBoolean('ReadAlarmList')) {
                try { $this->SetValueByIdent('AlarmList', $this->ReadAlarmListText()); $ok++; } catch (Exception $e) { $errors[] = '1.1092: ' . $e->getMessage(); }
            }
        }
        $this->SetValueByIdent('Online', $ok > 0 || $staticOnly);
        $this->SetValueByIdent('SuccessfulReads', $ok);
        $this->SetValueByIdent('LastUpdate', time());
        $this->SetValueByIdent('LastError', implode("\n", array_slice($errors, 0, 40)));
        $this->SetValueByIdent('WriteMode', $this->ReadPropertyBoolean('EnableWritePreparation'));
        if ($ok > 0 || $staticOnly) { $this->SetStatus(102); return true; }
        $this->SetStatus(202); return false;
    }

    private function ReadValueItem($definition, &$errors)
    {
        try {
            list($type, $id) = explode('.', $definition['xml']);
            $item = $this->ReadItem((int)$type, (int)$id);
            if (!array_key_exists('value', $item)) { throw new Exception('Attribut value fehlt'); }
            $this->SetValueByIdent($definition['ident'], (float)$item['value']); return true;
        } catch (Exception $e) { $errors[] = $definition['xml'] . ' ' . $definition['name'] . ': ' . $e->getMessage(); return false; }
    }

    private function ReadAlarmItem($id, $label, &$errors)
    {
        try {
            $item = $this->ReadItem(44, $id);
            $this->SetValueByIdent('AlarmActive' . $id, isset($item['active']) && ((int)$item['active']) === 1);
            $this->SetValueByIdent('AlarmDisplayed' . $id, isset($item['displayed']) && ((int)$item['displayed']) === 1);
            return true;
        } catch (Exception $e) { $errors[] = '44.' . $id . ' ' . $label . ': ' . $e->getMessage(); return false; }
    }

    private function CreateStructure() { foreach (self::CATEGORIES as $ident => $meta) { $this->GetCategoryID($ident, $meta['name'], $meta['pos']); } }

    private function RegisterDataVariables()
    {
        $pos = 10;
        foreach (self::ITEMS as $item) {
            if ($item['kind'] === 'setpoint' && !$this->ReadPropertyBoolean('ReadSetpoints')) { continue; }
            $cat = $this->GetCategoryID($item['category'], self::CATEGORIES[$item['category']]['name'], self::CATEGORIES[$item['category']]['pos']);
            $this->RegisterFloat($cat, $item['ident'], $item['name'], $item['profile'], $pos++);
        }
        if ($this->ReadPropertyBoolean('ReadAlarms')) {
            $cat = $this->GetCategoryID('Alarms', 'Alarme', 50); $pos = 10;
            foreach (self::ALARMS as $id => $label) {
                $this->RegisterBoolean($cat, 'AlarmActive' . $id, $label . ' aktiv', 'BPMXML.Alarm', $pos++);
                $this->RegisterBoolean($cat, 'AlarmDisplayed' . $id, $label . ' angezeigt', 'BPMXML.Alarm', $pos++);
            }
            $this->RegisterString($cat, 'AlarmList', 'Alarmuebersicht', '', 900);
        }
    }

    private function RegisterServiceVariables()
    {
        $cat = $this->GetCategoryID('Service', 'Service', 900);
        $this->RegisterBoolean($cat, 'Online', 'Kommunikation online', 'BPMXML.Online', 10);
        $this->RegisterInteger($cat, 'SuccessfulReads', 'Erfolgreiche XML-Abfragen', '', 20);
        $this->RegisterInteger($cat, 'LastUpdate', 'Letzte Aktualisierung', '~UnixTimestamp', 30);
        $this->RegisterString($cat, 'LastError', 'Letzte Fehler / uebersprungene Adressen', '', 40);
        $this->RegisterBoolean($cat, 'WriteMode', 'Schreibmodus vorbereitet', 'BPMXML.WriteMode', 50);
    }

    private function RegisterExplorerVariables()
    {
        $cat = $this->GetCategoryID('Explorer', 'XML Explorer', 800);
        $this->RegisterString($cat, 'ExplorerSummary', 'Scan-Zusammenfassung', '', 10);
        $this->RegisterString($cat, 'ExplorerCSV', 'Scan-Ergebnis CSV', '', 20);
        $this->RegisterString($cat, 'ExplorerJSON', 'Scan-Ergebnis JSON', '', 30);
        $this->RegisterInteger($cat, 'ExplorerLastRun', 'Letzter Explorer-Lauf', '~UnixTimestamp', 40);
        $this->RegisterString($cat, 'ExplorerSnapshotA', 'Snapshot A JSON', '', 50);
        $this->RegisterString($cat, 'ExplorerSnapshotB', 'Snapshot B JSON', '', 60);
        $this->RegisterString($cat, 'ExplorerCompare', 'Snapshot Vergleich', '', 70);
        $this->RegisterString($cat, 'ExplorerDefinition', 'PHP-Definition Vorschlag', '', 80);
    }

    private function RegisterProfiles()
    {
        $this->CreateFloatProfile('BPMXML.pH', 'pH', 2); $this->CreateFloatProfile('BPMXML.mg_l', 'mg/l', 2); $this->CreateFloatProfile('BPMXML.mV', 'mV', 0);
        $this->CreateFloatProfile('BPMXML.C', ' C', 1); $this->CreateFloatProfile('BPMXML.V', 'V', 2); $this->CreateFloatProfile('BPMXML.l', 'l', 1);
        $this->CreateBoolProfile('BPMXML.Alarm', 'OK', 'Alarm', 0x00AA00, 0xFF0000); $this->CreateBoolProfile('BPMXML.Online', 'Offline', 'Online', 0xFF0000, 0x00AA00); $this->CreateBoolProfile('BPMXML.WriteMode', 'Read-only', 'Freigegeben', 0x00AA00, 0xFFA500);
    }

    private function ConfigureWritePreparation()
    {
        foreach (self::ITEMS as $item) {
            if (!isset($item['writeProperty'])) { continue; }
            $id = $this->FindObjectByIdent($item['ident']); if ($id === false) { continue; }
            IPS_SetVariableCustomAction($id, $this->IsWritePrepared($item['ident']) ? $this->InstanceID : 0);
        }
    }

    private function IsWritePrepared($ident)
    {
        if (!$this->ReadPropertyBoolean('EnableWritePreparation')) { return false; }
        foreach (self::ITEMS as $item) { if ($item['ident'] === $ident && isset($item['writeProperty'])) { return $this->ReadPropertyBoolean($item['writeProperty']); } }
        return false;
    }

    private function ReadItem($type, $id)
    {
        $xml = $this->FetchXml($type, $id); if (!isset($xml->item)) { throw new Exception('XML enthaelt kein item'); }
        $result = []; foreach ($xml->item->attributes() as $key => $value) { $result[(string)$key] = (string)$value; } return $result;
    }

    private function ReadAlarmListText()
    {
        $xml = $this->FetchXml(1, 1092); $lines = [];
        if (isset($xml->item->item)) { foreach ($xml->item->item as $alarm) { $a = $alarm->attributes(); $lines[] = sprintf('%s: aktiv=%s, angezeigt=%s', (string)$a['label'], (string)$a['active'], (string)$a['displayed']); } }
        return implode("\n", $lines);
    }

    private function FetchXml($type, $id) { return $this->ParseXmlString($this->FetchRawXml($type, $id)); }

    private function FetchRawXml($type, $id)
    {
        $host = preg_replace('#^https?://#', '', trim($this->ReadPropertyString('Host')));
        $url = 'http://' . $host . '/cgi-bin/webgui.fcgi?xmlitem=' . $type . '.' . $id;
        $context = stream_context_create(['http' => ['method' => 'GET', 'timeout' => $this->ReadPropertyInteger('Timeout'), 'ignore_errors' => true, 'header' => "Connection: close\r\nUser-Agent: IP-Symcon BayrolPoolmanagerXML\r\n"]]);
        $raw = @file_get_contents($url, false, $context); if ($raw === false || trim($raw) === '') { throw new Exception('keine HTTP-Antwort'); }
        return trim($raw);
    }

    private function ParseXmlString($raw)
    {
        $xmlStart = strpos($raw, '<'); if ($xmlStart === false) { throw new Exception('Antwort ist kein XML: ' . substr($raw, 0, 100)); }
        if ($xmlStart > 0) { $raw = substr($raw, $xmlStart); }
        if ($this->ReadPropertyBoolean('DebugXml')) { $this->SendDebug('XML', substr($raw, 0, 500), 0); }
        $xml = @simplexml_load_string($raw); if (!$xml instanceof SimpleXMLElement) { throw new Exception('ungueltiges XML: ' . substr($raw, 0, 120)); }
        return $xml;
    }

    private function GetCategoryID($ident, $name, $position)
    {
        $id = @IPS_GetObjectIDByIdent($ident, $this->InstanceID);
        if ($id === false) { $id = IPS_CreateCategory(); IPS_SetParent($id, $this->InstanceID); IPS_SetIdent($id, $ident); IPS_SetName($id, $name); }
        IPS_SetPosition($id, $position); return $id;
    }

    private function RegisterFloat($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 2, $position); IPS_SetVariableCustomProfile($id, $profile); }
    private function RegisterInteger($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 1, $position); if ($profile !== '') { IPS_SetVariableCustomProfile($id, $profile); } }
    private function RegisterBoolean($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 0, $position); IPS_SetVariableCustomProfile($id, $profile); }
    private function RegisterString($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 3, $position); if ($profile !== '') { IPS_SetVariableCustomProfile($id, $profile); } }

    private function GetOrCreateVariable($parent, $ident, $name, $type, $position)
    {
        $id = @IPS_GetObjectIDByIdent($ident, $parent);
        if ($id === false) { $id = IPS_CreateVariable($type); IPS_SetParent($id, $parent); IPS_SetIdent($id, $ident); }
        IPS_SetName($id, $name); IPS_SetPosition($id, $position); return $id;
    }

    private function FindObjectByIdent($ident)
    {
        foreach (self::CATEGORIES as $catIdent => $meta) { $cat = @IPS_GetObjectIDByIdent($catIdent, $this->InstanceID); if ($cat === false) { continue; } $id = @IPS_GetObjectIDByIdent($ident, $cat); if ($id !== false) { return $id; } }
        return false;
    }

    private function SetValueByIdent($ident, $value) { $id = $this->FindObjectByIdent($ident); if ($id !== false) { SetValue($id, $value); } }
    private function GetValueByIdent($ident) { $id = $this->FindObjectByIdent($ident); return $id === false ? '' : GetValue($id); }

    private function CreateFloatProfile($name, $suffix, $digits) { if (!IPS_VariableProfileExists($name)) { IPS_CreateVariableProfile($name, 2); } IPS_SetVariableProfileDigits($name, $digits); IPS_SetVariableProfileText($name, '', ' ' . $suffix); }
    private function CreateBoolProfile($name, $falseText, $trueText, $falseColor, $trueColor) { if (!IPS_VariableProfileExists($name)) { IPS_CreateVariableProfile($name, 0); } IPS_SetVariableProfileAssociation($name, false, $falseText, '', $falseColor); IPS_SetVariableProfileAssociation($name, true, $trueText, '', $trueColor); }
    private function Csv($value) { return '"' . str_replace('"', '""', (string)$value) . '"'; }
    private function MapExplorerResult($data) { $map = []; foreach ($data as $entry) { if (isset($entry['xmlitem'])) { $map[$entry['xmlitem']] = $entry; } } return $map; }
    private function SuggestProfile($unit, $attributes) { if ($unit === 'pH') { return 'BPMXML.pH'; } if ($unit === 'mg/l') { return 'BPMXML.mg_l'; } if ($unit === 'mV') { return 'BPMXML.mV'; } if ($unit === 'C' || $unit === '°C') { return 'BPMXML.C'; } if ($unit === 'V') { return 'BPMXML.V'; } if ($unit === 'l') { return 'BPMXML.l'; } return isset($attributes['value']) ? '' : 'BPMXML.Alarm'; }
    private function MakeIdent($label, $xmlitem) { $base = preg_replace('/[^A-Za-z0-9]/', '', ucwords((string)$label)); if ($base === '') { $base = 'XML' . str_replace('.', '_', $xmlitem); } if (preg_match('/^[0-9]/', $base)) { $base = 'XML' . $base; } return $base; }
}
