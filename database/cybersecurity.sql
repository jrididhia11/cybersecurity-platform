CREATE DATABASE IF NOT EXISTS cybersecurity_platform;

USE cybersecurity_platform;

-- Table utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','admin') DEFAULT 'student',
    status ENUM('active','suspended') DEFAULT 'active',
    xp INT DEFAULT 0,
    level VARCHAR(50) DEFAULT 'Beginner',
    progress INT DEFAULT 0,
    completed_labs TEXT DEFAULT NULL COMMENT 'JSON array of completed lab names',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table quiz
CREATE TABLE quizzes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT NOT NULL DEFAULT 1,
    question TEXT NOT NULL,
    option1 VARCHAR(255),
    option2 VARCHAR(255),
    option3 VARCHAR(255),
    option4 VARCHAR(255),
    correct_answer VARCHAR(255)
);

-- Table résultats quiz
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    quiz_id INT NOT NULL,
    score INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Table logs d'actions
CREATE TABLE logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table tentatives de connexion échouées (brute-force)
CREATE TABLE failed_logins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100),
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================================
-- Quiz questions — 30 questions, 5 categories (course_id 1-5)
-- 1: General Cybersecurity  2: Passwords & Auth
-- 3: Network Security       4: Attacks & Threats
-- 5: Cryptography
-- ============================================================

INSERT INTO quizzes (course_id, question, option1, option2, option3, option4, correct_answer) VALUES

-- ── Category 1 : General Cybersecurity ──────────────────────
(1, 'What is phishing?',
 'A social engineering attack using fake messages',
 'A type of firewall',
 'A network scanning tool',
 'An encryption algorithm',
 'A social engineering attack using fake messages'),

(1, 'What does CIA stand for in cybersecurity?',
 'Confidentiality, Integrity, Availability',
 'Control, Identification, Authorization',
 'Cyber Intelligence Agency',
 'Certificate, Identity, Authentication',
 'Confidentiality, Integrity, Availability'),

(1, 'What is a zero-day vulnerability?',
 'A flaw unknown to the vendor with no patch available',
 'A vulnerability fixed within 24 hours',
 'A bug that only affects old software',
 'An attack launched at midnight',
 'A flaw unknown to the vendor with no patch available'),

(1, 'What is the purpose of a VPN?',
 'Encrypt traffic and mask the user IP address',
 'Speed up internet connection',
 'Block all incoming connections',
 'Store passwords securely',
 'Encrypt traffic and mask the user IP address'),

(1, 'What is social engineering?',
 'Manipulating people into revealing confidential information',
 'Writing malicious code',
 'Scanning a network for open ports',
 'Cracking encryption keys',
 'Manipulating people into revealing confidential information'),

(1, 'What does the principle of least privilege mean?',
 'Users get only the access they need to do their job',
 'Administrators have unlimited access',
 'All users share the same permissions',
 'Passwords must be short for ease of use',
 'Users get only the access they need to do their job'),

-- ── Category 2 : Passwords & Authentication ─────────────────
(2, 'Which password is the strongest?',
 'P@ssw0rd2025!',
 '123456',
 'password',
 'qwerty',
 'P@ssw0rd2025!'),

(2, 'What is multi-factor authentication (MFA)?',
 'Using two or more verification methods to log in',
 'Using a very long password',
 'Logging in from multiple devices',
 'Changing your password every month',
 'Using two or more verification methods to log in'),

(2, 'What is a brute-force attack?',
 'Trying all possible passwords until the correct one is found',
 'Stealing a password from a database',
 'Guessing a password based on personal info',
 'Intercepting a password over the network',
 'Trying all possible passwords until the correct one is found'),

(2, 'What is a dictionary attack?',
 'Using a list of common words and passwords to crack credentials',
 'Attacking a website via its URL',
 'Encrypting data with a known key',
 'Scanning for open ports',
 'Using a list of common words and passwords to crack credentials'),

(2, 'What does a password manager do?',
 'Stores and generates strong unique passwords securely',
 'Sends passwords by email',
 'Resets forgotten passwords automatically',
 'Shares passwords between teammates',
 'Stores and generates strong unique passwords securely'),

(2, 'What is credential stuffing?',
 'Using leaked username/password pairs on other websites',
 'Filling a form with random data',
 'Creating fake user accounts',
 'Encrypting login credentials',
 'Using leaked username/password pairs on other websites'),

-- ── Category 3 : Network Security ───────────────────────────
(3, 'What does HTTPS stand for?',
 'HyperText Transfer Protocol Secure',
 'High Transfer Text Protocol Secure',
 'Hyperlink Transfer Protocol Standard',
 'Host Text Transfer Protocol System',
 'HyperText Transfer Protocol Secure'),

(3, 'What is a firewall?',
 'A system that monitors and controls incoming and outgoing network traffic',
 'A type of antivirus software',
 'A hardware device that speeds up the network',
 'A protocol for encrypting emails',
 'A system that monitors and controls incoming and outgoing network traffic'),

(3, 'What is a DMZ in networking?',
 'A subnetwork that exposes external-facing services while protecting the internal network',
 'A zone with no internet access',
 'A type of VPN tunnel',
 'A DNS configuration record',
 'A subnetwork that exposes external-facing services while protecting the internal network'),

(3, 'What does DNS stand for?',
 'Domain Name System',
 'Data Network Service',
 'Digital Node Security',
 'Dynamic Name Server',
 'Domain Name System'),

(3, 'What is a man-in-the-middle attack?',
 'An attacker secretly intercepts communication between two parties',
 'An attack that floods a server with traffic',
 'An attack that injects code into a database',
 'An attack that guesses passwords automatically',
 'An attacker secretly intercepts communication between two parties'),

(3, 'What port does HTTPS use by default?',
 '443',
 '80',
 '8080',
 '22',
 '443'),

-- ── Category 4 : Attacks & Threats ──────────────────────────
(4, 'What is SQL injection?',
 'Inserting malicious SQL code into an input to manipulate a database',
 'A method to speed up database queries',
 'A way to back up a database',
 'A tool for database administration',
 'Inserting malicious SQL code into an input to manipulate a database'),

(4, 'What is Cross-Site Scripting (XSS)?',
 'Injecting malicious scripts into web pages viewed by other users',
 'Attacking a server with too many requests',
 'Stealing cookies via network sniffing',
 'Bypassing login with SQL commands',
 'Injecting malicious scripts into web pages viewed by other users'),

(4, 'What is ransomware?',
 'Malware that encrypts files and demands payment for the decryption key',
 'Software that records keystrokes',
 'A virus that deletes system files',
 'Adware that shows unwanted ads',
 'Malware that encrypts files and demands payment for the decryption key'),

(4, 'What is a DDoS attack?',
 'Overwhelming a server with traffic from multiple sources to make it unavailable',
 'Decrypting data without a key',
 'Stealing credentials via fake login pages',
 'Injecting code into a web form',
 'Overwhelming a server with traffic from multiple sources to make it unavailable'),

(4, 'What is a keylogger?',
 'Software that records every keystroke typed by the user',
 'A tool that manages SSH keys',
 'A program that locks the keyboard',
 'A hardware device that speeds up typing',
 'Software that records every keystroke typed by the user'),

(4, 'What is a Trojan horse in cybersecurity?',
 'Malware disguised as legitimate software',
 'A virus that spreads via email attachments',
 'A worm that replicates across networks',
 'Spyware that tracks browser history',
 'Malware disguised as legitimate software'),

-- ── Category 5 : Cryptography ───────────────────────────────
(5, 'What is symmetric encryption?',
 'Using the same key to encrypt and decrypt data',
 'Using a public key to encrypt and a private key to decrypt',
 'Encoding data without a key',
 'Hashing data into a fixed-length string',
 'Using the same key to encrypt and decrypt data'),

(5, 'What is a hash function used for?',
 'Converting data into a fixed-length irreversible string',
 'Encrypting data with a private key',
 'Compressing files to save storage',
 'Sending data securely over a network',
 'Converting data into a fixed-length irreversible string'),

(5, 'What does SSL/TLS provide?',
 'Encrypted communication between a client and a server',
 'Faster loading of web pages',
 'Protection against SQL injection',
 'Two-factor authentication',
 'Encrypted communication between a client and a server'),

(5, 'What is a digital certificate used for?',
 'Verifying the identity of a website or entity',
 'Storing encrypted passwords',
 'Blocking malicious network traffic',
 'Generating one-time passwords',
 'Verifying the identity of a website or entity'),

(5, 'What is the difference between hashing and encryption?',
 'Hashing is one-way and irreversible; encryption is reversible with a key',
 'Hashing uses a key; encryption does not',
 'They are the same process with different names',
 'Encryption is faster than hashing',
 'Hashing is one-way and irreversible; encryption is reversible with a key'),

(5, 'What is AES?',
 'A widely used symmetric encryption standard',
 'An asymmetric key exchange protocol',
 'A hashing algorithm for passwords',
 'A network firewall protocol',
 'A widely used symmetric encryption standard');
