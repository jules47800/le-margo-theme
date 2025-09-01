# 🚀 GUIDE DE RÉFÉRENCEMENT SEO - LE MARGO

## ✅ PROBLÈMES CORRIGÉS

### 1. Robots.txt optimisé
- ✅ Suppression du blocage des CSS/JS
- ✅ Autorisation des ressources essentielles
- ✅ URL du sitemap corrigée

### 2. Sitemap.xml mis à jour  
- ✅ URLs corrigées avec lemargo.fr
- ✅ Dates mises à jour (2025-06-07)
- ✅ Pages existantes uniquement

### 3. Google Analytics installé
- ✅ Code gtag.js ajouté dans header.php
- ⚠️ **REMPLACER "G-XXXXXXXXXX" par votre ID Analytics**

### 4. URL canonique corrigée
- ✅ Fonction WordPress wp_get_canonical_url() utilisée

---

## 🎯 ACTIONS OBLIGATOIRES À FAIRE

### 1. Google Analytics - ID à remplacer
```javascript
// Dans header.php, ligne ~32-39
// REMPLACER cette ligne :
gtag('config', 'G-XXXXXXXXXX');
// PAR votre vrai ID Google Analytics :
gtag('config', 'G-VOTRE-ID-REEL');
```

### 2. Google Search Console
1. **Aller sur** : https://search.google.com/search-console
2. **Ajouter lemargo.fr** comme propriété
3. **Valider la propriété** via Google Analytics ou fichier HTML
4. **Soumettre le sitemap** : https://lemargo.fr/sitemap.xml

### 3. Google My Business
1. **Créer/Revendiquer la fiche** : Le Margo Eymet
2. **Adresse complète** + horaires + téléphone
3. **Photos du restaurant** et des plats
4. **Catégorie** : Restaurant, Cuisine française

---

## 🔧 OPTIMISATIONS SEO RECOMMANDÉES

### Meta descriptions manquantes
Ajouter dans chaque page :
```php
// Page d'accueil
<meta name="description" content="Le Margo, restaurant gastronomique à Eymet (24500). Cuisine locale, produits du terroir et vins naturels. Réservez votre table au cœur de la Dordogne.">

// Page menus  
<meta name="description" content="Découvrez les menus du restaurant Le Margo à Eymet. Cuisine du terroir, produits locaux et carte des vins naturels. Menu du jour et formules.">

// Page à propos
<meta name="description" content="L'histoire du Margo, restaurant d'Antoine et Floriane à Eymet. Notre philosophie : cuisine locale, producteurs du terroir et vins naturels.">
```

### Schema.org - Données structurées
Ajouter dans le footer :
```json
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Restaurant",
  "name": "Le Margo",
  "image": "https://lemargo.fr/wp-content/themes/le-margo/assets/images/restaurant-exterieur.jpg",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Place Gambetta",
    "addressLocality": "Eymet",
    "postalCode": "24500",
    "addressCountry": "FR"
  },
  "telephone": "+33-X-XX-XX-XX-XX",
  "servesCuisine": "Française",
  "priceRange": "€€",
  "openingHours": ["Mo-Su 12:00-14:00", "Mo-Su 19:00-22:00"],
  "url": "https://lemargo.fr"
}
</script>
```

### Améliorations techniques
1. **Images** : Ajouter des ALT tags descriptifs
2. **Vitesse** : Optimiser/compresser les images
3. **HTTPS** : Vérifier que tout fonctionne en HTTPS
4. **Mobile** : Tester la responsivité

---

## 📈 SUIVI ET MONITORING

### Dans Google Analytics (après installation)
- **Audiences** : Visiteurs uniques/récurrents
- **Acquisition** : Sources de trafic (Google, direct, réseaux)
- **Comportement** : Pages les plus vues
- **Conversions** : Réservations (à configurer)

### Dans Google Search Console
- **Couverture** : Pages indexées/erreurs
- **Performance** : Requêtes, clics, impressions
- **Liens** : Liens entrants vers le site

---

## ⚡ ACTIONS RAPIDES POUR BOOSTER LE SEO

### 1. Contenu local
- ✅ Mentions "Eymet", "Dordogne", "Périgord"
- ✅ Producteurs locaux détaillés
- ➕ Ajouter blog : "Spécialités d'Eymet", "Vignerons locaux"

### 2. Réseaux sociaux
- **Facebook Business** : Le Margo Eymet
- **Instagram** : Photos plats/restaurant
- **Google Posts** : Actualités/événements

### 3. Backlinks locaux
- **Annuaires** : Yelp, TripAdvisor, LaFourchette
- **Partenaires** : Sites de vos producteurs
- **Presse locale** : Journal Sud Ouest, blogs gastronomiques

---

## 🎯 CHECKLIST DE VALIDATION

### Immédiat (aujourd'hui)
- [ ] Remplacer l'ID Google Analytics
- [ ] Créer compte Google Search Console
- [ ] Soumettre le sitemap
- [ ] Vérifier que le site est accessible

### Cette semaine
- [ ] Google My Business
- [ ] Profils réseaux sociaux
- [ ] Optimiser les images (compression + ALT)
- [ ] Ajouter schema.org

### Ce mois-ci
- [ ] Créer du contenu blog
- [ ] Demander des avis clients
- [ ] Partenariats avec sites locaux
- [ ] Monitoring mensuel des performances

---

## 📞 CONTACTS UTILES

- **Google Search Console** : https://search.google.com/search-console
- **Google Analytics** : https://analytics.google.com
- **Google My Business** : https://business.google.com
- **Test vitesse site** : https://pagespeed.web.dev
- **Test mobile** : https://search.google.com/test/mobile-friendly

---

*Guide créé le 7 juin 2025 - À mettre à jour selon les évolutions* 

# Guide des Meta Données SEO - Le Margo

## Page d'Accueil (front-page.php)
- **Meta Title**: "Le Margo | Bistrot Moderne à Eymet, Dordogne"
- **Meta Description**: "Le Margo, bistrot moderne à Eymet où cuisine du marché rime avec convivialité. Une carte qui évolue au fil des saisons, des produits locaux et une ambiance chaleureuse."

## Page À Propos (page-a-propos.php)
- **Meta Title**: "À Propos du Margo | Bistrot Moderne & Cuisine de Saison"
- **Meta Description**: "Découvrez l'histoire du Margo, bistrot moderne d'Eymet. Une cuisine sincère et généreuse, des producteurs locaux et une équipe passionnée vous accueillent en Dordogne."

## Page Menus (page-menus.php)
- **Meta Title**: "La Carte | Le Margo - Bistrot Moderne à Eymet"
- **Meta Description**: "Notre carte bistronomique : produits frais du marché, poissons du jour, viandes maturées. Une cuisine sincère qui respecte les saisons, dans une ambiance décontractée."

## Page Galerie (page-galerie.php)
- **Meta Title**: "En Images | Le Margo - Bistrot Moderne Eymet"
- **Meta Description**: "Découvrez l'ambiance du Margo en images : cuisine de bistrot revisitée, produits du marché, moments conviviaux. Un bistrot moderne qui cultive l'art de recevoir à Eymet."

## Page Réservation (page-reserver.php)
- **Meta Title**: "Réserver | Le Margo - Bistrot Moderne Eymet"
- **Meta Description**: "Réservez votre table au Margo, bistrot moderne d'Eymet. Du mardi au samedi soir, venez partager un moment convivial autour d'une cuisine de bistrot créative."

## Page Eymet (page-eymet.php)
- **Meta Title**: "Le Margo à Eymet | Bistrot Moderne en Dordogne"
- **Meta Description**: "Le Margo, votre bistrot moderne au cœur d'Eymet. Au 6 avenue du 6 juin 1944, savourez une cuisine de bistrot contemporaine dans un cadre chaleureux."

## Archives Menu du Jour (archive-daily_menu.php)
- **Meta Title**: "Le Menu du Jour | Bistrot Le Margo Eymet"
- **Meta Description**: "Notre ardoise du jour : une cuisine de bistrot généreuse qui suit le marché. Des plats fait maison qui changent chaque jour selon les arrivages et la saison."

## Archives Témoignages (archive-testimonial.php)
- **Meta Title**: "Avis Clients | Bistrot Le Margo à Eymet"
- **Meta Description**: "Les retours de nos clients sur leur expérience au Margo. Un bistrot moderne apprécié pour sa cuisine sincère et son ambiance conviviale au cœur d'Eymet."

## Pages Légales
### Politique de Confidentialité (page-politique-confidentialite.php)
- **Meta Title**: "Politique de Confidentialité | Le Margo Eymet"
- **Meta Description**: "Consultez notre politique de confidentialité. Le Margo s'engage à protéger vos données personnelles conformément à la réglementation en vigueur."

### Suppression des Données (page-suppression-donnees.php)
- **Meta Title**: "Suppression des Données | Le Margo Eymet"
- **Meta Description**: "Informations sur la suppression de vos données personnelles au Margo. Nous respectons votre droit à la vie privée."

## Page 404
- **Meta Title**: "Page Non Trouvée | Le Margo - Bistrot Moderne Eymet"
- **Meta Description**: "La page que vous recherchez n'existe plus. Découvrez notre carte bistronomique et nos suggestions du jour sur le site du Margo à Eymet."

---

### Notes d'Utilisation
- Ces meta données sont optimisées pour le référencement local et la bistronomie
- Chaque meta title est limité à 60 caractères maximum
- Chaque meta description est limitée à 155-160 caractères
- Les mots-clés principaux sont : bistrot moderne, cuisine de saison, Eymet, Dordogne, Le Margo
- L'accent est mis sur :
  - La cuisine de bistrot contemporaine
  - Les produits frais du marché
  - L'ambiance conviviale et décontractée
  - Le respect des saisons
  - La proximité avec les producteurs locaux 