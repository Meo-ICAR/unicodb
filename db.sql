-- Adminer 5.4.2 MySQL 8.0.45-0ubuntu0.24.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

SET NAMES utf8mb4;

CREATE TABLE `abis` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco interno',
  `abi` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice ABI a 5 cifre',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome ufficiale (es. AGOS DUCATO S.P.A.)',
  `type` enum('BANCA','INTERMEDIARIO_106','IP_IMEL') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Banca o Finanziaria ex Art. 106 TUB',
  `capogruppo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Gruppo bancario di appartenenza',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OPERATIVO' COMMENT 'OPERATIVO, CANCELLATO, IN_LIQUIDAZIONE',
  `data_iscrizione` date DEFAULT NULL,
  `data_cancellazione` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Data creazione record',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Data ultimo aggiornamento',
  PRIMARY KEY (`id`),
  UNIQUE KEY `abis_abi_unique` (`abi`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `activity_log` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `log_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint unsigned DEFAULT NULL,
  `causer_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint unsigned DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `practice_id` int unsigned DEFAULT NULL COMMENT 'Riferimento alla pratica',
  `client_id` int unsigned DEFAULT NULL COMMENT 'Riferimento al cliente tramite il mandato',
  `tipo_evento` enum('instaurazione_rapporto','esecuzione_operazione','chiusura_rapporto') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_evento` date DEFAULT NULL,
  `importo_rilevato` decimal(15,2) DEFAULT NULL,
  `payload_dati_cliente` json DEFAULT NULL,
  `stato` enum('da_consolidare','consolidato','scartato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'da_consolidare',
  `note_operatore` text COLLATE utf8mb4_unicode_ci,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`),
  KEY `activity_log_company_id_foreign` (`company_id`),
  CONSTRAINT `activity_log_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `address_types` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'ID univoco tipo indirizzo',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `addressable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Classe del Modello collegato (es. App\\Models\\Client)',
  `addressable_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID del Modello (VARCHAR 36 per supportare sia UUID che Integer)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `numero` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero civico o identificativo indirizzo',
  `street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Via e numero civico',
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Città o Comune',
  `zip_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CAP (Codice di Avviamento Postale)',
  `address_type_id` int DEFAULT NULL COMMENT 'Relazione con tipologia indirizzo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data inserimento indirizzo',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento',
  PRIMARY KEY (`id`),
  KEY `addresses_ibfk_1` (`address_type_id`),
  CONSTRAINT `addresses_ibfk_1` FOREIGN KEY (`address_type_id`) REFERENCES `address_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella polimorfica per salvare molteplici indirizzi associabili a Company, Clienti o Utenti.';


CREATE TABLE `agents` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco agente',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome agente',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `supervisor_type` enum('no','si','filiale') COLLATE utf8mb4_unicode_ci DEFAULT 'no' COMMENT 'Se supervisore indicare e specificare se di filiale',
  `oam` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Oam',
  `oam_at` date DEFAULT NULL COMMENT 'Data iscrizione OAM',
  `oam_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Denominazione sociale registrata in OAM',
  `stipulated_at` date DEFAULT NULL COMMENT 'Data stipula contratto collaborazione',
  `dismissed_at` date DEFAULT NULL COMMENT 'Data cessazione rapporto',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Agente / Mediatore / Consulente / Call Center ',
  `contribute` decimal(10,2) DEFAULT NULL COMMENT 'Importo contributo fisso/quota',
  `contributeFrequency` int DEFAULT '1' COMMENT 'Frequenza contributo (mesi)',
  `contributeFrom` date DEFAULT NULL COMMENT 'Data inizio addebito contributi',
  `remburse` decimal(10,2) DEFAULT NULL COMMENT 'Importo rimborsi spese concordati',
  `vat_number` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Partita IVA Agente',
  `vat_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ragione Sociale Fiscale',
  `enasarco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Enasarco no / monomandatario / plurimandatario / societa',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica se la banca è attualmente convenzionata',
  `is_art108` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Esente art. 108 - ex art. 128-novies TUB',
  `contoCOGE` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Conto COGE',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coordinated_by_id` int unsigned DEFAULT NULL COMMENT 'ID del dipendente coordinatore',
  `coordinated_by_agent_id` int unsigned DEFAULT NULL COMMENT 'ID dell''agente coordinatore',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente collegato',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `agents_user_id_foreign` (`user_id`),
  KEY `agents_ibfk_1` (`company_id`),
  KEY `agents_ibfk_2` (`coordinated_by_id`),
  KEY `agents_ibfk_3` (`coordinated_by_agent_id`),
  CONSTRAINT `agents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `agents_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `agents_ibfk_2` FOREIGN KEY (`coordinated_by_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `agents_ibfk_3` FOREIGN KEY (`coordinated_by_agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `agents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella globale agenti convenzionati.';


CREATE TABLE `api_configurations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco configurazione',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `software_application_id` int unsigned NOT NULL COMMENT 'Software con cui interfacciarsi',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome mnemonico della connessione',
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL base dell''API (es. https://api.crmesterno.it/v1)',
  `auth_type` enum('BASIC','BEARER_TOKEN','API_KEY','OAUTH2') COLLATE utf8mb4_unicode_ci DEFAULT 'API_KEY' COMMENT 'Metodo di autenticazione',
  `api_key` text COLLATE utf8mb4_unicode_ci COMMENT 'Chiave API o Client ID',
  `api_secret` text COLLATE utf8mb4_unicode_ci COMMENT 'Segreto API o Client Secret',
  `access_token` text COLLATE utf8mb4_unicode_ci COMMENT 'Token di accesso attuale (se OAUTH2 o BEARER)',
  `refresh_token` text COLLATE utf8mb4_unicode_ci COMMENT 'Token per il rinnovo della sessione',
  `token_expires_at` timestamp NULL DEFAULT NULL COMMENT 'Data di scadenza del token attuale',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Indica se l''integrazione è abilitata',
  `webhook_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chiave per validare i dati in entrata (Webhooks)',
  `last_sync_at` timestamp NULL DEFAULT NULL COMMENT 'Data e ora dell''ultima sincronizzazione riuscita',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data creazione configurazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento configurazione',
  PRIMARY KEY (`id`),
  KEY `api_configurations_ibfk_1` (`company_id`),
  KEY `api_configurations_ibfk_2` (`software_application_id`),
  CONSTRAINT `api_configurations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `api_configurations_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `api_configurations_ibfk_2` FOREIGN KEY (`software_application_id`) REFERENCES `software_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurazioni tecniche per l''interfacciamento API con software terzi.';


CREATE TABLE `api_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `api_configuration_id` int unsigned NOT NULL COMMENT 'Riferimento alla configurazione usata',
  `api_loggable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Classe del Modello collegato',
  `api_loggable_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID del Modello (VARCHAR 36)',
  `endpoint` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'L''endpoint specifico chiamato',
  `method` enum('GET','POST','PUT','DELETE','PATCH') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `request_payload` json DEFAULT NULL COMMENT 'Dati inviati',
  `response_payload` json DEFAULT NULL COMMENT 'Dati ricevuti',
  `status_code` int DEFAULT NULL COMMENT 'Codice HTTP (es. 200, 401, 500)',
  `execution_time_ms` int DEFAULT NULL COMMENT 'Tempo di risposta in millisecondi',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione dell''errore se fallito',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e ora della chiamata',
  PRIMARY KEY (`id`),
  KEY `api_logs_ibfk_1` (`api_configuration_id`),
  CONSTRAINT `api_logs_ibfk_1` FOREIGN KEY (`api_configuration_id`) REFERENCES `api_configurations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro storico di tutte le chiamate API effettuate per monitoraggio e risoluzione problemi.';


CREATE TABLE `audit_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID singola riga di controllo',
  `audit_id` int unsigned NOT NULL COMMENT 'Riferimento alla sessione di audit',
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Classe dell''oggetto controllato (es. App\\Models\\Practice)',
  `auditable_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID dell''oggetto controllato',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `result` enum('OK','RILIEVO','GRAVE_INADEMPIENZA','NON_CONTROLLATO') COLLATE utf8mb4_unicode_ci DEFAULT 'OK',
  `finding_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione dell''eventuale anomalia riscontrata',
  `is_template` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se è un elemento template riutilizzabile',
  `remediation_plan` text COLLATE utf8mb4_unicode_ci COMMENT 'Azioni correttive richieste per sanare l''anomalia',
  `remediation_deadline` date DEFAULT NULL COMMENT 'Scadenza entro cui sanare il rilievo',
  `is_resolved` tinyint(1) DEFAULT '0' COMMENT 'Indica se il rilievo è stato chiuso con successo',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione dettagliata dell''elemento',
  `audit_phase` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Fase dell''audit (es. preparazione, esecuzione, follow-up)',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice identificativo dell''elemento',
  PRIMARY KEY (`id`),
  KEY `audit_items_ibfk_1` (`audit_id`),
  KEY `audit_items_is_template_index` (`is_template`),
  KEY `audit_items_audit_phase_index` (`audit_phase`),
  CONSTRAINT `audit_items_ibfk_1` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Singole verifiche effettuate durante un audit su specifiche pratiche o fascicoli agenti.';


CREATE TABLE `audits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco audit',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `requester_type` enum('OAM','PRINCIPAL','INTERNAL','EXTERNAL') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chi richiede l''audit: Ente Regolatore, Mandante o Auto-controllo interno',
  `auditable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo del modello auditabile (agent, employee, company_branch, principal)',
  `auditable_id` int unsigned DEFAULT NULL COMMENT 'ID del modello auditabile',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Titolo dell''ispezione (es. Audit Semestrale Trasparenza 2026)',
  `emails` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Lista email per notifiche esiti audit',
  `reference_period` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Periodo oggetto di analisi (es. Q1-Q2 2025)',
  `start_date` date DEFAULT NULL COMMENT 'Data inizio ispezione',
  `end_date` date DEFAULT NULL COMMENT 'Data chiusura ispezione',
  `status` enum('PROGRAMMATO','IN_CORSO','COMPLETATO','ARCHIVIATO') COLLATE utf8mb4_unicode_ci DEFAULT 'PROGRAMMATO',
  `overall_score` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Valutazione sintetica finale (es. Conforme, Conforme con rilievi, Non Conforme)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `regulatory_body_id` int unsigned DEFAULT NULL COMMENT 'Ente regolatore che richiede l''audit (se applicabile)',
  `client_id` int unsigned DEFAULT NULL COMMENT 'Cliente specifico oggetto di audit (se applicabile)',
  PRIMARY KEY (`id`),
  KEY `audits_company_id_foreign` (`company_id`),
  KEY `regulatory_body_id` (`regulatory_body_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `audits_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audits_ibfk_4` FOREIGN KEY (`regulatory_body_id`) REFERENCES `regulatory_bodies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `audits_ibfk_5` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sessioni di Audit richieste da OAM, Mandanti o effettuate internamente.';


CREATE TABLE `aui_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `activity_log_id` bigint unsigned DEFAULT NULL,
  `practice_id` int unsigned DEFAULT NULL COMMENT 'Riferimento alla pratica',
  `client_id` int unsigned DEFAULT NULL COMMENT 'Riferimento al cliente',
  `codice_univoco_aui` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_registrazione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_registrazione` date NOT NULL,
  `importo_operazione` decimal(15,2) NOT NULL,
  `profilo_rischio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basso',
  `is_annullato` tinyint(1) NOT NULL DEFAULT '0',
  `motivo_annullamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `aui_records_codice_univoco_aui_unique` (`codice_univoco_aui`),
  KEY `aui_records_activity_log_id_foreign` (`activity_log_id`),
  KEY `aui_records_company_id_foreign` (`company_id`),
  CONSTRAINT `aui_records_activity_log_id_foreign` FOREIGN KEY (`activity_log_id`) REFERENCES `activity_log` (`id`) ON DELETE SET NULL,
  CONSTRAINT `aui_records_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `breezy_sessions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `authenticatable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authenticatable_id` bigint unsigned NOT NULL,
  `panel_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `breezy_sessions_authenticatable_type_authenticatable_id_index` (`authenticatable_type`,`authenticatable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `business_functions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `macro_area` enum('Governance','Business / Commerciale','Supporto','Controlli (II Livello)','Controlli (III Livello)','Controlli / Privacy') COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` enum('Consiglio di Amministrazione / Direzione','Direzione Commerciale','Gestione Rete e Collaboratori','Back Office / Istruttoria Pratiche','Amministrazione e Contabilità','IT e Sicurezza Dati','Marketing e Comunicazione','Gestione Reclami e Controversie','Risorse Umane (HR) e Formazione','Compliance (Conformità)','Risk Management','Antiriciclaggio (AML)','Internal Audit (Revisione Interna)','Data Protection Officer (DPO)') COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('Strategica','Operativa','Supporto','Controllo') COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `outsourcable_status` enum('yes','no','partial') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `business_functions_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `checklist_answers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `checklist_submission_id` bigint unsigned NOT NULL,
  `checklist_item_id` bigint unsigned NOT NULL,
  `value_text` text COLLATE utf8mb4_unicode_ci COMMENT 'Risposta testuale libera o motivo di non trasparenza',
  `value_boolean` tinyint(1) DEFAULT NULL COMMENT 'Risposta Vero/Falso per i toggle (es. Ha la targa OAM?)',
  `value_array` json DEFAULT NULL COMMENT 'Array JSON per selezioni multiple (es. ID delle pratiche estratte a campione)',
  `annotation` text COLLATE utf8mb4_unicode_ci COMMENT 'Note interne o annotazioni dell''ispettore/operatore',
  `attached_model_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo del modello a cui i file sono stati allegati (es. Principal, Agent)',
  `attached_model_id` bigint unsigned DEFAULT NULL COMMENT 'ID del modello a cui i file sono stati allegati',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Agenzia proprietaria (multi-tenant)',
  `ordine` int NOT NULL DEFAULT '0' COMMENT 'Ordine di visualizzazione',
  `n_documents` tinyint NOT NULL DEFAULT '0' COMMENT 'Numero di documenti richiesti (0=nessuno, 1=esatto, 99=multipli)',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_answer_per_submission` (`checklist_submission_id`,`checklist_item_id`),
  KEY `checklist_answers_checklist_item_id_foreign` (`checklist_item_id`),
  KEY `checklist_answers_ordine_index` (`ordine`),
  KEY `checklist_answers_company_id_foreign` (`company_id`),
  CONSTRAINT `checklist_answers_checklist_item_id_foreign` FOREIGN KEY (`checklist_item_id`) REFERENCES `checklist_items` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklist_answers_checklist_submission_id_foreign` FOREIGN KEY (`checklist_submission_id`) REFERENCES `checklist_submissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklist_answers_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `checklist_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `checklist_id` bigint unsigned NOT NULL COMMENT 'Checklist di appartenenza',
  `ordine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ordine della domanda/elemento',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome della domanda/elemento',
  `item_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice univoco della domanda',
  `question` text COLLATE utf8mb4_unicode_ci COMMENT 'Testo della domanda',
  `answer` text COLLATE utf8mb4_unicode_ci COMMENT 'Risposta data dall''utente',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione o note aggiuntive',
  `annotation` text COLLATE utf8mb4_unicode_ci COMMENT 'Annotazioni interne',
  `is_required` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Se obbligatorio',
  `attach_model` enum('principal','agent','company','audit') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Modello a cui allegare documento',
  `attach_model_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID del modello per allegato',
  `n_documents` int NOT NULL DEFAULT '0' COMMENT 'Numero documenti da allegare 0= no, 99=multi',
  `repeatable_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice se ripetibile (es. documenti annuali)',
  `depends_on_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Il codice della domanda da cui dipende',
  `depends_on_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Il valore che deve avere per attivarsi',
  `dependency_type` enum('show_if','hide_if') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Attiva / Disattiva condizionale',
  `url_step` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link esterno per step procedure',
  `url_callback` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link esterno per callback',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checklist_items_checklist_id_index` (`checklist_id`),
  KEY `checklist_items_attach_model_attach_model_id_index` (`attach_model`,`attach_model_id`),
  CONSTRAINT `checklist_items_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `checklists` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Elementi delle checklist con domande e allegati';


CREATE TABLE `checklist_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Agenzia proprietaria (multi-tenant)',
  `checklist_id` bigint unsigned NOT NULL,
  `submittable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `submittable_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `status` enum('draft','in_progress','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft' COMMENT 'Stato di avanzamento della compilazione',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Data di completamento definitivo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checklist_submissions_checklist_id_foreign` (`checklist_id`),
  KEY `checklist_submissions_submittable_type_submittable_id_index` (`submittable_type`,`submittable_id`),
  KEY `checklist_submissions_user_id_foreign` (`user_id`),
  KEY `submittable_index` (`submittable_type`,`submittable_id`),
  KEY `checklist_submissions_company_id_index` (`company_id`),
  CONSTRAINT `checklist_submissions_checklist_id_foreign` FOREIGN KEY (`checklist_id`) REFERENCES `checklists` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklist_submissions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklist_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `checklists` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Agenzia proprietaria (multi-tenant)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome della checklist',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice della checklist',
  `type` enum('loan_management','audit') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo di checklist',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione della checklist',
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Principal specifico (se applicabile)',
  `document_type_id` int unsigned DEFAULT NULL COMMENT 'Tipo di documento associato alla checklist',
  `is_practice` tinyint(1) DEFAULT '0' COMMENT 'Se riferisce a pratiche',
  `is_audit` tinyint(1) DEFAULT '0' COMMENT 'Se per audit/compliance',
  `is_template` tinyint(1) DEFAULT '1',
  `target_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `target_id` bigint unsigned DEFAULT NULL,
  `status` enum('da_compilare','in_corso','completata') COLLATE utf8mb4_unicode_ci DEFAULT 'da_compilare',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `checklists_target_type_target_id_index` (`target_type`,`target_id`),
  KEY `checklists_company_id_type_index` (`company_id`,`type`),
  KEY `checklists_principal_id_index` (`principal_id`),
  KEY `checklists_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `checklists_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `checklists_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE SET NULL,
  CONSTRAINT `checklists_principal_id_foreign` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Checklist per workflow con domande e allegati';


CREATE TABLE `client_mandates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `client_id` int unsigned NOT NULL COMMENT 'Riferimento al cliente coinvolto',
  `numero_mandato` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_firma_mandato` date NOT NULL COMMENT 'Innesca Instaurazione Rapporto AUI',
  `data_scadenza_mandato` date NOT NULL COMMENT 'Innesca Chiusura Rapporto AUI (se non erogato prima)',
  `importo_richiesto_mandato` decimal(15,2) DEFAULT NULL,
  `scopo_finanziamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_consegna_trasparenza` date DEFAULT NULL COMMENT 'Deve essere <= data_firma',
  `stato` enum('attivo','concluso_con_successo','scaduto','revocato') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attivo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `client_mandates_numero_mandato_unique` (`numero_mandato`),
  KEY `client_mandates_client_id_foreign` (`client_id`),
  CONSTRAINT `client_mandates_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `client_practice` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del legame',
  `practice_id` int unsigned NOT NULL COMMENT 'Riferimento alla pratica',
  `client_id` int unsigned NOT NULL COMMENT 'Riferimento al cliente coinvolto',
  `role` enum('intestatario','cointestatario','garante','terzo_datore') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'intestatario' COMMENT 'Ruolo legale del cliente nella pratica: Intestatario principale, Co-intestatario, Garante o Terzo Datore di ipoteca',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note specifiche sul ruolo per questa pratica (es. "Garante solo per quota 50%")',
  `purpose_of_relationship` text COLLATE utf8mb4_unicode_ci COMMENT 'Es: Acquisto prima casa',
  `funds_origin` text COLLATE utf8mb4_unicode_ci COMMENT 'Es: Risparmi, donazione, stipendio',
  `oam_delivered` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Foglio informativo consegnato a questo soggetto?',
  `role_risk_level` enum('basso','medio','alto') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_client_practice` (`practice_id`,`client_id`),
  KEY `client_practice_ibfk_2` (`client_id`),
  KEY `client_practice_ibfk_3` (`company_id`),
  CONSTRAINT `client_practice_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `client_practice_ibfk_1` FOREIGN KEY (`practice_id`) REFERENCES `practices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_practice_ibfk_2` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_practice_ibfk_3` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di legame tra Clienti e Pratiche. Gestisce chi sono gli intestatari e chi i garanti per ogni pratica.';


CREATE TABLE `client_privacies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` int unsigned NOT NULL COMMENT 'Riferimento al cliente',
  `request_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Accesso, Rettifica, Cancellazione, Portabilità',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ricevuta, In lavorazione, Evasa',
  `completed_at` timestamp NULL DEFAULT NULL COMMENT 'Data della risposta definitiva',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_privacies_company_id_foreign` (`company_id`),
  KEY `client_privacies_client_id_index` (`client_id`),
  CONSTRAINT `client_privacies_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `client_relations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` int unsigned NOT NULL,
  `client_id` int unsigned NOT NULL,
  `shares_percentage` decimal(5,2) DEFAULT NULL,
  `is_titolare` tinyint(1) NOT NULL DEFAULT '0',
  `client_type_id` int unsigned DEFAULT NULL,
  `data_inizio_ruolo` date DEFAULT NULL,
  `data_fine_ruolo` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_relations_company_id_foreign` (`company_id`),
  KEY `client_relations_client_id_foreign` (`client_id`),
  KEY `client_relations_client_type_id_foreign` (`client_type_id`),
  CONSTRAINT `client_relations_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_relations_client_type_id_foreign` FOREIGN KEY (`client_type_id`) REFERENCES `client_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `client_relations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `client_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco tipo cliente',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione',
  `is_person` tinyint(1) DEFAULT '1' COMMENT 'Persona fisica (true) o giuridica (false)',
  `is_company` tinyint(1) DEFAULT '0' COMMENT 'Indica se è una società/azienda',
  `privacy_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruolo Privacy (es. Titolare Autonomo, Responsabile Esterno)',
  `purpose` text COLLATE utf8mb4_unicode_ci COMMENT 'Finalità del trattamento',
  `data_subjects` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Interessati',
  `data_categories` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Dati Trattati',
  `retention_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tempi di Conservazione (Data Retention)',
  `extra_eu_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Trasferimento Extra-UE',
  `security_measures` text COLLATE utf8mb4_unicode_ci COMMENT 'Misure di Sicurezza',
  `privacy_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Altri Dati Privacy',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catalogo globale: Classificazione lavorativa del cliente (fondamentale per le logiche di delibera del credito).';


CREATE TABLE `clients` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_person` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Persona fisica (true) o giuridica (false)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cognome (se persona fisica) o Ragione Sociale (se giuridica)',
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome persona fisica',
  `tax_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice Fiscale o Partita IVA del cliente',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email di contatto principale',
  `phone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Recapito telefonico',
  `is_pep` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Persona Politicamente Esposta',
  `client_type_id` int unsigned DEFAULT NULL COMMENT 'Classificazione cliente',
  `is_sanctioned` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Presente in liste antiterrorismo/blacklists',
  `is_remote_interaction` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Operatività a distanza = Rischio più alto',
  `general_consent_at` timestamp NULL DEFAULT NULL COMMENT 'Consenso generale al trattamento base',
  `privacy_policy_read_at` timestamp NULL DEFAULT NULL COMMENT 'Data presa visione informativa Art.13',
  `consent_special_categories_at` timestamp NULL DEFAULT NULL COMMENT 'Consenso dati sanitari/giudiziari per polizze/CQS',
  `consent_sic_at` timestamp NULL DEFAULT NULL COMMENT 'Consenso interrogazione CRIF/CTC/Experian',
  `consent_marketing_at` timestamp NULL DEFAULT NULL COMMENT 'Consenso comunicazioni commerciali e newsletter',
  `consent_profiling_at` timestamp NULL DEFAULT NULL COMMENT 'Consenso profilazione abitudini di consumo/spesa',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'raccolta_dati' COMMENT 'raccolta_dati, valutazione_aml, approvata, sos_inviata, chiusa',
  `is_company` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True se il cliente è un''azienda fornitore',
  `is_lead` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True se è un lead non ancora convertito',
  `leadsource_id` int unsigned DEFAULT NULL COMMENT 'ID del client che ha fornito il lead',
  `acquired_at` timestamp NULL DEFAULT NULL COMMENT 'Data di acquisizione del contatto',
  `contoCOGE` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Conto COGE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data acquisizione cliente',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica anagrafica',
  `privacy_consent` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Consenso privacy del cliente',
  `is_client` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'contraente contratto',
  `subfornitori` text COLLATE utf8mb4_unicode_ci COMMENT 'Subfornitori da comunicare per gradimento',
  `is_requiredApprovation` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Da far approvare per gradimento',
  `is_approved` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Approvata per gradimento',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Cliente anonimo (non comunicabile)',
  `blacklist_at` timestamp NULL DEFAULT NULL COMMENT 'Data inserimento in blacklist',
  `blacklisted_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID dell''utente che ha inserito in blacklist (senza link esterni)',
  `salary` decimal(10,2) DEFAULT NULL COMMENT 'Retribuzione annuale del cliente',
  `salary_quote` decimal(10,2) DEFAULT NULL COMMENT 'Quota retribuzione per calcoli finanziari',
  `is_art108` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Esente art. 108 - ex art. 128-novies TUB',
  PRIMARY KEY (`id`),
  KEY `clients_ibfk_1` (`company_id`),
  KEY `clients_ibfk_2` (`client_type_id`),
  KEY `clients_ibfk_4` (`leadsource_id`),
  KEY `clients_blacklist_at_index` (`blacklist_at`),
  KEY `clients_is_anonymous_index` (`is_anonymous`),
  KEY `clients_is_approved_index` (`is_approved`),
  CONSTRAINT `clients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `clients_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `clients_ibfk_2` FOREIGN KEY (`client_type_id`) REFERENCES `client_types` (`id`),
  CONSTRAINT `clients_ibfk_4` FOREIGN KEY (`leadsource_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Clienti (Richiedenti credito) associati in modo esclusivo a una specifica agenzia (Tenant).';


CREATE TABLE `coges` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fonte` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Fonte del movimento contabile',
  `entrata_uscita` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Entrata o Uscita',
  `conto_avere` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Conto Avere',
  `descrizione_avere` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione Conto Avere',
  `conto_dare` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Conto Dare',
  `descrizione_dare` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione Conto Dare',
  `annotazioni` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Note aggiuntive',
  `value_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Quadratura' COMMENT 'Tipo valore: Quadratura',
  `value_period` enum('Adesso','Oggi','Ieri','Settimana','Quindicinale','Mese','Trimestre') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Oggi' COMMENT 'Periodo di riferimento',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `coges_company_id_foreign` (`company_id`),
  CONSTRAINT `coges_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Piano dei conti e configurazioni per la contabilità generale';


CREATE TABLE `companies` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UUID v4 generato da Laravel (Chiave Primaria)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Ragione Sociale della società di mediazione',
  `vat_number` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Partita IVA o Codice Fiscale dell''agenzia',
  `vat_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Denominazione fiscale per fatturazione',
  `oam` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero iscrizione OAM Società',
  `oam_at` date DEFAULT NULL COMMENT 'Data iscrizione OAM Società',
  `oam_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome registrato negli elenchi OAM',
  `company_type_id` int unsigned DEFAULT NULL COMMENT 'Tipo forma giuridica della società',
  `page_header` text COLLATE utf8mb4_unicode_ci COMMENT 'Intestazione per carta intestata',
  `page_footer` text COLLATE utf8mb4_unicode_ci COMMENT 'Piè di pagina per carta intestata',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione del tenant',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data di ultima modifica',
  PRIMARY KEY (`id`),
  KEY `companies_company_type_id_foreign` (`company_type_id`),
  CONSTRAINT `companies_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella principale dei Tenant (Società di Mediazione Creditizia).';


CREATE TABLE `company_api_usage_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `service_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo di servizio API es. OCR, Signature, etc.',
  `software_cost` decimal(10,4) NOT NULL DEFAULT '0.0000' COMMENT 'Costo reale dell''API',
  `charged_credits` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Crediti addebitati al tenant',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Stato della chiamata API',
  `request_data` json DEFAULT NULL COMMENT 'Dati della richiesta API',
  `response_data` json DEFAULT NULL COMMENT 'Dati della risposta API',
  `error_message` text COLLATE utf8mb4_unicode_ci COMMENT 'Messaggio di errore se presente',
  `response_time_ms` int DEFAULT NULL COMMENT 'Tempo di risposta in millisecondi',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_api_usage_logs_company_id_service_type_index` (`company_id`,`service_type`),
  KEY `company_api_usage_logs_company_id_created_at_index` (`company_id`,`created_at`),
  KEY `company_api_usage_logs_service_type_status_index` (`service_type`,`status`),
  KEY `company_api_usage_logs_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `company_api_usage_logs_status_index` (`status`),
  CONSTRAINT `company_api_usage_logs_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_api_usage_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `company_branches` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco filiale',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome della sede (es. Sede Centrale, Filiale Milano Nord)',
  `is_main_office` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se questa è la sede legale/principale dell''agenzia',
  `manager_first_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome del referente/responsabile della sede',
  `manager_last_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cognome del referente/responsabile della sede',
  `manager_tax_code` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice Fiscale del referente della sede',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data creazione sede',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento sede',
  PRIMARY KEY (`id`),
  KEY `idx_company_main` (`company_id`,`is_main_office`),
  CONSTRAINT `company_branches_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `company_branches_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anagrafica delle sedi operative e legali delle società di mediazione con relativi referenti.';


CREATE TABLE `company_clients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `client_id` int unsigned NOT NULL COMMENT 'ID del cliente',
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'privacy' COMMENT 'Ruolo privacy per consulenti esterni',
  `privacy_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruolo Privacy (es. Titolare Autonomo, Responsabile Esterno)',
  `purpose` text COLLATE utf8mb4_unicode_ci COMMENT 'Finalità del trattamento',
  `data_subjects` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Interessati',
  `data_categories` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Dati Trattati',
  `retention_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tempi di Conservazione (Data Retention)',
  `extra_eu_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Trasferimento Extra-UE',
  `security_measures` text COLLATE utf8mb4_unicode_ci COMMENT 'Misure di Sicurezza',
  `privacy_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Altri Dati Privacy',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_clients_company_id_client_id_unique` (`company_id`,`client_id`),
  KEY `company_clients_client_id_foreign` (`client_id`),
  CONSTRAINT `company_clients_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_clients_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `company_functions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_function_id` bigint unsigned NOT NULL,
  `employee_id` int unsigned DEFAULT NULL COMMENT 'ID del dipendente referente interno',
  `client_id` int unsigned DEFAULT NULL COMMENT 'ID del cliente referente esterno',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_privacy` tinyint(1) NOT NULL DEFAULT '1',
  `is_outsourced` tinyint(1) NOT NULL DEFAULT '0',
  `report_frequency` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contract_expiry_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_functions_company_id_foreign` (`company_id`),
  KEY `company_functions_business_function_id_foreign` (`business_function_id`),
  KEY `company_functions_employee_id_foreign` (`employee_id`),
  KEY `company_functions_client_id_foreign` (`client_id`),
  CONSTRAINT `company_functions_business_function_id_foreign` FOREIGN KEY (`business_function_id`) REFERENCES `business_functions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_functions_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL,
  CONSTRAINT `company_functions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_functions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `company_software_application` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `software_application_id` int unsigned NOT NULL COMMENT 'ID del software',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ATTIVO' COMMENT 'Stato dell''associazione (es. ATTIVO, SOSPESO)',
  `apikey` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'API Key per il software',
  `wallet_balance` decimal(10,2) DEFAULT NULL COMMENT 'Saldo del wallet',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note specifiche per l''azienda',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_company_software` (`company_id`,`software_application_id`),
  KEY `company_software_application_software_application_id_foreign` (`software_application_id`),
  CONSTRAINT `company_software_application_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `company_software_application_software_application_id_foreign` FOREIGN KEY (`software_application_id`) REFERENCES `software_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `company_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es. S.p.A., S.r.l., Ditta Individuale',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Forme giuridiche delle società.';


CREATE TABLE `company_wallets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `software_application_id` int unsigned NOT NULL,
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Credito a disposizione',
  `start_date` date NOT NULL COMMENT 'Data di inizio validità',
  `trial_date` date DEFAULT NULL COMMENT 'Data fine periodo trial',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Wallet attivo',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome del wallet/servizio',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione del servizio',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_company_software` (`company_id`,`software_application_id`),
  KEY `company_wallets_company_id_is_active_index` (`company_id`,`is_active`),
  KEY `company_wallets_software_application_id_is_active_index` (`software_application_id`,`is_active`),
  KEY `company_wallets_start_date_trial_date_index` (`start_date`,`trial_date`),
  CONSTRAINT `company_wallets_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_wallets_software_application_id_foreign` FOREIGN KEY (`software_application_id`) REFERENCES `software_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `company_websites` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del sito',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome del sito (es. Portale Agenti Roma)',
  `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Dominio o sottodominio (es. agenzia-x.mediaconsulence.it)',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipologia sito (Vetrina, Portale, Landing)',
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Mandante di riferimento per landing dedicate',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Stato del sito (online/offline)',
  `is_typical` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Sito utilizzato per attività tipica',
  `privacy_date` date DEFAULT NULL COMMENT 'Data aggiornamento privacy',
  `transparency_date` date DEFAULT NULL COMMENT 'Data aggiornamento trasparenza',
  `privacy_prior_date` date DEFAULT NULL COMMENT 'Precedente aggiornamento privacy',
  `transparency_prior_date` date DEFAULT NULL COMMENT 'Precedente aggiornamento trasparenza',
  `url_privacy` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL pagina privacy policy',
  `url_cookies` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL pagina cookie policy',
  `is_footercompilant` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True se il footer è conforme GDPR',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `company_websites_domain_unique` (`domain`),
  KEY `company_websites_ibfk_1` (`company_id`),
  KEY `company_websites_ibfk_2` (`principal_id`),
  CONSTRAINT `company_websites_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `company_websites_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `company_websites_ibfk_2` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurazioni dei siti web e portali personalizzati per ogni agenzia.';


CREATE TABLE `compliance_violations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente collegato',
  `violatable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `violatable_id` bigint unsigned DEFAULT NULL,
  `violation_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es: accesso_non_autorizzato, kyc_scaduto, forzatura_stato, data_breach',
  `severity` enum('basso','medio','alto','critico') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medio',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione dettagliata dell''evento',
  `affected_subjects_count` int DEFAULT NULL COMMENT 'Numero approssimativo di clienti/utenti coinvolti',
  `likely_consequences` text COLLATE utf8mb4_unicode_ci COMMENT 'Possibili conseguenze per gli interessati (es. furto d''identità, frode finanziaria)',
  `discovery_date` datetime DEFAULT NULL COMMENT 'Data e ora in cui l''azienda ha scoperto la violazione (inizio delle 72h)',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci COMMENT 'Browser e dispositivo utilizzato',
  `is_dpa_notified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Il Garante Privacy è stato notificato?',
  `dpa_notified_at` datetime DEFAULT NULL,
  `dpa_not_notified_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'Se non notificato, motivazione legale (es. rischio improbabile per i diritti)',
  `are_subjects_notified` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'I clienti coinvolti sono stati avvisati?',
  `resolved_at` timestamp NULL DEFAULT NULL,
  `resolved_by` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente collegato',
  `resolution_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Come è stata sanata la violazione?',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `compliance_violations_company_id_violation_type_unique` (`company_id`,`violation_type`),
  KEY `compliance_violations_user_id_foreign` (`user_id`),
  KEY `compliance_violations_violatable_type_violatable_id_index` (`violatable_type`,`violatable_id`),
  KEY `compliance_violations_resolved_by_foreign` (`resolved_by`),
  CONSTRAINT `compliance_violations_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `compliance_violations_resolved_by_foreign` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `compliance_violations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `comunes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codice_regione` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_unita_territoriale` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_provincia_storico` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `progressivo_comune` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_alfanumerico` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_italiano` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_altra_lingua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codice_ripartizione_geografica` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ripartizione_geografica` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_regione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `denominazione_unita_territoriale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipologia_unita_territoriale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capoluogo_provincia` tinyint(1) NOT NULL DEFAULT '0',
  `sigla_automobilistica` varchar(2) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_numerico` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_110_province` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_107_province` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_comune_103_province` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_catastale` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts1_2021` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts2_2021` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts3_2021` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts1_2024` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts2_2024` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_nuts3_2024` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comunes_codice_regione_index` (`codice_regione`),
  KEY `comunes_codice_provincia_storico_index` (`codice_provincia_storico`),
  KEY `comunes_denominazione_regione_index` (`denominazione_regione`),
  KEY `comunes_sigla_automobilistica_index` (`sigla_automobilistica`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `contacts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contactable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contactable_id` bigint unsigned DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome e cognome del referente',
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero di telefono',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Indirizzo email',
  `role_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruolo o tipo di referente',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Note o descrizione aggiuntiva',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contacts_contactable_type_contactable_id_index` (`contactable_type`,`contactable_id`),
  KEY `contacts_company_id_contactable_type_contactable_id_index` (`company_id`,`contactable_type`,`contactable_id`),
  KEY `contacts_name_index` (`name`),
  KEY `contacts_email_index` (`email`),
  CONSTRAINT `contacts_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `document_scopes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco ambito',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dell''ambito: Privacy, AML, OAM, Istruttoria, Contrattualistica',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione della finalità normativa',
  `color_code` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#6B7280' COMMENT 'Codice colore per i tag nell''interfaccia (Filament Badge)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_scopes_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale: Definisce le finalità normative dei documenti.';


CREATE TABLE `document_status` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco dello stato',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dello stato (es. ASSENTE, DA VERIFICARE, etc.)',
  `status` enum('ASSENTE','DA VERIFICARE','IN VERIFICA','OK','DIFFORME','RICHIESTA INFO','ERRATO','ANNULLATO','SCADUTO') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_ok` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True se il documento è valido e accettato',
  `is_rejected` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True se il documento è stato respinto',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione dettagliata dello stato',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `document_status_status_unique` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella stati dei documenti';


CREATE TABLE `document_type_scope` (
  `document_type_id` int unsigned NOT NULL COMMENT 'ID tipo documento',
  `document_scope_id` int unsigned NOT NULL COMMENT 'ID ambito normativo',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  PRIMARY KEY (`document_type_id`,`document_scope_id`),
  KEY `document_type_scope_ibfk_2` (`document_scope_id`),
  CONSTRAINT `document_type_scope_ibfk_1` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `document_type_scope_ibfk_2` FOREIGN KEY (`document_scope_id`) REFERENCES `document_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella pivot per associare uno o più ambiti (tag) a ogni tipologia di documento.';


CREATE TABLE `document_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice documento',
  `is_person` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Documento inerente Persona o azienda',
  `is_signed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se il documento deve essere firmato',
  `is_monitored` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se la scadenza documento deve essere monitorata nel tempo',
  `duration` int DEFAULT NULL COMMENT 'Validità dal rilascio in giorni',
  `emitted_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ente di rilascio',
  `is_sensible` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se contiene dati sensibili',
  `is_template` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se forniamo noi il documento',
  `is_stored` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se il documento deve avere conservazione sostitutiva',
  `is_practice` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True se il documento è relativo alla pratica',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Tipologie di documenti riconosciuti per l''Adeguata Verifica.';


CREATE TABLE `documents` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'UUID del documento',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `documentable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo di modello associato (es. Client, Employee, Practice)',
  `documentable_id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ID del modello associato',
  `document_type_id` int unsigned DEFAULT NULL COMMENT 'ID del tipo di documento associato',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome del documento',
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'uploaded' COMMENT 'Stato del documento',
  `is_template` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se forniamo noi il documento',
  `expires_at` date DEFAULT NULL COMMENT 'Scadenza documento',
  `emitted_at` date DEFAULT NULL COMMENT 'Data emissione documento',
  `docnumber` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero documento',
  `rejection_note` text COLLATE utf8mb4_unicode_ci COMMENT 'Motivazione in caso di documento rifiutato',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'Data e ora della verifica',
  `verified_by` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente/admin che ha verificato il documento',
  `uploaded_by` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente/admin che ha caricato il documento',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documents_company_id_foreign` (`company_id`),
  KEY `documents_verified_by_foreign` (`verified_by`),
  KEY `documents_uploaded_by_foreign` (`uploaded_by`),
  KEY `documents_documentable_type_documentable_id_index` (`documentable_type`,`documentable_id`),
  KEY `documents_document_type_id_foreign` (`document_type_id`),
  CONSTRAINT `documents_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_document_type_id_foreign` FOREIGN KEY (`document_type_id`) REFERENCES `document_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `documents_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco dipendente',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente collegato',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome completo dipendente',
  `role_title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Qualifica aziendale (es. Responsabile Backoffice)',
  `cf` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice Fiscale',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email aziendale dipendente',
  `phone` varchar(16) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefono o interno dipendente',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dipartimento (es. Amministrazione, Compliance)',
  `oam` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice OAM individuale dipendente',
  `ivass` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice IVASS individuale dipendente',
  `hiring_date` date DEFAULT NULL COMMENT 'Data di assunzione',
  `termination_date` date DEFAULT NULL COMMENT 'Data di fine rapporto',
  `company_branch_id` int unsigned DEFAULT NULL COMMENT 'Sede fisica di assegnazione',
  `coordinated_by_id` int unsigned DEFAULT NULL COMMENT 'ID del coordinatore (altro dipendente della stessa sede)',
  `employee_types` enum('dipendente','collaboratore','stagista','consulente','amministratore') COLLATE utf8mb4_unicode_ci DEFAULT 'dipendente' COMMENT 'Tipologia di dipendente',
  `supervisor_type` enum('no','si','filiale') COLLATE utf8mb4_unicode_ci DEFAULT 'no' COMMENT 'Se supervisore indicare e specificare se di filiale',
  `privacy_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruolo Privacy (es. Titolare Autonomo, Responsabile Esterno)',
  `purpose` text COLLATE utf8mb4_unicode_ci COMMENT 'Finalità del trattamento',
  `data_subjects` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Interessati',
  `data_categories` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Dati Trattati',
  `retention_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tempi di Conservazione (Data Retention)',
  `extra_eu_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Trasferimento Extra-UE',
  `security_measures` text COLLATE utf8mb4_unicode_ci COMMENT 'Misure di Sicurezza',
  `privacy_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Altri Dati Privacy',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_structure` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica personale di struttura',
  `is_ghost` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Personale prestato',
  PRIMARY KEY (`id`),
  KEY `employees_user_id_foreign` (`user_id`),
  KEY `employees_ibfk_1` (`company_id`),
  KEY `employees_ibfk_3` (`company_branch_id`),
  KEY `fk_employees_coordinated_by` (`coordinated_by_id`),
  CONSTRAINT `employees_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`company_branch_id`) REFERENCES `company_branches` (`id`),
  CONSTRAINT `employees_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_employees_coordinated_by` FOREIGN KEY (`coordinated_by_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anagrafica dipendenti interni delle società di mediazione.';


CREATE TABLE `employment_types` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione',
  `company_type_id` int unsigned DEFAULT NULL COMMENT 'Ruolo specifico per quella determinata categoria di company',
  `client_type_id` int unsigned DEFAULT NULL COMMENT 'Ruolo specifico per quella determinata categoria di clienti',
  `privacy_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruolo Privacy (es. Titolare Autonomo, Responsabile Esterno)',
  `purpose` text COLLATE utf8mb4_unicode_ci COMMENT 'Finalità del trattamento',
  `data_subjects` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Interessati',
  `data_categories` text COLLATE utf8mb4_unicode_ci COMMENT 'Categorie di Dati Trattati',
  `retention_period` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tempi di Conservazione (Data Retention)',
  `extra_eu_transfer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Trasferimento Extra-UE',
  `security_measures` text COLLATE utf8mb4_unicode_ci COMMENT 'Misure di Sicurezza',
  `privacy_data` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Altri Dati Privacy',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`),
  KEY `employment_types_company_type_id_foreign` (`company_type_id`),
  CONSTRAINT `employment_types_company_type_id_foreign` FOREIGN KEY (`company_type_id`) REFERENCES `company_types` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catalogo globale: Classificazione lavorativa del cliente (fondamentale per le logiche di delibera del credito).';


CREATE TABLE `enasarco_limits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione',
  `year` int NOT NULL COMMENT 'Anno di riferimento per l''aliquota',
  `minimal_amount` decimal(10,2) NOT NULL COMMENT 'Minimale contributivo annuo in Euro',
  `maximal_amount` decimal(10,2) NOT NULL COMMENT 'Massimale provvigionale annuo in Euro',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data inserimento record',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data aggiornamento importi',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Massimali e minimali annui stabiliti dalla Fondazione Enasarco.';


CREATE TABLE `exports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exporter` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente collegato',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `exports_user_id_foreign` (`user_id`),
  CONSTRAINT `exports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `failed_import_rows` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `data` json NOT NULL,
  `import_id` bigint unsigned NOT NULL,
  `validation_error` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `failed_import_rows_import_id_foreign` (`import_id`),
  CONSTRAINT `failed_import_rows_import_id_foreign` FOREIGN KEY (`import_id`) REFERENCES `imports` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `firrs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `minimo` decimal(10,2) DEFAULT NULL COMMENT 'Importo minimo',
  `massimo` decimal(10,2) DEFAULT NULL COMMENT 'Importo massimo',
  `aliquota` decimal(5,2) DEFAULT NULL COMMENT 'Aliquota FIRR',
  `competenza` int NOT NULL DEFAULT '2025' COMMENT 'Anno di competenza',
  `enasarco` enum('monomandatario','plurimandatario','societa','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plurimandatario' COMMENT 'Tipo mandato ENASARCO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Aliquote per il calcolo dell''Indennità Risoluzione Rapporto (FIRR)';


CREATE TABLE `function_privacys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `business_function_id` bigint unsigned NOT NULL,
  `processing_activity` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_subjects` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_categories` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `purpose` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `legal_basis` enum('Consenso','Esecuzione di un contratto','Obbligo di legge','Legittimo interesse','Interesse vitale','Interesse pubblico') COLLATE utf8mb4_unicode_ci NOT NULL,
  `recipients` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `non_eu_transfer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Nessuno',
  `retention_period` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `security_measures` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `start_at` timestamp NULL DEFAULT NULL COMMENT 'Data di inizio validità',
  `end_at` timestamp NULL DEFAULT NULL COMMENT 'Data di fine validità',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `function_privacys_business_function_id_foreign` (`business_function_id`),
  CONSTRAINT `function_privacys_business_function_id_foreign` FOREIGN KEY (`business_function_id`) REFERENCES `business_functions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `imports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `completed_at` timestamp NULL DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `importer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `processed_rows` int unsigned NOT NULL DEFAULT '0',
  `total_rows` int unsigned NOT NULL,
  `successful_rows` int unsigned NOT NULL DEFAULT '0',
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente collegato',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `imports_user_id_foreign` (`user_id`),
  CONSTRAINT `imports_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `media` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'public',
  `size` bigint unsigned DEFAULT NULL,
  `manipulations` json DEFAULT NULL,
  `custom_properties` json DEFAULT NULL,
  `generated_conversions` json DEFAULT NULL,
  `responsive_images` json DEFAULT NULL,
  `order_column` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `media_uuid_unique` (`uuid`),
  KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  KEY `media_order_column_index` (`order_column`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oam_scopes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID autoincrementante',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice ambito OAM',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Descrizione ambito operativo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oam_scopes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale (Senza Tenant): Ambiti operativi OAM.';


CREATE TABLE `oams` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `autorizzato_ad_operare` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `persona` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codice_fiscale` varchar(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domicilio_sede_legale` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `elenco` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_iscrizione` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_iscrizione` date DEFAULT NULL,
  `stato` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data_stato` date DEFAULT NULL,
  `causale_stato_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `oams_codice_fiscale_unique` (`codice_fiscale`),
  KEY `oams_codice_fiscale_index` (`codice_fiscale`),
  KEY `oams_elenco_index` (`elenco`),
  KEY `oams_stato_index` (`stato`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `passkeys` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `authenticatable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `authenticatable_id` bigint unsigned NOT NULL,
  `panel_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `credential_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` json NOT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `passkeys_authenticatable_type_authenticatable_id_index` (`authenticatable_type`,`authenticatable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `practice_commission_statuses` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stato pagamento',
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_perfectioned` tinyint(1) DEFAULT NULL,
  `is_working` tinyint(1) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `practice_commissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `practice_id` int unsigned NOT NULL COMMENT 'La pratica che ha generato la provvigione',
  `proforma_id` int unsigned DEFAULT NULL COMMENT 'Il proforma in cui questa provvigione è stata liquidata (NULL se non ancora liquidata)',
  `practice_commission_status_id` tinyint unsigned DEFAULT NULL COMMENT 'ID stato commissione pratica',
  `agent_id` int unsigned DEFAULT NULL COMMENT 'Agente beneficiario',
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Mandante',
  `CRM_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice CRM',
  `inserted_at` date DEFAULT NULL COMMENT 'Data inserimento',
  `is_enasarco` tinyint(1) DEFAULT '1' COMMENT 'Provvigione da conteggiare per ENASARCO',
  `is_insurance` tinyint(1) DEFAULT '1' COMMENT 'Provvigione assicurativa',
  `is_payment` tinyint(1) DEFAULT NULL COMMENT 'Provvigione passiva verso rete',
  `is_recurrent` tinyint(1) DEFAULT NULL COMMENT 'Compenso ricorrente',
  `is_prize` tinyint(1) DEFAULT NULL COMMENT 'Premio da mandante',
  `is_client` tinyint(1) DEFAULT NULL COMMENT 'Compenso da cliente',
  `is_coordination` tinyint(1) DEFAULT '0' COMMENT 'Compenso coordinamento',
  `tipo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo di provvigione',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Provvigione',
  `amount` decimal(10,2) DEFAULT NULL COMMENT 'Importo provvigionale lordo per questa singola pratica',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dettaglio (es. Bonus extra o Provvigione base)',
  `status_payment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stato pagamento perfezionata',
  `status_at` date DEFAULT NULL COMMENT 'Data stato pagamento',
  `perfected_at` date DEFAULT NULL COMMENT 'Data perfezionamento provvigione',
  `cancellation_at` date DEFAULT NULL COMMENT 'Data annullamento provvigione',
  `invoice_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero fattura',
  `invoice_at` date DEFAULT NULL COMMENT 'Data fattura',
  `paided_at` date DEFAULT NULL COMMENT 'Data pagamento',
  `is_storno` tinyint(1) DEFAULT NULL COMMENT 'Storno provvigionale',
  `storned_at` date DEFAULT NULL COMMENT 'Data storno',
  `storno_amount` decimal(10,2) DEFAULT NULL COMMENT 'Importo provvigionale stornato',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `practice_commissions_ibfk_1` (`company_id`),
  KEY `practice_commissions_ibfk_2` (`practice_id`),
  KEY `practice_commissions_ibfk_3` (`proforma_id`),
  KEY `practice_commissions_practice_commission_status_id_foreign` (`practice_commission_status_id`),
  CONSTRAINT `practice_commissions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `practice_commissions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practice_commissions_ibfk_2` FOREIGN KEY (`practice_id`) REFERENCES `practices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practice_commissions_ibfk_3` FOREIGN KEY (`proforma_id`) REFERENCES `proformas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `practice_commissions_practice_commission_status_id_foreign` FOREIGN KEY (`practice_commission_status_id`) REFERENCES `practice_commission_statuses` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Singole righe provvigionali maturate dalle pratiche. Vengono raggruppate nel proforma mensile.';


CREATE TABLE `practice_scopes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Es. Mutui Ipotecari, Cessioni del Quinto, Prestiti Personali',
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `oam_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_oneclient` tinyint(1) DEFAULT '1' COMMENT 'Finanziamento mono cliente',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di creazione',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella tipologia finanziamento';


CREATE TABLE `practice_status_lookup` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dello stato (es. istruttoria, deliberata, erogata)',
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Colore del badge per Filament (es. warning, success, danger)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione dettagliata dello stato',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Se lo stato è utilizzabile',
  `sort_order` int NOT NULL DEFAULT '0' COMMENT 'Ordinamento visualizzazione',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella lookup per gli stati delle pratiche con colori associati';


CREATE TABLE `practice_statuses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice dello stato (es. istruttoria, delibera, erogata, annullata)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `ordine` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ordine stato per workflow operatore',
  `status` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stato pratica working / rejected / perfected / renewable',
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Colore dello stato',
  `is_rejected` tinyint(1) DEFAULT '0' COMMENT 'Stato respinto',
  `is_working` tinyint(1) DEFAULT '0' COMMENT 'Stato in lavorazione',
  `is_completed` tinyint(1) DEFAULT '0' COMMENT 'Stato completato',
  `is_perfectioned` tinyint(1) DEFAULT '0' COMMENT 'Stato perfezionato',
  `rejected_month` int DEFAULT '0' COMMENT 'Mese di rifiuto da inserimento pratica',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Data creazione record',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Data ultimo aggiornamento',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Stati della  pratica';


CREATE TABLE `practices` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante della pratica',
  `client_mandate_id` bigint unsigned DEFAULT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_id` int unsigned DEFAULT NULL COMMENT 'Mandante (banca)',
  `agent_id` int unsigned DEFAULT NULL COMMENT 'Agente o collaboratore per provvigioni',
  `practice_status_id` int unsigned DEFAULT NULL COMMENT 'ID dello stato della pratica',
  `stato_pratica` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stato pratica originale da sistema esterno',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice o nome identificativo (es. Mutuo Acquisto Prima Casa Rossi)',
  `CRM_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice CRM interno',
  `principal_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice mandante',
  `amount` decimal(12,2) DEFAULT NULL COMMENT 'Importo del finanziamento/mutuo richiesto o erogato',
  `net` decimal(12,2) DEFAULT NULL COMMENT 'Netto erogato',
  `brokerage_fee` decimal(10,2) DEFAULT NULL COMMENT 'Provvigione pattuita',
  `practice_scope_id` int unsigned DEFAULT NULL COMMENT 'Ambito della pratica',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'working' COMMENT 'Stato interno: working, rejected, perfected',
  `statoproforma` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stato proforma: Inserito / Sospeso / Annullato / Inviato / Abbinato',
  `inserted_at` date DEFAULT NULL COMMENT 'Data inserimento pratica',
  `erogated_at` date DEFAULT NULL COMMENT 'Data erogazione finanziamento / stipula mutuo notaio',
  `rejected_at` date DEFAULT NULL COMMENT 'Data rifiuto pratica',
  `rejected_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Causale rifiuto pratica es. Rifiutata banca',
  `status_at` date DEFAULT NULL COMMENT 'Data stato perfezionata ovvero possibile emissione proforma ad agente',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Descrizione pratica',
  `annotation` text COLLATE utf8mb4_unicode_ci COMMENT 'Annotazioni interne sulla pratica',
  `perfected_at` date DEFAULT NULL COMMENT 'Data perfezionamento pratica',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Pratica attiva/inattiva',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `practices_client_mandate_id_foreign` (`client_mandate_id`),
  KEY `practices_principal_id_foreign` (`principal_id`),
  KEY `practices_agent_id_foreign` (`agent_id`),
  KEY `practices_ibfk_1` (`company_id`),
  KEY `practices_ibfk_5` (`practice_scope_id`),
  KEY `practices_practice_status_id_foreign` (`practice_status_id`),
  CONSTRAINT `practices_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL,
  CONSTRAINT `practices_client_mandate_id_foreign` FOREIGN KEY (`client_mandate_id`) REFERENCES `client_mandates` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practices_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `practices_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `practices_ibfk_5` FOREIGN KEY (`practice_scope_id`) REFERENCES `practice_scopes` (`id`),
  CONSTRAINT `practices_practice_scope_id_foreign` FOREIGN KEY (`practice_scope_id`) REFERENCES `practice_scopes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `practices_practice_status_id_foreign` FOREIGN KEY (`practice_status_id`) REFERENCES `practice_statuses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `practices_principal_id_foreign` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Pratiche di mediazione (Mutui, Cessioni, Prestiti personali) caricate a sistema.';


CREATE TABLE `principal_contacts` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco contatto mandante',
  `principal_id` int unsigned NOT NULL COMMENT 'Riferimento alla banca mandante',
  `first_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome del referente bancario',
  `last_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cognome del referente bancario',
  `role_title` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ruolo (es. Responsabile Istruttoria, Area Manager, Deliberante)',
  `department` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Dipartimento (es. Ufficio Mutui, Compliance, Estero)',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email diretta del referente',
  `phone_office` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Telefono ufficio / interno',
  `phone_mobile` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Cellulare aziendale',
  `is_active` tinyint(1) DEFAULT '1' COMMENT 'Indica se il referente è ancora il punto di contatto',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note utili (es. "Contattare solo per pratiche sopra i 200k")',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_principal_contact_search` (`last_name`,`department`),
  KEY `principal_contacts_ibfk_1` (`principal_id`),
  CONSTRAINT `principal_contacts_ibfk_1` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Rubrica dei referenti presso le banche mandanti per comunicazioni operative e istruttoria.';


CREATE TABLE `principal_employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `principal_id` int unsigned NOT NULL,
  `usercode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice identificativo utente',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione ruolo o note',
  `start_date` date NOT NULL COMMENT 'Data inizio autorizzazione',
  `end_date` date DEFAULT NULL COMMENT 'Data fine autorizzazione',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Stato attivo/inattivo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `principal_employees_usercode_unique` (`usercode`),
  KEY `principal_employees_principal_id_is_active_index` (`principal_id`,`is_active`),
  KEY `principal_employees_usercode_index` (`usercode`),
  KEY `principal_employees_start_date_end_date_index` (`start_date`,`end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `principal_mandates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco del mandato',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `principal_id` int unsigned NOT NULL COMMENT 'Banca o Istituto mandante',
  `mandate_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero di protocollo o identificativo del contratto di mandato',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `start_date` date DEFAULT NULL COMMENT 'Data di decorrenza del mandato',
  `end_date` date DEFAULT NULL COMMENT 'Data di scadenza (NULL se a tempo indeterminato)',
  `is_exclusive` tinyint(1) DEFAULT '0' COMMENT 'Indica se il mandato prevede l''esclusiva per quella categoria',
  `status` enum('ATTIVO','SCADUTO','RECEDUTO','SOPESO') COLLATE utf8mb4_unicode_ci DEFAULT 'ATTIVO' COMMENT 'Stato operativo del mandato',
  `contract_file_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Riferimento al PDF del contratto firmato',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note su provvigioni particolari o patti specifici',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `mandates_ibfk_1` (`company_id`),
  KEY `mandates_ibfk_2` (`principal_id`),
  CONSTRAINT `mandates_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `principal_mandates_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `principal_mandates_ibfk_2` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contratti di mandato che legano l''agenzia agli Istituti Bancari.';


CREATE TABLE `principal_scopes` (
  `principal_id` int unsigned NOT NULL COMMENT 'Riferimento al mandato',
  `practice_scope_id` int unsigned NOT NULL COMMENT 'Riferimento all''ambito (es. Cessione del Quinto, Mutui)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  KEY `principal_scopes_ibfk_2` (`practice_scope_id`),
  KEY `principal_scopes_ibfk_3` (`principal_id`),
  CONSTRAINT `principal_scopes_ibfk_2` FOREIGN KEY (`practice_scope_id`) REFERENCES `practice_scopes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `principal_scopes_ibfk_3` FOREIGN KEY (`principal_id`) REFERENCES `principals` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella pivot: definisce quali comparti operativi sono autorizzati per ogni singolo mandato.';


CREATE TABLE `principals` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dell''istituto bancario o finanziaria (es. Intesa Sanpaolo, Compass)',
  `abi` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Abi per banche o codice ISVASS',
  `stipulated_at` date DEFAULT NULL COMMENT 'Data stipula contratto convenzione',
  `dismissed_at` date DEFAULT NULL COMMENT 'Data cessazione rapporto convenzione',
  `vat_number` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Partita IVA dell''istituto',
  `vat_name` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ragione sociale fiscale',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Banca / Assicurazione / Utility',
  `oam` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice di iscrizione OAM',
  `ivass` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Codice di iscrizione IVASS',
  `is_active` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica se la banca è attualmente convenzionata',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mandate_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero di protocollo o identificativo del contratto di mandato',
  `start_date` date DEFAULT NULL COMMENT 'Data di decorrenza del mandato',
  `end_date` date DEFAULT NULL COMMENT 'Data di scadenza (NULL se a tempo indeterminato)',
  `is_exclusive` tinyint(1) DEFAULT '0' COMMENT 'Indica se il mandato prevede l''esclusiva per quella categoria',
  `status` enum('ATTIVO','SCADUTO','RECEDUTO','SOPESO') COLLATE utf8mb4_unicode_ci DEFAULT 'ATTIVO' COMMENT 'Stato operativo del mandato',
  `is_dummy` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Mandante fittizio / non convenzionato',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note su provvigioni particolari o patti specifici',
  `principal_type` enum('--','banca','agente_assicurativo','agente_captive') COLLATE utf8mb4_unicode_ci DEFAULT 'banca' COMMENT 'Tipologia del mandante',
  `submission_type` enum('--','accesso portale','inoltro','entrambi') COLLATE utf8mb4_unicode_ci DEFAULT 'accesso portale' COMMENT 'Modalita inoltro pratiche',
  `contoCOGE` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Conto COGE',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `principals_ibfk_1` (`company_id`),
  CONSTRAINT `principals_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `principals_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella globale delle banche ed enti eroganti convenzionati.';


CREATE TABLE `proforma_status` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT NULL,
  `is_payable` tinyint(1) DEFAULT NULL,
  `is_external` tinyint(1) DEFAULT NULL,
  `is_ok` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `proforma_status_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco log stato',
  `proforma_id` int unsigned NOT NULL COMMENT 'Riferimento al proforma',
  `status` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Lo stato assunto dal proforma',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `changed_by` int unsigned NOT NULL COMMENT 'L''utente (amministratore) che ha effettuato l''azione',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Eventuali note sul cambio stato (es. motivo dell''annullamento)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e ora esatta del passaggio di stato',
  PRIMARY KEY (`id`),
  KEY `proforma_status_history_ibfk_1` (`proforma_id`),
  CONSTRAINT `proforma_status_history_ibfk_1` FOREIGN KEY (`proforma_id`) REFERENCES `proformas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro storico dei passaggi di stato del proforma per controllo amministrativo.';


CREATE TABLE `proformas` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID intero autoincrementante',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `agent_id` int unsigned NOT NULL COMMENT 'L''agente beneficiario delle provvigioni',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Riferimento documento (es. Proforma 01/2026 - Rossi Mario)',
  `commission_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_commissions` decimal(10,2) DEFAULT NULL COMMENT 'Totale provvigioni lorde maturate nel periodo',
  `enasarco_retained` decimal(10,2) DEFAULT NULL COMMENT 'Quota Enasarco trattenuta dall''agenzia (50% del totale contributo)',
  `remburse` decimal(10,2) DEFAULT NULL,
  `remburse_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contribute` decimal(10,2) DEFAULT NULL,
  `contribute_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `refuse` decimal(10,2) DEFAULT NULL,
  `refuse_label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `net_amount` decimal(10,2) DEFAULT NULL COMMENT 'Importo netto da liquidare all''agente',
  `month` int DEFAULT NULL COMMENT 'Mese di competenza della liquidazione (1-12)',
  `year` int DEFAULT NULL COMMENT 'Anno di competenza',
  `status` enum('INSERITO','INVIATO','ANNULLATO','FATTURATO','PAGATO','STORICO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INSERITO',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data di generazione del proforma',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultima modifica prima della fatturazione definitiva',
  PRIMARY KEY (`id`),
  KEY `proformas_ibfk_1` (`company_id`),
  CONSTRAINT `proformas_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `proformas_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Proforma mensili generati dal sistema per calcolare compensi e ritenute Enasarco degli agenti.';


CREATE TABLE `regulatory_bodies` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco dell''ente',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome dell''ente (es. OAM - Organismo Agenti e Mediatori, Garante per la Protezione dei Dati Personali)',
  `acronym` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sigla (es. OAM, GPDP, IVASS)',
  `official_website` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sito web istituzionale',
  `pec_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Indirizzo PEC per comunicazioni legali',
  `portal_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL del portale riservato per invio flussi/segnalazioni',
  `contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Eventuale referente o dirigente di riferimento',
  `phone_support` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Numero di telefono assistenza/ispettorato',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Note su modalità di invio documenti o scadenze fisse',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `regulatory_bodies_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Anagrafica delle Autorità di Vigilanza e degli Enti preposti ai controlli normativi.';


CREATE TABLE `regulatory_body_scopes` (
  `regulatory_body_id` int unsigned NOT NULL COMMENT 'Riferimento all''ente',
  `document_scope_id` int unsigned NOT NULL COMMENT 'Riferimento all''ambito (es. Privacy, AML, OAM)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  PRIMARY KEY (`regulatory_body_id`,`document_scope_id`),
  KEY `regulatory_body_scope_ibfk_2` (`document_scope_id`),
  CONSTRAINT `regulatory_body_scope_ibfk_1` FOREIGN KEY (`regulatory_body_id`) REFERENCES `regulatory_bodies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `regulatory_body_scope_ibfk_2` FOREIGN KEY (`document_scope_id`) REFERENCES `document_scopes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella pivot per definire quali ambiti normativi sono di competenza di ciascun ente.';


CREATE TABLE `remediations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `audit_id` int unsigned DEFAULT NULL COMMENT 'ID dell''audit di riferimento',
  `remediation_type` enum('AML','Gestione Reclami','Monitoraggio Rete','Privacy','Trasparenza','Assetto Organizzativo') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'categorizzare il rimedio',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'nome rimedio',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'codice rimedio',
  `description` text COLLATE utf8mb4_unicode_ci,
  `business_function_id` bigint unsigned DEFAULT NULL,
  `timeframe_hours` int DEFAULT NULL,
  `timeframe_desc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `remediations_audit_id_foreign` (`audit_id`),
  KEY `remediations_business_function_id_foreign` (`business_function_id`),
  CONSTRAINT `remediations_audit_id_foreign` FOREIGN KEY (`audit_id`) REFERENCES `audits` (`id`) ON DELETE CASCADE,
  CONSTRAINT `remediations_business_function_id_foreign` FOREIGN KEY (`business_function_id`) REFERENCES `business_functions` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_company_id_name_guard_name_unique` (`company_id`,`name`,`guard_name`),
  CONSTRAINT `roles_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `socialite_users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL COMMENT 'ID dell''utente collegato',
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `provider_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `socialite_users_provider_provider_id_unique` (`provider`,`provider_id`),
  KEY `socialite_users_user_id_foreign` (`user_id`),
  CONSTRAINT `socialite_users_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `software_applications` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco software',
  `category_id` int unsigned NOT NULL COMMENT 'Riferimento alla categoria',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome commerciale (es. Salesforce, XCrm, Teamsystem, Namirial)',
  `provider_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome della software house produttrice',
  `website_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Sito web ufficiale del produttore',
  `api_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sandbox_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_parameters` text COLLATE utf8mb4_unicode_ci,
  `is_cloud` tinyint(1) DEFAULT '1' COMMENT 'Indica se il software è SaaS/Cloud o On-Premise',
  `apikey` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'API Key per il software',
  `wallet_balance` decimal(10,2) DEFAULT NULL COMMENT 'Saldo del wallet',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `software_applications_ibfk_1` (`category_id`),
  CONSTRAINT `software_applications_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `software_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale: Elenco dei software più comuni nel settore finanziario.';


CREATE TABLE `software_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco categoria',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Es. CRM, Call Center, Contabilità, AML, Firma Elettronica',
  `code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Codice tecnico (es. CRM, CALL_CENTER, ACC, AML)',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione della tipologia di software',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `software_categories_name_unique` (`name`),
  UNIQUE KEY `software_categories_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabella di lookup globale: Categorie di software utilizzati dalle agenzie.';


CREATE TABLE `software_mappings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco mappatura',
  `software_application_id` int unsigned NOT NULL COMMENT 'Il software sorgente (es. CRM esterno)',
  `mapping_type` enum('PRACTICE_TYPE','PRACTICE_STATUS','CLIENT_TYPE','BANK_NAME','COMMISSION_STATUS') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Cosa stiamo mappando (es. Tipo Pratica o Stato Pratica)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nostro codice alfanumerico (es. "MUT_ACQ")',
  `external_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Il valore testuale nel CRM sorgente (es. "Mutuo immobiliare")',
  `internal_id` int unsigned DEFAULT NULL COMMENT 'L''ID corrispondente nel nostro database (es. ID di "Mutuo Acquisto")',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_mapping_lookup` (`software_application_id`,`mapping_type`,`external_value`),
  CONSTRAINT `software_mappings_ibfk_1` FOREIGN KEY (`software_application_id`) REFERENCES `software_applications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabelle di conversione (Cross-Reference) per tradurre i dati da software esterni al formato interno.';


CREATE TABLE `training_records` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID record partecipazione',
  `training_session_id` int unsigned DEFAULT NULL COMMENT 'La sessione seguita',
  `trainable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Classe del Modello collegato (es. App\\Models\\Employee, App\\Models\\Agent, etc.)',
  `trainable_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ID del Modello (VARCHAR 36 per supportare sia UUID che Integer)',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrizione',
  `status` enum('ISCRITTO','FREQUENTANTE','COMPLETATO','NON_SUPERATO') COLLATE utf8mb4_unicode_ci DEFAULT 'ISCRITTO',
  `hours_attended` decimal(5,2) DEFAULT '0.00' COMMENT 'Ore effettivamente frequentate dal singolo utente',
  `score` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Esito test finale (es. 28/30 o Idoneo)',
  `completion_date` date DEFAULT NULL COMMENT 'Data esatta di conseguimento titolo',
  `certificate_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link al PDF dell''attestato (se salvato fuori da Media Library)',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data creazione record',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data ultimo aggiornamento record',
  PRIMARY KEY (`id`),
  KEY `trainable_index` (`trainable_type`,`trainable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro presenze e certificazioni: traccia la formazione di agenti e dipendenti per scopi normativi.';


CREATE TABLE `training_sessions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco sessione',
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `training_template_id` int unsigned DEFAULT NULL COMMENT 'Riferimento al template del corso',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Nome specifico (es. Sessione Autunnale OAM Roma)',
  `total_hours` decimal(5,2) DEFAULT '1.00' COMMENT 'Numero ore effettive erogate in questa sessione',
  `trainer_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome del docente o ente formatore',
  `start_date` date DEFAULT NULL COMMENT 'Data inizio corso',
  `end_date` date DEFAULT NULL COMMENT 'Data fine corso',
  `location` enum('ONLINE','PRESENZA','IBRIDO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ONLINE' COMMENT 'Modalità di erogazione',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `training_sessions_company_id_foreign` (`company_id`),
  KEY `training_template_id` (`training_template_id`),
  CONSTRAINT `training_sessions_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sessioni reali di formazione erogate o pianificate dalle agenzie.';


CREATE TABLE `training_templates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID univoco template',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Titolo del corso (es. Aggiornamento Professionale OAM 2024)',
  `category` enum('OAM','IVASS','GDPR','SICUREZZA','PRODOTTO','SOFT_SKILLS') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OAM' COMMENT 'Categoria normativa o tecnica del corso',
  `base_hours` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Numero di ore standard previste per questo corso',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'Programma del corso e obiettivi formativi',
  `is_mandatory` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Indica se il corso è obbligatorio per legge',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Catalogo globale: Modelli predefiniti di corsi di formazione.';


CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome e Cognome dell''utente',
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Email usata per il login',
  `email_verified_at` timestamp NULL DEFAULT NULL COMMENT 'Data verifica email',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Password hashata tramite bcrypt/argon2',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Token per la sessione "Ricordami"',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data registrazione utente',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Ultimo aggiornamento profilo',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_ibfk_1` (`company_id`),
  CONSTRAINT `users_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Utenti del sistema: SuperAdmin, Titolari, Agenti e Backoffice.';


CREATE TABLE `vcoge` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mese` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mese (formato YYYY-MM)',
  `entrata` decimal(38,2) DEFAULT NULL COMMENT 'Totale entrate',
  `uscita` decimal(38,2) DEFAULT NULL COMMENT 'Totale uscite',
  PRIMARY KEY (`id`),
  KEY `vcoge_company_id_foreign` (`company_id`),
  CONSTRAINT `vcoge_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Riepilogo mensile entrate e uscite';


CREATE TABLE `venasarcotot` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `produttore` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ragione sociale del referente',
  `montante` decimal(37,2) DEFAULT NULL COMMENT 'Montante provvigioni',
  `contributo` decimal(47,8) DEFAULT NULL COMMENT 'Contributo ENASARCO',
  `X` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Flag X',
  `imposta` decimal(47,8) DEFAULT NULL COMMENT 'Imposta sostitutiva',
  `firr` decimal(37,2) DEFAULT NULL COMMENT 'Importo FIRR',
  `competenza` int DEFAULT NULL COMMENT 'Anno di competenza',
  `enasarco` enum('monomandatario','plurimandatario','societa','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plurimandatario' COMMENT 'Tipo di mandato ENASARCO',
  PRIMARY KEY (`id`),
  KEY `venasarcotot_company_id_foreign` (`company_id`),
  CONSTRAINT `venasarcotot_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Totali ENASARCO per produttore';


CREATE TABLE `venasarcotrimestre` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `company_id` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `competenza` int unsigned DEFAULT NULL COMMENT 'Anno di competenza',
  `trimestre` int DEFAULT NULL COMMENT 'Numero trimestre (1-4)',
  `produttore` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Ragione sociale del referente',
  `enasarco` enum('no','monomandatario','plurimandatario','societa') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'plurimandatario' COMMENT 'Tipo di mandato ENASARCO',
  `montante` decimal(37,2) DEFAULT NULL COMMENT 'Montante provvigioni',
  `contributo` decimal(47,8) DEFAULT NULL COMMENT 'Contributo ENASARCO',
  PRIMARY KEY (`id`),
  KEY `venasarcotrimestre_company_id_foreign` (`company_id`),
  CONSTRAINT `venasarcotrimestre_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Totali ENASARCO trimestrali per produttore';


CREATE TABLE `vwoam` (`id` int unsigned, `company_id` char(36), `principal_id` int unsigned, `agent_id` int unsigned, `name` varchar(255), `CRM_code` varchar(255), `principal_code` varchar(255), `amount` decimal(12,2), `net` decimal(12,2), `brokerage_fee` decimal(10,2), `practice_scope_id` int unsigned, `status` varchar(50), `inserted_at` date, `status_at` date, `description` text, `annotation` text, `perfected_at` date, `is_active` tinyint(1), `created_at` timestamp, `updated_at` timestamp);


CREATE TABLE `vwpractice_oam_view` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `vwoam`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `vwoam` AS select `p`.`id` AS `id`,`p`.`company_id` AS `company_id`,`p`.`principal_id` AS `principal_id`,`p`.`agent_id` AS `agent_id`,`p`.`name` AS `name`,`p`.`CRM_code` AS `CRM_code`,`p`.`principal_code` AS `principal_code`,`p`.`amount` AS `amount`,`p`.`net` AS `net`,`p`.`brokerage_fee` AS `brokerage_fee`,`p`.`practice_scope_id` AS `practice_scope_id`,`p`.`status` AS `status`,`p`.`inserted_at` AS `inserted_at`,`p`.`status_at` AS `status_at`,`p`.`description` AS `description`,`p`.`annotation` AS `annotation`,`p`.`perfected_at` AS `perfected_at`,`p`.`is_active` AS `is_active`,`p`.`created_at` AS `created_at`,`p`.`updated_at` AS `updated_at` from (`practices` `p` join `practice_scopes` `s` on((`p`.`practice_scope_id` = `s`.`id`))) where ((`p`.`inserted_at` > '2025-01-01') or ((`p`.`perfected_at` > '2025-01-01') and (`p`.`perfected_at` < '2026-01-01'))) limit 10;

-- 2026-03-01 15:19:36 UTC