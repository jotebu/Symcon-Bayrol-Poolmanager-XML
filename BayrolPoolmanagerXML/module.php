<?php

require_once __DIR__ . '/lib/ModuleIntegration.php';

class BayrolPoolmanagerXML extends IPSModule
{
    private const TIMER_MEASUREMENTS = 'UpdateTimer';
    private const TIMER_STATIC = 'StaticUpdateTimer';
    private const TIMER_DISCOVERY = 'DiscoverySchedulerTimer';

    private const CATEGORIES = [
        'Info' => ['name' => 'Informationen', 'pos' => 10],
        'Measurements' => ['name' => 'Messwerte', 'pos' => 20],
        'Setpoints' => ['name' => 'Sollwerte', 'pos' => 30],
        'Limits' => ['name' => 'Alarmgrenzen', 'pos' => 40],
        'Alarms' => ['name' => 'Alarme', 'pos' => 50],
        'Explorer' => ['name' => 'XML Explorer', 'pos' => 800],
        'Discovery' => ['name' => 'Discovery Engine', 'pos' => 850],
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
        $this->RegisterPropertyInteger('DiscoveryTypeStart', 1);
        $this->RegisterPropertyInteger('DiscoveryTypeEnd', 60);
        $this->RegisterPropertyInteger('DiscoveryIdStart', 0);
        $this->RegisterPropertyInteger('DiscoveryIdEnd', 500);
        $this->RegisterPropertyInteger('DiscoveryMaxPerRun', 500);
        $this->RegisterPropertyInteger('DiscoverySchedulerInterval', 10);
        $this->RegisterPropertyBoolean('DiscoveryOnlyValid', true);
        $this->RegisterPropertyBoolean('DiscoveryStoreRaw', false);
        $this->RegisterPropertyBoolean('EnableWritePreparation', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointPH', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointChlorineBromine', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointRedox1', false);
        $this->RegisterPropertyBoolean('AllowWriteSetpointRedox2', false);
        $this->RegisterTimer(self::TIMER_MEASUREMENTS, 0, 'BPMXML_Update($_IPS["TARGET"]);');
        $this->RegisterTimer(self::TIMER_STATIC, 0, 'BPMXML_UpdateStatic($_IPS["TARGET"]);');
        $this->RegisterTimer(self::TIMER_DISCOVERY, 0, 'BPMXML_RunDiscoveryBatch($_IPS["TARGET"]);');
    }

    public function ApplyChanges()
    {
        parent::ApplyChanges();
        $this->RegisterProfiles();
        $this->CreateStructure();
        $this->RegisterServiceVariables();
        $this->RegisterExplorerVariables();
        $this->RegisterDiscoveryVariables();
        $this->RegisterDataVariables();
        $this->ConfigureWritePreparation();

        if (trim($this->ReadPropertyString('Host')) === '') {
            $this->SetStatus(201);
            $this->SetTimerInterval(self::TIMER_MEASUREMENTS, 0);
            $this->SetTimerInterval(self::TIMER_STATIC, 0);
            $this->SetTimerInterval(self::TIMER_DISCOVERY, 0);
            return;
        }

        $this->SetTimerInterval(self::TIMER_MEASUREMENTS, max(0, $this->ReadPropertyInteger('Interval')) * 1000);
        $this->SetTimerInterval(self::TIMER_STATIC, max(0, $this->ReadPropertyInteger('StaticInterval')) * 60 * 1000);
        $this->SetTimerInterval(self::TIMER_DISCOVERY, $this->GetValueByIdent('DiscoverySchedulerRunning') ? max(1, $this->ReadPropertyInteger('DiscoverySchedulerInterval')) * 1000 : 0);
        $this->SetStatus(102);
        if ($this->ReadPropertyBoolean('AutoUpdateAfterApply')) { $this->Update(); }
    }

    public function Update() { return $this->UpdateInternal(false); }
    public function UpdateStatic() { return $this->UpdateInternal(true); }
    public function RunExplorer() { return $this->RunExplorerRange($this->ReadPropertyInteger('ExplorerType'), $this->ReadPropertyInteger('ExplorerStart'), $this->ReadPropertyInteger('ExplorerEnd'), 'Freier Scan'); }
    public function RunExplorerMeasurements() { return $this->RunExplorerRange(34, 4000, 4100, 'Preset Messwerte 34.4000-4100'); }
    public function RunExplorerSetpoints() { return $this->RunExplorerRange(34, 3000, 3100, 'Preset Sollwerte 34.3000-3100'); }
    public function RunExplorerAlarms() { return $this->RunExplorerRange(44, 2000, 2050, 'Preset Alarme 44.2000-2050'); }
    public function RunDiscovery() { return $this->RunDiscoveryRange($this->ReadPropertyInteger('DiscoveryTypeStart'), $this->ReadPropertyInteger('DiscoveryTypeEnd'), $this->ReadPropertyInteger('DiscoveryIdStart'), $this->ReadPropertyInteger('DiscoveryIdEnd'), 'Discovery'); }

    public function StartDiscoveryScheduler()
    {
        $this->SetValueByIdent('DiscoverySchedulerRunning', true);
        if ($this->GetValueByIdent('DiscoveryCursorType') == 0 && $this->GetValueByIdent('DiscoveryCursorId') == 0) {
            $this->SetValueByIdent('DiscoveryCursorType', $this->ReadPropertyInteger('DiscoveryTypeStart'));
            $this->SetValueByIdent('DiscoveryCursorId', $this->ReadPropertyInteger('DiscoveryIdStart'));
        }
        $this->SetTimerInterval(self::TIMER_DISCOVERY, max(1, $this->ReadPropertyInteger('DiscoverySchedulerInterval')) * 1000);
        return $this->RunDiscoveryBatch();
    }

    public function StopDiscoveryScheduler()
    {
        $this->SetValueByIdent('DiscoverySchedulerRunning', false);
        $this->SetTimerInterval(self::TIMER_DISCOVERY, 0);
        $this->SetValueByIdent('DiscoverySummary', 'Discovery Scheduler gestoppt: ' . date('Y-m-d H:i:s'));
        return true;
    }

    public function RunDiscoveryBatch()
    {
        if (trim($this->ReadPropertyString('Host')) === '') { $this->SetStatus(201); return false; }
        $typeStart = $this->ReadPropertyInteger('DiscoveryTypeStart'); $typeEnd = $this->ReadPropertyInteger('DiscoveryTypeEnd');
        $idStart = $this->ReadPropertyInteger('DiscoveryIdStart'); $idEnd = $this->ReadPropertyInteger('DiscoveryIdEnd');
        if ($typeEnd < $typeStart) { $tmp = $typeStart; $typeStart = $typeEnd; $typeEnd = $tmp; }
        if ($idEnd < $idStart) { $tmp = $idStart; $idStart = $idEnd; $idEnd = $tmp; }
        $type = (int)$this->GetValueByIdent('DiscoveryCursorType'); $id = (int)$this->GetValueByIdent('DiscoveryCursorId');
        if ($type < $typeStart || $type > $typeEnd) { $type = $typeStart; }
        if ($id < $idStart || $id > $idEnd) { $id = $idStart; }

        $max = max(1, min($this->ReadPropertyInteger('DiscoveryMaxPerRun'), 5000));
        $storeRaw = $this->ReadPropertyBoolean('DiscoveryStoreRaw');
        $onlyValid = $this->ReadPropertyBoolean('DiscoveryOnlyValid');
        $results = []; $csv = "xmlitem;class;confidence;valid;label;unit;value;active;displayed;attributes;duration_ms;error\n";
        $scanned = 0; $valid = 0; $errors = 0; $completed = false;

        while ($type <= $typeEnd && $scanned < $max) {
            $entry = $this->ScanXmlItem($type, $id, $storeRaw);
            $scanned++;
            if (!empty($entry['valid'])) { $valid++; } else { $errors++; }
            if (!$onlyValid || !empty($entry['valid'])) {
                $results[] = $entry;
                $a = $entry['attributes'];
                $csv .= $this->Csv($entry['xmlitem']) . ';' . $this->Csv($entry['class']) . ';' . $this->Csv((string)$entry['confidence']) . ';' . ($entry['valid'] ? '1' : '0') . ';' . $this->Csv($a['label'] ?? '') . ';' . $this->Csv($a['unit'] ?? '') . ';' . $this->Csv($a['value'] ?? '') . ';' . $this->Csv($a['active'] ?? '') . ';' . $this->Csv($a['displayed'] ?? '') . ';' . $this->Csv(json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . ';' . $this->Csv((string)$entry['duration_ms']) . ';' . $this->Csv($entry['error']) . "\n";
            }
            $id++;
            if ($id > $idEnd) { $id = $idStart; $type++; }
        }
        if ($type > $typeEnd) { $completed = true; $type = $typeEnd; $id = $idEnd; }

        $this->SetValueByIdent('DiscoveryCursorType', $type);
        $this->SetValueByIdent('DiscoveryCursorId', $id);
        $total = max(1, (($typeEnd - $typeStart + 1) * ($idEnd - $idStart + 1)));
        $done = min($total, max(0, (($type - $typeStart) * ($idEnd - $idStart + 1)) + ($id - $idStart)));
        $progress = $completed ? 100 : (int)floor(($done / $total) * 100);
        $this->SetValueByIdent('DiscoveryProgress', $progress);

        $db = $this->MergeDiscoveryDB($results);
        $summary = 'Discovery Batch: abgefragt ' . $scanned . ', gueltig ' . $valid . ', gespeichert ' . count($results) . ', Cursor ' . $type . '.' . $id . ', Fortschritt ' . $progress . '%, Zeit ' . date('Y-m-d H:i:s');
        if ($completed) { $summary .= ' - abgeschlossen'; $this->SetValueByIdent('DiscoverySchedulerRunning', false); $this->SetTimerInterval(self::TIMER_DISCOVERY, 0); }
        $this->SetValueByIdent('DiscoverySummary', $summary);
        $this->SetValueByIdent('DiscoveryCSV', $csv);
        $this->SetValueByIdent('DiscoveryJSON', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->SetValueByIdent('DiscoveryDB', json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $this->SetValueByIdent('DiscoveryReport', $this->BuildDiscoveryReport($results, $db, $scanned, $valid, $errors));
        $this->SetValueByIdent('DiscoveryLastRun', time());
        return true;
    }

    public function StoreDiscoverySnapshotA() { $this->RunDiscovery(); $this->SetValueByIdent('DiscoverySnapshotA', $this->GetValueByIdent('DiscoveryJSON')); $this->SetValueByIdent('DiscoveryCompare', 'Discovery Snapshot A gespeichert: ' . date('Y-m-d H:i:s')); return true; }
    public function StoreDiscoverySnapshotB() { $this->RunDiscovery(); $this->SetValueByIdent('DiscoverySnapshotB', $this->GetValueByIdent('DiscoveryJSON')); $this->SetValueByIdent('DiscoveryCompare', 'Discovery Snapshot B gespeichert: ' . date('Y-m-d H:i:s')); return true; }
    public function CompareDiscoverySnapshots() { $a = json_decode($this->GetValueByIdent('DiscoverySnapshotA'), true); $b = json_decode($this->GetValueByIdent('DiscoverySnapshotB'), true); if (!is_array($a) || !is_array($b)) { $this->SetValueByIdent('DiscoveryCompare', 'Discovery Snapshot A oder B fehlt / ist kein gueltiges JSON.'); return false; } $this->SetValueByIdent('DiscoveryCompare', $this->BuildSnapshotCompareText($a, $b)); return true; }
    public function StoreSnapshotA() { $this->RunExplorer(); $this->SetValueByIdent('ExplorerSnapshotA', $this->GetValueByIdent('ExplorerJSON')); $this->SetValueByIdent('ExplorerCompare', 'Snapshot A gespeichert: ' . date('Y-m-d H:i:s')); return true; }
    public function StoreSnapshotB() { $this->RunExplorer(); $this->SetValueByIdent('ExplorerSnapshotB', $this->GetValueByIdent('ExplorerJSON')); $this->SetValueByIdent('ExplorerCompare', 'Snapshot B gespeichert: ' . date('Y-m-d H:i:s')); return true; }
    public function CompareSnapshots() { $a = json_decode($this->GetValueByIdent('ExplorerSnapshotA'), true); $b = json_decode($this->GetValueByIdent('ExplorerSnapshotB'), true); if (!is_array($a) || !is_array($b)) { $this->SetValueByIdent('ExplorerCompare', 'Snapshot A oder B fehlt / ist kein gueltiges JSON.'); return false; } $this->SetValueByIdent('ExplorerCompare', $this->BuildSnapshotCompareText($a, $b)); return true; }
    public function GenerateDefinitionFromExplorer() { $data = json_decode($this->GetValueByIdent('ExplorerJSON'), true); if (!is_array($data)) { $this->SetValueByIdent('ExplorerDefinition', 'Kein gueltiges Explorer-JSON vorhanden.'); return false; } $this->SetValueByIdent('ExplorerDefinition', $this->GenerateDefinitionText($data)); return true; }
    public function ClearExplorer() { $ids = ['ExplorerSummary','ExplorerCSV','ExplorerJSON','ExplorerLastRun','ExplorerSnapshotA','ExplorerSnapshotB','ExplorerCompare','ExplorerDefinition','DiscoverySummary','DiscoveryCSV','DiscoveryJSON','DiscoveryDB','DiscoverySnapshotA','DiscoverySnapshotB','DiscoveryCompare','DiscoveryReport','DiscoveryLastRun','DiscoverySchedulerRunning','DiscoveryCursorType','DiscoveryCursorId','DiscoveryProgress']; foreach ($ids as $ident) { $this->SetValueByIdent($ident, (strpos($ident, 'LastRun') !== false || strpos($ident, 'Cursor') !== false || $ident === 'DiscoveryProgress') ? 0 : ($ident === 'DiscoverySchedulerRunning' ? false : '')); } $this->SetTimerInterval(self::TIMER_DISCOVERY, 0); }
    public function RequestAction($Ident, $Value) { if (!$this->IsWritePrepared($Ident)) { throw new Exception('Schreibzugriff fuer ' . $Ident . ' ist nicht freigegeben.'); } throw new Exception('Schreiben ist absichtlich noch gesperrt. Die Schreibfreigaben sind nur vorbereitet, bis die PM5-Write-URLs sicher getestet wurden.'); }

    private function RunDiscoveryRange($typeStart, $typeEnd, $idStart, $idEnd, $title)
    {
        if (trim($this->ReadPropertyString('Host')) === '') { $this->SetStatus(201); return false; }
        if ($typeEnd < $typeStart) { $tmp = $typeStart; $typeStart = $typeEnd; $typeEnd = $tmp; }
        if ($idEnd < $idStart) { $tmp = $idStart; $idStart = $idEnd; $idEnd = $tmp; }
        $max = max(1, min($this->ReadPropertyInteger('DiscoveryMaxPerRun'), 5000)); $onlyValid = $this->ReadPropertyBoolean('DiscoveryOnlyValid'); $storeRaw = $this->ReadPropertyBoolean('DiscoveryStoreRaw');
        $results = []; $csv = "xmlitem;class;confidence;valid;label;unit;value;active;displayed;attributes;duration_ms;error\n"; $scanned = 0; $valid = 0; $errors = 0;
        for ($type = $typeStart; $type <= $typeEnd && $scanned < $max; $type++) { for ($id = $idStart; $id <= $idEnd && $scanned < $max; $id++) { $entry = $this->ScanXmlItem($type, $id, $storeRaw); $scanned++; if (!empty($entry['valid'])) { $valid++; } else { $errors++; } if (!$onlyValid || !empty($entry['valid'])) { $results[] = $entry; $a = $entry['attributes']; $csv .= $this->Csv($entry['xmlitem']) . ';' . $this->Csv($entry['class']) . ';' . $this->Csv((string)$entry['confidence']) . ';' . ($entry['valid'] ? '1' : '0') . ';' . $this->Csv($a['label'] ?? '') . ';' . $this->Csv($a['unit'] ?? '') . ';' . $this->Csv($a['value'] ?? '') . ';' . $this->Csv($a['active'] ?? '') . ';' . $this->Csv($a['displayed'] ?? '') . ';' . $this->Csv(json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . ';' . $this->Csv((string)$entry['duration_ms']) . ';' . $this->Csv($entry['error']) . "\n"; } } }
        $db = $this->MergeDiscoveryDB($results); $summary = $title . ': Typ ' . $typeStart . '-' . $typeEnd . ', ID ' . $idStart . '-' . $idEnd . ', abgefragt ' . $scanned . ', gueltig ' . $valid . ', gespeichert ' . count($results) . ', Zeit ' . date('Y-m-d H:i:s');
        $this->SetValueByIdent('DiscoverySummary', $summary); $this->SetValueByIdent('DiscoveryCSV', $csv); $this->SetValueByIdent('DiscoveryJSON', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); $this->SetValueByIdent('DiscoveryDB', json_encode($db, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); $this->SetValueByIdent('DiscoveryReport', $this->BuildDiscoveryReport($results, $db, $scanned, $valid, $errors)); $this->SetValueByIdent('DiscoveryLastRun', time()); return true;
    }

    private function ScanXmlItem($type, $id, $storeRaw) { $started = microtime(true); $entry = ['xmlitem' => $type . '.' . $id, 'valid' => false, 'attributes' => [], 'duration_ms' => 0, 'error' => '', 'class' => 'invalid', 'confidence' => 0]; try { $raw = $this->FetchRawXml($type, $id); $entry['duration_ms'] = round((microtime(true) - $started) * 1000, 1); $xml = $this->ParseXmlString($raw); if (!isset($xml->item)) { throw new Exception('XML enthaelt kein item'); } foreach ($xml->item->attributes() as $key => $value) { $entry['attributes'][(string)$key] = (string)$value; } $entry['valid'] = true; $class = $this->ClassifyDiscoveryEntry($entry['attributes']); $entry['class'] = $class['class']; $entry['confidence'] = $class['confidence']; if ($storeRaw) { $entry['raw'] = substr(trim($raw), 0, 2000); } } catch (Exception $e) { $entry['duration_ms'] = round((microtime(true) - $started) * 1000, 1); $entry['error'] = $e->getMessage(); } return $entry; }
    private function ClassifyDiscoveryEntry($a) { $label = strtolower((string)($a['label'] ?? '')); $unit = (string)($a['unit'] ?? ''); $value = (string)($a['value'] ?? ''); if (isset($a['active']) || isset($a['displayed'])) { return ['class' => 'alarm', 'confidence' => 95]; } if (strpos($label, 'status') !== false) { return ['class' => 'status', 'confidence' => 90]; } if (strpos($label, 'betriebsart') !== false) { return ['class' => 'operating_mode', 'confidence' => 90]; } foreach (['out ', 'relais', 'licht', 'pumpe', 'filter', 'heizung', 'solar', 'eco', 'flock', 'salz', 'elektrolyse', 'ventil'] as $kw) { if (strpos($label, $kw) !== false && $unit === '' && ($value === '0' || $value === '1')) { return ['class' => 'output_status', 'confidence' => 88]; } } if (strpos($label, 'alarm') !== false || strpos($label, 'grenze') !== false) { return ['class' => 'limit', 'confidence' => 80]; } if (strpos($label, 'soll') !== false || strpos($label, 'setpoint') !== false) { return ['class' => 'setpoint', 'confidence' => 80]; } if (strpos($label, 'kalib') !== false || preg_match('/(ph|mv|cl).*\d/', $label)) { return ['class' => 'calibration', 'confidence' => 70]; } if (in_array($unit, ['pH', 'mV', 'mg/l', 'µA', 'mA', '°C', 'C', 'V', 'l', 'min', '%', 'mS/cm'], true)) { return ['class' => 'measurement', 'confidence' => 75]; } if ($unit === '' && is_numeric($value)) { return ['class' => 'numeric_status_or_config', 'confidence' => 50]; } return ['class' => 'unknown', 'confidence' => 20]; }
    private function MergeDiscoveryDB($results) { $db = json_decode($this->GetValueByIdent('DiscoveryDB'), true); if (!is_array($db)) { $db = []; } $now = date('Y-m-d H:i:s'); foreach ($results as $entry) { if (empty($entry['valid'])) { continue; } $key = $entry['xmlitem']; if (!isset($db[$key])) { $db[$key] = ['xmlitem' => $key, 'first_seen' => $now, 'scan_count' => 0]; } $db[$key]['last_seen'] = $now; $db[$key]['scan_count'] = (int)($db[$key]['scan_count'] ?? 0) + 1; $db[$key]['class'] = $entry['class']; $db[$key]['confidence'] = $entry['confidence']; $db[$key]['attributes'] = $entry['attributes']; } ksort($db); return $db; }
    private function BuildDiscoveryReport($results, $db, $scanned, $valid, $errors) { $classes = []; foreach ($results as $entry) { $c = $entry['class'] ?? 'unknown'; $classes[$c] = ($classes[$c] ?? 0) + 1; } ksort($classes); $lines = ['Bayrol PM5 Discovery Report','Zeit: ' . date('Y-m-d H:i:s'),'Abfragen: ' . $scanned,'Gueltig: ' . $valid,'Fehler/ungueltig: ' . $errors,'Discovery-DB Eintraege gesamt: ' . count($db),'','Klassen im aktuellen Lauf:']; foreach ($classes as $class => $count) { $lines[] = '- ' . $class . ': ' . $count; } return implode("\n", $lines); }
    private function BuildSnapshotCompareText($a, $b) { $ma = $this->MapExplorerResult($a); $mb = $this->MapExplorerResult($b); $relevant = []; $ignored = []; $all = []; foreach ($mb as $xmlitem => $entryB) { if (!isset($ma[$xmlitem])) { $line = '+ ' . $this->FormatExplorerEntry($xmlitem, [], $entryB['attributes'] ?? []); $relevant[] = $line; $all[] = $line; continue; } $oldAttr = $ma[$xmlitem]['attributes'] ?? []; $newAttr = $entryB['attributes'] ?? []; if (json_encode($oldAttr) === json_encode($newAttr)) { continue; } $line = '* ' . $this->FormatExplorerEntry($xmlitem, $oldAttr, $newAttr); $all[] = $line; if ($this->IsRelevantSwitchChange($oldAttr, $newAttr)) { $relevant[] = $line; } else { $ignored[] = $line; } } foreach ($ma as $xmlitem => $entryA) { if (!isset($mb[$xmlitem])) { $line = '- ' . $this->FormatExplorerEntry($xmlitem, $entryA['attributes'] ?? [], []); $relevant[] = $line; $all[] = $line; } } $text = ['Relevante Status-/Schalt-Aenderungen: ' . count($relevant),'Ignorierte Messwert-Drift: ' . count($ignored),'Alle Aenderungen: ' . count($all),'']; if (count($relevant) > 0) { $text[] = '--- RELEVANT ---'; $text[] = implode("\n", $relevant); } else { $text[] = 'Keine relevanten Status-/Schalt-Aenderungen erkannt.'; } if (count($ignored) > 0) { $text[] = ''; $text[] = '--- IGNORIERT: Messwert-Drift / analoge Werte ---'; $text[] = implode("\n", array_slice($ignored, 0, 80)); if (count($ignored) > 80) { $text[] = '... weitere ignorierte Aenderungen: ' . (count($ignored) - 80); } } return implode("\n", $text); }
    private function GenerateDefinitionText($data) { $out = "[\n"; foreach ($data as $entry) { if (empty($entry['valid']) || empty($entry['attributes'])) { continue; } $a = $entry['attributes']; $label = $a['label'] ?? $entry['xmlitem']; $unit = $a['unit'] ?? ''; $profile = $this->SuggestProfile($unit, $a); $ident = $this->MakeIdent($label, $entry['xmlitem']); $category = isset($a['active']) || isset($a['displayed']) ? 'Alarms' : 'Measurements'; $out .= "    ['xml' => '" . $entry['xmlitem'] . "', 'ident' => '" . $ident . "', 'name' => '" . addslashes($label) . "', 'category' => '" . $category . "', 'profile' => '" . $profile . "', 'kind' => 'explored'],\n"; } return $out . "]"; }
    private function RunExplorerRange($type, $start, $end, $title) { if (trim($this->ReadPropertyString('Host')) === '') { $this->SetStatus(201); return false; } if ($end < $start) { $tmp = $start; $start = $end; $end = $tmp; } $max = max(1, min($this->ReadPropertyInteger('ExplorerMaxPerRun'), 1000)); $onlyValid = $this->ReadPropertyBoolean('ExplorerOnlyValid'); $storeRaw = $this->ReadPropertyBoolean('ExplorerStoreRaw'); $results = []; $csv = "xmlitem;valid;label;unit;value;active;displayed;attributes;duration_ms;error\n"; $scanned = 0; $valid = 0; for ($id = $start; $id <= $end && $scanned < $max; $id++) { $entry = $this->ScanXmlItem($type, $id, $storeRaw); $scanned++; if (!empty($entry['valid'])) { $valid++; } if (!$onlyValid || !empty($entry['valid'])) { $results[] = $entry; $a = $entry['attributes']; $csv .= $this->Csv($entry['xmlitem']) . ';' . ($entry['valid'] ? '1' : '0') . ';' . $this->Csv($a['label'] ?? '') . ';' . $this->Csv($a['unit'] ?? '') . ';' . $this->Csv($a['value'] ?? '') . ';' . $this->Csv($a['active'] ?? '') . ';' . $this->Csv($a['displayed'] ?? '') . ';' . $this->Csv(json_encode($a, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . ';' . $this->Csv((string)$entry['duration_ms']) . ';' . $this->Csv($entry['error']) . "\n"; } } $summary = $title . ': Typ ' . $type . ', Bereich ' . $start . '-' . $end . ', abgefragt ' . $scanned . ', gueltig ' . $valid . ', gespeichert ' . count($results) . ', Zeit ' . date('Y-m-d H:i:s'); $this->SetValueByIdent('ExplorerSummary', $summary); $this->SetValueByIdent('ExplorerCSV', $csv); $this->SetValueByIdent('ExplorerJSON', json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)); $this->SetValueByIdent('ExplorerLastRun', time()); return true; }
    private function UpdateInternal($staticOnly) { if (trim($this->ReadPropertyString('Host')) === '') { $this->SetStatus(201); return false; } $ok = 0; $errors = []; if (!$staticOnly) { foreach (self::ITEMS as $item) { if ($item['kind'] === 'setpoint' && !$this->ReadPropertyBoolean('ReadSetpoints')) { continue; } if ($this->ReadValueItem($item, $errors)) { $ok++; } } if ($this->ReadPropertyBoolean('ReadAlarms')) { foreach (self::ALARMS as $id => $label) { if ($this->ReadAlarmItem($id, $label, $errors)) { $ok++; } } } if ($this->ReadPropertyBoolean('ReadAlarmList')) { try { $this->SetValueByIdent('AlarmList', $this->ReadAlarmListText()); $ok++; } catch (Exception $e) { $errors[] = '1.1092: ' . $e->getMessage(); } } } $this->SetValueByIdent('Online', $ok > 0 || $staticOnly); $this->SetValueByIdent('SuccessfulReads', $ok); $this->SetValueByIdent('LastUpdate', time()); $this->SetValueByIdent('LastError', implode("\n", array_slice($errors, 0, 40))); $this->SetValueByIdent('WriteMode', $this->ReadPropertyBoolean('EnableWritePreparation')); if ($ok > 0 || $staticOnly) { $this->SetStatus(102); return true; } $this->SetStatus(202); return false; }
    private function ReadValueItem($definition, &$errors) { try { list($type, $id) = explode('.', $definition['xml']); $item = $this->ReadItem((int)$type, (int)$id); if (!array_key_exists('value', $item)) { throw new Exception('Attribut value fehlt'); } $this->SetValueByIdent($definition['ident'], (float)$item['value']); return true; } catch (Exception $e) { $errors[] = $definition['xml'] . ' ' . $definition['name'] . ': ' . $e->getMessage(); return false; } }
    private function ReadAlarmItem($id, $label, &$errors) { try { $item = $this->ReadItem(44, $id); $this->SetValueByIdent('AlarmActive' . $id, isset($item['active']) && ((int)$item['active']) === 1); $this->SetValueByIdent('AlarmDisplayed' . $id, isset($item['displayed']) && ((int)$item['displayed']) === 1); return true; } catch (Exception $e) { $errors[] = '44.' . $id . ' ' . $label . ': ' . $e->getMessage(); return false; } }
    private function CreateStructure() { foreach (self::CATEGORIES as $ident => $meta) { $this->GetCategoryID($ident, $meta['name'], $meta['pos']); } }
    private function RegisterDataVariables() { $pos = 10; foreach (self::ITEMS as $item) { if ($item['kind'] === 'setpoint' && !$this->ReadPropertyBoolean('ReadSetpoints')) { continue; } $cat = $this->GetCategoryID($item['category'], self::CATEGORIES[$item['category']]['name'], self::CATEGORIES[$item['category']]['pos']); $this->RegisterFloat($cat, $item['ident'], $item['name'], $item['profile'], $pos++); } if ($this->ReadPropertyBoolean('ReadAlarms')) { $cat = $this->GetCategoryID('Alarms', 'Alarme', 50); $pos = 10; foreach (self::ALARMS as $id => $label) { $this->RegisterBoolean($cat, 'AlarmActive' . $id, $label . ' aktiv', 'BPMXML.Alarm', $pos++); $this->RegisterBoolean($cat, 'AlarmDisplayed' . $id, $label . ' angezeigt', 'BPMXML.Alarm', $pos++); } $this->RegisterString($cat, 'AlarmList', 'Alarmuebersicht', '', 900); } }
    private function RegisterServiceVariables() { $cat = $this->GetCategoryID('Service', 'Service', 900); $this->RegisterBoolean($cat, 'Online', 'Kommunikation online', 'BPMXML.Online', 10); $this->RegisterInteger($cat, 'SuccessfulReads', 'Erfolgreiche XML-Abfragen', '', 20); $this->RegisterInteger($cat, 'LastUpdate', 'Letzte Aktualisierung', '~UnixTimestamp', 30); $this->RegisterString($cat, 'LastError', 'Letzte Fehler / uebersprungene Adressen', '', 40); $this->RegisterBoolean($cat, 'WriteMode', 'Schreibmodus vorbereitet', 'BPMXML.WriteMode', 50); }
    private function RegisterExplorerVariables() { $cat = $this->GetCategoryID('Explorer', 'XML Explorer', 800); $this->RegisterString($cat, 'ExplorerSummary', 'Scan-Zusammenfassung', '', 10); $this->RegisterString($cat, 'ExplorerCSV', 'Scan-Ergebnis CSV', '', 20); $this->RegisterString($cat, 'ExplorerJSON', 'Scan-Ergebnis JSON', '', 30); $this->RegisterInteger($cat, 'ExplorerLastRun', 'Letzter Explorer-Lauf', '~UnixTimestamp', 40); $this->RegisterString($cat, 'ExplorerSnapshotA', 'Snapshot A JSON', '', 50); $this->RegisterString($cat, 'ExplorerSnapshotB', 'Snapshot B JSON', '', 60); $this->RegisterString($cat, 'ExplorerCompare', 'Snapshot Vergleich', '', 70); $this->RegisterString($cat, 'ExplorerDefinition', 'PHP-Definition Vorschlag', '', 80); }
    private function RegisterDiscoveryVariables() { $cat = $this->GetCategoryID('Discovery', 'Discovery Engine', 850); $this->RegisterString($cat, 'DiscoverySummary', 'Discovery Zusammenfassung', '', 10); $this->RegisterString($cat, 'DiscoveryCSV', 'Discovery CSV', '', 20); $this->RegisterString($cat, 'DiscoveryJSON', 'Discovery JSON aktueller Lauf', '', 30); $this->RegisterString($cat, 'DiscoveryDB', 'Discovery Datenbank JSON', '', 40); $this->RegisterString($cat, 'DiscoverySnapshotA', 'Discovery Snapshot A JSON', '', 50); $this->RegisterString($cat, 'DiscoverySnapshotB', 'Discovery Snapshot B JSON', '', 60); $this->RegisterString($cat, 'DiscoveryCompare', 'Discovery Snapshot Vergleich', '', 70); $this->RegisterString($cat, 'DiscoveryReport', 'Discovery Report', '', 80); $this->RegisterInteger($cat, 'DiscoveryLastRun', 'Letzter Discovery-Lauf', '~UnixTimestamp', 90); $this->RegisterBoolean($cat, 'DiscoverySchedulerRunning', 'Scheduler aktiv', 'BPMXML.Online', 100); $this->RegisterInteger($cat, 'DiscoveryCursorType', 'Cursor Typ', '', 110); $this->RegisterInteger($cat, 'DiscoveryCursorId', 'Cursor ID', '', 120); $this->RegisterInteger($cat, 'DiscoveryProgress', 'Discovery Fortschritt', 'BPMXML.percent_int', 130); }
    private function RegisterProfiles() { $this->CreateFloatProfile('BPMXML.pH', 'pH', 2); $this->CreateFloatProfile('BPMXML.mg_l', 'mg/l', 2); $this->CreateFloatProfile('BPMXML.mV', 'mV', 0); $this->CreateFloatProfile('BPMXML.C', ' C', 1); $this->CreateFloatProfile('BPMXML.V', 'V', 2); $this->CreateFloatProfile('BPMXML.l', 'l', 1); $this->CreateFloatProfile('BPMXML.percent', '%', 0); $this->CreateIntegerProfile('BPMXML.percent_int', '%'); $this->CreateFloatProfile('BPMXML.min', 'min', 0); $this->CreateFloatProfile('BPMXML.microA', 'µA', 2); $this->CreateFloatProfile('BPMXML.mA', 'mA', 1); $this->CreateFloatProfile('BPMXML.mScm', 'mS/cm', 1); $this->CreateBoolProfile('BPMXML.Alarm', 'OK', 'Alarm', 0x00AA00, 0xFF0000); $this->CreateBoolProfile('BPMXML.Online', 'Offline', 'Online', 0xFF0000, 0x00AA00); $this->CreateBoolProfile('BPMXML.WriteMode', 'Read-only', 'Freigegeben', 0x00AA00, 0xFFA500); }
    private function ConfigureWritePreparation() { foreach (self::ITEMS as $item) { if (!isset($item['writeProperty'])) { continue; } $id = $this->FindObjectByIdent($item['ident']); if ($id === false) { continue; } IPS_SetVariableCustomAction($id, $this->IsWritePrepared($item['ident']) ? $this->InstanceID : 0); } }
    private function IsWritePrepared($ident) { if (!$this->ReadPropertyBoolean('EnableWritePreparation')) { return false; } foreach (self::ITEMS as $item) { if ($item['ident'] === $ident && isset($item['writeProperty'])) { return $this->ReadPropertyBoolean($item['writeProperty']); } } return false; }
    private function ReadItem($type, $id) { return $this->XmlClient()->itemAttributes((int)$type, (int)$id); }
    private function ReadAlarmListText() { $xml = $this->FetchXml(1, 1092); $lines = []; if (isset($xml->item->item)) { foreach ($xml->item->item as $alarm) { $a = $alarm->attributes(); $lines[] = sprintf('%s: aktiv=%s, angezeigt=%s', (string)$a['label'], (string)$a['active'], (string)$a['displayed']); } } return implode("\n", $lines); }
    private function FetchXml($type, $id) { return $this->ParseXmlString($this->FetchRawXml($type, $id)); }
    private function FetchRawXml($type, $id) { return $this->XmlClient()->fetchRaw((int)$type, (int)$id); }
    private function ParseXmlString($raw) { return $this->XmlClient()->parseXml((string)$raw); }
    private function XmlClient() { return new BPMXML_XmlClient($this->ReadPropertyString('Host'), $this->ReadPropertyInteger('Timeout'), $this->ReadPropertyBoolean('DebugXml'), function ($name, $message) { $this->SendDebug($name, $message, 0); }); }
    private function GetCategoryID($ident, $name, $position) { $id = @IPS_GetObjectIDByIdent($ident, $this->InstanceID); if ($id === false) { $id = IPS_CreateCategory(); IPS_SetParent($id, $this->InstanceID); IPS_SetIdent($id, $ident); IPS_SetName($id, $name); } IPS_SetPosition($id, $position); return $id; }
    private function RegisterFloat($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 2, $position); IPS_SetVariableCustomProfile($id, $profile); }
    private function RegisterInteger($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 1, $position); if ($profile !== '') { IPS_SetVariableCustomProfile($id, $profile); } }
    private function RegisterBoolean($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 0, $position); IPS_SetVariableCustomProfile($id, $profile); }
    private function RegisterString($parent, $ident, $name, $profile, $position) { $id = $this->GetOrCreateVariable($parent, $ident, $name, 3, $position); if ($profile !== '') { IPS_SetVariableCustomProfile($id, $profile); } }
    private function GetOrCreateVariable($parent, $ident, $name, $type, $position) { $id = @IPS_GetObjectIDByIdent($ident, $parent); if ($id === false) { $id = IPS_CreateVariable($type); IPS_SetParent($id, $parent); IPS_SetIdent($id, $ident); } IPS_SetName($id, $name); IPS_SetPosition($id, $position); return $id; }
    private function FindObjectByIdent($ident) { foreach (self::CATEGORIES as $catIdent => $meta) { $cat = @IPS_GetObjectIDByIdent($catIdent, $this->InstanceID); if ($cat === false) { continue; } $id = @IPS_GetObjectIDByIdent($ident, $cat); if ($id !== false) { return $id; } } return false; }
    private function SetValueByIdent($ident, $value) { $id = $this->FindObjectByIdent($ident); if ($id !== false) { SetValue($id, $value); } }
    private function GetValueByIdent($ident) { $id = $this->FindObjectByIdent($ident); return $id === false ? '' : GetValue($id); }
    private function CreateFloatProfile($name, $suffix, $digits) { if (!IPS_VariableProfileExists($name)) { IPS_CreateVariableProfile($name, 2); } IPS_SetVariableProfileDigits($name, $digits); IPS_SetVariableProfileText($name, '', ' ' . $suffix); }
    private function CreateIntegerProfile($name, $suffix) { if (!IPS_VariableProfileExists($name)) { IPS_CreateVariableProfile($name, 1); } IPS_SetVariableProfileText($name, '', ' ' . $suffix); }
    private function CreateBoolProfile($name, $falseText, $trueText, $falseColor, $trueColor) { if (!IPS_VariableProfileExists($name)) { IPS_CreateVariableProfile($name, 0); } IPS_SetVariableProfileAssociation($name, false, $falseText, '', $falseColor); IPS_SetVariableProfileAssociation($name, true, $trueText, '', $trueColor); }
    private function Csv($value) { return '"' . str_replace('"', '""', (string)$value) . '"'; }
    private function MapExplorerResult($data) { $map = []; foreach ($data as $entry) { if (isset($entry['xmlitem'])) { $map[$entry['xmlitem']] = $entry; } } return $map; }
    private function SuggestProfile($unit, $attributes) { if ($unit === 'pH') { return 'BPMXML.pH'; } if ($unit === 'mg/l') { return 'BPMXML.mg_l'; } if ($unit === 'mV') { return 'BPMXML.mV'; } if ($unit === 'C' || $unit === '°C') { return 'BPMXML.C'; } if ($unit === 'V') { return 'BPMXML.V'; } if ($unit === 'l') { return 'BPMXML.l'; } if ($unit === '%') { return 'BPMXML.percent'; } if ($unit === 'min') { return 'BPMXML.min'; } if ($unit === 'µA') { return 'BPMXML.microA'; } if ($unit === 'mA') { return 'BPMXML.mA'; } if ($unit === 'mS/cm') { return 'BPMXML.mScm'; } return isset($attributes['value']) ? '' : 'BPMXML.Alarm'; }
    private function MakeIdent($label, $xmlitem) { $base = preg_replace('/[^A-Za-z0-9]/', '', ucwords((string)$label)); if ($base === '') { $base = 'XML' . str_replace('.', '_', $xmlitem); } if (preg_match('/^[0-9]/', $base)) { $base = 'XML' . $base; } return $base; }
    private function IsRelevantSwitchChange($oldAttr, $newAttr) { $label = strtolower(($newAttr['label'] ?? '') . ' ' . ($oldAttr['label'] ?? '')); $unit = (string)($newAttr['unit'] ?? $oldAttr['unit'] ?? ''); $oldValue = (string)($oldAttr['value'] ?? ''); $newValue = (string)($newAttr['value'] ?? ''); if (isset($newAttr['active']) || isset($newAttr['displayed']) || isset($oldAttr['active']) || isset($oldAttr['displayed'])) { return true; } $keywords = ['status','betrieb','betriebsart','out','relais','licht','lampe','pumpe','filter','flock','heizung','solar','eco','salz','elektrolyse','cover','abdeckung','ventil']; $keywordHit = false; foreach ($keywords as $keyword) { if (strpos($label, $keyword) !== false) { $keywordHit = true; break; } } if ($keywordHit && $unit === '' && in_array($oldValue, ['0','1'], true) && in_array($newValue, ['0','1'], true) && $oldValue !== $newValue) { return true; } if ($keywordHit && $oldValue !== $newValue && !$this->IsAnalogDrift($oldAttr, $newAttr)) { return true; } return false; }
    private function IsAnalogDrift($oldAttr, $newAttr) { $unit = (string)($newAttr['unit'] ?? $oldAttr['unit'] ?? ''); $oldValue = (string)($oldAttr['value'] ?? ''); $newValue = (string)($newAttr['value'] ?? ''); if ($oldValue === $newValue) { return false; } $analogUnits = ['pH','mV','mg/l','µA','mA','°C','C','V','l','min','%','mS/cm']; return in_array($unit, $analogUnits, true) && is_numeric($oldValue) && is_numeric($newValue); }
    private function FormatExplorerEntry($xmlitem, $oldAttr, $newAttr) { $label = $newAttr['label'] ?? $oldAttr['label'] ?? ''; $unit = $newAttr['unit'] ?? $oldAttr['unit'] ?? ''; $oldValue = $oldAttr['value'] ?? ($oldAttr['active'] ?? ''); $newValue = $newAttr['value'] ?? ($newAttr['active'] ?? ''); $suffix = $unit !== '' ? ' ' . $unit : ''; return trim($xmlitem . ' ' . $label . ': ' . $oldValue . $suffix . ' -> ' . $newValue . $suffix); }
}
