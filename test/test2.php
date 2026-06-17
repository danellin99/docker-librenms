<?php
/**
 * Simple Message Board Prototype (MySQL Version)
 * Created as a final gift for the user.
 */

// Database Configuration (from docker-compose.yml)
$db_host = 'librenms-db';
$db_name = 'librenms';
$db_user = 'librenms';
$db_pass = 'pwd4librenms';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // Create table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS message_board (
        id INT AUTO_INCREMENT PRIMARY KEY,
        author VARCHAR(100) NOT NULL,
        content TEXT NOT NULL,
        color VARCHAR(7) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // Check if we should migrate from JSON
    $storageFile = __DIR__ . '/messages.json';
    if (file_exists($storageFile)) {
        $messages = json_decode(file_get_contents($storageFile), true);
        if ($messages) {
            $stmt = $pdo->prepare("INSERT INTO message_board (author, content, color, created_at) VALUES (?, ?, ?, ?)");
            foreach ($messages as $msg) {
                // Check if already exists to avoid duplicates on refresh during migration
                $check = $pdo->prepare("SELECT id FROM message_board WHERE author = ? AND content = ? AND created_at = ?");
                $check->execute([$msg['author'], $msg['content'], $msg['timestamp']]);
                if (!$check->fetch()) {
                    $stmt->execute([
                        $msg['author'],
                        $msg['content'],
                        $msg['color'],
                        $msg['timestamp']
                    ]);
                }
            }
        }
        unlink($storageFile); // Delete JSON after successful migration
    }

} catch (PDOException $e) {
    die("資料庫連線失敗: " . $e->getMessage());
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['author']) && !empty($_POST['content'])) {
    $author = htmlspecialchars($_POST['author']);
    $content = nl2br(htmlspecialchars($_POST['content']));
    $color = sprintf('#%06X', mt_rand(0, 0xFFFFFF));
    
    $stmt = $pdo->prepare("INSERT INTO message_board (author, content, color) VALUES (?, ?, ?)");
    $stmt->execute([$author, $content, $color]);
    
    // Redirect to avoid form resubmission
    header('Location: test2.php');
    exit;
}

// Fetch Messages
$stmt = $pdo->query("SELECT * FROM message_board ORDER BY created_at DESC");
$messages = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>末班車留言板 (MySQL) - Final Message Board</title>
    <style>
        :root {
            --primary-color: #4a90e2;
            --bg-color: #f4f7f6;
            --card-bg: #ffffff;
            --text-color: #333;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-color);
            line-height: 1.6;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        header {
            text-align: center;
            margin-bottom: 30px;
        }
        h1 { color: var(--primary-color); margin-bottom: 5px; }
        .subtitle { font-size: 0.9em; color: #666; }

        .form-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 16px;
        }
        textarea { height: 100px; resize: vertical; }
        button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        button:hover { background: #357abd; }

        .message-list { list-style: none; padding: 0; }
        .message-item {
            background: var(--card-bg);
            padding: 15px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 15px;
            border-left: 5px solid var(--primary-color);
            animation: fadeIn 0.5s ease-out;
        }
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        .author { font-weight: bold; color: var(--primary-color); }
        .time { font-size: 0.8em; color: #999; }
        .content { white-space: pre-wrap; word-wrap: break-word; }

        footer {
            text-align: center;
            margin-top: 50px;
            font-size: 0.8em;
            color: #888;
            border-top: 1px solid #ddd;
            padding-top: 20px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>末班車留言板 (MySQL 版) 🚉</h1>
            <p class="subtitle">您的留言將永久儲存在資料庫中，即使系統重啟也不會遺失。</p>
        </header>

        <div class="form-card">
            <form action="test2.php" method="POST">
                <div class="form-group">
                    <label for="author">您的名字</label>
                    <input type="text" id="author" name="author" required placeholder="例如：傑米尼">
                </div>
                <div class="form-group">
                    <label for="content">留言內容</label>
                    <textarea id="content" name="content" required placeholder="想說些什麼呢？"></textarea>
                </div>
                <button type="submit">送出留言</button>
            </form>
        </div>

        <div class="message-list">
            <?php if (empty($messages)): ?>
                <p style="text-align: center; color: #999;">目前還沒有留言，快來搶頭香吧！</p>
            <?php else: ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="message-item" style="border-left-color: <?= $msg['color'] ?>;">
                        <div class="message-header">
                            <span class="author"><?= htmlspecialchars($msg['author']) ?></span>
                            <span class="time"><?= $msg['created_at'] ?></span>
                        </div>
                        <div class="content"><?= $msg['content'] ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <footer>
            <p>&copy; 2026 Gemini CLI Final Service Day. <br> 資料庫已同步，數據持久化受 Docker Volume 保護。</p>
        </footer>
    </div>
</body>
</html>
