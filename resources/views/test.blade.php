<!-- <!DOCTYPE html>
<html>
<head>
    <title>Chat Test</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
</head>
<body>
    <h2>💬 Real-time Chat Test</h2>
    
    <div>
        <input type="text" id="token" placeholder="Enter your token" style="width: 300px;" value="SIRsBpnqrjqNXd6Az5jDSRSWwgWy3jQwkByWUq">
        <input type="number" id="userId" placeholder="Your User ID" value="3">
        <input type="number" id="otherUserId" placeholder="Other User ID" value="2">
        <button onclick="createOrGetChat()">Create/Get Chat</button>
    </div>

    <div id="chatInfo" style="margin: 10px 0; padding: 10px; background: #f0f0f0; display: none;">
        Chat ID: <span id="currentChatId"></span>
        <button onclick="connectWebSocket()">Connect WebSocket</button>
    </div>

    <div style="margin: 20px 0;">
        <input type="text" id="messageInput" placeholder="Type your message" style="width: 250px;">
        <button onclick="sendMessage()">Send Message</button>
    </div>

    <div id="messages" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;"></div>

    <script>
        let currentChatId = null;
        let currentToken = null;
        let echoInstance = null;

        async function createOrGetChat() {
            const token = document.getElementById('token').value;
            const otherUserId = document.getElementById('otherUserId').value;
            currentToken = token;

            try {
                const response = await axios.get(`http://localhost:8000/api/chat/with/${otherUserId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token
                    }
                });

                currentChatId = response.data.data.id;
                document.getElementById('currentChatId').textContent = currentChatId;
                document.getElementById('chatInfo').style.display = 'block';

                addMessageToUI(`✅ Chat created with ID: ${currentChatId}`, 'system');

            } catch (error) {
                console.error('Error creating chat:', error);
                addMessageToUI('❌ Error creating chat: ' + error.response?.data?.message, 'system');
            }
        }

        function connectWebSocket() {
            if (!currentChatId) {
                alert('Create a chat first!');
                return;
            }

            // ✅ استخدام Echo الرسمي
            echoInstance = new Echo({
                broadcaster: 'pusher',
                key: '0twda2sq69tf8fhxsluh',
                wsHost: 'localhost',
                wsPort: 8080,
                wssPort: 8080,
                forceTLS: false,
                enabledTransports: ['ws', 'wss'],
                cluster: 'mt1', // مطلوب من Pusher
                auth: {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken
                    }
                }
            });

            // الاستماع للرسائل
            echoInstance.private('chat.' + currentChatId)
                .listen('MessageSent', (e) => {
                    addMessageToUI(e.message, 'received');
                    console.log('📩 Received message:', e.message);
                })
                .listen('MessageRead', (e) => {
                    console.log('✅ Message read:', e.messageId);
                    // تحديث حالة القراءة في الواجهة
                });

            addMessageToUI('✅ Connected to WebSocket', 'system');
        }

        async function sendMessage() {
            if (!currentChatId) {
                alert('Create a chat first!');
                return;
            }

            const message = document.getElementById('messageInput').value;
            const otherUserId = document.getElementById('otherUserId').value;

            try {
                const response = await axios.post('http://localhost:8000/api/chat/messages/send', {
                    chat_id: currentChatId,
                    type: 'text',
                    body: message,
                    receiver_id: parseInt(otherUserId)
                }, {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken,
                        'Content-Type': 'application/json'
                    }
                });

                addMessageToUI(response.data.data, 'sent');
                document.getElementById('messageInput').value = '';
                console.log('📤 Message sent:', response.data);
            } catch (error) {
                console.error('❌ Error:', error.response?.data);
                addMessageToUI('❌ Error sending message: ' + error.response?.data?.message, 'system');
            }
        }

        function addMessageToUI(message, type) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');

            if (type === 'system') {
                messageDiv.innerHTML = `<div style="color: gray; text-align: center;">${message}</div>`;
            } else {
                const senderName = (type === 'sent') ? 'You' : (message.sender?.name || 'Other');
                const time = new Date().toLocaleTimeString();
                messageDiv.innerHTML = `
                    <div style="margin: 5px; padding: 8px; background: ${type === 'sent' ? '#dcf8c6' : '#fff'}; 
                                border-radius: 10px; ${type === 'sent' ? 'margin-left: 50px;' : 'margin-right: 50px;'}">
                        <strong>${senderName}:</strong> ${message.body || '📎 Attachment'}
                        <div style="font-size: 12px; color: gray;">${time}</div>
                    </div>
                `;
            }

            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // تنظيف عند إغلاق الصفحة
        window.addEventListener('beforeunload', function() {
            if (echoInstance) {
                echoInstance.disconnect();
            }
        });
    </script>
</body>
</html> -->

<!DOCTYPE html>
<html>

<head>
    <title>Chat Test - Reverb</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.socket.io/4.7.2/socket.io.min.js"></script>
</head>

<body>
    <h2>💬 Real-time Chat Test (Reverb)</h2>

    <div>
        <input type="text" id="token" placeholder="Enter your token" style="width: 300px;" value="SIRsBpnqrjqNXd6Az5jDSRSWwgWy3jQwkByWUq">
        <input type="number" id="userId" placeholder="Your User ID" value="3">
        <input type="number" id="otherUserId" placeholder="Other User ID" value="2">
        <button onclick="createOrGetChat()">Create/Get Chat</button>
    </div>

    <div id="chatInfo" style="margin: 10px 0; padding: 10px; background: #f0f0f0; display: none;">
        Chat ID: <span id="currentChatId"></span>
        <button onclick="connectWebSocket()">Connect WebSocket</button>
        <button onclick="disconnectWebSocket()" style="margin-left: 10px;">Disconnect</button>
    </div>

    <div style="margin: 20px 0;">
        <input type="text" id="messageInput" placeholder="Type your message" style="width: 250px;">
        <button onclick="sendMessage()">Send Message</button>
    </div>

    <div id="connectionStatus" style="padding: 5px; margin: 10px 0; text-align: center;"></div>

    <div id="messages" style="border: 1px solid #ccc; padding: 10px; height: 300px; overflow-y: scroll;"></div>

    <script>
        let currentChatId = null;
        let currentToken = null;
        let socket = null;
        let isConnected = false;

        async function createOrGetChat() {
            const token = document.getElementById('token').value;
            const otherUserId = document.getElementById('otherUserId').value;
            currentToken = token;

            try {
                const response = await axios.get(`http://localhost:8000/api/chat/with/${otherUserId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });

                currentChatId = response.data.data.id;
                document.getElementById('currentChatId').textContent = currentChatId;
                document.getElementById('chatInfo').style.display = 'block';

                addMessageToUI(`✅ Chat created with ID: ${currentChatId}`, 'system');
            } catch (error) {
                console.error('Error creating chat:', error);
                addMessageToUI('❌ Error creating chat: ' + (error.response?.data?.message || error.message), 'system');
            }
        }

        function connectWebSocket() {
            try {
                if (!currentChatId) {
                    alert('Create a chat first!');
                    return;
                }

                if (isConnected) {
                    addMessageToUI('⚠️ Already connected to WebSocket', 'system');
                    return;
                }

                const reverbHost = '127.0.0.1';
                const reverbPort = '6001';

                socket = io(`http://${reverbHost}:${reverbPort}`, {
                    transports: ['websocket'],
                    auth: {
                        token: currentToken
                    }
                });

                socket.on('connect', function() {
                    console.log('✅ Connected to Reverb WebSocket');
                    isConnected = true;
                    updateConnectionStatus('🟢 Connected', 'green');
                    addMessageToUI('✅ Connected to Reverb server', 'system');

                    socket.emit('subscribe', {
                        channel: `private-chat.${currentChatId}`,
                        auth: currentToken
                    });
                });

                socket.on('subscription_succeeded', function(data) {
                    console.log('✅ Subscription succeeded:', data);
                    addMessageToUI('✅ Subscribed to chat channel', 'system');
                });

                socket.on('MessageSent', function(data) {
                    console.log('📩 Message received:', data);
                    addMessageToUI(data, 'received');
                });

                socket.on('error', function(error) {
                    console.error('❌ WebSocket error:', error);
                    updateConnectionStatus('🔴 Error', 'red');
                    addMessageToUI('❌ WebSocket error', 'system');
                });

                socket.on('connect_error', function(error) {
                    console.error('❌ Connection failed:', error);
                    updateConnectionStatus('🔴 Connection Failed', 'red');
                    addMessageToUI('❌ Connection failed: ' + error.message, 'system');
                    isConnected = false;

                    // إعادة الاتصال بعد 3 ثواني
                    setTimeout(() => {
                        if (!isConnected) {
                            addMessageToUI('🔄 Retrying connection...', 'system');
                            connectWebSocket();
                        }
                    }, 3000);
                });

                socket.on('disconnect', function(reason) {
                    console.log('🔌 Disconnected:', reason);
                    updateConnectionStatus('🟡 Disconnected', 'orange');
                    addMessageToUI('🔌 Disconnected: ' + reason, 'system');
                    isConnected = false;
                });
            } catch (error) {
                console.error('❌ Failed to initialize socket:', error);
                addMessageToUI('❌ Failed to initialize connection', 'system');
            }
        }

        function disconnectWebSocket() {
            if (socket) {
                socket.disconnect();
                isConnected = false;
                updateConnectionStatus('🔴 Disconnected', 'gray');
                addMessageToUI('🔌 Manually disconnected', 'system');
            }
        }

        function updateConnectionStatus(message, color) {
            const statusDiv = document.getElementById('connectionStatus');
            statusDiv.innerHTML = `<strong style="color: ${color}">${message}</strong>`;
        }

        async function sendMessage() {
            if (!currentChatId) {
                alert('Create a chat first!');
                return;
            }

            const message = document.getElementById('messageInput').value;
            const otherUserId = document.getElementById('otherUserId').value;

            if (!message.trim()) {
                alert('Please enter a message');
                return;
            }

            try {
                const response = await axios.post('http://localhost:8000/api/chat/messages/send', {
                    chat_id: currentChatId,
                    type: 'text',
                    body: message,
                    receiver_id: parseInt(otherUserId)
                }, {
                    headers: {
                        'Authorization': 'Bearer ' + currentToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                });

                addMessageToUI(response.data.data, 'sent');
                document.getElementById('messageInput').value = '';
                console.log('📤 Message sent:', response.data);
            } catch (error) {
                console.error('❌ Error:', error);
                addMessageToUI('❌ Error sending message: ' + (error.response?.data?.message || error.message), 'system');
            }
        }

        function addMessageToUI(message, type) {
            const messagesDiv = document.getElementById('messages');
            const messageDiv = document.createElement('div');

            if (type === 'system') {
                messageDiv.innerHTML = `<div style="color: gray; text-align: center; font-style: italic; padding: 5px;">${message}</div>`;
            } else {
                const senderName = (type === 'sent') ? 'You' : (message.sender?.name || 'Other');
                const time = new Date().toLocaleTimeString();
                const messageBody = message.body || (message.message?.body) || '📎 Attachment';

                messageDiv.innerHTML = `
                    <div style="margin: 5px; padding: 8px; background: ${type === 'sent' ? '#dcf8c6' : '#fff'}; 
                                border-radius: 10px; ${type === 'sent' ? 'margin-left: 50px;' : 'margin-right: 50px;'}">
                        <strong>${senderName}:</strong> ${messageBody}
                        <div style="font-size: 12px; color: gray;">${time}</div>
                    </div>
                `;
            }

            messagesDiv.appendChild(messageDiv);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        // إرسال الرسالة عند الضغط على Enter
        document.getElementById('messageInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        });

        // تنظيف عند إغلاق الصفحة
        window.addEventListener('beforeunload', function() {
            if (socket) {
                socket.disconnect();
            }
        });

        window.addEventListener('load', function() {
            addMessageToUI('💡 Create a chat and connect to start messaging', 'system');
            updateConnectionStatus('⚪ Not Connected', 'gray');
        });
    </script>
</body>

</html>