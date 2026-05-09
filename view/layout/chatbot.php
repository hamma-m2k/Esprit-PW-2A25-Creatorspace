<div id="ai-chatbot-widget">
    <button id="chatbot-toggle" aria-label="Ouvrir l'assistant IA">💬</button>
    
    <div id="chatbot-container" class="hidden">
        <div class="chatbot-header">
            <h4>✨ CreatorSpace Guide</h4>
            <button id="chatbot-close">✕</button>
        </div>
        <div id="chatbot-messages">
            <div class="message bot">
                Bonjour ! Je suis l'assistant IA de CreatorSpace. Comment puis-je vous aider aujourd'hui ?
            </div>
        </div>
        <div class="chatbot-input">
            <input type="text" id="chatbot-input-field" placeholder="Posez votre question..." />
            <button id="chatbot-send">Envoyer</button>
        </div>
    </div>
</div>

<style>
#ai-chatbot-widget {
    position: fixed;
    bottom: 30px;
    right: 30px;
    z-index: 9999;
    font-family: 'Syne', sans-serif;
}

#chatbot-toggle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #6C3FC5, #9D4EDD);
    color: white;
    font-size: 24px;
    border: none;
    cursor: pointer;
    box-shadow: 0 10px 25px rgba(108, 63, 197, 0.4);
    transition: transform 0.3s ease;
}

#chatbot-toggle:hover {
    transform: scale(1.1);
}

#chatbot-container {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    height: 500px;
    background: var(--bg, #111111);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.5);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

#chatbot-container.hidden {
    opacity: 0;
    pointer-events: none;
    transform: translateY(20px);
}

.chatbot-header {
    background: linear-gradient(135deg, #6C3FC5, #9D4EDD);
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
}

.chatbot-header h4 {
    margin: 0;
    font-size: 16px;
}

#chatbot-close {
    background: transparent;
    border: none;
    color: white;
    font-size: 18px;
    cursor: pointer;
}

#chatbot-messages {
    flex: 1;
    padding: 15px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.message {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.4;
}

.message.bot {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    align-self: flex-start;
    border-bottom-left-radius: 4px;
}

.message.user {
    background: #6C3FC5;
    color: white;
    align-self: flex-end;
    border-bottom-right-radius: 4px;
}

.message.loading {
    background: transparent;
    font-style: italic;
    color: rgba(255, 255, 255, 0.5);
}

.chatbot-input {
    display: flex;
    padding: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    background: rgba(0, 0, 0, 0.2);
}

#chatbot-input-field {
    flex: 1;
    background: transparent;
    border: none;
    color: white;
    outline: none;
    padding: 5px;
}

#chatbot-send {
    background: #6C3FC5;
    color: white;
    border: none;
    padding: 8px 15px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('chatbot-toggle');
    const closeBtn = document.getElementById('chatbot-close');
    const container = document.getElementById('chatbot-container');
    const sendBtn = document.getElementById('chatbot-send');
    const inputField = document.getElementById('chatbot-input-field');
    const messagesDiv = document.getElementById('chatbot-messages');

    toggleBtn.addEventListener('click', () => {
        container.classList.toggle('hidden');
        if (!container.classList.contains('hidden')) {
            inputField.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        container.classList.add('hidden');
    });

    const addMessage = (text, sender) => {
        const div = document.createElement('div');
        div.className = `message ${sender}`;
        div.textContent = text;
        messagesDiv.appendChild(div);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
        return div;
    };

    const sendMessage = async () => {
        const text = inputField.value.trim();
        if (!text) return;

        addMessage(text, 'user');
        inputField.value = '';
        inputField.disabled = true;
        sendBtn.disabled = true;

        const loadingMsg = addMessage('L\'assistant réfléchit...', 'loading');

        try {
            const response = await fetch('index.php?ctrl=user&action=chatbot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ message: text })
            });
            
            const data = await response.json();
            loadingMsg.remove();
            
            if (data.reply) {
                addMessage(data.reply, 'bot');
            } else if (data.error) {
                addMessage('Erreur : ' + data.error, 'bot');
            } else {
                addMessage('Désolé, une erreur est survenue.', 'bot');
            }
        } catch (error) {
            loadingMsg.remove();
            addMessage('Erreur de connexion. L\'assistant est injoignable.', 'bot');
        }

        inputField.disabled = false;
        sendBtn.disabled = false;
        inputField.focus();
    };

    sendBtn.addEventListener('click', sendMessage);
    inputField.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
});
</script>
