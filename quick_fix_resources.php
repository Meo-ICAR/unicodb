<?php

// Script rapido per correggere i nomi dei Resource più comuni

$basePath = __DIR__ . '/app/Filament/Resources';

$corrections = [
    'ApiLogs' => 'ApiLog',
    'AuditItems' => 'AuditItem', 
    'Audits' => 'Audit',
    'AuiRecords' => 'AuiRecord',
    'BusinessFunctions' => 'BusinessFunction',
    'ChecklistAudits' => 'ChecklistAudit',
    'ChecklistDocuments' => 'ChecklistDocument',
    'ChecklistItems' => 'ChecklistItem',
    'Checklists' => 'Checklist',
    'ClientMandates' => 'ClientMandate',
    'ClientRelations' => 'ClientRelation',
    'ClientTypes' => 'ClientType',
    'CompanyBranches' => 'CompanyBranch',
    'CompanyClients' => 'CompanyClient',
    'CompanyFunctions' => 'CompanyFunction',
    'CompanySenders' => 'CompanySender',
    'CompanyTypes' => 'CompanyType',
    'ComplianceViolations' => 'ComplianceViolation',
    'Comunes' => 'Comune',
    'DocumentScopes' => 'DocumentScope',
    'DocumentStatuses' => 'DocumentStatus',
    'DocumentTypeScopes' => 'DocumentTypeScope',
    'DocumentTypes' => 'DocumentType',
    'Employees' => 'Employee',
    'EmploymentTypes' => 'EmploymentType',
    'EnasarcoLimits' => 'EnasarcoLimit',
    'Firrs' => 'Firr',
    'OamScopes' => 'OamScope',
    'Oams' => 'Oam',
    'PracticeCommissionStatuses' => 'PracticeCommissionStatus',
    'PracticeCommissions' => 'PracticeCommission',
    'PracticeOams' => 'PracticeOam',
    'PracticeScopes' => 'PracticeScope',
    'PracticeStatuses' => 'PracticeStatus',
    'Principals' => 'Principal',
    'ProcessTasks' => 'ProcessTask',
    'ProformaStatusHistories' => 'ProformaStatusHistory',
    'ProformaStatuses' => 'ProformaStatus',
    'Proformas' => 'Proforma',
    'PurchaseInvoices' => 'PurchaseInvoice',
    'RegulatoryBodies' => 'RegulatoryBody',
    'RegulatoryBodyScopes' => 'RegulatoryBodyScope',
    'Remediations' => 'Remediation',
    'RaciAssignements' => 'RaciAssignement',
    'RuiAccessoris' => 'RuiAccessoris',
    'RuiAgentis' => 'RuiAgentis',
    'RuiCariches' => 'RuiCariches',
    'RuiCollaboratoris' => 'RuiCollaboratoris',
    'RuiMandatis' => 'RuiMandatis',
    'RuiSezds' => 'RuiSezds',
    'RuiSedis' => 'RuiSedis',
    'RuiWebSites' => 'RuiWebSites',
    'Ruis' => 'Ruis',
    'SalesInvoices' => 'SalesInvoice',
    'SoftwareApplications' => 'SoftwareApplication',
    'SoftwareCategories' => 'SoftwareCategory',
    'SoftwareMappings' => 'SoftwareMapping',
    'SosReports' => 'SosReport',
    'TrainingRecords' => 'TrainingRecord',
    'TrainingSessions' => 'TrainingSession',
    'TrainingTemplates' => 'TrainingTemplate',
    'Vcoges' => 'Vcoge',
    'Venasarcotots' => 'Venasarcotot',
    'Venasarcotrimestres' => 'Venasarcotrimestre'
];

echo "Correggo i nomi dei Resource...\n\n";

$fixed = 0;

foreach ($corrections as $plural => $singular) {
    $listFile = $basePath . '/' . $plural . '/Pages/List' . $plural . '.php';
    
    if (file_exists($listFile)) {
        $content = file_get_contents($listFile);
        
        // Correggi import
        $content = preg_replace(
            '/use App\\\\Filament\\\\Resources\\\\' . $plural . '\\\\' . $plural . 'Resource;/',
            "use App\\Filament\\Resources\\{$plural}\\{$singular}Resource;",
            $content
        );
        
        // Correggi riferimento
        $content = preg_replace(
            '/protected static string \$resource = ' . $plural . 'Resource::class;/',
            "protected static string \$resource = {$singular}Resource::class;",
            $content
        );
        
        file_put_contents($listFile, $content);
        echo "FIXED: List{$plural}.php -> {$singular}Resource\n";
        $fixed++;
    }
}

echo "\nCompletato! File corretti: $fixed\n";
