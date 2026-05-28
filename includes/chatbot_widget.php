<?php
/**
 * chatbot_widget.php
 * À inclure en bas de chaque page protégée (avant </body>).
 * Affiche un bouton flottant ouvrant le chatbot IA.
 */
?>
<!-- ── Chatbot Floating Widget ── -->
<style>
.chat-fab{position:fixed;bottom:28px;right:28px;z-index:9999;}
.chat-fab a{
  display:flex;align-items:center;justify-content:center;
  width:56px;height:56px;border-radius:16px;
  background:linear-gradient(135deg,#1e40af,#3b82f6);
  color:white;font-size:22px;text-decoration:none;
  box-shadow:0 8px 24px rgba(59,130,246,.45);
  transition:.2s;position:relative;
}
.chat-fab a:hover{transform:translateY(-3px);box-shadow:0 12px 30px rgba(59,130,246,.55);}
.chat-fab .fab-label{
  position:absolute;right:66px;white-space:nowrap;
  background:#0f172a;color:#93c5fd;font-size:12px;font-weight:600;
  padding:6px 12px;border-radius:10px;border:1px solid rgba(59,130,246,.2);
  opacity:0;pointer-events:none;transition:.2s;
}
.chat-fab a:hover .fab-label{opacity:1;}
.chat-fab .notif-dot{
  position:absolute;top:-4px;right:-4px;
  width:12px;height:12px;background:#22c55e;
  border-radius:50%;border:2px solid #020617;
}
</style>
<div class="chat-fab">
  <a href="<?= str_repeat('../', substr_count($_SERVER['PHP_SELF'], '/', strlen(str_replace('/cybersecurity-platform', '', dirname($_SERVER['PHP_SELF']))))) ?>chatbot.php" title="Assistant pédagogique IA">
    <span class="fab-label">CyberBot IA</span>
    <i class="fas fa-robot"></i>
    <span class="notif-dot"></span>
  </a>
</div>
