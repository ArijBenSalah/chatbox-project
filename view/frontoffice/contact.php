<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/userController.php");
require_once(__DIR__ . "/../../controller/messageController.php");
session_start();

$userController = new userController();
$messageController = new messageController();
$users = $userController->getOtherUsers($_SESSION['user']['username']);

$selectedUserId = isset($_GET['user_id']) ? $_GET['user_id'] : null;
$selectedUser = null;
$messages = [];

if ($selectedUserId) {
    $selectedUser = $userController->getUserById($selectedUserId);
    $messages = $messageController->listeMessages($_SESSION['user']['id'], $selectedUserId);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Messagerie</title>
    <style>
        body {
            margin: 0;
            font-family: sans-serif;
            display: flex;
            height: 100vh;
        }

        .users-sidebar {
            width: 300px;
            background: #c62828;
            color: white;
            overflow-y: auto;
            padding: 10px;
        }

        .users-header {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 10px;
        }

        .search-form input[type="text"] {
            padding: 8px 12px;
            border-radius: 20px;
            border: none;
            outline: none;
            width: 90%;
        }

        .user {
            display: flex;
            align-items: center;
            padding: 10px;
            background: rgba(255,255,255,0.1);
            border-radius: 5px;
            margin-bottom: 5px;
            text-decoration: none;
            color: white;
        }

        .user:hover {
            background: rgba(255,255,255,0.2);
        }

        .user-avatar img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
        }

        .user-name {
            flex: 1;
            margin-left: 10px;
        }

        .user-status {
            width: 10px;
            height: 10px;
            background: #4caf50;
            border-radius: 50%;
        }

        .messages-sidebar {
            flex: 1;
            background: #f9f9f9;
            display: flex;
            flex-direction: column;
        }

        .messages-header {
            background: #e53935;
            color: white;
            padding: 10px;
            display: flex;
            align-items: center;
        }

        .messages-header img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .messages-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .message {
            max-width: 60%;
            margin-bottom: 15px;
            padding: 10px 15px;
            border-radius: 20px;
            position: relative;
            word-wrap: break-word;
        }

        .message.received {
            background: #eee;
            align-self: flex-start;
        }

        .message.sent {
            background: #e53935;
            color: white;
            align-self: flex-end;
        }

        .message-time {
            font-size: 0.75rem;
            margin-top: 5px;
            opacity: 0.6;
            text-align: right;
        }

        .message-input {
            display: flex;
            padding: 15px;
            border-top: 1px solid #ddd;
            background: white;
        }

        .message-input textarea {
            flex: 1;
            resize: none;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            height: 40px;
        }

        .send-button {
            background: #e53935;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 0 20px;
            margin-left: 10px;
            cursor: pointer;
        }

        .message-actions {
            position: absolute;
            top: 5px;
            right: -60px;
            display: flex;
            gap: 5px;
        }

        .message:hover .message-actions {
            display: flex;
        }

        .edit-form {
            display: none;
            flex-direction: column;
            margin-top: 5px;
        }

        .edit-form.active {
            display: flex;
        }

        .edit-form textarea {
            width: 100%;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 5px;
        }

        .edit-form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 5px;
        }

        .action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="users-sidebar">
    <div class="users-header">Contacts</div>
    <div class="search-form">
        <input type="text" id="searchInput" placeholder="🔍 Rechercher un contact...">
    </div>
    <div class="user-list">
        <?php foreach ($users as $user): ?>
            <a href="?user_id=<?= htmlspecialchars($user['id']) ?>" class="user">
                <div class="user-avatar">
                    <img src="<?= !empty($user['photo']) ? $user['photo'] : 'https://via.placeholder.com/40' ?>" alt="<?= $user['username'] ?>">
                </div>
                <div class="user-name"><?= htmlspecialchars($user['username']) ?></div>
                <div class="user-status"></div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<div class="messages-sidebar">
    <?php if ($selectedUser): ?>
        <div class="messages-header">
            <img src="<?= !empty($selectedUser['photo']) ? $selectedUser['photo'] : 'https://via.placeholder.com/40' ?>">
            <strong><?= htmlspecialchars($selectedUser['username']) ?></strong>
        </div>

        <div class="messages-container">
            <?php foreach ($messages as $message): 
                $isSent = $message['idsender'] == $_SESSION['user']['id'];
            ?>
                <div class="message <?= $isSent ? 'sent' : 'received' ?>" data-message-id="<?= $message['message_id'] ?>">
                    <div class="message-text"><?= htmlspecialchars($message['content']) ?></div>
                    <div class="message-time"><?= date('H:i', strtotime($message['message_created_at'])) ?></div>
                    <?php if ($isSent): ?>
                        <div class="message-actions">
                            <button class="action-btn update-btn">✏️</button>
                            <a class="action-btn delete-btn" href="deleteMessage.php?id=<?= $message['message_id'] ?>">🗑️</a>
                        </div>
                        <div class="edit-form">
                            <textarea class="edit-textarea"><?= htmlspecialchars($message['content']) ?></textarea>
                            <div class="edit-form-buttons">
                                <button class="cancel-btn">Annuler</button>
                                <button class="save-btn">Enregistrer</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <form action="addmessage.php" method="POST">
            <input type="hidden" name="idreciever" value="<?= $selectedUser['id'] ?>">
            <input type="hidden" name="senderId" value="<?= $_SESSION['user']['id'] ?>">
            <div class="message-input">
                <textarea name="messageInput" placeholder="Écrivez votre message ici..." required></textarea>
                <button class="send-button" type="submit">Envoyer</button>
            </div>
        </form>
    <?php else: ?>
        <div style="margin: auto; text-align:center; color: #999;">Sélectionnez un contact à gauche pour discuter</div>
    <?php endif; ?>
</div>

<script>
    // Recherche dynamique
    document.getElementById('searchInput').addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll('.user').forEach(user => {
            const name = user.querySelector('.user-name').textContent.toLowerCase();
            user.style.display = name.includes(filter) ? '' : 'none';
        });
    });

    // Modifier message
    document.querySelectorAll('.update-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const messageDiv = this.closest('.message');
            messageDiv.querySelector('.edit-form').classList.add('active');
        });
    });

    // Annuler modification
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const messageDiv = this.closest('.message');
            messageDiv.querySelector('.edit-form').classList.remove('active');
        });
    });

    // Sauvegarder modification
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const messageDiv = this.closest('.message');
            const messageId = messageDiv.dataset.messageId;
            const newText = messageDiv.querySelector('.edit-textarea').value;

            fetch('EditMessage.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ messageId: messageId, content: newText })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    messageDiv.querySelector('.message-text').textContent = newText;
                    messageDiv.querySelector('.edit-form').classList.remove('active');
                } else {
                    alert('Erreur lors de la modification');
                }
            });
        });
    });
</script>

</body>
</html>
