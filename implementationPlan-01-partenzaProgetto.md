# Implementation Plan - Automazione Circolari & Gestione Iscritti (Laravel 12)

## Obiettivo
Implementare un sistema automatizzato in Laravel 12 che interroghi periodicamente le WordPress REST API del sito scolastico, rilevi nuove circolari e invii notifiche email tramite **Resend** agli utenti iscritti, gestiti attraverso una semplice interfaccia web protetta da autenticazione.

## Fasi di Implementazione

### Fase 1: Modelli e Database
- **Subscribers (`subscribers`)**:
  - Campi: `id`, `name`, `email`, `created_at`, `updated_at`.
- **Circolari Registrate (`circulars`)**:
  - Campi: `id`, `wp_post_id` (ID univoco post WP), `title`, `url`, `published_at`, `created_at`, `updated_at`.

### Fase 2: Autenticazione e Rotte Protette
- Configurare la protezione delle rotte amministrative per la gestione degli iscritti dietro un account/middleware di autenticazione (`auth`).
- Creare i Controller e le View/API per l'inserimento, visualizzazione ed eliminazione degli iscritti.

### Fase 3: Service Layer per WordPress API
- Sviluppare `WordpressApiService` per interrogare `https://www.icbaseggio.edu.it/wp-json/wp/v2/posts`.
- Mappare i dati delle circolari ricevute (ID WP, Titolo, URL, Data di pubblicazione).

### Fase 4: Task Schedulato e Rilevamento Nuove Circolari
- Creare un comando Artisan/Job schedulato (`CheckNewCircularsJob` o `circulars:check`).
- Confrontare gli ID del feed WordPress con la tabella `circulars`.
- Salvare le nuove circolari trovate nel DB per evitare l'invio duplicato.

### Fase 5: Integrazione Resend & Invio Email
- Configurare il client **Resend** per Laravel.
- Creare una classe Mailable (`NewCircularNotification`).
- Inviare la notifica a tutti gli iscritti registrati nella tabella `subscribers` quando viene rilevata una nuova circolare.

### Fase 6: Testing e Verifica
- Testare il flusso completo (fetch WP -> salvataggio DB -> invio mail Resend).
