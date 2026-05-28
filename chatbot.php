<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
require_once 'includes/xp_system.php';

header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");

$xp         = getXP();
$level      = getLevel();
$levelTitle = getLevelTitle($xp);
$levelColor = getLevelColor($levelTitle);
$userId     = (int)$_SESSION['user_id'];

// Récupérer les 20 derniers messages de l'historique
$history = [];
$stmt = $conn->prepare(
    "SELECT role, content, created_at FROM chatbot_history
     WHERE user_id = ? ORDER BY created_at DESC LIMIT 20"
);
if ($stmt) {
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $history = array_reverse($rows);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CyberBot — Assistant Pédagogique IA</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{background:#020617;color:white;font-family:'Inter',sans-serif;height:100vh;display:flex;flex-direction:column;}

/* ── Topbar ── */
.topbar{background:#0f172a;border-bottom:1px solid rgba(255,255,255,.06);padding:14px 24px;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.topbar .logo{font-size:18px;font-weight:800;color:white;display:flex;align-items:center;gap:10px;}
.topbar .logo span{color:#3b82f6;}
.back-btn{display:inline-flex;align-items:center;gap:8px;color:#64748b;text-decoration:none;font-size:13px;padding:8px 14px;border-radius:10px;border:1px solid rgba(255,255,255,.06);transition:.2s;}
.back-btn:hover{color:white;background:rgba(255,255,255,.05);}
.xp-badge{display:flex;align-items:center;gap:8px;background:rgba(59,130,246,.1);border:1px solid rgba(59,130,246,.2);padding:6px 14px;border-radius:20px;font-size:13px;}
.xp-badge .title{font-weight:700;color:#60a5fa;}

/* ── Layout ── */
.chat-layout{display:flex;flex:1;overflow:hidden;}

/* ── Sidebar ── */
.sidebar{width:280px;background:#0f172a;border-right:1px solid rgba(255,255,255,.05);padding:24px 18px;display:flex;flex-direction:column;gap:16px;overflow-y:auto;flex-shrink:0;}
.sidebar-title{font-size:11px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:1.2px;margin-bottom:4px;}
.topic-btn{display:flex;align-items:center;gap:10px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.06);border-radius:12px;padding:11px 14px;color:#94a3b8;font-size:13px;cursor:pointer;text-align:left;width:100%;transition:.2s;}
.topic-btn:hover{background:rgba(59,130,246,.1);border-color:rgba(59,130,246,.3);color:#93c5fd;}
.topic-btn i{width:18px;text-align:center;color:#3b82f6;}
.stats-card{background:#111827;border-radius:12px;padding:16px;border:1px solid rgba(255,255,255,.06);}
.stats-card .stat{display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid rgba(255,255,255,.04);font-size:13px;color:#64748b;}
.stats-card .stat:last-child{border-bottom:none;}
.stats-card .stat span{color:white;font-weight:600;}

/* ── Chat zone ── */
.chat-main{flex:1;display:flex;flex-direction:column;overflow:hidden;}
.messages-area{flex:1;overflow-y:auto;padding:28px 32px;display:flex;flex-direction:column;gap:16px;}
.messages-area::-webkit-scrollbar{width:6px;}
.messages-area::-webkit-scrollbar-track{background:transparent;}
.messages-area::-webkit-scrollbar-thumb{background:#1e293b;border-radius:4px;}

/* ── Messages ── */
.msg{display:flex;gap:12px;max-width:800px;}
.msg.user{align-self:flex-end;flex-direction:row-reverse;}
.msg.assistant{align-self:flex-start;}
.avatar{width:36px;height:36px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0;}
.avatar.bot{background:linear-gradient(135deg,#1e3a8a,#3b82f6);}
.avatar.usr{background:linear-gradient(135deg,#065f46,#10b981);}
.bubble{background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px 18px;font-size:14px;line-height:1.7;color:#e2e8f0;max-width:680px;}
.msg.user .bubble{background:linear-gradient(135deg,#1e3a8a22,#1e40af22);border-color:rgba(59,130,246,.2);}
.bubble code{background:#1e293b;padding:2px 7px;border-radius:5px;font-family:'Courier New',monospace;font-size:12px;color:#93c5fd;}
.bubble pre{background:#0d1117;border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:14px;margin:10px 0;overflow-x:auto;}
.bubble pre code{background:none;padding:0;color:#e2e8f0;font-size:13px;}
.time-label{font-size:11px;color:#334155;margin-top:5px;text-align:right;}
.msg.assistant .time-label{text-align:left;}

/* ── Welcome screen ── */
.welcome{text-align:center;padding:60px 20px;color:#475569;}
.welcome .bot-icon{width:80px;height:80px;background:linear-gradient(135deg,#1e3a8a,#3b82f6);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 20px;}
.welcome h2{font-size:22px;font-weight:700;color:white;margin-bottom:8px;}
.welcome p{font-size:14px;color:#64748b;margin-bottom:28px;}
.suggestions{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;max-width:600px;margin:0 auto;}
.sug-btn{background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#94a3b8;padding:10px 16px;border-radius:12px;font-size:13px;cursor:pointer;transition:.2s;}
.sug-btn:hover{background:rgba(59,130,246,.1);border-color:rgba(59,130,246,.3);color:#93c5fd;}

/* ── Input zone ── */
.input-zone{padding:18px 32px 22px;border-top:1px solid rgba(255,255,255,.06);background:#020617;flex-shrink:0;}
.input-wrap{display:flex;gap:12px;align-items:flex-end;background:#0f172a;border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:12px 16px;transition:.2s;}
.input-wrap:focus-within{border-color:rgba(59,130,246,.4);box-shadow:0 0 0 3px rgba(59,130,246,.08);}
#userInput{flex:1;background:none;border:none;outline:none;color:white;font-size:14px;resize:none;min-height:22px;max-height:140px;line-height:1.5;font-family:inherit;}
#userInput::placeholder{color:#334155;}
.send-btn{background:linear-gradient(135deg,#1e40af,#3b82f6);border:none;color:white;width:38px;height:38px;border-radius:10px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:.2s;flex-shrink:0;}
.send-btn:hover{filter:brightness(1.15);}
.send-btn:disabled{opacity:.4;cursor:not-allowed;}
.input-hint{font-size:12px;color:#1e293b;margin-top:8px;text-align:center;}

/* ── Typing indicator ── */
.typing{display:none;align-items:center;gap:6px;padding:12px 18px;background:#0f172a;border:1px solid rgba(255,255,255,.06);border-radius:16px;width:fit-content;}
.typing span{width:7px;height:7px;border-radius:50%;background:#3b82f6;animation:bounce .8s infinite;}
.typing span:nth-child(2){animation-delay:.15s;}
.typing span:nth-child(3){animation-delay:.3s;}
@keyframes bounce{0%,60%,100%{transform:translateY(0);}30%{transform:translateY(-6px);}}

/* ── XP toast ── */
.xp-toast{position:fixed;top:80px;right:24px;background:linear-gradient(135deg,#14532d,#16a34a);border:1px solid rgba(34,197,94,.3);color:white;padding:12px 20px;border-radius:14px;font-size:14px;font-weight:600;display:none;z-index:999;box-shadow:0 8px 24px rgba(0,0,0,.4);}

@media(max-width:768px){
  .sidebar{display:none;}
  .messages-area{padding:16px;}
  .input-zone{padding:12px 16px 16px;}
}
</style>
</head>
<body>

<!-- Topbar -->
<div class="topbar">
  <a href="dashboard.php" class="back-btn"><i class="fas fa-arrow-left"></i> Dashboard</a>
  <div class="logo"><i class="fas fa-robot" style="color:#3b82f6;"></i>Cyber<span>Bot</span></div>
  <div class="xp-badge">
    <i class="fas fa-star" style="color:#fbbf24;font-size:12px;"></i>
    <span><?= $xp ?> XP</span>
    <span class="title" style="color:<?= $levelColor ?>;"><?= htmlspecialchars($levelTitle) ?></span>
  </div>
</div>

<!-- XP Toast -->
<div class="xp-toast" id="xpToast">🎉 +3 XP — Engagement pédagogique !</div>

<div class="chat-layout">

  <!-- Sidebar -->
  <div class="sidebar">
    <div>
      <div class="sidebar-title">Sujets rapides</div>
      <button class="topic-btn" onclick="sendSuggestion('Explique-moi l\'injection SQL et comment s\'en protéger')">
        <i class="fas fa-database"></i> Injection SQL
      </button>
      <button class="topic-btn" onclick="sendSuggestion('Comment fonctionne une attaque XSS et quelle est la défense ?')">
        <i class="fas fa-code"></i> Cross-Site Scripting
      </button>
      <button class="topic-btn" onclick="sendSuggestion('Comment détecter un email de phishing ?')">
        <i class="fas fa-envelope"></i> Phishing
      </button>
      <button class="topic-btn" onclick="sendSuggestion('Quels sont les critères d\'un mot de passe robuste ?')">
        <i class="fas fa-lock"></i> Mots de passe
      </button>
      <button class="topic-btn" onclick="sendSuggestion('Explique-moi la protection CSRF avec des tokens')">
        <i class="fas fa-shield-halved"></i> Protection CSRF
      </button>
      <button class="topic-btn" onclick="sendSuggestion('Comment fonctionne le hashage bcrypt en PHP ?')">
        <i class="fas fa-hashtag"></i> Hashage bcrypt
      </button>
      <button class="topic-btn" onclick="sendSuggestion('Fais-moi un mini-quiz sur les attaques OWASP Top 10')">
        <i class="fas fa-question-circle"></i> Mini-quiz
      </button>
    </div>
    <div>
      <div class="sidebar-title">Ma progression</div>
      <div class="stats-card">
        <div class="stat">XP total <span><?= $xp ?></span></div>
        <div class="stat">Niveau <span><?= $level ?></span></div>
        <div class="stat">Titre <span style="color:<?= $levelColor ?>;"><?= htmlspecialchars($levelTitle) ?></span></div>
        <div class="stat">Échanges <span id="exchangeCount">0</span></div>
      </div>
    </div>
  </div>

  <!-- Chat main -->
  <div class="chat-main">
    <div class="messages-area" id="messagesArea">

      <?php if (empty($history)): ?>
      <!-- Welcome screen -->
      <div class="welcome" id="welcomeScreen">
        <div class="bot-icon">🤖</div>
        <h2>Bonjour, <?= htmlspecialchars($_SESSION['fullname'] ?? 'Apprenant') ?> !</h2>
        <p>Je suis CyberBot, ton assistant pédagogique. Pose-moi tes questions sur la cybersécurité.</p>
        <div class="suggestions">
          <button class="sug-btn" onclick="sendSuggestion('Qu\'est-ce que l\'OWASP Top 10 ?')">OWASP Top 10</button>
          <button class="sug-btn" onclick="sendSuggestion('Comment me protéger contre les injections SQL ?')">Protection SQL</button>
          <button class="sug-btn" onclick="sendSuggestion('Explique-moi le fonctionnement d\'un WAF')">C'est quoi un WAF ?</button>
          <button class="sug-btn" onclick="sendSuggestion('Comment créer un mot de passe très sécurisé ?')">Mot de passe fort</button>
          <button class="sug-btn" onclick="sendSuggestion('Fais-moi un mini-quiz sur la cybersécurité')">Mini-quiz !</button>
        </div>
      </div>
      <?php else: ?>
        <?php foreach ($history as $msg): ?>
        <div class="msg <?= $msg['role'] === 'user' ? 'user' : 'assistant' ?>">
          <div class="avatar <?= $msg['role'] === 'user' ? 'usr' : 'bot' ?>">
            <?= $msg['role'] === 'user' ? '👤' : '🤖' ?>
          </div>
          <div>
            <div class="bubble"><?= nl2br(htmlspecialchars($msg['content'])) ?></div>
            <div class="time-label"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Typing indicator -->
      <div class="msg assistant" id="typingIndicator" style="display:none;">
        <div class="avatar bot">🤖</div>
        <div class="typing" style="display:flex;">
          <span></span><span></span><span></span>
        </div>
      </div>

    </div>

    <!-- Input zone -->
    <div class="input-zone">
      <div class="input-wrap">
        <textarea id="userInput" placeholder="Pose ta question sur la cybersécurité..." rows="1"></textarea>
        <button class="send-btn" id="sendBtn" onclick="sendMessage()">
          <i class="fas fa-paper-plane" style="font-size:14px;"></i>
        </button>
      </div>
      <div class="input-hint">Entrée pour envoyer · Shift+Entrée pour nouvelle ligne · +3 XP après 3 échanges</div>
    </div>
  </div>
</div>

<script>
const messagesArea = document.getElementById('messagesArea');
const userInput    = document.getElementById('userInput');
const sendBtn      = document.getElementById('sendBtn');
const typingEl     = document.getElementById('typingIndicator');
let exchangeCount  = <?= (int)($_SESSION['chatbot_exchanges'] ?? 0) ?>;
const currentPage  = 'chatbot';

// Auto-resize textarea
userInput.addEventListener('input', function() {
  this.style.height = 'auto';
  this.style.height = Math.min(this.scrollHeight, 140) + 'px';
});

// Enter to send
userInput.addEventListener('keydown', function(e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault();
    sendMessage();
  }
});

function sendSuggestion(text) {
  document.getElementById('welcomeScreen')?.remove();
  userInput.value = text;
  sendMessage();
}

function addMessage(role, content) {
  const time = new Date().toLocaleTimeString('fr-FR', {hour:'2-digit', minute:'2-digit'});
  const isUser = role === 'user';

  // Convert markdown-like formatting
  const formatted = content
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/`([^`]+)`/g, '<code>$1</code>')
    .replace(/```([\s\S]*?)```/g, '<pre><code>$1</code></pre>')
    .replace(/\n/g, '<br>');

  const div = document.createElement('div');
  div.className = `msg ${isUser ? 'user' : 'assistant'}`;
  div.innerHTML = `
    <div class="avatar ${isUser ? 'usr' : 'bot'}">${isUser ? '👤' : '🤖'}</div>
    <div>
      <div class="bubble">${formatted}</div>
      <div class="time-label">${time}</div>
    </div>`;

  // Insert before typing indicator
  messagesArea.insertBefore(div, typingEl);
  scrollToBottom();
}

function scrollToBottom() {
  messagesArea.scrollTop = messagesArea.scrollHeight;
}

async function sendMessage() {
  const msg = userInput.value.trim();
  if (!msg || sendBtn.disabled) return;

  document.getElementById('welcomeScreen')?.remove();
  addMessage('user', msg);
  userInput.value = '';
  userInput.style.height = 'auto';

  sendBtn.disabled = true;
  typingEl.style.display = 'flex';
  scrollToBottom();

  try {
    const res = await fetch('api/chatbot.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({message: msg, page: currentPage})
    });
    const data = await res.json();

    typingEl.style.display = 'none';

    if (data.error) {
      addMessage('assistant', '⚠️ ' + data.error);
    } else {
      addMessage('assistant', data.response);
      exchangeCount = data.exchanges;
      document.getElementById('exchangeCount').textContent = exchangeCount;

      // Afficher toast XP si bonus déclenché
      if (data.chatbot_xp_bonus) {
        const toast = document.getElementById('xpToast');
        toast.style.display = 'block';
        setTimeout(() => toast.style.display = 'none', 3000);
      }
    }
  } catch(err) {
    typingEl.style.display = 'none';
    addMessage('assistant', '⚠️ Erreur de connexion. Vérifie ta connexion et réessaie.');
  }

  sendBtn.disabled = false;
  userInput.focus();
}

// Scroll to bottom on load
scrollToBottom();
</script>
</body>
</html>
