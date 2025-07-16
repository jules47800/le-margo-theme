# Le Margo - Thème WordPress

Un thème WordPress personnalisé pour le restaurant Le Margo. Ce thème a été développé pour mettre en valeur l'ambiance et les délices culinaires du restaurant.

## Caractéristiques

- Design élégant et responsive
- Types de contenu personnalisés pour le menu et les témoignages
- Modèle de page d'accueil avec sections personnalisables
- Support complet des widgets
- Options de personnalisation étendues

## Installation

1. Téléchargez le dossier du thème dans le répertoire `wp-content/themes/` de votre installation WordPress.
2. Activez le thème depuis le tableau de bord WordPress (Apparence > Thèmes).
3. Configurez les menus, widgets et autres options selon vos besoins.

## Personnalisation

### Menu principal

Le thème comprend deux emplacements de menu :
- Menu Principal (menu-principal) : Affiché dans l'en-tête du site
- Menu Pied de page (menu-footer) : Affiché dans le pied de page

### Types de contenu personnalisés

Le thème inclut deux types de contenu personnalisés :
- Plats (menu_item) : Pour présenter les plats du restaurant
- Témoignages (testimonial) : Pour afficher les avis des clients

### Page d'accueil

La page d'accueil peut être personnalisée en créant une page statique et en la définissant comme page d'accueil dans les réglages de lecture. Le modèle `front-page.php` sera alors utilisé pour l'affichage.

## Structure des fichiers

```
le-margo/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── inc/
│   ├── template-functions.php
│   └── template-tags.php
├── template-parts/
│   ├── content.php
│   ├── content-none.php
│   ├── content-menu_item.php
│   └── content-testimonial.php
├── functions.php
├── header.php
├── footer.php
├── index.php
├── sidebar.php
├── front-page.php
├── style.css
└── README.md
```

## Développement

Ce thème a été développé en suivant les meilleures pratiques de WordPress pour assurer la compatibilité avec les futures versions.

## Licence

Ce thème est sous licence GNU General Public License v2 ou ultérieure. 