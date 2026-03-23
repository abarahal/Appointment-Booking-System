# ScheduleHub — Appointment Booking System

Module Drupal 10 personnalisé permettant aux visiteurs (anonymes ou connectés) de **réserver, modifier, annuler et consulter** des rendez-vous via un **formulaire wizard en 6 étapes**, avec une **interface d'administration** complète et un **système de notifications par email** (confirmation, modification, annulation, rappel).

---

## Fonctionnalités principales

- Formulaire de réservation en 6 étapes avec navigation avant/arrière
- Calendrier interactif (FullCalendar) pour la sélection de créneaux
- Modification et annulation de rendez-vous existants
- Recherche de rendez-vous par email ou téléphone
- Tableau de bord administrateur avec statistiques
- Liste filtrée et paginée des rendez-vous (admin)
- Export CSV par lot (Batch API)
- Emails HTML transactionnels (confirmation, modification, annulation)
- Emails de rappel automatiques via le système de file d'attente (Queue + Cron)
- Configuration modulaire (durée des créneaux, horaires, rappels)
- 4 permissions granulaires

---

## Structure du module

```
appointment/
├── appointment.info.yml              → Déclaration du module (nom, dépendances)
├── appointment.install               → Hooks d'installation et de mise à jour (seed data)
├── appointment.module                → Hooks Drupal (hook_theme, hook_mail, hook_cron)
├── appointment.routing.yml           → Définition des routes (14 URLs)
├── appointment.services.yml          → Déclaration des services (manager, email)
├── appointment.permissions.yml       → 4 permissions personnalisées
├── appointment.libraries.yml         → Bibliothèques CSS/JS
├── appointment.links.menu.yml        → Liens dans le menu admin
├── appointment.links.task.yml        → Onglets admin (Dashboard, Liste, Export, Config)
├── config/
│   └── install/
│       └── appointment.settings.yml  → Configuration par défaut du module
├── css/
│   └── appointment.admin.css         → Styles de l'interface d'administration
├── js/
│   └── dialog-stack.js               → Système de dialogues JavaScript
├── templates/
│   ├── appointment-confirmation.html.twig → Page de confirmation
│   ├── appointment-list.html.twig         → Liste des rendez-vous
│   └── email/
│       ├── appointment-email-confirmation.html.twig   → Template email confirmation
│       ├── appointment-email-modification.html.twig   → Template email modification
│       ├── appointment-email-cancellation.html.twig   → Template email annulation
│       └── appointment-email-reminder.html.twig       → Template email rappel
└── src/
    ├── Controller/
    │   ├── AppointmentController.php       → Pages publiques (confirmation, mes RDV, API)
    │   └── AppointmentAdminController.php  → Pages admin (dashboard, liste, CSV)
    ├── Entity/
    │   ├── AgencyEntity.php                → Entité "agence"
    │   └── AppointmentEntity.php           → Entité "rendez-vous"
    ├── Form/
    │   ├── AppointmentBookForm.php         → Formulaire wizard 6 étapes
    │   ├── AppointmentCancelForm.php       → Formulaire d'annulation
    │   ├── AppointmentLookupForm.php       → Recherche de rendez-vous
    │   ├── AppointmentSettingsForm.php     → Formulaire de configuration
    │   └── AppointmentExportForm.php       → Formulaire d'export CSV
    ├── Service/
    │   ├── AppointmentManagerService.php   → Logique métier (disponibilité, CRUD)
    │   └── EmailService.php                → Envoi d'emails (direct + file d'attente)
    └── Plugin/
        └── QueueWorker/
            └── AppointmentEmailQueueWorker.php → Worker cron pour emails différés
```

---

## Concepts Drupal utilisés

| Concept           | Fichier(s)                     | Rôle                                                                                           |
| ----------------- | ------------------------------ | ---------------------------------------------------------------------------------------------- |
| **Module info**   | `appointment.info.yml`         | Déclare le module auprès de Drupal (nom, type, dépendances core).                              |
| **Routing**       | `appointment.routing.yml`      | Associe chaque URL à un contrôleur ou formulaire PHP.                                          |
| **Entity**        | `src/Entity/*.php`             | Type de données personnalisé stocké dans sa propre table SQL. Ici : `agency` et `appointment`. |
| **Form**          | `src/Form/*.php`               | Classe PHP qui construit un formulaire HTML, valide les données et traite la soumission.       |
| **Controller**    | `src/Controller/*.php`         | Classe PHP qui retourne une page (HTML ou JSON). Utilisée pour les pages non-formulaires.      |
| **Service**       | `src/Service/*.php`            | Classe réutilisable enregistrée dans `appointment.services.yml`, injectable partout.           |
| **QueueWorker**   | `src/Plugin/QueueWorker/*.php` | Plugin qui traite les tâches en file d'attente lors du cron Drupal.                            |
| **Config**        | `config/install/*.yml`         | Valeurs de configuration par défaut installées avec le module.                                 |
| **Permissions**   | `appointment.permissions.yml`  | Définit les droits d'accès (réserver, voir, administrer, gérer).                               |
| **Install hooks** | `appointment.install`          | `hook_install()` s'exécute à l'activation. `hook_update_N()` lors de `drush updb`.             |
| **hook_mail**     | `appointment.module`           | Construit le contenu des emails (sujet + body HTML) pour chaque type de notification.          |
| **hook_cron**     | `appointment.module`           | Planifie les emails de rappel pour les rendez-vous à venir.                                    |
| **Batch API**     | `AppointmentExportForm.php`    | Traite l'export CSV par lots pour éviter les timeouts sur de gros volumes.                     |
| **TempStore**     | `AppointmentBookForm.php`      | Stockage temporaire côté serveur pour garder les données entre les étapes du wizard.           |

---

## Routes (URLs)

### Pages publiques

| URL                              | Description                                       |
| -------------------------------- | ------------------------------------------------- |
| `/appointment/book`              | Démarrer le wizard de réservation (étape 1)       |
| `/appointment/book/{step}`       | Aller à une étape spécifique du wizard (1–6)      |
| `/appointment/{id}/edit`         | Modifier un rendez-vous existant                  |
| `/appointment/{id}/edit/{step}`  | Modifier à une étape spécifique                   |
| `/appointment/{id}/cancel`       | Annuler un rendez-vous                            |
| `/appointment/{id}/confirmation` | Page de confirmation après réservation            |
| `/appointment/my`                | Liste des rendez-vous de l'utilisateur courant    |
| `/appointment/lookup`            | Rechercher un rendez-vous par email/téléphone     |
| `/api/appointment/booked-slots`  | API JSON : créneaux réservés (pour le calendrier) |

### Pages d'administration (permission : `administer appointments`)

| URL                                     | Description                              |
| --------------------------------------- | ---------------------------------------- |
| `/admin/structure/appointment`          | Tableau de bord avec statistiques        |
| `/admin/structure/appointment/list`     | Liste filtrée et paginée des rendez-vous |
| `/admin/structure/appointment/export`   | Export CSV par lot                       |
| `/admin/structure/appointment/csv`      | Téléchargement du fichier CSV généré     |
| `/admin/structure/appointment/settings` | Configuration du module                  |

---

## Le wizard de réservation en 6 étapes

1. **Agence** — Choisir une agence dans un menu déroulant
2. **Type** — Choisir le type de rendez-vous (Consultation, Suivi, Support)
3. **Conseiller** — Choisir un conseiller de l'agence sélectionnée
4. **Date & Heure** — Choisir un créneau via le calendrier FullCalendar (vérifie les conflits)
5. **Informations personnelles** — Saisir nom, email, téléphone, notes
6. **Confirmation** — Vérifier le récapitulatif et confirmer

Les données sont stockées entre les étapes via le **Private TempStore** de Drupal.

---

## Système d'emails

Le module envoie 4 types d'emails HTML :

| Type             | Déclencheur                            | Template Twig                                    |
| ---------------- | -------------------------------------- | ------------------------------------------------ |
| **Confirmation** | Création d'un nouveau rendez-vous      | `email/appointment-email-confirmation.html.twig` |
| **Modification** | Modification d'un rendez-vous existant | `email/appointment-email-modification.html.twig` |
| **Annulation**   | Annulation d'un rendez-vous            | `email/appointment-email-cancellation.html.twig` |
| **Rappel**       | Cron automatique (X heures avant)      | `email/appointment-email-reminder.html.twig`     |

Les rappels sont gérés par le **système de file d'attente Drupal** :

1. `hook_cron()` trouve les rendez-vous à rappeler
2. Les emails sont mis en file d'attente (`appointment_email`)
3. Le `QueueWorker` les traite lors du prochain cron

### Test avec MailHog

```bash
# Lancer MailHog (Docker)
docker run -d --name mailhog -p 1025:1025 -p 8025:8025 mailhog/mailhog

# Configurer Drupal SMTP → localhost:1025
# Voir les emails dans : http://localhost:8025

# Tester les rappels
drush cron
```

---

## Tables en base de données

| Table             | Description                                                                                  |
| ----------------- | -------------------------------------------------------------------------------------------- |
| **`agency`**      | Agences : nom, adresse, conseillers (JSON), statut actif/inactif                             |
| **`appointment`** | Rendez-vous : agence, conseiller, type, horaires, infos client, statut, token, reminder_sent |

Le module utilise également le vocabulaire **taxonomy** `appointment_type` pour les types de rendez-vous.

---

## Données de démonstration

À l'installation, le module crée :

- **3 types de rendez-vous** : Consultation, Follow-up, Support
- **2 agences** :
  - _Central Agency_ (123 Main Street) — conseillers : Alice Morgan, David Reed
  - _North Branch_ (77 North Avenue) — conseillers : Rina Patel, Leo Grant
- **1 rendez-vous de démonstration**

---

## Permissions

| Permission                | Description                              |
| ------------------------- | ---------------------------------------- |
| `administer appointments` | Accès complet aux pages d'administration |
| `view own appointments`   | Voir ses propres rendez-vous             |
| `book appointments`       | Créer un nouveau rendez-vous             |
| `manage own appointments` | Modifier/annuler ses propres rendez-vous |

---

## Configuration (`appointment.settings`)

| Paramètre               | Défaut   | Description                                              |
| ----------------------- | -------- | -------------------------------------------------------- |
| `slot_duration`         | 60 min   | Durée d'un créneau de rendez-vous                        |
| `working_hours_start`   | 08:00    | Début des heures ouvrables                               |
| `working_hours_end`     | 18:00    | Fin des heures ouvrables                                 |
| `max_advance_days`      | 90 jours | Limite de réservation dans le futur                      |
| `csv_batch_size`        | 100      | Nombre de lignes par lot lors de l'export CSV            |
| `notification_email`    | (vide)   | Email admin pour les notifications (sinon email du site) |
| `reminder_hours_before` | 24h      | Heures avant le rendez-vous pour envoyer le rappel       |

---

## Hooks Drupal implémentés

| Hook                              | Fichier               | Rôle                                                                     |
| --------------------------------- | --------------------- | ------------------------------------------------------------------------ |
| `hook_install()`                  | `appointment.install` | Crée le vocabulaire, les termes, les agences et un rendez-vous de démo.  |
| `hook_update_10001()` → `10004()` | `appointment.install` | Mises à jour progressives du schéma et des données.                      |
| `hook_theme()`                    | `appointment.module`  | Enregistre 6 hooks de thème (2 pages + 4 templates email).               |
| `hook_page_attachments()`         | `appointment.module`  | Attache la bibliothèque JS/CSS `appointment.frontend` à chaque page.     |
| `hook_mail()`                     | `appointment.module`  | Construit le sujet et le body HTML de chaque type d'email via Twig.      |
| `hook_cron()`                     | `appointment.module`  | Met en file d'attente les emails de rappel pour les rendez-vous à venir. |

---

## Rôle des classes et de leurs méthodes

### 1. `AgencyEntity` — Entité Agence

**Fichier :** `src/Entity/AgencyEntity.php`
**Namespace :** `Drupal\appointment\Entity`
**Hérite de :** `ContentEntityBase`
**Table SQL :** `agency`

Représente une agence physique avec ses conseillers. Les conseillers sont stockés en JSON dans le champ `advisers`.

| Méthode                  | Rôle                                                                                                                       |
| ------------------------ | -------------------------------------------------------------------------------------------------------------------------- |
| `baseFieldDefinitions()` | Définit les champs : `name` (string), `address` (texte long), `advisers` (JSON), `status` (booléen), `created`, `changed`. |

---

### 2. `AppointmentEntity` — Entité Rendez-vous

**Fichier :** `src/Entity/AppointmentEntity.php`
**Namespace :** `Drupal\appointment\Entity`
**Hérite de :** `ContentEntityBase`
**Table SQL :** `appointment`

Représente un rendez-vous réservé avec toutes les informations du client et du conseiller.

| Méthode                  | Rôle                                                                                                                                                                                                                                                                                                              |
| ------------------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `baseFieldDefinitions()` | Définit 15 champs : `uid`, `agency` (réf. entité), `adviser_name`, `adviser_email`, `appointment_type` (réf. taxonomie), `start_time`/`end_time` (timestamp), `client_name`/`client_email`/`client_phone`, `notes`, `status` (booked/confirmed/cancelled), `access_token`, `created`, `changed`, `reminder_sent`. |

---

### 3. `AppointmentManagerService` — Logique métier

**Fichier :** `src/Service/AppointmentManagerService.php`
**Namespace :** `Drupal\appointment\Service`
**Service ID :** `appointment.manager`

Service central contenant la logique métier : chargement des options, vérification de disponibilité, sauvegarde des rendez-vous.

| Méthode                       | Rôle                                                                                                             |
| ----------------------------- | ---------------------------------------------------------------------------------------------------------------- |
| `__construct()`               | Injecte le gestionnaire d'entités, le formateur de dates et le service de temps.                                 |
| `getAgencyOptions()`          | Retourne les agences actives sous forme `[id => nom]` pour les menus déroulants.                                 |
| `getAdviserOptions()`         | Décode le JSON des conseillers d'une agence et retourne `[email => "Nom (email)"]`.                              |
| `getAppointmentTypeOptions()` | Retourne les termes du vocabulaire `appointment_type` sous forme `[tid => nom]`.                                 |
| `isSlotAvailable()`           | Vérifie qu'aucun autre rendez-vous actif ne chevauche le créneau demandé pour un conseiller donné.               |
| `saveAppointment()`           | Crée ou met à jour une entité `appointment` à partir d'un tableau de valeurs.                                    |
| `getAppointments()`           | Récupère les rendez-vous d'un utilisateur (par uid) ou d'un anonyme (par email), avec filtre optionnel par type. |
| `formatTime()`                | Formate un timestamp UNIX en chaîne `Y-m-d H:i`.                                                                 |

---

### 4. `EmailService` — Service d'envoi d'emails

**Fichier :** `src/Service/EmailService.php`
**Namespace :** `Drupal\appointment\Service`
**Service ID :** `appointment.email_service`

Gère l'envoi des emails transactionnels, soit de manière immédiate soit via la file d'attente Drupal.

| Méthode                   | Rôle                                                                                           |
| ------------------------- | ---------------------------------------------------------------------------------------------- |
| `__construct()`           | Injecte le gestionnaire de mail, le logger, le gestionnaire de langues et la factory de queue. |
| `sendAppointmentEmail()`  | Envoie immédiatement un email HTML au client via le système de mail Drupal (SMTP/MailHog).     |
| `queueAppointmentEmail()` | Ajoute un email à la file d'attente `appointment_email` pour traitement différé par le cron.   |

---

### 5. `AppointmentController` — Contrôleur public

**Fichier :** `src/Controller/AppointmentController.php`
**Namespace :** `Drupal\appointment\Controller`
**Hérite de :** `ControllerBase`

Gère les pages publiques : confirmation de rendez-vous, liste des rendez-vous, et API JSON.

| Méthode             | Rôle                                                                                                          |
| ------------------- | ------------------------------------------------------------------------------------------------------------- |
| `confirmation()`    | Affiche la page de confirmation avec les détails du rendez-vous et les liens modifier/annuler.                |
| `myAppointments()`  | Liste les rendez-vous de l'utilisateur courant (par uid ou email/téléphone en paramètre GET).                 |
| `bookedSlots()`     | Retourne en JSON les créneaux déjà réservés pour un conseiller et une plage de dates (API pour FullCalendar). |
| `getRequest()`      | Retourne l'objet Request Symfony courant.                                                                     |
| `loadTypeOptions()` | Charge les types de rendez-vous en options `[tid => nom]` avec une option « Tous les types ».                 |

---

### 6. `AppointmentAdminController` — Contrôleur d'administration

**Fichier :** `src/Controller/AppointmentAdminController.php`
**Namespace :** `Drupal\appointment\Controller`
**Hérite de :** `ControllerBase`

Gère les pages d'administration : tableau de bord, liste filtrée et téléchargement CSV.

| Méthode                | Rôle                                                                                                 |
| ---------------------- | ---------------------------------------------------------------------------------------------------- |
| `__construct()`        | Injecte le RequestStack pour accéder aux paramètres GET.                                             |
| `create()`             | Méthode factory pour l'injection de dépendances.                                                     |
| `dashboard()`          | Affiche le tableau de bord avec les statistiques (total, réservés, confirmés, annulés, aujourd'hui). |
| `listAppointments()`   | Affiche la liste paginée et filtrée de tous les rendez-vous avec lien d'export CSV.                  |
| `buildSelectOptions()` | Génère les balises `<option>` HTML avec l'attribut `selected` correct.                               |
| `downloadCsv()`        | Sert le fichier CSV généré par le batch et le supprime après téléchargement.                         |

---

### 7. `AppointmentBookForm` — Formulaire wizard de réservation

**Fichier :** `src/Form/AppointmentBookForm.php`
**Namespace :** `Drupal\appointment\Form`
**Hérite de :** `FormBase`
**Constante :** `TOTAL_STEPS = 6`

Formulaire multi-étapes pour créer ou modifier un rendez-vous. Utilise le TempStore pour conserver les données entre les étapes.

| Méthode                     | Rôle                                                                                                                                     |
| --------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------- |
| `getFormId()`               | Retourne `'appointment_book_form'`.                                                                                                      |
| `buildForm()`               | Construit le formulaire de l'étape courante : (1) agence, (2) type, (3) conseiller, (4) calendrier, (5) infos client, (6) récapitulatif. |
| `validateForm()`            | Valide l'étape 4 (format date/heure + disponibilité du créneau) et l'étape 5 (format téléphone).                                         |
| `submitForm()`              | Gère la navigation (précédent/suivant via TempStore) ; à l'étape 6, crée/modifie le rendez-vous et envoie l'email.                       |
| `getCurrentStep()`          | Lit le paramètre de route `{step}`, limité entre 1 et 6.                                                                                 |
| `getStoreKey()`             | Retourne la clé TempStore : `edit_{id}` en modification, `new_{uid}_{session}` en création.                                              |
| `stepUrl()`                 | Construit l'URL d'une étape donnée du wizard (création ou modification).                                                                 |
| `storeCurrentStepValues()`  | Fusionne les valeurs du formulaire courant dans le tableau de données TempStore.                                                         |
| `buildSummaryMarkup()`      | Génère le HTML `<ul>` récapitulatif de toutes les données pour l'étape de confirmation.                                                  |
| `seedDataFromAppointment()` | Extrait les valeurs d'un rendez-vous existant dans le tableau du wizard (mode édition).                                                  |
| `loadAgencyOptions()`       | Charge les agences actives en options de sélection.                                                                                      |
| `loadTypeOptions()`         | Charge les termes `appointment_type` en options de sélection.                                                                            |
| `loadAdviserOptions()`      | Parse le JSON des conseillers d'une agence en options `[email => label]`.                                                                |
| `loadAdviserName()`         | Extrait le nom du conseiller à partir de son email et de l'agence.                                                                       |
| `isSlotAvailable()`         | Vérifie l'absence de chevauchement avec d'autres rendez-vous actifs du même conseiller.                                                  |

---

### 8. `AppointmentCancelForm` — Formulaire d'annulation

**Fichier :** `src/Form/AppointmentCancelForm.php`
**Namespace :** `Drupal\appointment\Form`
**Hérite de :** `ConfirmFormBase`

Formulaire de confirmation d'annulation d'un rendez-vous.

| Méthode            | Rôle                                                                                           |
| ------------------ | ---------------------------------------------------------------------------------------------- |
| `getFormId()`      | Retourne `'appointment_cancel_form'`.                                                          |
| `getQuestion()`    | Retourne la question de confirmation avec l'ID du rendez-vous.                                 |
| `getCancelUrl()`   | Retourne l'URL de retour (page « Mes rendez-vous »).                                           |
| `getConfirmText()` | Retourne le texte du bouton de confirmation.                                                   |
| `buildForm()`      | Capture le rendez-vous depuis la route et construit le formulaire de confirmation.             |
| `submitForm()`     | Passe le statut à `cancelled`, envoie l'email d'annulation, redirige vers « Mes rendez-vous ». |

---

### 9. `AppointmentSettingsForm` — Formulaire de configuration

**Fichier :** `src/Form/AppointmentSettingsForm.php`
**Namespace :** `Drupal\appointment\Form`
**Hérite de :** `ConfigFormBase`

Formulaire de configuration du module accessible depuis `/admin/structure/appointment/settings`.

| Méthode                    | Rôle                                                                                                            |
| -------------------------- | --------------------------------------------------------------------------------------------------------------- |
| `getEditableConfigNames()` | Retourne `['appointment.settings']` (la config modifiable par ce formulaire).                                   |
| `getFormId()`              | Retourne `'appointment_settings_form'`.                                                                         |
| `buildForm()`              | Construit les champs : durée créneau, horaires, jours max, taille batch CSV, email notification, heures rappel. |
| `validateForm()`           | Valide le format `HH:MM` des champs d'horaires.                                                                 |
| `submitForm()`             | Sauvegarde toutes les valeurs dans la config `appointment.settings`.                                            |

---

### 10. `AppointmentExportForm` — Formulaire d'export CSV

**Fichier :** `src/Form/AppointmentExportForm.php`
**Namespace :** `Drupal\appointment\Form`
**Hérite de :** `FormBase`

Formulaire permettant d'exporter les rendez-vous en CSV avec filtres et traitement par lots (Batch API).

| Méthode           | Rôle                                                                                                |
| ----------------- | --------------------------------------------------------------------------------------------------- |
| `__construct()`   | Injecte le gestionnaire d'entités et le service de système de fichiers.                             |
| `create()`        | Méthode factory pour l'injection de dépendances.                                                    |
| `getFormId()`     | Retourne `'appointment_export_form'`.                                                               |
| `buildForm()`     | Construit le formulaire avec filtres de statut, agence et dates.                                    |
| `submitForm()`    | Requête les rendez-vous correspondants, découpe en lots, lance le Batch API pour la génération CSV. |
| `processBatch()`  | Opération batch : charge un lot de rendez-vous et écrit les lignes dans le fichier CSV.             |
| `batchFinished()` | Callback de fin de batch : stocke le chemin CSV en session et affiche le lien de téléchargement.    |

---

### 11. `AppointmentLookupForm` — Formulaire de recherche

**Fichier :** `src/Form/AppointmentLookupForm.php`
**Namespace :** `Drupal\appointment\Form`
**Hérite de :** `FormBase`

Formulaire permettant aux visiteurs anonymes de retrouver leurs rendez-vous par email ou téléphone.

| Méthode          | Rôle                                                                                               |
| ---------------- | -------------------------------------------------------------------------------------------------- |
| `getFormId()`    | Retourne `'appointment_lookup_form'`.                                                              |
| `buildForm()`    | Construit un formulaire avec un choix radio (email/téléphone) et le champ de saisie correspondant. |
| `validateForm()` | Vérifie que le champ sélectionné (email ou téléphone) n'est pas vide.                              |
| `submitForm()`   | Redirige vers `/appointment/my` avec l'email ou le téléphone en paramètre GET.                     |

---

### 12. `AppointmentEmailQueueWorker` — Worker de file d'attente

**Fichier :** `src/Plugin/QueueWorker/AppointmentEmailQueueWorker.php`
**Namespace :** `Drupal\appointment\Plugin\QueueWorker`
**Hérite de :** `QueueWorkerBase` (implémente `ContainerFactoryPluginInterface`)
**Queue ID :** `appointment_email` (temps cron : 30s)

Plugin qui traite les emails mis en file d'attente (notamment les rappels). Exécuté automatiquement par le cron Drupal.

| Méthode         | Rôle                                                                                                              |
| --------------- | ----------------------------------------------------------------------------------------------------------------- |
| `__construct()` | Injecte le service email, le gestionnaire d'entités et le logger.                                                 |
| `create()`      | Méthode factory pour l'injection de dépendances du plugin.                                                        |
| `processItem()` | Charge le rendez-vous par ID depuis les données de la queue, puis appelle `EmailService::sendAppointmentEmail()`. |

---

## Prérequis

- **Drupal 10**
- **PHP 8.1+**
- Modules core : `user`, `options`, `taxonomy`, `datetime`
- Module contrib : `smtp` (pour les emails via MailHog/SMTP)
- **Docker** (optionnel, pour MailHog)

---

## Installation

```bash
# Activer le module
drush en appointment -y

# Exécuter les mises à jour de base de données
drush updb -y

# Vider le cache
drush cr
```

---

## Commandes Drush utiles

```bash
# Vider le cache (après toute modification de code)
drush cr

# Exécuter les mises à jour de BDD en attente
drush updb -y

# Voir les logs récents
drush watchdog:show --count=20

# Lancer le cron (pour traiter les rappels)
drush cron

# Vérifier la configuration
drush cget appointment.settings --format=yaml

# Vérifier si une table existe
drush sqlq "SHOW TABLES LIKE 'agency';"
```

---

## Dépannage

| Problème                                  | Solution                                                                                                    |
| ----------------------------------------- | ----------------------------------------------------------------------------------------------------------- |
| **500 sur `/appointment/book`**           | `drush updb -y && drush cr` — les tables sont peut-être manquantes.                                         |
| **Menu déroulant des agences vide**       | `drush updb -y` — le hook de seed data va créer les agences.                                                |
| **"Class does not exist" sur `drush cr`** | Vérifier `appointment.routing.yml` — utiliser `\Drupal\...` (simple backslash).                             |
| **Modifications non visibles**            | Toujours exécuter `drush cr` après modification de fichiers PHP ou YAML.                                    |
| **Emails non reçus**                      | Vérifier que `system.mail.interface.default` = `SMTPMailSystem` et que MailHog tourne sur `localhost:1025`. |
| **Rappels non envoyés**                   | Lancer `drush cron` et vérifier `reminder_hours_before` dans la config.                                     |
