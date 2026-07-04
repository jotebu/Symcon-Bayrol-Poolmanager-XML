<?php

declare(strict_types=1);

final class BPMXML_LearningAssistant
{
    private const ACTIONS = [
        'light' => [
            'name' => 'Licht',
            'steps' => ['Bitte Licht ausschalten', 'Bitte Licht einschalten', 'Bitte Licht wieder ausschalten'],
            'keywords' => ['licht', 'light', 'lampe', 'out', 'relais']
        ],
        'filter_pump' => [
            'name' => 'Filterpumpe',
            'steps' => ['Bitte Filterpumpe ausschalten', 'Bitte Filterpumpe einschalten', 'Bitte Filterpumpe wieder ausschalten'],
            'keywords' => ['filter', 'pumpe', 'pump']
        ],
        'heater' => [
            'name' => 'Heizung',
            'steps' => ['Bitte Heizung ausschalten', 'Bitte Heizung einschalten', 'Bitte Heizung wieder ausschalten'],
            'keywords' => ['heizung', 'heater', 'heat']
        ],
        'solar' => [
            'name' => 'Solar',
            'steps' => ['Bitte Solar ausschalten', 'Bitte Solar einschalten', 'Bitte Solar wieder ausschalten'],
            'keywords' => ['solar']
        ],
        'eco' => [
            'name' => 'Eco',
            'steps' => ['Bitte Eco deaktivieren', 'Bitte Eco aktivieren', 'Bitte Eco wieder deaktivieren'],
            'keywords' => ['eco']
        ],
        'flockmatic' => [
            'name' => 'Flockmatic',
            'steps' => ['Bitte Flockmatic ausschalten', 'Bitte Flockmatic einschalten', 'Bitte Flockmatic wieder ausschalten'],
            'keywords' => ['flock', 'flockmatic']
        ]
    ];

    public function getActions(): array
    {
        return self::ACTIONS;
    }

    public function createSession(string $actionKey): array
    {
        if (!isset(self::ACTIONS[$actionKey])) {
            throw new InvalidArgumentException('Unknown learning action: ' . $actionKey);
        }

        return [
            'action' => $actionKey,
            'name' => self::ACTIONS[$actionKey]['name'],
            'created_at' => date('Y-m-d H:i:s'),
            'step' => 0,
            'snapshots' => [],
            'result' => []
        ];
    }

    public function nextInstruction(array $session): string
    {
        $actionKey = (string)($session['action'] ?? '');
        if (!isset(self::ACTIONS[$actionKey])) {
            return 'Keine gueltige Learning-Session.';
        }

        $step = (int)($session['step'] ?? 0);
        $steps = self::ACTIONS[$actionKey]['steps'];
        if (!isset($steps[$step])) {
            return 'Learning abgeschlossen. Bitte Analyse ausfuehren.';
        }

        return $steps[$step] . ' und danach Snapshot speichern.';
    }

    public function addSnapshot(array $session, array $snapshot): array
    {
        $session['snapshots'][] = [
            'time' => date('Y-m-d H:i:s'),
            'data' => $snapshot
        ];
        $session['step'] = (int)($session['step'] ?? 0) + 1;
        return $session;
    }

    public function analyze(array $session): array
    {
        $actionKey = (string)($session['action'] ?? '');
        $keywords = self::ACTIONS[$actionKey]['keywords'] ?? [];
        $snapshots = $session['snapshots'] ?? [];
        if (count($snapshots) < 2) {
            return ['summary' => 'Zu wenige Snapshots fuer Analyse.', 'candidates' => []];
        }

        $changes = [];
        for ($i = 1; $i < count($snapshots); $i++) {
            $before = $this->mapByXmlItem($snapshots[$i - 1]['data'] ?? []);
            $after = $this->mapByXmlItem($snapshots[$i]['data'] ?? []);
            foreach ($after as $xmlitem => $entryAfter) {
                if (!isset($before[$xmlitem])) {
                    continue;
                }
                $old = $before[$xmlitem]['attributes'] ?? [];
                $new = $entryAfter['attributes'] ?? [];
                if (json_encode($old) === json_encode($new)) {
                    continue;
                }
                $changes[$xmlitem][] = ['old' => $old, 'new' => $new];
            }
        }

        $candidates = [];
        foreach ($changes as $xmlitem => $items) {
            $lastNew = end($items)['new'] ?? [];
            $label = mb_strtolower((string)($lastNew['label'] ?? ''));
            $score = 40;
            foreach ($keywords as $keyword) {
                if (strpos($label, mb_strtolower($keyword)) !== false) {
                    $score += 25;
                }
            }
            if ($this->hasBooleanToggle($items)) {
                $score += 30;
            }
            if ($this->looksAnalog($lastNew)) {
                $score -= 30;
            }
            $score = max(0, min(100, $score));
            $candidates[] = [
                'xmlitem' => $xmlitem,
                'label' => (string)($lastNew['label'] ?? ''),
                'score' => $score,
                'changes' => count($items),
                'last_attributes' => $lastNew
            ];
        }

        usort($candidates, static fn(array $a, array $b): int => $b['score'] <=> $a['score']);

        return [
            'summary' => 'Learning Analyse fuer ' . ($session['name'] ?? $actionKey) . ': ' . count($candidates) . ' Kandidaten gefunden.',
            'candidates' => $candidates
        ];
    }

    private function mapByXmlItem(array $data): array
    {
        $map = [];
        foreach ($data as $entry) {
            if (isset($entry['xmlitem'])) {
                $map[(string)$entry['xmlitem']] = $entry;
            }
        }
        return $map;
    }

    private function hasBooleanToggle(array $changes): bool
    {
        foreach ($changes as $change) {
            $old = (string)($change['old']['value'] ?? $change['old']['active'] ?? '');
            $new = (string)($change['new']['value'] ?? $change['new']['active'] ?? '');
            if (in_array($old, ['0', '1'], true) && in_array($new, ['0', '1'], true) && $old !== $new) {
                return true;
            }
        }
        return false;
    }

    private function looksAnalog(array $attributes): bool
    {
        $unit = (string)($attributes['unit'] ?? '');
        return in_array($unit, ['pH', 'mV', 'mg/l', 'µA', 'mA', '°C', 'C', 'V', 'l', 'min', '%', 'mS/cm'], true);
    }
}
