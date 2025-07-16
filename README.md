# Thème WordPress "Le Margo" - Un Système de Gestion Complet pour Restaurant

Ce dépôt contient le thème WordPress sur-mesure pour le restaurant "Le Margo". Bien plus qu'une simple vitrine, ce thème intègre un système de gestion de réservations complet, des outils d'administration avancés et des optimisations SEO poussées.

## Fonctionnalités Clés

- **Système de Réservation Avancé** :
    - Formulaire de réservation sur-mesure avec validation en temps réel.
    - Vérification de la disponibilité basée sur les horaires et la capacité.
    - Base de données personnalisée (`wp_reservations`) pour une gestion optimale.
    - Administration complète des réservations (ajout, modification, statut).
    - Envoi d'emails de confirmation (au client et à l'admin) et de rappels automatiques.
- **Gestion des Contenus du Restaurant** :
    - **Menus du Jour** (`daily_menu`) : Pour publier facilement les menus quotidiens ou hebdomadaires.
    - **Témoignages** (`testimonial`) : Pour mettre en avant les avis des clients.
- **Administration Améliorée** :
    - **Dashboard Personnalisé** : Widgets pour visualiser les réservations à venir et les statistiques clients.
    - **Gestion des Clients** : Page dédiée pour suivre l'historique et les informations des clients.
    - **Statistiques Avancées** : Analyse de la fréquentation, des revenus estimés, etc.
    - **Configuration Facile** : Panneau pour gérer les horaires d'ouverture et le mode maintenance.
- **Optimisation SEO & Performance** :
    - **Données Structurées (Schema.org)** : Balisage pour `LocalBusiness`, `Restaurant`, `Event` pour une meilleure visibilité sur Google.
    - **Meta-tags Sociaux** : Optimisation du partage sur les réseaux sociaux (Open Graph).
    - **Optimisation des Images** : Prise en charge du format WebP et compression à la volée.
    - **Sitemap Dynamique**.
- **Design & UX** :
    - Design 100% responsive.
    - Pages personnalisées (Galerie, Eymet, À Propos).
    - Navigation et expérience utilisateur soignées.

## Installation et Configuration

1.  **Prérequis** : Ce thème ne nécessite aucun plugin externe pour ses fonctionnalités principales. Il est compatible avec WordPress 6.0+ et PHP 7.4+.
2.  **Téléchargement** : Clonez ou téléchargez ce dépôt.
3.  **Installation** : Placez le dossier du thème `le-margo` dans le répertoire `wp-content/themes/` de votre installation WordPress.
4.  **Activation** : Activez le thème depuis le tableau de bord WordPress (Apparence > Thèmes).
5.  **Création de la table de réservation** : Le thème est conçu pour créer et mettre à jour automatiquement la table `wp_reservations` dans la base de données lors de son activation. Aucune action manuelle n'est requise.
6.  **Configuration des Pages** :
    - Créez une page et assignez-lui le modèle "Page d'accueil" pour afficher le contenu de `front-page.php`.
    - Créez les pages "Réserver", "Merci", "Galerie", etc., et assurez-vous que leurs slugs correspondent à ceux utilisés dans le code (ex: `/reserver`, `/merci`).
7.  **Configuration des Horaires** : Allez dans `Apparence > Horaires d'ouverture` pour définir les heures de service. Ces informations sont cruciales pour le système de réservation.

## Structure des Fichiers

La structure ci-dessous met en évidence les fichiers les plus importants du thème.

```
le-margo/
├── assets/                 # Fichiers CSS, JS, et images
│   ├── css/
│   └── js/
├── inc/                    # Logique principale du thème
│   ├── class-le-margo-reservation-manager.php  # Cœur du système de réservation
│   ├── reservations-admin.php  # Interface d'admin pour les réservations
│   ├── customer-admin-page.php # Page de gestion des clients
│   ├── advanced-stats-page.php # Page de statistiques
│   ├── post-types.php        # Déclaration des CPTs (Menus, Témoignages)
│   ├── enqueue.php           # Chargement des styles et scripts
│   ├── core-setup.php        # Configuration initiale du thème
│   └── ...                   # Autres fichiers fonctionnels
├── template-parts/         # Morceaux de templates réutilisables
│   └── reservation-form.php  # Le formulaire de réservation
├── functions.php           # Fichier principal d'inclusion
├── front-page.php          # Template de la page d'accueil
├── page-reserver.php       # Template pour la page de réservation
├── style.css               # Déclaration du thème et styles principaux
└── README.md
```

## Licence

Ce thème est sous licence GNU General Public License v2 ou ultérieure. 

## Documentation complémentaire

- [Guide SEO détaillé](SEO-OPTIMIZATION-GUIDE.md)
- [Checklist référencement](GUIDE-REFERENCEMENT-SEO.md)
- [Mapping des images](IMAGES-MAPPING.md)
- [Guide des témoignages](README-TEMOIGNAGES.md)
