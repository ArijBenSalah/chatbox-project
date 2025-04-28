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
            --light-gray: #f5f5f5;
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
            transition: all 0.3s;
        }
        
        .sidebar-header {
            padding: 20px;
            background-color: var(--primary-dark);
        }
        
        .sidebar-menu {
            padding: 0;
            list-style: none;
        }
        
        .sidebar-menu li a {
            display: block;
            padding: 15px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background-color: var(--primary-dark);
        }
        
        .sidebar-menu li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .main-content {
            margin-left: 250px;
            padding: 20px;
            transition: all 0.3s;
        }
        
        /* Chat Container Styles */
        .chat-container {
            display: flex;
            height: calc(100vh - 120px);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .user-item {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: all 0.2s;
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
            position: sticky;
            top: 0;
            z-index: 10;
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
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        
        .message.received .message-bubble {
            background-color: white;
            border-top-left-radius: 5px;
            color: #333;
            border: 1px solid #e0e0e0;
        }
        
        .message.sent .message-bubble {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            border-top-right-radius: 5px;
        }
        
        .message-time {
            font-size: 0.75rem;
            color: #999;
            margin-top: 5px;
        }
        
        .message.sent .message-time {
            color: rgba(255, 255, 255, 0.7);
            text-align: right;
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
            resize: none;
            height: 50px;
            outline: none;
            transition: all 0.3s;
        }
        
        .chat-input textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(179, 0, 0, 0.2);
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
            transition: all 0.3s;
            box-shadow: 0 2px 5px rgba(179, 0, 0, 0.3);
        }
        
        .send-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(179, 0, 0, 0.3);
        }
        
        /* Message Table Styles */
        .message-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .message-table th {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 15px;
            text-align: left;
        }
        
        .message-table td {
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .message-table tr:hover {
            background-color: #f9f9f9;
        }
        
        .badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: normal;
        }
        
        .badge-unread {
            background-color: var(--primary-color);
            color: white;
        }
        
        .action-btns .btn {
            padding: 5px 10px;
            font-size: 0.875rem;
        }
        
        @media (max-width: 992px) {
            .chat-container {
                flex-direction: column;
                height: auto;
            }
            
            .user-list {
                width: 100%;
                height: 300px;
            }
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
            <div class="row mb-4">
                <div class="col-12">
                    <h2 class="mb-0">Gestion des Messages</h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Messagerie</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Conversations</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="chat-container">
                                <!-- User List -->
                                <div class="user-list">
                                    <div class="user-list-header">
                                        <i class="fas fa-users me-2"></i>Contacts
                                    </div>
                                    <div class="user-item active">
                                        <div class="user-avatar">JD</div>
                                        <div>
                                            <div class="fw-bold">John Doe</div>
                                            <small class="text-muted">Dernier message: 12:30</small>
                                        </div>
                                        <span class="badge badge-unread ms-auto">3</span>
                                    </div>
                                    <div class="user-item">
                                        <div class="user-avatar">AS</div>
                                        <div>
                                            <div class="fw-bold">Alice Smith</div>
                                            <small class="text-muted">Dernier message: Hier</small>
                                        </div>
                                    </div>
                                    <div class="user-item">
                                        <div class="user-avatar">MJ</div>
                                        <div>
                                            <div class="fw-bold">Mike Johnson</div>
                                            <small class="text-muted">Dernier message: 2 jours</small>
                                        </div>
                                    </div>
                                    <div class="user-item">
                                        <div class="user-avatar">SW</div>
                                        <div>
                                            <div class="fw-bold">Sarah Williams</div>
                                            <small class="text-muted">Dernier message: 1 semaine</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Chat Area -->
                                <div class="chat-area">
                                    <div class="chat-header">
                                        <div class="user-avatar me-3">JD</div>
                                        <div>
                                            <div class="fw-bold">John Doe</div>
                                            <small>En ligne</small>
                                        </div>
                                    </div>
                                    
                                    <div class="chat-messages">
                                        <div class="d-flex flex-column">
                                            <!-- Received Message -->
                                            <div class="message received">
                                                <div class="message-bubble">
                                                    <div>Bonjour, comment puis-je vous aider aujourd'hui ?</div>
                                                    <div class="message-time">12:30 PM</div>
                                                </div>
                                            </div>
                                            
                                            <!-- Sent Message -->
                                            <div class="message sent">
                                                <div class="message-bubble">
                                                    <div>J'ai un problème avec ma réservation</div>
                                                    <div class="message-time">12:32 PM</div>
                                                </div>
                                            </div>
                                            
                                            <!-- Received Message -->
                                            <div class="message received">
                                                <div class="message-bubble">
                                                    <div>Pouvez-vous me donner plus de détails sur le problème ?</div>
                                                    <div class="message-time">12:33 PM</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="chat-input">
                                        <textarea placeholder="Écrivez votre message ici..."></textarea>
                                        <button class="send-button">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Messages Table -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Tous les Messages</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="message-table">
                                    <thead>
                                        <tr>
                                            <th>Expéditeur</th>
                                            <th>Destinataire</th>
                                            <th>Message</th>
                                            <th>Date</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-2">JD</div>
                                                    <span>John Doe</span>
                                                </div>
                                            </td>
                                            <td>Admin</td>
                                            <td>Bonjour, j'ai un problème avec ma réservation...</td>
                                            <td>12:30 PM</td>
                                            <td><span class="badge badge-unread">Non lu</span></td>
                                            <td class="action-btns">
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-2">AS</div>
                                                    <span>Alice Smith</span>
                                                </div>
                                            </td>
                                            <td>Admin</td>
                                            <td>Merci pour votre aide hier !</td>
                                            <td>Hier</td>
                                            <td><span class="badge bg-secondary">Lu</span></td>
                                            <td class="action-btns">
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="user-avatar me-2">MJ</div>
                                                    <span>Mike Johnson</span>
                                                </div>
                                            </td>
                                            <td>Admin</td>
                                            <td>Je voudrais annuler ma réservation</td>
                                            <td>2 jours</td>
                                            <td><span class="badge bg-secondary">Lu</span></td>
                                            <td class="action-btns">
                                                <button class="btn btn-sm btn-outline-primary me-1">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script pour gérer les interactions de messagerie
        document.addEventListener('DOMContentLoaded', function() {
            // Sélectionner un utilisateur
            const userItems = document.querySelectorAll('.user-item');
            userItems.forEach(item => {
                item.addEventListener('click', function() {
                    userItems.forEach(i => i.classList.remove('active'));
                    this.classList.add('active');
                    // Ici vous chargeriez les messages de cet utilisateur
                });
            });
            
            // Confirmation avant suppression
            const deleteButtons = document.querySelectorAll('.btn-outline-danger');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer ce message ?')) {
                        e.preventDefault();
                    }
                });
            });
            
            // Auto-scroll des messages
            const chatMessages = document.querySelector('.chat-messages');
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
    </script>
</body>
</html>