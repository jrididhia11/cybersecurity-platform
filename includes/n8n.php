<?php
/**
 * n8n Integration — Trigger d'événements pour workflows automatisés
 * Chaque appel insert un event dans n8n_events.
 * n8n surveille cette table via un webhook/polling et exécute le workflow correspondant.
 */

if (!isset($conn)) {
    require_once __DIR__ . '/db.php';
}

/**
 * Déclenche un événement n8n.
 * @param int    $userId    ID de l'utilisateur concerné
 * @param string $eventType Type d'événement (lab_completed, level_up, quiz_perfect, login_bonus, brute_force_alert)
 * @param array  $payload   Données supplémentaires encodées en JSON
 */
function triggerN8nEvent(int $userId, string $eventType, array $payload): void
{
    global $conn;
    if (!$conn) return;

    // Récupérer email utilisateur pour le payload
    if (!isset($payload['user_email'])) {
        $stmt = $conn->prepare("SELECT email, fullname FROM users WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row) {
                $payload['user_email']    = $row['email'];
                $payload['user_fullname'] = $row['fullname'];
            }
        }
    }

    $payload['timestamp'] = date('Y-m-d H:i:s');
    $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);

    $stmt = $conn->prepare(
        "INSERT INTO n8n_events (user_id, event_type, payload, processed, created_at)
         VALUES (?, ?, ?, 0, NOW())"
    );
    if (!$stmt) return;
    $stmt->bind_param("iss", $userId, $eventType, $payloadJson);
    $stmt->execute();
}

/**
 * Déclenche une alerte brute force pour l'admin (pas besoin de user_id réel).
 */
function triggerBruteForceAlert(string $ip, string $emailTried): void
{
    global $conn;
    if (!$conn) return;

    $payload = json_encode([
        'ip'           => $ip,
        'email_tried'  => $emailTried,
        'timestamp'    => date('Y-m-d H:i:s'),
        'alert_type'   => 'brute_force'
    ]);

    $stmt = $conn->prepare(
        "INSERT INTO n8n_events (user_id, event_type, payload, processed, created_at)
         VALUES (0, 'brute_force_alert', ?, 0, NOW())"
    );
    if (!$stmt) return;
    $stmt->bind_param("s", $payload);
    $stmt->execute();
}
