# Changements - Suppression de l'ancien système de réservation

## Vue d'ensemble
Ce document décrit les modifications apportées pour supprimer l'ancien système de réservation basé sur la distinction déjeuner/dîner et simplifier les statistiques clients.

## Fichiers modifiés

### 1. `inc/customer-stats.php`
**Modifications :**
- Suppression des calculs séparés pour déjeuner/dîner dans les statistiques d'occupation
- Simplification de la requête SQL pour les statistiques par service (plus de distinction lunch/dinner)
- Suppression des variables `$capacity_lunch`, `$capacity_dinner`, `$total_slots_lunch`, `$total_slots_dinner`
- Simplification de la fonction `le_margo_calculate_occupancy_data()` pour ne calculer qu'un taux d'occupation global

**Avant :**
```php
// Stats par service (déjeuner vs dîner)
$service_stats = $wpdb->get_results(
    "SELECT 
        CASE 
            WHEN CAST(reservation_time AS TIME) < '18:00:00' THEN 'lunch'
            ELSE 'dinner'
        END as meal_type,
        COUNT(*) as reservation_count,
        SUM(people) as total_guests
    FROM $reservations_table 
    $where_clause
    GROUP BY meal_type"
);
```

**Après :**
```php
// Stats par service (général)
$service_stats = $wpdb->get_results(
    "SELECT 
        'general' as meal_type,
        COUNT(*) as reservation_count,
        SUM(people) as total_guests
    FROM $reservations_table 
    $where_clause"
);
```

### 2. `inc/advanced-stats-page.php`
**Modifications :**
- Suppression complète de la section "Déjeuner vs Dîner"
- Simplification du tableau d'occupation (plus de colonnes séparées pour déjeuner/dîner)
- Suppression des graphiques JavaScript qui distinguent déjeuner/dîner
- Simplification du graphique d'occupation pour n'afficher qu'un seul taux global

**Supprimé :**
- Section "Déjeuner vs Dîner" avec graphique en camembert
- Colonnes "Déjeuner" et "Dîner" dans le tableau d'occupation
- Graphiques JavaScript pour lunch/dinner

### 3. `inc/dashboard-widgets.php`
**Modifications :**
- Suppression de la colonne "Service" dans les widgets du tableau de bord
- Suppression de l'affichage du type de repas (déjeuner/dîner)
- Simplification des tableaux pour n'afficher que l'heure, le nombre de personnes et le statut

**Avant :**
```php
echo '<th>' . esc_html__('Service', 'le-margo') . '</th>';
// ...
$meal_type = $reservation->meal_type === 'lunch' ? __('Déjeuner', 'le-margo') : __('Dîner', 'le-margo');
echo '<td>' . esc_html($meal_type) . '</td>';
```

**Après :**
```php
// Colonne "Service" supprimée
// Affichage simplifié sans distinction de type de repas
```

### 4. `inc/class-le-margo-email-manager.php`
**Modifications :**
- Remplacement de l'affichage du type de repas par "Réservation" dans les emails

**Avant :**
```php
$reservation->meal_type === 'lunch' ? __('Déjeuner', 'le-margo') : __('Dîner', 'le-margo')
```

**Après :**
```php
__('Réservation', 'le-margo')
```

### 5. `inc/class-le-margo-utils.php`
**Déjà modifié :**
- Le champ `meal_type` était déjà défini comme 'general' dans la fonction `sanitize_reservation_data()`

## Fichiers supprimés

### 1. `test-api-availability.php`
- Fichier de test qui faisait référence à l'ancien système avec distinction lunch/dinner
- Contenait des tests pour les créneaux de déjeuner et dîner séparés

### 2. `test-availability-fix.php`
- Fichier de test qui affichait les anciens paramètres de configuration
- Faisait référence aux horaires déjeuner/dîner

## Impact des changements

### Avantages
1. **Simplification** : Interface plus claire sans distinction inutile entre services
2. **Cohérence** : Alignement avec le nouveau système d'horaires par jour
3. **Maintenance** : Moins de code à maintenir et moins de complexité
4. **Performance** : Requêtes SQL simplifiées

### Compatibilité
- Les données existantes dans la base de données ne sont pas affectées
- Le champ `meal_type` existe toujours mais n'est plus utilisé pour l'affichage
- Toutes les fonctionnalités de réservation continuent de fonctionner normalement

### Statistiques
- Les statistiques d'occupation sont maintenant globales par jour
- Les graphiques sont simplifiés et plus lisibles
- Les widgets du tableau de bord sont plus épurés

## Tests recommandés
1. Vérifier que les statistiques avancées s'affichent correctement
2. Tester les widgets du tableau de bord
3. Vérifier que les emails de confirmation sont envoyés correctement
4. Tester l'affichage des réservations dans l'interface d'administration

## Notes importantes
- Ces changements sont purement cosmétiques et n'affectent pas la logique de réservation
- Le nouveau système d'horaires par jour reste inchangé
- Toutes les fonctionnalités de gestion des réservations continuent de fonctionner 