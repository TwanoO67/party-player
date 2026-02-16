<?php
/**
 * YouTube API v3 Proxy
 *
 * Ce proxy sécurise l'accès à l'API YouTube en:
 * - Gardant la clé API côté serveur (non exposée au client)
 * - Ajoutant du rate limiting pour éviter les abus
 * - Gérant les erreurs de manière centralisée
 * - Évitant les problèmes de CORS et de référent HTTP
 */

// Charger la configuration
require_once __DIR__ . '/../config.php';

// Headers CORS pour permettre les requêtes AJAX
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Gérer les requêtes OPTIONS (preflight CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/**
 * Rate limiting simple (optionnel)
 * Limite à 100 requêtes par IP par heure
 */
function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $cacheFile = sys_get_temp_dir() . '/youtube_proxy_' . md5($ip) . '.cache';

    $now = time();
    $hourAgo = $now - 3600;

    // Lire les requêtes précédentes
    $requests = [];
    if (file_exists($cacheFile)) {
        $data = file_get_contents($cacheFile);
        $requests = json_decode($data, true) ?: [];
    }

    // Filtrer les requêtes de la dernière heure
    $requests = array_filter($requests, function($timestamp) use ($hourAgo) {
        return $timestamp > $hourAgo;
    });

    // Vérifier la limite
    if (count($requests) >= 100) {
        http_response_code(429);
        echo json_encode([
            'error' => [
                'code' => 429,
                'message' => 'Trop de requêtes. Veuillez réessayer plus tard.'
            ]
        ]);
        exit;
    }

    // Ajouter la requête actuelle
    $requests[] = $now;
    file_put_contents($cacheFile, json_encode($requests));
}

/**
 * Faire un appel à l'API YouTube avec cURL
 */
function callYouTubeAPI($endpoint, $params) {
    // Ajouter la clé API
    $params['key'] = YOUTUBE_V3_APIKEY;

    // Construire l'URL
    $baseUrl = 'https://www.googleapis.com/youtube/v3/';
    $url = $baseUrl . $endpoint . '?' . http_build_query($params);

    // Initialiser cURL
    $ch = curl_init();

    // Options cURL
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    // Envoyer le Referer pour correspondre aux restrictions de la clé API
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $referer = $scheme . '://' . $_SERVER['HTTP_HOST'] . '/';
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json'
    ]);

    // Exécuter la requête
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);

    curl_close($ch);

    // Gérer les erreurs
    if ($response === false || !empty($error)) {
        http_response_code(500);
        return json_encode([
            'error' => [
                'code' => 500,
                'message' => 'Erreur de connexion à l\'API YouTube',
                'details' => $error
            ]
        ]);
    }

    // Définir le code de statut HTTP
    http_response_code($httpCode);
    return $response;
}

/**
 * Valider les paramètres de requête
 */
function validateParams($endpoint, $params) {
    $errors = [];

    switch ($endpoint) {
        case 'search':
            // Paramètres requis: q (query), type, part
            if (empty($params['q'])) {
                $errors[] = 'Le paramètre "q" (query) est requis';
            }
            if (empty($params['part'])) {
                $errors[] = 'Le paramètre "part" est requis';
            }
            break;

        case 'videos':
            // Paramètres requis: id, part
            if (empty($params['id'])) {
                $errors[] = 'Le paramètre "id" est requis';
            }
            if (empty($params['part'])) {
                $errors[] = 'Le paramètre "part" est requis';
            }
            break;

        case 'playlistItems':
            // Paramètres requis: playlistId, part
            if (empty($params['playlistId'])) {
                $errors[] = 'Le paramètre "playlistId" est requis';
            }
            if (empty($params['part'])) {
                $errors[] = 'Le paramètre "part" est requis';
            }
            break;

        default:
            $errors[] = 'Endpoint non supporté: ' . $endpoint;
    }

    return $errors;
}

// Vérifier que la clé API est configurée
if (!defined('YOUTUBE_V3_APIKEY') || empty(YOUTUBE_V3_APIKEY)) {
    http_response_code(500);
    echo json_encode([
        'error' => [
            'code' => 500,
            'message' => 'Clé API YouTube non configurée dans config.php'
        ]
    ]);
    exit;
}

// Vérifier le rate limiting (optionnel, peut être désactivé)
// checkRateLimit();

// Récupérer l'endpoint demandé
$endpoint = $_GET['endpoint'] ?? '';

if (empty($endpoint)) {
    http_response_code(400);
    echo json_encode([
        'error' => [
            'code' => 400,
            'message' => 'Paramètre "endpoint" manquant. Exemples: search, videos, playlistItems'
        ]
    ]);
    exit;
}

// Récupérer tous les paramètres sauf 'endpoint' et 'key'
$params = $_GET;
unset($params['endpoint']);
unset($params['key']); // Sécurité: on ignore toute clé envoyée par le client

// Valider les paramètres
$validationErrors = validateParams($endpoint, $params);
if (!empty($validationErrors)) {
    http_response_code(400);
    echo json_encode([
        'error' => [
            'code' => 400,
            'message' => 'Paramètres invalides',
            'details' => $validationErrors
        ]
    ]);
    exit;
}

// Appeler l'API YouTube et retourner la réponse
echo callYouTubeAPI($endpoint, $params);
