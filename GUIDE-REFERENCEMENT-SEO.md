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