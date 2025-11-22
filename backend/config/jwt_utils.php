<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function generate_jwt($user_id, $rol) {
    $secret_key = 'tu_clave_secreta_super_segura'; // ¡Cámbiala y guárdala de forma segura!
    $issuer_claim = "http://localhost/Api_Ruti"; // El emisor del token
    $audience_claim = "http://localhost/Api_Ruti"; // La audiencia del token
    $issuedat_claim = time(); // Hora de emisión
    $notbefore_claim = $issuedat_claim; // Token no válido antes de este tiempo
    $expire_claim = $issuedat_claim + 3600; // Expira en 1 hora

    $payload = array(
        "iss" => $issuer_claim,
        "aud" => $audience_claim,
        "iat" => $issuedat_claim,
        "nbf" => $notbefore_claim,
        "exp" => $expire_claim,
        "data" => array(
            "id" => $user_id,
            "rol" => $rol
        )
    );

    return JWT::encode($payload, $secret_key, 'HS256');
}

function validate_jwt($token) {
    try {
        $secret_key = 'tu_clave_secreta_super_segura'; // Usa la misma clave secreta
        $decoded = JWT::decode($token, new Key($secret_key, 'HS256'));
        return (array) $decoded->data;
    } catch (Exception $e) {
        return null;
    }
}
?>