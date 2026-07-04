<?php

declare(strict_types=1);

require_once __DIR__ . '/XmlClient.php';
require_once __DIR__ . '/DiscoveryClassifier.php';
require_once __DIR__ . '/DiscoveryDatabase.php';
require_once __DIR__ . '/SnapshotComparator.php';
require_once __DIR__ . '/DiscoveryExporter.php';
require_once __DIR__ . '/DiscoverySchedulerState.php';
require_once __DIR__ . '/LearningAssistant.php';
require_once __DIR__ . '/DiscoveryHistory.php';
require_once __DIR__ . '/VariableGenerator.php';
require_once __DIR__ . '/FirmwareProfile.php';
require_once __DIR__ . '/WriteManager.php';

/**
 * Central include file for the staged module refactor.
 *
 * The current IP-Symcon runtime still lives in module.php. During the next
 * step, module.php will include this file once and delegate one responsibility
 * at a time to the component classes below.
 */
final class BPMXML_ModuleIntegration
{
    public static function componentList(): array
    {
        return [
            BPMXML_XmlClient::class,
            BPMXML_DiscoveryClassifier::class,
            BPMXML_DiscoveryDatabase::class,
            BPMXML_SnapshotComparator::class,
            BPMXML_DiscoveryExporter::class,
            BPMXML_DiscoverySchedulerState::class,
            BPMXML_LearningAssistant::class,
            BPMXML_DiscoveryHistory::class,
            BPMXML_VariableGenerator::class,
            BPMXML_FirmwareProfile::class,
            BPMXML_WriteManager::class
        ];
    }
}
