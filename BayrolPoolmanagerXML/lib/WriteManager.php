<?php

declare(strict_types=1);

final class BPMXML_WriteManager
{
    public function assertAllowed(array $writeConfig, string $ident): void
    {
        if (empty($writeConfig['global_enabled'])) {
            throw new RuntimeException('Schreibzugriff global nicht freigegeben.');
        }

        $allowed = $writeConfig['allowed_idents'] ?? [];
        if (!in_array($ident, $allowed, true)) {
            throw new RuntimeException('Schreibzugriff fuer ' . $ident . ' nicht freigegeben.');
        }
    }

    public function planWrite(array $definition, $newValue, array $writeConfig): array
    {
        $ident = (string)($definition['ident'] ?? '');
        $this->assertAllowed($writeConfig, $ident);

        return [
            'created_at' => date('Y-m-d H:i:s'),
            'ident' => $ident,
            'xml' => (string)($definition['xml'] ?? ''),
            'old_value' => $definition['current_value'] ?? null,
            'new_value' => $newValue,
            'requires_confirmation' => true,
            'requires_readback' => true,
            'rollback_supported' => false,
            'status' => 'planned_not_executed',
            'reason' => 'Write URL format has not been validated yet. No HTTP write will be sent.'
        ];
    }

    public function executeDisabled(array $plan): void
    {
        throw new RuntimeException('Schreiben ist absichtlich deaktiviert. Geplanter Schreibvorgang wurde nicht ausgefuehrt: ' . ($plan['ident'] ?? 'unknown'));
    }

    public function verifyReadback($expected, $actual): array
    {
        $ok = ((string)$expected === (string)$actual);
        return [
            'ok' => $ok,
            'expected' => $expected,
            'actual' => $actual,
            'time' => date('Y-m-d H:i:s')
        ];
    }

    public function logEntry(array $plan, string $result, string $message = ''): array
    {
        return [
            'time' => date('Y-m-d H:i:s'),
            'ident' => $plan['ident'] ?? '',
            'xml' => $plan['xml'] ?? '',
            'new_value' => $plan['new_value'] ?? null,
            'result' => $result,
            'message' => $message
        ];
    }
}
