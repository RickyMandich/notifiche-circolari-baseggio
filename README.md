# Notifiche Circolari Baseggio

Applicazione Laravel 12 che controlla periodicamente il sito dell'Istituto Comprensivo di Baseggio
(<https://www.icbaseggio.edu.it>), rileva le nuove circolari tramite le WordPress REST API e invia
una email di notifica a tutti gli iscritti tramite [Resend](https://resend.com).
Gli iscritti si gestiscono da una piccola interfaccia web protetta da login.

## Come funziona

```
scheduler (ogni 30 min)
   └─ php artisan circulars:check
        ├─ WordpressApiService  → GET {WORDPRESS_POSTS_URL}  (ultimi N post)
        ├─ confronto wp_post_id con la tabella `circulars`
        ├─ salva le circolari nuove
        └─ per ogni nuova circolare e ogni iscritto accoda NewCircularNotification
                                   └─ worker di coda → mailer Resend
```

Note importanti:

- **Prima esecuzione**: se la tabella `circulars` è vuota, il comando salva le circolari attuali
  *senza* inviare email (evita di mandare agli iscritti tutto l'arretrato). Le notifiche partono
  dalla seconda esecuzione in poi.
- Ogni iscritto riceve una mail separata (gli indirizzi non sono visibili agli altri).
- Se il sito WordPress non risponde il comando termina con errore e non modifica il database;
  riprova al giro successivo.

## Struttura del codice

| Percorso | Ruolo |
|---|---|
| `app/Services/WordpressApiService.php` | Chiama le API WP e normalizza i post (titolo ripulito da HTML/entità, data da `date_gmt`) |
| `app/Console/Commands/CheckNewCirculars.php` | Comando `circulars:check` (logica di rilevamento e invio) |
| `app/Console/Commands/CreateAdminUser.php` | Comando `users:create {name} {email}` per creare chi può accedere |
| `app/Mail/NewCircularNotification.php` | Mailable in coda; vista in `resources/views/emails/new-circular.blade.php` |
| `app/Http/Controllers/SubscriberController.php` | Elenco / aggiunta / eliminazione iscritti |
| `app/Http/Controllers/Auth/LoginController.php` | Login e logout a sessione |
| `app/Models/Subscriber.php`, `Circular.php` | Modelli (`subscribers`, `circulars`) |
| `routes/web.php` | Rotte; tutto sotto `/iscritti` richiede `auth` |
| `routes/console.php` | Schedulazione di `circulars:check` (ogni 30 minuti, senza sovrapposizioni) |
| `config/services.php` | Sezioni `resend` e `wordpress` |
| `tests/Feature/` | Test Pest (iscritti/auth, rilevamento circolari, mail) |

## Setup locale

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install && npm run build   # opzionale, solo per gli stili Tailwind
php artisan users:create "Nome Cognome" tu@example.com   # chiede la password
php artisan serve
```

Poi apri `http://localhost:8000`, accedi e aggiungi gli iscritti.

Per provare il flusso a mano:

```bash
php artisan circulars:check      # 1ª volta: salva lo stato attuale, nessuna mail
php artisan circulars:check      # dalle successive notifica le nuove circolari
php artisan queue:work           # elabora le mail in coda
```

Con `MAIL_MAILER=log` (default in `.env.example`) le mail finiscono in `storage/logs/laravel.log`.

## Configurazione (`.env`)

| Variabile | Descrizione |
|---|---|
| `MAIL_MAILER` | `log` in sviluppo, `resend` in produzione |
| `RESEND_API_KEY` | Chiave API Resend |
| `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` | Mittente; in produzione deve appartenere a un dominio verificato su Resend |
| `WORDPRESS_POSTS_URL` | Endpoint dei post (default: `https://www.icbaseggio.edu.it/wp-json/wp/v2/posts`) |
| `WORDPRESS_POSTS_PER_PAGE` | Quanti post leggere a ogni controllo (default 20) |
| `QUEUE_CONNECTION` | `database` (le mail sono accodate, serve un worker) |

## Test

```bash
php artisan test
```

I test usano SQLite in memoria, `Http::fake()` per WordPress e `Mail::fake()` per le email:
non servono rete né chiavi Resend.

## Deploy (Docker)

`docker-compose.yml` avvia `app`, `nginx` (dietro Traefik), `db` (MariaDB) e `worker`.
Il container `worker` esegue via supervisor sia `queue:work` sia `schedule:work`, quindi
scheduler e invio mail sono già coperti. Su un nuovo ambiente:

```bash
docker compose exec app php artisan migrate --force
docker compose exec app php artisan users:create "Nome Cognome" tu@example.com
```

Ricorda di impostare `MAIL_MAILER=resend` e `RESEND_API_KEY` nel `.env` montato nei container.

## Convenzioni di sviluppo

- Non modificare `vendor/` né `node_modules/`.
- Le modifiche non banali partono da un `implementationPlan-{modifica}.md` nella root,
  rinominato `implementationPlan-V-{modifica}.md` a lavoro concluso.
- Tenere aggiornati `todo.md` e questo README a ogni modifica.
