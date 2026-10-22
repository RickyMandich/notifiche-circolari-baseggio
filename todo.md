# TODO

## Fatto (implementationPlan-V-01-partenzaProgetto)
- [x] Migrazioni e modelli `subscribers` / `circulars` (+ factory)
- [x] Login a sessione, rotte `/iscritti` protette da `auth`, CRUD iscritti (index/store/destroy)
- [x] Comando `users:create` per creare gli utenti che possono accedere
- [x] `WordpressApiService` (API WP, titolo ripulito, `date_gmt` → UTC)
- [x] Comando `circulars:check` + schedulazione ogni 30 minuti
- [x] Mailable `NewCircularNotification` (in coda) e config Resend/WordPress in `config/services.php` e `.env.example`
- [x] Test Pest (iscritti/auth, rilevamento circolari, mail); `RefreshDatabase` attivo sui Feature test
- [x] README riscritto come documentazione di progetto

## Da fare / verificare
- [ ] Eseguire `composer install`, `php artisan migrate` e `php artisan test` in locale (i test non sono ancora stati eseguiti)
- [ ] Verificare che l'endpoint `/wp-json/wp/v2/posts` del sito contenga davvero solo/le circolari (altrimenti filtrare per categoria con `categories=<id>`)
- [ ] Configurare in produzione `MAIL_MAILER=resend`, `RESEND_API_KEY` e un mittente con dominio verificato
- [ ] Eseguire migrazioni e `users:create` sull'ambiente Docker
- [ ] (Opzionale) Link di disiscrizione nelle email
