<?php
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . "/../../controller/userController.php");
require_once(__DIR__ . "/../../controller/messageController.php");
session_start();
$userController = new userController();
$messageController = new messageController();
$users = $userController->getOtherUsers($_SESSION['user']['username']);


// Get the selected user ID from URL parameter
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Box - Design Moderne</title>
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            display: flex;
            height: 100vh;
            background-color: #f5f5f5;
        }
        
        /* Left Sidebar - Users */
        .users-sidebar {
            width: 300px;
            background: linear-gradient(135deg, #d32f2f, #b71c1c);
            color: white;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }
        
        .users-header {
            padding: 20px;
            background-color: #b71c1c;
            font-size: 1.2rem;
            font-weight: 600;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 20;
        }
        
        .user {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .user:before {
            content: '';
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.4s ease;
        }
        
        .user:hover:before {
            left: 0;
        }
        
        .user.active {
            background-color: rgba(0, 0, 0, 0.2);
            border-left: 4px solid white;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .user-name {
            flex: 1;
        }
        
        .user-status {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #4caf50;
        }
        
        /* Right Sidebar - Messages */
        .messages-sidebar {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: white;
        }
        
        .messages-header {
            padding: 15px 20px;
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .header-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .header-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .header-info {
            flex: 1;
        }
        
        .header-status {
            font-size: 0.8rem;
            opacity: 0.8;
        }
        
        .messages-container {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background-color: #f9f9f9;
            background-image: radial-gradient(#e0e0e0 1px, transparent 1px);
            background-size: 15px 15px;
            display: flex;
            flex-direction: column;
        }
        
        .message {
            margin-bottom: 15px;
            max-width: 70%;
            position: relative;
            transform: translateY(20px);
            opacity: 0;
            animation: fadeInUp 0.4s ease forwards;
        }
        
        @keyframes fadeInUp {
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .message.received {
            align-self: flex-start;
        }
        
        .message.sent {
            align-self: flex-end;
        }
        
        .message-bubble {
            padding: 12px 15px;
            border-radius: 18px;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .message.received .message-bubble {
            background-color: white;
            border-top-left-radius: 5px;
            color: #333;
        }
        
        .message.sent .message-bubble {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            border-top-right-radius: 5px;
        }
        
        .message-actions {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            display: none;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            z-index: 10;
            animation: slideIn 0.2s ease-out forwards;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-50%) translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateY(-50%) translateX(0);
            }
        }
        
        .message:hover .message-actions {
            display: flex;
        }
        
        .message.received .message-actions {
            right: -60px;
        }
        
        .message.sent .message-actions {
            left: -60px;
        }
        
        .action-btn {
            padding: 8px 12px;
            background: none;
            border: none;
            cursor: pointer;
            color: #666;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }
        
        .action-btn:hover {
            color: #d32f2f;
            background-color: #f5f5f5;
        }
        
        /* Edit Form Styles */
        .edit-form-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        
        .edit-form {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 18px;
            padding: 12px;
            display: flex;
            flex-direction: column;
            transform: translateY(10px);
            opacity: 0;
            transition: all 0.3s ease;
        }
        
        .edit-form.active {
            transform: translateY(0);
            opacity: 1;
        }
        
        .edit-form textarea {
            flex: 1;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 10px;
            resize: none;
            margin-bottom: 8px;
            font-family: inherit;
            font-size: inherit;
        }
        
        .edit-form-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        
        .edit-form button {
            padding: 6px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        
        .edit-form .save-btn {
            background-color: #4caf50;
            color: white;
        }
        
        .edit-form .save-btn:hover {
            background-color: #3e8e41;
        }
        
        .edit-form .cancel-btn {
            background-color: #f44336;
            color: white;
        }
        
        .edit-form .cancel-btn:hover {
            background-color: #d32f2f;
        }
        
        /* Message content transition */
        .message-content-container {
            position: relative;
            transition: all 0.3s ease;
        }
        
        .message-bubble {
            transition: all 0.3s ease;
        }
        
        .message.editing .message-bubble {
            opacity: 0;
            transform: scale(0.95);
        }
        
        .message.editing .edit-form {
            transform: translateY(0);
            opacity: 1;
        }
        
        .message-info {
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        
        .message-sender {
            font-weight: bold;
            margin-right: 8px;
        }
        
        .message-time {
            font-size: 0.75rem;
            color: #999;
        }
        
        .message.sent .message-time {
            color: rgba(255, 255, 255, 0.7);
            text-align: right;
            margin-top: 5px;
        }
        
        .message-input {
            padding: 15px;
            background-color: white;
            border-top: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
        }
        
        .message-input textarea {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 25px;
            resize: none;
            height: 50px;
            outline: none;
            transition: all 0.3s;
        }
        
        .message-input textarea:focus {
            border-color: #f44336;
            box-shadow: 0 0 0 2px rgba(244, 67, 54, 0.2);
        }
        
        .send-button {
            background: linear-gradient(135deg, #f44336, #d32f2f);
            color: white;
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-left: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(244, 67, 54, 0.3);
        }
        
        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(244, 67, 54, 0.3);
        }
        
        /* Scrollbar styling */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* No conversation selected */
        .no-conversation {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            color: #777;
            text-align: center;
            padding: 20px;
        }
        
        .no-conversation i {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #d32f2f;
        }
    </style>
</head>
<body>
    <!-- Left sidebar - Users -->
    <div class="users-sidebar">
        <div class="users-header">Contacts</div>
        <?php foreach ($users as $user): ?>
        <a href="?user_id=<?php echo $user['id']; ?>" class="user <?php echo ($selectedUserId == $user['id']) ? 'active' : ''; ?>">
            <div class="user-avatar">
                <img src="<?php echo !empty($user['photo']) ? $user['photo'] : 'https://via.placeholder.com/40'; ?>" alt="<?php echo $user['username']; ?>">
            </div>
            <div class="user-name"><?php echo htmlspecialchars($user['username']); ?></div>
            <div class="user-status"></div>
        </a>
        <?php endforeach; ?>
    </div>
    
    <!-- Right sidebar - Messages -->
    <div class="messages-sidebar">
        <?php if ($selectedUser): ?>
            <div class="messages-header">
                <div class="header-avatar">
                    <img src="<?php echo !empty($selectedUser['photo']) ? $selectedUser['photo'] : 'https://via.placeholder.com/40'; ?>" alt="<?php echo $selectedUser['username']; ?>">
                </div>
                <div class="header-info">
                    <div><?php echo $selectedUser['username']; ?></div>
                    <div class="header-status">En ligne</div>
                </div>
            </div>
            
            <div class="messages-container">
                <?php if (empty($messages)): ?>
                    <div class="no-messages" style="text-align: center; margin-top: 50px; color: #777;">
                        Aucun message échangé pour le moment. Envoyez votre premier message !
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $message): 
                        $isSent = $message['idsender'] == $_SESSION['user']['id'];
                        $sender = $isSent ? null : $userController->getUserById($message['idsender']);
                    ?>
                        <div class="message <?php echo $isSent ? 'sent' : 'received'; ?>" style="animation-delay: 0.1s" data-message-id="<?php echo $message['message_id']; ?>">
                            <?php if (!$isSent): ?>
                                <div class="message-info">
                                    <div class="message-sender"><?php echo $sender['username']; ?></div>
                                    <div class="message-time"><?php echo date('H:i', strtotime($message['message_created_at'])); ?></div>
                                </div>
                            <?php endif; ?>
                            <div class="message-content-container">
                                <div class="message-bubble">
                                    <div class="message-text"><?php echo htmlspecialchars($message['content']); ?></div>
                                    <?php if ($isSent): ?>
                                        <div class="message-time"><?php echo date('H:i', strtotime($message['message_created_at'])); ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="edit-form-container">
                                    <div class="edit-form">
                                        <textarea class="edit-textarea"><?php echo htmlspecialchars($message['content']); ?></textarea>
                                        <div class="edit-form-buttons">
                                            <button class="cancel-btn">Annuler</button>
                                            <button class="save-btn">Enregistrer</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="message-actions">
                                <button class="action-btn update-btn">✏️</button>
                                <a class="action-btn delete-btn" href="deleteMessage.php?id=<?=htmlspecialchars($message['message_id'])?>">🗑️</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <form action="addmessage.php" method="POST">
                <input type="hidden" name="idreciever" value="<?php echo $selectedUser['id']; ?>">
                <div class="message-input">
                    <input type="text" name="senderId" value ="<?php echo $_SESSION['user']['id']; ?>" hidden>
                    <textarea name="messageInput" placeholder="Écrivez votre message ici..." required></textarea>
                    <button type="submit" class="send-button">➤</button>
                </div>
            </form>
        <?php else: ?>
            <div class="no-conversation">
                <i>💬</i>
                <h2>Aucune conversation sélectionnée</h2>
                <p>Sélectionnez un contact pour commencer à discuter</p>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        const messagesContainer = document.querySelector('.messages-container');
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
        
        // Highlight active user in sidebar
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        
        if (userId) {
            const userElements = document.querySelectorAll('.user');
            userElements.forEach(user => {
                user.classList.remove('active');
                if (user.getAttribute('href').includes(`user_id=${userId}`)) {
                    user.classList.add('active');
                }
            });
        }
        
        // Edit message functionality
        document.querySelectorAll('.update-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const message = this.closest('.message');
                const editForm = message.querySelector('.edit-form');
                const messageText = message.querySelector('.message-text').textContent;
                
                // Toggle editing state
                message.classList.add('editing');
                editForm.classList.add('active');
                editForm.querySelector('.edit-textarea').value = messageText;
                editForm.querySelector('.edit-textarea').focus();
            });
        });
        
        // Cancel edit
        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const message = this.closest('.message');
                message.classList.remove('editing');
                message.querySelector('.edit-form').classList.remove('active');
            });
        });
        
        // Save edit
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const message = this.closest('.message');
                const messageId = message.dataset.messageId;
                const newText = message.querySelector('.edit-textarea').value;
                
                // Update the message text
                message.querySelector('.message-text').textContent = newText;
                
                // Remove editing state
                message.classList.remove('editing');
                message.querySelector('.edit-form').classList.remove('active');
                
                // Here you would typically save to the server
                console.log(`Saving message ${messageId}: ${newText}`);
                
                fetch('EditMessage.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        messageId: messageId,
                        content: newText
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert('Error saving message');
                        // Revert the change if needed
                    }
                });
                
            });
        });
        
        // Close edit form when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.edit-form') && !e.target.closest('.update-btn')) {
                document.querySelectorAll('.message.editing').forEach(msg => {
                    msg.classList.remove('editing');
                    msg.querySelector('.edit-form').classList.remove('active');
                });
            }
        });
    </script>
</body>
</html>