<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    require_once __DIR__ . '/db.php';
}

// ─── Chargement XP depuis BDD ────────────────────────────────────────────────
function loadXPFromDB(): void
{
    global $conn;
    if (!isset($_SESSION['user_id'])) return;
    if (isset($_SESSION['xp_loaded'])) return;
    if (!$conn) return;

    $stmt = $conn->prepare(
        "SELECT xp, completed_labs, login_bonus_date, defense_pages_visited
         FROM users WHERE id = ?"
    );
    if (!$stmt) return;

    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    if ($row) {
        $_SESSION['xp']            = (int)$row['xp'];
        $_SESSION['completed_labs'] = $row['completed_labs']
            ? json_decode($row['completed_labs'], true) : [];
        $_SESSION['defense_visited'] = $row['defense_pages_visited']
            ? json_decode($row['defense_pages_visited'], true) : [];
        $_SESSION['login_bonus_date'] = $row['login_bonus_date'];
        $_SESSION['level']           = floor($_SESSION['xp'] / 100) + 1;
    }

    $_SESSION['xp_loaded'] = true;
}

// ─── Ajout XP ────────────────────────────────────────────────────────────────
function addXP(int $amount): void
{
    global $conn;
    if ($amount <= 0) return;

    $_SESSION['xp']    = ($_SESSION['xp'] ?? 0) + $amount;
    $prevLevel         = $_SESSION['level'] ?? 1;
    $_SESSION['level'] = floor($_SESSION['xp'] / 100) + 1;

    if (isset($_SESSION['user_id']) && $conn) {
        $levelName = getLevelName($_SESSION['level']);
        $stmt = $conn->prepare("UPDATE users SET xp = ?, level = ? WHERE id = ?");
        if (!$stmt) return;
        $stmt->bind_param("isi", $_SESSION['xp'], $levelName, $_SESSION['user_id']);
        $stmt->execute();

        // Déclencher événement n8n si passage de niveau
        if ($_SESSION['level'] > $prevLevel) {
            if (!function_exists('triggerN8nEvent')) {
                require_once __DIR__ . '/n8n.php';
            }
            triggerN8nEvent($_SESSION['user_id'], 'level_up', [
                'new_level'  => $_SESSION['level'],
                'new_title'  => getLevelName($_SESSION['level']),
                'total_xp'   => $_SESSION['xp'],
            ]);
        }
    }
}

// ─── Getters XP / Niveau ─────────────────────────────────────────────────────
function getXP(): int
{
    loadXPFromDB();
    return $_SESSION['xp'] ?? 0;
}

function getLevel(): int
{
    loadXPFromDB();
    return $_SESSION['level'] ?? 1;
}

/**
 * getLevelName — 5 niveaux progressifs incluant Master
 */
function getLevelName(int $level): string
{
    if ($level >= 11) return 'Master';
    if ($level >= 7)  return 'Expert';
    if ($level >= 4)  return 'Advanced';
    if ($level >= 2)  return 'Intermediate';
    return 'Beginner';
}

/**
 * getLevelTitle basé directement sur XP total (pour affichage badges)
 */
function getLevelTitle(int $xp): string
{
    if ($xp >= 1000) return 'Master';
    if ($xp >= 600)  return 'Expert';
    if ($xp >= 300)  return 'Advanced';
    if ($xp >= 100)  return 'Intermediate';
    return 'Beginner';
}

/**
 * Couleur badge par titre
 */
function getLevelColor(string $title): string
{
    return match($title) {
        'Master'       => '#dc2626',
        'Expert'       => '#ea580c',
        'Advanced'     => '#16a34a',
        'Intermediate' => '#0891b2',
        default        => '#6b7280',
    };
}

// ─── Complétion Lab ───────────────────────────────────────────────────────────
function completeLab(string $labName, int $xp): bool
{
    global $conn;
    loadXPFromDB();

    if (!isset($_SESSION['completed_labs'])) {
        $_SESSION['completed_labs'] = [];
    }

    if (in_array($labName, $_SESSION['completed_labs'])) {
        return false; // Déjà complété
    }

    $_SESSION['completed_labs'][] = $labName;
    addXP($xp);

    if (isset($_SESSION['user_id']) && $conn) {
        $labsJson = json_encode($_SESSION['completed_labs']);
        $stmt = $conn->prepare("UPDATE users SET completed_labs = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $labsJson, $_SESSION['user_id']);
            $stmt->execute();
        }

        // Événement n8n — email récapitulatif lab
        if (!function_exists('triggerN8nEvent')) {
            require_once __DIR__ . '/n8n.php';
        }
        triggerN8nEvent($_SESSION['user_id'], 'lab_completed', [
            'lab_name'   => $labName,
            'xp_earned'  => $xp,
            'total_xp'   => $_SESSION['xp'],
            'total_labs' => count($_SESSION['completed_labs']),
        ]);
    }

    return true;
}

function isLabCompleted(string $labName): bool
{
    loadXPFromDB();
    return in_array($labName, $_SESSION['completed_labs'] ?? []);
}

// ─── XP Defense Center (5 XP par page, une fois par jour) ────────────────────
function awardDefenseXP(string $pageName): bool
{
    global $conn;
    loadXPFromDB();

    $visited = $_SESSION['defense_visited'] ?? [];
    $key     = $pageName . '_' . date('Y-m-d');

    if (in_array($key, $visited)) {
        return false; // Déjà visité aujourd'hui
    }

    $visited[]                    = $key;
    $_SESSION['defense_visited']  = $visited;
    addXP(5);

    if (isset($_SESSION['user_id']) && $conn) {
        $json = json_encode($visited);
        $stmt = $conn->prepare(
            "UPDATE users SET defense_pages_visited = ? WHERE id = ?"
        );
        if ($stmt) {
            $stmt->bind_param("si", $json, $_SESSION['user_id']);
            $stmt->execute();
        }
    }
    return true;
}

// ─── Bonus connexion quotidienne (5 XP / jour) ───────────────────────────────
function awardDailyLoginBonus(): bool
{
    global $conn;
    loadXPFromDB();

    $today = date('Y-m-d');
    if (($_SESSION['login_bonus_date'] ?? '') === $today) {
        return false; // Déjà donné aujourd'hui
    }

    $_SESSION['login_bonus_date'] = $today;
    addXP(5);

    if (isset($_SESSION['user_id']) && $conn) {
        $stmt = $conn->prepare(
            "UPDATE users SET login_bonus_date = ? WHERE id = ?"
        );
        if ($stmt) {
            $stmt->bind_param("si", $today, $_SESSION['user_id']);
            $stmt->execute();
        }
    }
    return true;
}

// ─── XP Chatbot (3 XP après 3 échanges, une fois par session) ─────────────────
function awardChatbotXP(): bool
{
    if (isset($_SESSION['chatbot_xp_awarded'])) return false;
    $_SESSION['chatbot_xp_awarded'] = true;
    addXP(3);
    return true;
}

// ─── Préchargement automatique ────────────────────────────────────────────────
if (isset($_SESSION['user_id'])) {
    loadXPFromDB();
}
