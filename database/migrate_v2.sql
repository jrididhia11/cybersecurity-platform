-- ============================================================
-- Migration v2 — Plateforme Cybersécurité
-- Nouvelles tables : chatbot_history, n8n_events
-- Modification : colonne login_bonus_date dans users
-- ============================================================

USE cybersecurity_platform;

-- Table historique chatbot IA
CREATE TABLE IF NOT EXISTS chatbot_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role ENUM('user','assistant') NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_created (user_id, created_at)
);

-- Table événements n8n (file de déclenchement de workflows)
CREATE TABLE IF NOT EXISTS n8n_events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    event_type VARCHAR(100) NOT NULL COMMENT 'lab_completed, level_up, login_bonus, quiz_perfect, brute_force_alert',
    payload JSON NOT NULL,
    processed TINYINT(1) DEFAULT 0 COMMENT '0=pending, 1=processed by n8n',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_processed (processed),
    INDEX idx_event_type (event_type)
);

-- Ajout colonne bonus connexion quotidienne
ALTER TABLE users 
    ADD COLUMN IF NOT EXISTS login_bonus_date DATE DEFAULT NULL 
    COMMENT 'Date du dernier bonus XP de connexion quotidienne';

-- Ajout colonne username visible (affichage leaderboard)
ALTER TABLE users
    ADD COLUMN IF NOT EXISTS defense_pages_visited TEXT DEFAULT NULL
    COMMENT 'JSON array des pages Defense Center visitées pour XP';
