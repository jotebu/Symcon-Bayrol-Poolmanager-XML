<?php

declare(strict_types=1);

final class BPMXML_DiscoverySchedulerState
{
    public static function normalizeRanges(int $typeStart, int $typeEnd, int $idStart, int $idEnd): array
    {
        if ($typeEnd < $typeStart) {
            [$typeStart, $typeEnd] = [$typeEnd, $typeStart];
        }
        if ($idEnd < $idStart) {
            [$idStart, $idEnd] = [$idEnd, $idStart];
        }
        return [$typeStart, $typeEnd, $idStart, $idEnd];
    }

    public static function normalizeCursor(int $cursorType, int $cursorId, int $typeStart, int $typeEnd, int $idStart, int $idEnd): array
    {
        if ($cursorType < $typeStart || $cursorType > $typeEnd) {
            $cursorType = $typeStart;
        }
        if ($cursorId < $idStart || $cursorId > $idEnd) {
            $cursorId = $idStart;
        }
        return [$cursorType, $cursorId];
    }

    public static function advanceCursor(int $type, int $id, int $idStart, int $idEnd): array
    {
        $id++;
        if ($id > $idEnd) {
            $id = $idStart;
            $type++;
        }
        return [$type, $id];
    }

    public static function progress(int $type, int $id, int $typeStart, int $typeEnd, int $idStart, int $idEnd, bool $completed): int
    {
        $total = max(1, (($typeEnd - $typeStart + 1) * ($idEnd - $idStart + 1)));
        if ($completed) {
            return 100;
        }
        $done = min($total, max(0, (($type - $typeStart) * ($idEnd - $idStart + 1)) + ($id - $idStart)));
        return (int)floor(($done / $total) * 100);
    }
}
