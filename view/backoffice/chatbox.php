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
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Backoffice - Messagerie</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #b30000;
      --primary-dark: #800000;
      --primary-light: #ff4d4d;
      --secondary-color: #f8f9fa;
      --text-color: #333;
    }
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: var(--secondary-color);
      color: var(--text-color);
    }
    .sidebar {
      width: 250px;
      background-color: var(--primary-color);
      color: white;
      min-height: 100vh;
      position: fixed;
    }
    .sidebar-header {
      padding: 20px;
      background-color: var(--primary-dark);
    }
    .sidebar-menu {
      list-style: none;
      padding: 0;
    }
    .sidebar-menu li a {
      display: block;
      padding: 15px 20px;
      color: white;
      text-decoration: none;
    }
    .sidebar-menu li a:hover,
    .sidebar-menu li a.active {
      background-color: var(--primary-dark);
    }
    .main-content {
      margin-left: 250px;
      padding: 20px;
    }
    .chat-container {
      display: flex;
      height: calc(100vh - 120px);
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .user-list {
      width: 300px;
      background-color: white;
      border-right: 1px solid #e0e0e0;
      overflow-y: auto;
    }
    .user-list-header {
      padding: 15px;
      background-color: var(--primary-color);
      color: white;
      font-weight: bold;
    }
    .user-item {
      padding: 15px;
      border-bottom: 1px solid #f0f0f0;
      cursor: pointer;
      display: flex;
      align-items: center;
    }
    .user-item:hover {
      background-color: #f9f9f9;
    }
    .user-item.active {
      background-color: var(--primary-light);
      color: white;
    }
    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 10px;
      font-weight: bold;
    }
    .chat-area {
      flex: 1;
      display: flex;
      flex-direction: column;
      background-color: #f9f9f9;
    }
    .chat-header {
      padding: 15px;
      background-color: var(--primary-color);
      color: white;
      display: flex;
      align-items: center;
    }
    .chat-messages {
      flex: 1;
      padding: 20px;
      overflow-y: auto;
      background-color: #fff;
    }
    .message {
      margin-bottom: 15px;
      max-width: 70%;
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
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .message.received .message-bubble {
      background-color: white;
      border: 1px solid #e0e0e0;
      color: #333;
    }
    .message.sent .message-bubble {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
      color: white;
    }
    .message-time {
      font-size: 0.75rem;
      color: #999;
      margin-top: 5px;
    }
    .chat-input {
      padding: 15px;
      background-color: white;
      border-top: 1px solid #e0e0e0;
      display: flex;
      align-items: center;
    }
    .chat-input textarea {
      flex: 1;
      padding: 12px 15px;
      border: 1px solid #e0e0e0;
      border-radius: 25px;
      height: 50px;
    }
    .send-button {
      background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
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
    }
    .message-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 30px;
    }
    .message-table th {
      background-color: var(--primary-color);
      color: white;
      padding: 10px;
    }
    .message-table td {
      padding: 10px;
      border: 1px solid #ddd;
    }
    .badge-unread {
      background-color: var(--primary-color);
      color: white;
      padding: 5px 10px;
      border-radius: 20px;
    }
  </style>
</head>
<body>
<div class="sidebar">
  <div class="sidebar-header">
    <h3 class="text-center">Admin Panel</h3>
  </div>
  <ul class="sidebar-menu">
    <li><a href="#"><i class="fas fa-chart-line"></i> Dashboard</a></li>
    <li><a href="#" class="active"><i class="fas fa-comments"></i> Messagerie</a></li>
    <li><a href="#"><i class="fas fa-users"></i> Utilisateurs</a></li>
    <li><a href="#"><i class="fas fa-calendar-check"></i> Réservations</a></li>
    <li><a href="#"><i class="fas fa-sign-out-alt"></i> Déconnexion</a></li>
  </ul>
</div>
<div class="main-content">
  <div class="container-fluid">
    <div class="chat-container">
      <div class="user-list">
        <div class="user-list-header">
          <i class="fas fa-users me-2"></i>Contacts
        </div>
        <?php foreach ($users as $user): ?>
          <a href="?user_id=<?php echo $user['id']; ?>" class="user-item <?php echo ($selectedUserId == $user['id']) ? 'active' : ''; ?>">
            <div class="user-avatar">
              <?php echo strtoupper(substr($user['username'], 0, 2)); ?>
            </div>
            <div>
              <div class="fw-bold"><?php echo htmlspecialchars($user['username']); ?></div>
              <small class="text-muted">Connecté</small>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
      <div class="chat-area">
        <div class="chat-header">
          <?php if ($selectedUser): ?>
            <div class="user-avatar me-3">
              <?php echo strtoupper(substr($selectedUser['username'], 0, 2)); ?>
            </div>
            <div>
              <div class="fw-bold"><?php echo htmlspecialchars($selectedUser['username']); ?></div>
              <small>En ligne</small>
            </div>
          <?php endif; ?>
        </div>
        <div class="chat-messages">
          <?php foreach ($messages as $msg): ?>
            <?php $isSent = $msg['idsender'] == 1; ?>
            <div class="message <?php echo $isSent ? 'sent' : 'received'; ?>">
              <div class="message-bubble">
                <?php echo htmlspecialchars($msg['content']); ?>
              </div>
              <div class="message-time">
                <?php echo date("H:i", strtotime($msg['message_created_at'])); ?>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
        <form action="addmessage.php" method="POST">
        <input type="hidden" name="idreciever" value="<?php echo $selectedUserId; ?>">
        <div class="chat-input">
          <textarea placeholder="Écrivez votre message ici..." name="messageInput"></textarea>
          <button class="send-button">
            <i class="fas fa-paper-plane"></i>
          </button>
        </div>
        </form>
      </div>
    </div>

    
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
