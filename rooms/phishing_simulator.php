<?php
session_start();
include '../includes/xp_system.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// 3 phishing rounds, each with one correct answer
$rounds = [
    1 => [
        'emails' => [
            'A' => [
                'sender'  => 'support@paypa1.com',
                'name'    => 'PayPal Support',
                'subject' => 'Your account has been limited',
                'body'    => 'Dear Customer,<br><br>We have <strong>temporarily limited</strong> your PayPal account due to suspicious activity.<br><br>Please <a style="color:#38bdf8">click here</a> to restore access immediately or your account will be <strong>permanently closed within 24 hours</strong>.<br><br>PayPal Security Team',
                'is_phishing' => true,
                'clues' => ['Domain <strong>paypa1.com</strong> uses number "1" instead of letter "l"', 'Urgent 24h threat to create panic', 'Vague "suspicious activity" with no details', 'Generic "Dear Customer" instead of your name']
            ],
            'B' => [
                'sender'  => 'no-reply@paypal.com',
                'name'    => 'PayPal',
                'subject' => 'Receipt for your payment of $25.00',
                'body'    => 'Hello John,<br><br>You sent a payment of <strong>$25.00</strong> to Netflix on Dec 12, 2025.<br><br>Transaction ID: 7XJ291-KLQ<br><br>If you did not authorize this payment, visit paypal.com/dispute.<br><br>Thanks,<br>PayPal',
                'is_phishing' => false,
                'clues' => ['Official domain paypal.com', 'Personalized with your name', 'Specific transaction details', 'Points to official site for disputes']
            ]
        ],
        'answer' => 'A',
        'xp' => 20
    ],
    2 => [
        'emails' => [
            'A' => [
                'sender'  => 'it-helpdesk@company.com',
                'name'    => 'IT Helpdesk',
                'subject' => 'Scheduled maintenance — no action needed',
                'body'    => 'Hi Team,<br><br>We will be performing scheduled server maintenance on Saturday Dec 14 from 2:00 AM to 4:00 AM.<br><br>Services affected: internal email, VPN.<br><br>No action required from your side.<br><br>— IT Department',
                'is_phishing' => false,
                'clues' => ['No link or attachment to click', 'Specific maintenance window with dates', 'No credentials requested', 'Legitimate sender domain']
            ],
            'B' => [
                'sender'  => 'it-helpdesk@company-support.net',
                'name'    => 'IT Helpdesk',
                'subject' => 'ACTION REQUIRED: Reset your password NOW',
                'body'    => 'Dear Employee,<br><br>Our security system detected that your password <strong>expires today</strong>. You must reset it immediately to avoid losing access to all company systems.<br><br><a style="color:#f87171">Click HERE to reset your password</a><br><br>Failure to act will result in account suspension.',
                'is_phishing' => true,
                'clues' => ['Domain <strong>company-support.net</strong> is different from company.com', 'ALL CAPS subject creates panic', 'Vague threat with no specific details', 'Suspicious link with no visible URL']
            ]
        ],
        'answer' => 'B',
        'xp' => 20
    ],
    3 => [
        'emails' => [
            'A' => [
                'sender'  => 'security-alert@microsoft-verify.com',
                'name'    => 'Microsoft Security',
                'subject' => 'Unusual sign-in activity detected',
                'body'    => 'Dear Microsoft User,<br><br>We detected a sign-in to your Microsoft account from an <strong>unrecognized device in Russia</strong>.<br><br>If this was not you, your account may be compromised. <a style="color:#f87171">Verify your identity now</a> to secure your account.<br><br>Microsoft Security Team',
                'is_phishing' => true,
                'clues' => ['Domain <strong>microsoft-verify.com</strong> is NOT microsoft.com', '"Russia" creates fear to rush you', '"Dear Microsoft User" — no name', 'Real Microsoft alerts link to account.microsoft.com only']
            ],
            'B' => [
                'sender'  => 'account-security@microsoft.com',
                'name'    => 'Microsoft Account Team',
                'subject' => 'Your monthly account activity summary',
                'body'    => 'Hello Alex,<br><br>Here is your Microsoft account activity for November 2025:<br><br>• 12 sign-ins from Windows 11 device<br>• Last sign-in: Dec 1, Paris, France<br><br>Review full activity at <span style="color:#38bdf8">account.microsoft.com</span><br><br>Microsoft Account Team',
                'is_phishing' => false,
                'clues' => ['Official microsoft.com domain', 'Personalized with your name', 'Specific activity data', 'Points to official Microsoft domain']
            ]
        ],
        'answer' => 'A',
        'xp' => 20
    ]
];

// Gestion de la progression
if (!isset($_SESSION['phishing_round']))  $_SESSION['phishing_round']  = 1;
if (!isset($_SESSION['phishing_score']))  $_SESSION['phishing_score']  = 0;
if (!isset($_SESSION['phishing_history'])) $_SESSION['phishing_history'] = [];

$currentRound  = $_SESSION['phishing_round'];
$feedback      = null;
$showSummary   = false;

if (isset($_POST['answer']) && $currentRound <= 3) {
    $chosen   = $_POST['answer'];
    $correct  = $rounds[$currentRound]['answer'];
    $isRight  = ($chosen === $correct);

    if ($isRight) $_SESSION['phishing_score']++;

    $_SESSION['phishing_history'][$currentRound] = [
        'chosen'  => $chosen,
        'correct' => $correct,
        'right'   => $isRight,
        'clues'   => $rounds[$currentRound]['emails'][$correct]['clues']
    ];

    $_SESSION['phishing_round']++;

    if ($_SESSION['phishing_round'] > 3) {
        $showSummary = true;
        $score = $_SESSION['phishing_score'];
        $xpEarned = $score * 20;
        unset($_SESSION['phishing_replaying']);
        completeLab('phishing', $xpEarned > 0 ? $xpEarned : 0);
    } else {
        $feedback = $_SESSION['phishing_history'][$currentRound - 1];
        $currentRound = $_SESSION['phishing_round'];
    }
}

if (isset($_POST['restart'])) {
    unset($_SESSION['phishing_round'], $_SESSION['phishing_score'], $_SESSION['phishing_history']);
    $_SESSION['phishing_replaying'] = true;
    header("Location: phishing_simulator.php");
    exit();
}

$alreadyDone   = isLabCompleted('phishing');
$phishReplaying = !empty($_SESSION['phishing_replaying']);

if ($alreadyDone && !$phishReplaying && !isset($_POST['answer'])) {
    $showSummary = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Phishing Simulator Lab</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body { background:#020617; color:white; font-family:'Inter',sans-serif; }
.sidebar { position:fixed; width:260px; height:100vh; background:#0f172a; padding:30px 20px; border-right:1px solid rgba(255,255,255,.05); overflow-y:auto; }
.sidebar-logo { font-size:24px; font-weight:700; margin-bottom:40px; }
.sidebar-logo span { color:#3b82f6; }
.sidebar a { display:flex; align-items:center; gap:12px; color:#94a3b8; text-decoration:none; padding:12px 16px; border-radius:12px; margin-bottom:6px; font-size:14px; transition:.2s; }
.sidebar a:hover, .sidebar a.active { background:linear-gradient(135deg,#2563eb,#3b82f6); color:white; }
.main { margin-left:260px; padding:40px; max-width:1100px; }

.lab-header { background:linear-gradient(135deg,#0c4a2222,#0f172a); border:1px solid rgba(34,197,94,.15); border-radius:20px; padding:32px; margin-bottom:28px; }
.badge-easy { background:rgba(34,197,94,.15); color:#86efac; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; }
.xp-badge { background:rgba(234,179,8,.15); color:#fde047; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:600; }

/* Theory box */
.theory { background:#0f172a; border-left:4px solid #f59e0b; border-radius:0 12px 12px 0; padding:24px 28px; margin-bottom:28px; }
.theory h5 { color:#fbbf24; margin-bottom:12px; }
.theory ul { color:#94a3b8; font-size:14px; line-height:2; padding-left:20px; }

/* Progress */
.progress-bar-wrap { background:#0f172a; border-radius:12px; padding:20px 24px; margin-bottom:28px; display:flex; align-items:center; gap:16px; }
.round-dot { width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:14px; transition:.3s; }
.dot-done    { background:#22c55e; color:white; }
.dot-active  { background:#3b82f6; color:white; box-shadow:0 0 0 4px rgba(59,130,246,.25); }
.dot-pending { background:#1e293b; color:#475569; }

/* Feedback banner */
.feedback-right { background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.3); border-radius:14px; padding:20px 24px; margin-bottom:24px; }
.feedback-wrong { background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.3); border-radius:14px; padding:20px 24px; margin-bottom:24px; }
.clue-list { margin-top:12px; padding-left:20px; color:#94a3b8; font-size:13px; line-height:2; }

/* Email cards */
.email-card { background:#0f172a; border:2px solid rgba(255,255,255,.06); border-radius:18px; padding:24px; height:100%; transition:.25s; cursor:pointer; }
.email-card:hover { border-color:#3b82f6; transform:translateY(-3px); }
.email-meta { display:flex; align-items:center; gap:12px; margin-bottom:16px; }
.avatar { width:44px; height:44px; border-radius:12px; background:linear-gradient(135deg,#1e3a5f,#2563eb); display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0; }
.email-from { font-size:13px; color:#64748b; }
.email-from strong { color:#38bdf8; }
.email-subject { font-weight:700; font-size:17px; margin-bottom:12px; }
.email-body { color:#94a3b8; font-size:14px; line-height:1.8; border-top:1px solid rgba(255,255,255,.05); padding-top:14px; }
.pick-btn { width:100%; margin-top:18px; border:none; padding:12px; border-radius:12px; background:linear-gradient(135deg,#1e3a5f,#2563eb); color:white; font-weight:600; font-size:14px; transition:.2s; }
.pick-btn:hover { background:linear-gradient(135deg,#2563eb,#3b82f6); transform:scale(1.02); }

/* Summary */
.summary-card { background:#0f172a; border-radius:20px; padding:36px; text-align:center; }
.score-circle { width:120px; height:120px; border-radius:50%; display:flex; flex-direction:column; align-items:center; justify-content:center; margin:0 auto 24px; font-weight:700; }
.score-0 { background:rgba(239,68,68,.15); border:3px solid #ef4444; }
.score-1, .score-2 { background:rgba(234,179,8,.15); border:3px solid #eab308; }
.score-3 { background:rgba(34,197,94,.15); border:3px solid #22c55e; }
.round-recap { background:#111827; border-radius:12px; padding:16px 20px; margin-bottom:10px; text-align:left; font-size:14px; }
</style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-logo">Cyber<span>Lab</span></div>
    <a href="../dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
    <a href="intro-phishing.php" class="active"><i class="fas fa-envelope"></i> Phishing Lab</a>
    <a href="intro-sql.php"><i class="fas fa-database"></i> SQL Injection</a>
    <a href="intro-password.php"><i class="fas fa-lock"></i> Password</a>
    <a href="intro-xss.php"><i class="fas fa-code"></i> XSS Lab</a>
    <a href="../leaderboard.php"><i class="fas fa-trophy"></i> Leaderboard</a>
</div>

<div class="main">

    <div class="lab-header">
        <div class="d-flex gap-2 mb-3">
            <span class="badge-easy">Easy → Medium</span>
            <span class="xp-badge"><i class="fas fa-star me-1"></i>Up to +60 XP</span>
        </div>
        <h1 style="font-size:30px;font-weight:700;margin-bottom:8px;"><i class="fas fa-fish-fins me-2" style="color:#22c55e;"></i>Phishing Simulator</h1>
        <p style="color:#94a3b8;margin:0;">3 rounds — analyze each email and identify which one is malicious.</p>
    </div>

    <!-- Theory -->
    <div class="theory">
        <h5><i class="fas fa-book-open me-2"></i>How does phishing work?</h5>
        <ul>
            <li><strong style="color:#fbbf24;">Domain spoofing</strong> — The domain is slightly altered (paypa<strong>1</strong>.com instead of paypal.com)</li>
            <li><strong style="color:#fbbf24;">Artificial urgency</strong> — "Your account will be closed in 24h" — to make you act without thinking</li>
            <li><strong style="color:#fbbf24;">Generic greeting</strong> — "Dear Customer" instead of your real name</li>
            <li><strong style="color:#fbbf24;">Suspicious link</strong> — The real URL does not match the legitimate organization</li>
            <li><strong style="color:#fbbf24;">Credential request</strong> — No real company will ever ask for your password by email</li>
        </ul>
    </div>

    <?php if (!$showSummary): ?>

    <!-- Barre de progression -->
    <div class="progress-bar-wrap">
        <span style="color:#94a3b8;font-size:14px;margin-right:4px;">Progress:</span>
        <?php for($i=1; $i<=3; $i++): ?>
            <?php
                $dotClass = 'dot-pending';
                if (isset($_SESSION['phishing_history'][$i])) $dotClass = 'dot-done';
                elseif ($i == $currentRound) $dotClass = 'dot-active';
            ?>
            <div class="round-dot <?= $dotClass ?>"><?= $i ?></div>
            <?php if ($i < 3): ?><div style="flex:1;height:2px;background:<?= $dotClass=='dot-done' ? '#22c55e' : 'rgba(255,255,255,.08)' ?>;border-radius:2px;"></div><?php endif; ?>
        <?php endfor; ?>
        <span style="color:#64748b;font-size:13px;margin-left:8px;">Round <?= $currentRound ?>/3</span>
    </div>

    <!-- Previous round feedback -->
    <?php if ($feedback): ?>
    <div class="<?= $feedback['right'] ? 'feedback-right' : 'feedback-wrong' ?> mb-4">
        <strong><?= $feedback['right'] ? '✅ Correct !' : '❌ Incorrect' ?></strong>
        — <?= $feedback['right'] ? 'Well spotted. Here is why it was phishing:' : 'That was not the right email. Here are the clues you should have noticed:' ?>
        <ul class="clue-list">
            <?php foreach($feedback['clues'] as $clue): ?>
            <li><?= $clue ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <!-- Emails du round courant -->
    <?php if ($currentRound <= 3): $r = $rounds[$currentRound]; ?>
    <h5 style="color:#94a3b8;margin-bottom:20px;"><i class="fas fa-search me-2"></i>Round <?= $currentRound ?> — Which one is phishing?</h5>
    <div class="row g-4">
        <?php foreach(['A','B'] as $key): $email = $r['emails'][$key]; ?>
        <div class="col-md-6">
            <div class="email-card">
                <div class="email-meta">
                    <div class="avatar"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div style="font-weight:600;font-size:15px;"><?= htmlspecialchars($email['name']) ?></div>
                        <div class="email-from">From: <strong><?= htmlspecialchars($email['sender']) ?></strong></div>
                    </div>
                </div>
                <div class="email-subject"><?= htmlspecialchars($email['subject']) ?></div>
                <div class="email-body"><?= $email['body'] ?></div>
                <form method="POST">
                    <input type="hidden" name="answer" value="<?= $key ?>">
                    <button type="submit" class="pick-btn"><i class="fas fa-flag me-2"></i>Report Email <?= $key ?> as Phishing</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php else: /* SUMMARY */ ?>

    <?php
        $score   = $_SESSION['phishing_score'] ?? 0;
        $history = $_SESSION['phishing_history'] ?? [];
        $scoreClass = $score == 3 ? 'score-3' : ($score >= 1 ? 'score-1' : 'score-0');
        $medals = [0=>'😟', 1=>'😐', 2=>'😊', 3=>'🏆'];
    ?>
    <div class="summary-card">
        <div class="score-circle <?= $scoreClass ?>">
            <div style="font-size:32px;"><?= $medals[$score] ?></div>
            <div style="font-size:22px;"><?= $score ?>/3</div>
        </div>
        <h3 style="margin-bottom:6px;"><?= $score==3 ? 'Perfect!' : ($score>=2 ? 'Well done!' : ($score==1 ? 'Keep practicing' : 'Needs improvement')) ?></h3>
        <p style="color:#94a3b8;margin-bottom:28px;">You earned <strong style="color:#fde047;"><?= $score*20 ?> XP</strong> out of 60 possible.</p>

        <?php foreach($history as $rNum => $h): ?>
        <div class="round-recap">
            <strong>Round <?= $rNum ?></strong> —
            <?php if($h['right']): ?>
                <span style="color:#86efac;">✅ Correct</span>
            <?php else: ?>
                <span style="color:#fca5a5;">❌ Incorrect</span> — The correct answer was email <strong><?= $h['correct'] ?></strong>
            <?php endif; ?>
            <ul style="margin-top:8px;padding-left:20px;color:#64748b;font-size:13px;line-height:1.8;">
                <?php foreach($h['clues'] as $clue): ?>
                <li><?= $clue ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endforeach; ?>

        <div class="d-flex gap-3 justify-content-center mt-4 flex-wrap">
            <form method="POST"><button name="restart" class="pick-btn" style="width:auto;padding:12px 28px;"><i class="fas fa-rotate-right me-2"></i>Try Again</button></form>
            <a href="../dashboard.php" style="display:inline-block;background:#0f172a;border:1px solid rgba(255,255,255,.08);color:#94a3b8;padding:12px 28px;border-radius:12px;text-decoration:none;font-size:14px;"><i class="fas fa-home me-2"></i>Dashboard</a>
        </div>
    </div>
    <?php endif; ?>


<?php
if (file_exists(__DIR__ . '/../includes/chatbot_widget.php'))
    require_once __DIR__ . '/../includes/chatbot_widget.php';
?>
</body>
</html>
