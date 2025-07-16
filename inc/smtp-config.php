<?php
/**
 * Configuration SMTP pour l'envoi d'emails
 *
 * @package Le Margo
 */

// Vérifier que ce fichier est bien inclus
if (!defined('ABSPATH')) {
    die('Accès direct interdit');
}

// Log que le fichier de configuration SMTP est chargé
error_log('Configuration SMTP Le Margo chargée');

// Configuration SMTP OVH
define('SMTP_HOST', 'ssl0.ovh.net');
define('SMTP_PORT', 465);
define('SMTP_SECURE', 'ssl');

// Authentification SMTP
define('SMTP_USER', 'contact@lemargo.fr');
define('SMTP_PASS', 'Bdada24500+');

// Configuration des emails
define('RESERVATION_FROM_EMAIL', 'contact@lemargo.fr');
define('RESERVATION_FROM_NAME', 'Le Margo');

// Configuration de débogage
define('SMTP_DEBUG', true);
define('SMTP_DEBUG_OUTPUT', 'error_log');

// Paramètres supplémentaires pour améliorer la fiabilité
define('SMTP_TIMEOUT', 30); // Timeout en secondes
define('SMTP_KEEP_ALIVE', true); // Garder la connexion active
define('SMTP_VERIFY_PEER', false); // Désactiver la vérification SSL en développement
define('SMTP_VERIFY_PEER_NAME', false); // Désactiver la vérification du nom d'hôte en développement

// Log de confirmation de la configuration
error_log('Configuration SMTP Le Margo : Host=' . SMTP_HOST . ', Port=' . SMTP_PORT . ', User=' . SMTP_USER); 