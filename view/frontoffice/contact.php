<?php
require_once(__DIR__ . "/../../controller/chatboxcontroller.php");
require_once(__DIR__ . "/../../controller/messageController.php");
require_once(__DIR__ . "/../../model/message.php");
require_once(__DIR__ . "/../../config.php");

session_start();

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$sponsor = new chatboxcontroller();
$chatboxes = $sponsor->listeChatbox();
$messagesController = new messageController();
$messages = $messagesController->listeMessages();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ChatBox - Share a ride</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --accent-color: #4895ef;
      --light-color: #f8f9fa;
      --dark-color: #212529;
      --success-color: #4cc9f0;
      --warning-color: #f72585;
      --gray-color: #adb5bd;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
    }

    body {
      background-color: #f5f7fb;
      color: var(--dark-color);
      display: grid;
      grid-template-columns: 300px 1fr;
      grid-template-rows: auto 1fr;
      height: 100vh;
    }

    .sidebar {
      grid-row: 1 / -1;
      background: white;
      border-right: 1px solid #e0e0e0;
      padding: 20px;
      overflow-y: auto;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
    }

    .sidebar h2 {
      color: var(--primary-color);
      margin-bottom: 20px;
      font-size: 1.3rem;
      padding-bottom: 10px;
      border-bottom: 1px solid #eee;
    }

    .user {
      display: flex;
      align-items: center;
      padding: 10px;
      margin-bottom: 10px;
      border-radius: 8px;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .user:hover {
      background-color: #f0f4ff;
    }

    .user img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 10px;
      border: 2px solid var(--accent-color);
    }

    .user span {
      font-weight: 500;
      color: var(--dark-color);
    }

    .chat-container {
      display: grid;
      grid-template-rows: auto 1fr auto;
      height: 100vh;
    }

    .chat-header {
      background: white;
      padding: 15px 20px;
      border-bottom: 1px solid #e0e0e0;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .chat-header h2 {
      color: var(--primary-color);
      font-size: 1.2rem;
    }

    .messages {
      padding: 15px;
      overflow-y: auto;
      background-color: #f5f7fb;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .message {
      max-width: 25%;
      padding: 10px 15px;
      border-radius: 18px;
      line-height: 1.4;
      position: relative;
      word-wrap: break-word;
      transition: all 0.3s ease;
      animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(5px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .message.sent {
      align-self: flex-end;
      background-color: var(--primary-color);
      color: white;
      margin-left: auto;
      border-bottom-right-radius: 4px;
      border-bottom-left-radius: 18px;
    }

    .message.received {
      align-self: flex-start;
      background-color: white;
      color: var(--dark-color);
      margin-right: auto;
      border-bottom-left-radius: 4px;
      border-bottom-right-radius: 18px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .message-container {
      position: relative;
      margin-bottom: 10px;
    }

    .message-actions {
      display: flex;
      gap: 8px;
      justify-content: flex-end;
      padding-right: 10px;
      margin-top: 5px;
      opacity: 0;
      transform: translateY(-5px);
      transition: all 0.3s ease;
    }

    .message-container:hover .message-actions {
      opacity: 1;
      transform: translateY(0);
    }

    .edit-message,
    .delete-message {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 5px 10px;
      border-radius: 15px;
      text-decoration: none;
      color: var(--gray-color);
      background: white;
      border: 1px solid rgba(0, 0, 0, 0.1);
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      font-size: 0.8rem;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .edit-message:hover {
      color: white;
      background: var(--accent-color);
      border-color: var(--accent-color);
      transform: translateY(-2px);
      text-decoration: none;
    }

    .delete-message:hover {
      color: white;
      background: var(--warning-color);
      border-color: var(--warning-color);
      transform: translateY(-2px);
      text-decoration: none;
    }

    .edit-form {
      display: flex;
      gap: 8px;
      align-items: center;
      padding: 10px 15px;
      background: white;
      border-radius: 18px;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      max-width: 70%;
      margin-left: auto;
    }

    .edit-input {
      flex: 1;
      padding: 8px 12px;
      border: 1px solid #e0e0e0;
      border-radius: 15px;
      outline: none;
      font-family: 'Poppins', sans-serif;
    }

    .save-edit,
    .cancel-edit {
      padding: 6px 12px;
      border: none;
      border-radius: 15px;
      cursor: pointer;
      font-size: 0.8rem;
      transition: all 0.3s;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .save-edit {
      background: var(--success-color);
      color: white;
    }

    .save-edit:hover {
      background: #3aa8d8;
      transform: translateY(-2px);
    }

    .cancel-edit {
      background: #f0f0f0;
      color: var(--dark-color);
    }

    .cancel-edit:hover {
      background: #e0e0e0;
      transform: translateY(-2px);
    }

    .input-area {
      background: white;
      padding: 15px 20px;
      border-top: 1px solid #e0e0e0;
      display: flex;
      gap: 10px;
    }

    .input-area input {
      flex: 1;
      padding: 12px 15px;
      border: 1px solid #e0e0e0;
      border-radius: 24px;
      outline: none;
      font-size: 0.95rem;
    }

    .input-area button {
      background-color: var(--primary-color);
      color: white;
      border: none;
      border-radius: 24px;
      padding: 0 20px;
      cursor: pointer;
      transition: background 0.3s;
    }

    .input-area button:hover {
      background-color: var(--secondary-color);
    }

    .data-container {
      grid-column: 2;
      background: white;
      margin: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      overflow: hidden;
    }

    .data-header {
      padding: 20px;
      border-bottom: 1px solid #eee;
    }

    .chatbox-table {
      width: 100%;
      border-collapse: collapse;
    }

    .chatbox-table th,
    .chatbox-table td {
      padding: 12px 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }

    .chatbox-table th {
      background: var(--primary-color);
      color: white;
    }

    .action-btn {
      padding: 8px 12px;
      border-radius: 6px;
      border: none;
      background-color: var(--accent-color);
      color: white;
      cursor: pointer;
    }

    @media (max-width: 992px) {
      body {
        grid-template-columns: 1fr;
        grid-template-rows: auto auto 1fr;
      }

      .sidebar {
        grid-column: 1;
        grid-row: 1;
      }

      .chat-container {
        grid-column: 1;
        grid-row: 2;
      }

      .data-container {
        grid-column: 1;
        grid-row: 3;
        margin: 0;
      }
    }

    /* Confirmation dialog styles */
    .confirm-dialog {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: rgba(0, 0, 0, 0.5);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
    }

    .dialog-content {
      background: white;
      padding: 25px;
      border-radius: 12px;
      max-width: 320px;
      width: 90%;
      text-align: center;
      box-shadow: 0 5px 25px rgba(0, 0, 0, 0.2);
    }

    .dialog-content p {
      margin-bottom: 20px;
      color: var(--dark-color);
    }

    .dialog-buttons {
      display: flex;
      gap: 12px;
      justify-content: center;
    }

    .confirm-yes,
    .confirm-no {
      padding: 10px 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: all 0.3s;
      font-weight: 500;
    }

    .confirm-yes {
      background: var(--warning-color);
      color: white;
    }

    .confirm-no {
      background: var(--gray-color);
      color: white;
    }

    .confirm-yes:hover {
      background: #e61473;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(247, 37, 133, 0.3);
    }

    .confirm-no:hover {
      background: #9ca3af;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(156, 163, 175, 0.3);
    }
  </style>
</head>

<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <h2><i class="fas fa-users"></i> Utilisateurs</h2>
    <?php
    try {
      $db = config::getConnexion();
      $req = $db->prepare("SELECT * FROM users");
      $req->execute();
      $users = $req->fetchAll(PDO::FETCH_ASSOC);
      foreach ($users as $user) {
        echo '<div class="user">';
        echo '<img src="' . htmlspecialchars($user['photo']) . '" alt="User">';
        echo '<span>' . htmlspecialchars($user['username']) . '</span>';
        echo '</div>';
      }
    } catch (PDOException $e) {
      echo '<div class="error">Erreur: ' . $e->getMessage() . '</div>';
    }
    ?>
  </div>

  <!-- Chat Area -->
  <div class="chat-container">
    <div class="chat-header">
      <h2><i class="fas fa-comment-dots"></i> Discussion</h2>
    </div>

    <div id="messages" class="messages">
      <?php
      if (!empty($messages)) {
        foreach ($messages as $message) {
          $messageClass = ($message['sender_id'] ?? 0) == ($_SESSION['user_id'] ?? 0) ? 'sent' : 'received';
          echo '<div class="message-container" data-message-id="' . htmlspecialchars($message['id']) . '">';
          echo '<div class="message ' . $messageClass . '">';
          echo htmlspecialchars($message['content']);
          echo '</div>';

          // Add action buttons only for user's own messages
          if (($message['sender_id'] ?? 0) == ($_SESSION['user_id'] ?? 0)) {
            echo '<div class="message-actions">';
            echo '<a href="" class="edit-message" title="Modifier">';
            echo '<i class="fas fa-edit"></i> <span>Modifier</span>';
            echo '</a>';
            // Delete form with CSRF protection
            echo '<form action="deleteMessage.php" method="POST" class="delete-form" style="display:inline;">';
            echo '<input type="hidden" name="id" value="' . htmlspecialchars($message['id']) . '">';
            echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['csrf_token'] . '">';
            echo '<button type="submit" class="delete-message" title="Supprimer" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce message?\')">';
            echo '<i class="fas fa-trash"></i> <span>Supprimer</span>';
            echo '</button>';
            echo '</form>';

            echo '</div>';
          }
          echo '</div>';
        }
      }
      ?>
    </div>

    <form action="addMessage.php" method="post" class="input-area">
      <input name="messageInput" type="text" placeholder="Tape ton message..." required>
      <button type="submit"><i class="fas fa-paper-plane"></i> Envoyer</button>
    </form>
  </div>

  <!-- Data Table -->
  <div class="data-container">
    <div class="data-header">
      <h2><i class="fas fa-table"></i> ChatBox Disponibles</h2>
    </div>

    <div class="chatbox-table-container">
      <table class="chatbox-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Utilisateur</th>
            <th>Dernier Message</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          foreach ($chatboxes as $chat) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($chat['idChatbox']) . '</td>';
            echo '<td>' . htmlspecialchars($chat['name']) . '</td>';
            echo '<td>' . htmlspecialchars($chat['created_at']) . '</td>';
            echo '<td><button class="action-btn"><i class="fas fa-eye"></i> Voir</button></td>';
            echo '</tr>';
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
  <script>
  const csrfToken = '<?= $_SESSION['csrf_token'] ?>';
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit message functionality
    document.querySelectorAll('.edit-message').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const messageContainer = this.closest('.message-container');
            const messageId = messageContainer.getAttribute('data-message-id');
            const messageContent = messageContainer.querySelector('.message').textContent;
            const isSent = messageContainer.querySelector('.message').classList.contains('sent');

            // Create edit form
            const editForm = document.createElement('form');
            editForm.className = 'edit-form';
            editForm.style.background = isSent ? 'var(--primary-color)' : 'white';
            editForm.innerHTML = `
                <input type="hidden" name="id" value="${messageId}">
                <input type="hidden" name="csrf_token" value="${csrfToken}">
                <input type="text" name="content" value="${messageContent.trim()}" class="edit-input" 
                       style="background: ${isSent ? 'rgba(255,255,255,0.9)' : 'white'}">
                <button type="submit" class="save-edit" 
                        style="background: ${isSent ? 'white' : 'var(--success-color)'}; 
                               color: ${isSent ? 'var(--primary-color)' : 'white'}">
                    <i class="fas fa-check"></i>
                </button>
                <button type="button" class="cancel-edit" 
                        style="background: ${isSent ? 'rgba(255,255,255,0.2)' : '#f0f0f0'}">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            // Add animation when replacing
            const messageElement = messageContainer.querySelector('.message');
            messageElement.style.transform = 'scale(0.95)';
            messageElement.style.opacity = '0';
            
            setTimeout(() => {
                messageElement.replaceWith(editForm);
                editForm.querySelector('.edit-input').focus();
            }, 200);

            // Handle form submission
            editForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const newContent = this.querySelector('.edit-input').value.trim();

                if (newContent === '') return;

                try {
                    const formData = new URLSearchParams();
                    formData.append('id', messageId);
                    formData.append('content', newContent);
                    formData.append('csrf_token', csrfToken);

                    const response = await fetch('EditMessage.php', {
    method: 'POST',
    body: formData // ✅ LET THE BROWSER HANDLE Content-Type
});


                    const data = await response.json();
                    console.log("Response from server:", data);

                    if (data.success) {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message ' + (isSent ? 'sent' : 'received');
                        messageDiv.textContent = newContent;
                        messageDiv.style.opacity = '0';
                        messageDiv.style.transform = 'scale(0.95)';
                        editForm.replaceWith(messageDiv);

                        // Animate the new message in
                        setTimeout(() => {
                            messageDiv.style.opacity = '1';
                            messageDiv.style.transform = 'scale(1)';
                        }, 10);
                    } else {
                        alert(data.error || 'Error updating message');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert('Error updating message');
                }
            });

            // Handle cancel with animation
            editForm.querySelector('.cancel-edit').addEventListener('click', function() {
                const messageDiv = document.createElement('div');
                messageDiv.className = 'message ' + (isSent ? 'sent' : 'received');
                messageDiv.textContent = messageContent;
                messageDiv.style.opacity = '0';
                messageDiv.style.transform = 'scale(0.95)';
                editForm.style.transform = 'scale(0.95)';
                editForm.style.opacity = '0';

                setTimeout(() => {
                    editForm.replaceWith(messageDiv);

                    setTimeout(() => {
                        messageDiv.style.opacity = '1';
                        messageDiv.style.transform = 'scale(1)';
                    }, 10);
                }, 200);
            });
        });
    });
});
</script></body>

</html>