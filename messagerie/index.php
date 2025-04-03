
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messagerie</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background-color: #f8f5eb;
        }

        .container {
            width: 90%;
            max-width: 1000px;
            margin: 20px auto;
            background:rgb(175, 174, 172);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: #dfd3b8;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 18px;
            font-weight: bold;
        }

        .header .user-info {
            display: flex;
            align-items: center;
        }

        .header .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .header .actions {
            display: flex;
            gap: 15px;
        }

        .header .actions i {
            font-size: 20px;
            cursor: pointer;
        }

        .content {
            display: flex;
            height: 500px;
        }

        .sidebar {
            width: 30%;
            background: #ede0c4;
            padding: 15px;
            border-right: 2px solid #ccc;
            overflow-y: auto;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 8px;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .search-bar input {
            border: none;
            outline: none;
            width: 100%;
            padding: 5px;
        }

        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 15px;
        }

        .tabs button {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
        }

        .tabs .active {
            background: #f4a836;
            color: white;
        }

        .contact-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .contact {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 10px;
            border-radius: 10px;
            cursor: pointer;
        }

        .contact img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .chat {
            flex: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background: #fdfaf3;
        }

        .messages {
            flex: 1;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .message {
            max-width: 60%;
            padding: 10px;
            border-radius: 10px;
            background: white;
            align-self: flex-start;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .input-area {
            display: flex;
            align-items: center;
            background: #fff;
            padding: 10px;
            border-radius: 20px;
        }

        .input-area input {
            flex: 1;
            border: none;
            outline: none;
            padding: 10px;
        }

        .input-area button {
            background: #f4a836;
            border: none;
            padding: 10px 15px;
            color: white;
            border-radius: 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="user-info">
                <img src="user.png" alt="Avatar">
                Anonyme 1257
            </div>
            <div class="actions">
                <i>📞</i>
                <i>📹</i>
                <i>⚙️</i>
            </div>
        </div>

        <div class="content">
            <div class="sidebar">
                <div class="search-bar">
                    <input type="text" placeholder="Rechercher dans les discussions">
                </div>

                <div class="tabs">
                    <button class="active">Messagerie</button>
                    <button>Communautés</button>
                </div>

                <div class="contact-list">
                    <div class="contact">
                        <img src="user.png" alt="Avatar">
                        <span>Anonyme 1257</span>
                    </div>
                    <div class="contact">
                        <img src="expert.png" alt="Avatar">
                        <span>Expert 1024</span>
                    </div>
                </div>
            </div>

            <div class="chat">
                <div class="messages">
                    <div class="message">Comment faire lorsque je veux faire une chose légal</div>
                    <div class="message">Comment faire lorsque je veux faire une chose légal</div>
                    <div class="message">Comment faire lorsque je veux faire une chose légal</div>
                    <div class="message">Comment faire lorsque je veux faire une chose légal</div>
                </div>

                <div class="input-area">
                    <input type="text" placeholder="Aa...">
                    <button>Envoyer</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
