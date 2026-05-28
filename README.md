# 🛡️ Plateforme Pédagogique de Sensibilisation à la Cybersécurité

> Projet de Fin d'Études — BTS Réseau et Sécurité Informatique  
> IMSET Gabès | 2025–2026 | Élaboré par **Dhia Eddin Jridi**

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql)](https://mysql.com)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap)](https://getbootstrap.com)
[![License](https://img.shields.io/badge/License-MIT-green)](LICENSE)

---

## 📌 Description

Plateforme web interactive dédiée à la sensibilisation à la cybersécurité, combinant :

- 🔬 **4 Laboratoires interactifs** avec vidéos démonstratives originales
- 🏆 **Système de gamification** (XP, niveaux Beginner → Master, leaderboard)
- 🛡️ **Defense Center** — 5 techniques de défense professionnelles
- 🤖 **Assistant pédagogique IA** (chatbot contextuel)
- ⚙️ **Automatisation n8n** — workflows pédagogiques et alertes
- 🔒 **Sécurité OWASP** — bcrypt, requêtes préparées, CSRF, anti-brute force

---

## 🚀 Installation (XAMPP local)

### Prérequis
- [XAMPP](https://www.apachefriends.org) (PHP 8.x + MySQL 8.x + Apache)
- Navigateur moderne (Chrome, Firefox, Edge)

### Étapes

```bash
# 1. Cloner le dépôt dans htdocs
git clone https://github.com/TON_USERNAME/cybersecurity-platform.git
# Ou extraire le ZIP dans :
# C:\xampp\htdocs\cybersecurity-platform\

# 2. Démarrer XAMPP (Apache + MySQL)

# 3. Créer la base de données
# Ouvrir phpMyAdmin → Importer :
database/cybersecurity.sql     # schéma principal
database/add_quizzes.sql       # questions quiz
database/migrate.sql           # migrations v1
database/migrate_v2.sql        # migrations v2 (chatbot, n8n)
```

### Configuration

Éditer `config/db.php` :
```php
$host = 'localhost';
$user = 'root';
$pass = '';          // Ton mot de passe MySQL
$db   = 'cybersecurity_platform';
```

*(Optionnel)* Pour activer le chatbot IA réel, créer `config/config.php` :
```php
<?php
define('LLM_API_KEY', 'ta-cle-api-ici');
```
Sans clé, le chatbot fonctionne en **mode démo** avec des réponses prédéfinies.

### Accès

```
http://localhost/cybersecurity-platform/
```

---

## 🗂️ Structure du Projet

```
cybersecurity-platform/
├── index.php               # Page d'accueil
├── login.php               # Authentification sécurisée
├── register.php            # Inscription
├── dashboard.php           # Tableau de bord apprenant
├── chatbot.php             # Assistant pédagogique IA
├── leaderboard.php         # Classement général
├── defense.php             # Defense Center (accueil)
├── profile.php             # Profil utilisateur
│
├── api/
│   ├── chatbot.php         # Endpoint API chatbot IA
│   └── stats.php           # Statistiques JSON
│
├── rooms/                  # Laboratoires interactifs
│   ├── intro-sql.php       # Introduction Lab SQL
│   ├── sql-injection.php   # Lab SQL Injection
│   ├── intro-xss.php       # Introduction Lab XSS
│   ├── xss-lab.php         # Lab XSS
│   ├── intro-phishing.php  # Introduction Lab Phishing
│   ├── phishing_simulator.php  # Lab Phishing
│   ├── intro-password.php  # Introduction Lab Mots de passe
│   └── password-strength.php   # Lab Mots de passe
│
├── defense/                # Defense Center (5 techniques)
│   ├── siem.php            # SIEM
│   ├── ids-ips.php         # IDS / IPS
│   ├── waf.php             # WAF
│   ├── mfa.php             # MFA
│   └── password-policy.php # Politique MDP
│
├── quizzes/
│   ├── quiz1.php           # Quiz interactif
│   └── result.php          # Résultats avec XP et n8n
│
├── admin/                  # Interface administrateur
│   ├── admin_dashboard.php
│   ├── manage_users.php
│   ├── add_quiz.php
│   ├── view_results.php
│   └── logs.php
│
├── includes/               # Composants PHP réutilisables
│   ├── auth.php            # Authentification + session + fingerprint
│   ├── db.php              # Connexion base de données
│   ├── xp_system.php       # Système XP, niveaux, gamification
│   ├── n8n.php             # Déclencheur d'événements n8n
│   ├── chatbot_widget.php  # Widget flottant chatbot
│   ├── csrf.php            # Protection CSRF
│   └── logger.php          # Audit et traçabilité
│
├── database/
│   ├── cybersecurity.sql   # Schéma principal
│   ├── add_quizzes.sql     # Questions quiz
│   ├── migrate.sql         # Migration v1
│   └── migrate_v2.sql      # Migration v2 (chatbot, n8n)
│
├── assets/
│   ├── css/                # Feuilles de style
│   └── js/                 # Scripts JavaScript
│
└── config/
    └── db.php              # Configuration base de données
```

---

## 🔒 Sécurité Applicative (OWASP)

| Mécanisme | Implémentation |
|-----------|---------------|
| Injection SQL | Requêtes préparées `mysqli::prepare` / `bind_param` |
| Hashage MDP | `password_hash($pass, PASSWORD_BCRYPT, ['cost'=>12])` |
| Anti-Brute Force | Table `failed_logins` — blocage après 5 tentatives / 10 min |
| Protection CSRF | `bin2hex(random_bytes(32))` + `hash_equals()` |
| Gestion Sessions | Timeout 30 min + fingerprint SHA-256 User-Agent |

---

## 🎮 Système de Gamification

| Titre | XP requis |
|-------|-----------|
| Beginner | 0 – 99 XP |
| Intermediate | 100 – 299 XP |
| Advanced | 300 – 599 XP |
| Expert | 600 – 999 XP |
| **Master** | **1000+ XP** |

---

## ⚙️ Automatisation n8n

Les événements déclencheurs implémentés :

| Événement | Action n8n |
|-----------|------------|
| `lab_completed` | Email récapitulatif à l'apprenant |
| `level_up` | Notification de passage de niveau |
| `quiz_perfect` | Email de félicitations |
| `login_bonus` | Confirmation bonus quotidien |
| `brute_force_alert` | Alerte à l'administrateur |

---

## 📸 Captures d'écran

> *(Ajouter tes captures dans `/assets/screenshots/` et les référencer ici)*

---

## 🌐 Mise en Ligne

- **Hébergement PHP/MySQL** : [Infinityfree.net](https://infinityfree.net) — domaine gratuit `.rf.gd`
- **Pages statiques** : [GitHub Pages](https://pages.github.com)

---

## 📚 Technologies

`PHP 8` `MySQL 8` `Apache` `Bootstrap 5` `JavaScript ES6` `n8n` `API LLM` `OWASP`

---

## 👤 Auteur

**Dhia Eddin Jridi** — BTS Réseau et Sécurité Informatique  
IMSET Gabès | Encadrant : M. Riadh Hrizi

---

## 📄 Licence

Ce projet est sous licence [MIT](LICENSE). Libre à utiliser et modifier avec attribution.
