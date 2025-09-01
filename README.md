## Thème WordPress « Le Margo » — Système complet pour restaurant (réservations incluses)

Thème sur‑mesure pour le restaurant Le Margo, incluant un système de réservation robuste, un back‑office dédié, des outils SEO et une UX soignée.

## Fonctionnalités

- **Réservations avancées**
  - Vérification de disponibilité par créneau et capacité configurable
  - Règles intégrées: fermeture hebdomadaire, vacances, délai minimum 1h pour le public
  - Emails: confirmation, annulation, rappel (opt‑in), journalisation des envois
  - Admin rapide: ajout de réservation sans contrainte de capacité (surbooking possible)
- **Contenus**
  - CPT `daily_menu` (Menus) et `testimonial` (Témoignages) avec archives dédiées
- **Back‑office**
  - Pages admin: Réservations, Paramètres, Clients, Statistiques avancées, Page d’accueil (galerie), Horaires, Maintenance
  - Widgets de tableau de bord: couverts du jour, en attente, prévision 7 jours
- **SEO**
  - Schema.org: Restaurant, LocalBusiness, BlogPosting, Menu, Breadcrumb
  - Meta dynamiques et auto‑configurées, sitemap fourni
- **Performance & images**
  - WebP autorisé, compression d’images 75, enqueues ciblés, cache‑busting via `LE_MARGO_VERSION`

## Prérequis

- WordPress ≥ 6.x
- PHP ≥ 7.4

## Installation

1) Copier `le-margo` dans `wp-content/themes/` puis activer le thème (Apparence > Thèmes).
2) Les tables nécessaires sont créées automatiquement à l’activation.
3) Créer les pages publiques et slugs attendus:
   - `front-page.php` → Page d’accueil
   - `page-reserver.php` → `/reserver`
   - `page-merci.php` → `/merci`
   - `page-galerie.php`, `page-eymet.php`, `page-menus.php` si utilisés
4) Configurer:
   - Réservations: `Réservations > Paramètres` (capacité, rappels, vacances, planning quotidien)
   - Horaires affichés: `Apparence > Horaires d’ouverture`
   - Page d’accueil (galerie): `Apparence > Page d’accueil`
   - Mode maintenance: `Réglages > Maintenance`

## Structure

```
le-margo/
├─ assets/
│  ├─ css/ (main.css, admin.css, reservation.css, animations.css, ...)
│  ├─ js/  (reservation.js, admin-*.js, gallery-*.js, swiper-init.js, ...)
│  └─ images/
├─ inc/
│  ├─ class-le-margo-reservation-manager.php
│  ├─ reservations-admin.php
│  ├─ reservations-core.php
│  ├─ class-le-margo-email-manager.php
│  ├─ opening-hours-admin.php
│  ├─ customer-admin-page.php, customer-stats.php
│  ├─ dashboard-*.php
│  ├─ post-types.php, enqueue.php, schema-markup.php
│  └─ ...
├─ template-parts/ (reservation-form.php, cancel-reservation.php, ...)
├─ front-page.php, functions.php, style.css, header.php, footer.php, ...
└─ README.md
```

## Base de données

Créées/maintenues automatiquement via `inc/reservations-core.php` et hooks d’activation:

- `wp_reservations`
  - `reservation_date` (date), `reservation_time` (time), `people` (int)
  - `customer_name`, `customer_email` (nullable), `customer_phone` (nullable)
  - `status` (pending|confirmed|cancelled|no-show|completed), `source` (public|admin)
  - `confirmation_email_sent`, `reminder_sent`, consentements et métadonnées
- `wp_le_margo_rate_limits` (anti‑abus sur IP)
- `wp_customer_stats` (visites, VIP, consentements, dernière réservation)

Migrations complémentaires: ajout/ajustement de colonnes si manquantes (voir `le_margo_update_db_check`, `le_margo_update_reservations_table`).

## Réservations — logique métier

- Disponibilité: `Le_Margo_Reservation_Manager::check_availability($date, $time, $people, $source)`
  - Respecte `le_margo_daily_schedule`, `le_margo_holiday_dates`, capacité par créneau
  - Délai min 1h pour le public; non appliqué pour l’admin
- Création: `create_reservation($data)` puis email de confirmation si adresse fournie
- Admin actions: confirmer, annuler, no‑show, supprimer, renvoyer confirmation, envoyer rappel
- Rappels planifiés: événements cron `le_margo_daily_reminder_event` (18:00) et `le_margo_send_reminders` (10:00)

## Paramètres clés (Options)

- `le_margo_restaurant_capacity` (int, par créneau)
- `le_margo_reminder_time` (minutes avant la réservation)
- `le_margo_table_hold_time` (minutes de rétention)
- `le_margo_holiday_dates` (liste `YYYY-MM-DD`)
- `le_margo_daily_schedule` (jours ouverts, plages horaires multiples, intervalle 15/30/45/60)
- `le_margo_opening_hours` (horaires affichés par jour, texte libre)

## Endpoints et AJAX

- AJAX dispo: `le_margo_get_availability` (priv/nopriv) retourne créneaux et capacité
- Admin AJAX: `le_margo_confirm_reservation` (sécurisé par nonce et capability)
- Réécriture: `/annuler-reservation/{id}/{nonce}` → `pagename=annuler-reservation`

## Front & assets

- Enqueues front: `main.css`, `navigation.js`, `main.js`, Swiper (CDN), scripts galerie
- Page `/reserver`: Flatpickr (CDN), `reservation.css/js`, paramètres exposés via `le_margo_params`
- Admin: `admin.css/js`, `admin-reservations.js` + Flatpickr sur les pages concernées
- Police custom optionnelle via Customizer; cache‑busting avec `LE_MARGO_VERSION`

## SEO

- Schémas JSON‑LD: Restaurant, LocalBusiness, BlogPosting, Menu, Breadcrumb
- Meta automatiques et configuration SEO dynamique
- Sitemap inclus (`sitemap.xml`)

## Internationalisation

- Fichiers `.po/.mo` en `languages/` (fr, en, es)

## Développement

- PHP 7.4+ strict, validation/sécurisation systématique (nonce, capabilities, sanitization)
- Logs serveur utilisés pour le suivi (erreurs, disponibilité, envois)
- Pas d’étape de build front obligatoire (assets statiques déjà fournis)

## Licence

GNU GPL v2 ou ultérieure.

## Git & publication rapide (exemple)

Dans `wp-content/themes/le-margo/`:

```
git init
git branch -M main
git add .
git commit -m "Initial commit: Le Margo theme"
git remote add origin https://github.com/<votre-compte>/<votre-repo>.git
git push -u origin main
```

Avec GitHub CLI:

```
gh repo create <votre-repo> --source=. --public --push
```