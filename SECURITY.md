# Politique de Sécurité — Plateforme Cybersécurité

## Versions Supportées

| Version | Support |
|---------|---------|
| v2.x    | ✅ Supportée |
| v1.x    | ⚠️ Correctifs critiques uniquement |

---

## Signalement d'une Vulnérabilité

Si tu découvres une vulnérabilité de sécurité dans ce projet, merci de **ne pas créer une issue publique GitHub**.

### Comment signaler

Envoie un email à l'auteur du projet avec :

1. **Description** de la vulnérabilité
2. **Étapes de reproduction** détaillées
3. **Impact potentiel** estimé
4. **Suggestion de correction** si possible

### Délai de réponse

- Accusé de réception : sous **48 heures**
- Évaluation de la vulnérabilité : sous **7 jours**
- Correction et publication : sous **30 jours** selon sévérité

---

## Mécanismes de Sécurité Implémentés

### Protection contre les injections SQL
Toutes les requêtes utilisent des **prepared statements** avec paramètres liés. Aucune concaténation directe de données utilisateur dans les requêtes SQL.

### Hashage des mots de passe
Les mots de passe sont hashés avec **bcrypt** (`PASSWORD_BCRYPT`, cost factor 12). Jamais stockés en clair, jamais hashés avec MD5 ou SHA-1.

### Protection CSRF
Chaque formulaire intègre un **token CSRF cryptographique** généré par `bin2hex(random_bytes(32))`. Validé via `hash_equals()` pour résistance aux timing attacks.

### Anti-Brute Force
**Blocage automatique** après 5 tentatives de connexion échouées depuis la même IP en moins de 10 minutes.

### Gestion des Sessions
- **Timeout** automatique après 30 minutes d'inactivité
- **Fingerprint** navigateur (SHA-256 du User-Agent) pour détecter le vol de cookie
- Sessions régénérées après authentification réussie

### En-têtes de Sécurité HTTP
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Referrer-Policy: no-referrer
```

---

## Périmètre

Ce projet est une **plateforme éducative** destinée à l'apprentissage de la cybersécurité. Les laboratoires simulent des attaques dans un environnement contrôlé. Tout usage malveillant ou non éthique est strictement interdit.

---

## Crédits

Les bonnes pratiques de sécurité appliquées dans ce projet suivent les recommandations de :
- [OWASP Top 10](https://owasp.org/Top10/)
- [OWASP Cheat Sheet Series](https://cheatsheetseries.owasp.org)
- [PHP Security Best Practices](https://php.net/manual/en/security.php)
