/**
 * CHATBOT ASISTENTE VIRTUAL - MARKET PRIMAVERA
 */

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('chatbotContainer');
    const fabBtn = document.getElementById('chatbotFab');
    const closeBtn = document.getElementById('chatbotClose');
    const messagesBody = document.getElementById('chatbotBody');
    const suggestionsWrapper = document.getElementById('suggestionsWrapper');
    const chatForm = document.getElementById('chatbotForm');
    const chatInput = document.getElementById('chatbotInput');

    if (!container || !fabBtn) return;

    // Toggle ventana del chatbot
    fabBtn.addEventListener('click', function () {
        container.classList.toggle('activo');
        if (container.classList.contains('activo')) {
            chatInput.focus();
            scrollToBottom();
        }
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            container.classList.remove('activo');
        });
    }

    // Cargar preguntas recomendadas al iniciar
    cargarPreguntasRecomendadas();

    // Formulario de envío
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const texto = chatInput.value.trim();
        if (texto) {
            enviarMensaje(texto);
            chatInput.value = '';
        }
    });

    /**
     * Carga las preguntas recomendadas desde el backend PHP
     */
    function cargarPreguntasRecomendadas() {
        fetch(BASE_URL + '/controllers/ChatbotController.php?action=recomendadas')
            .then(res => res.json())
            .then(data => {
                if (data.ok && Array.isArray(data.preguntas)) {
                    renderizarPreguntasRecomendadas(data.preguntas);
                }
            })
            .catch(err => {
                console.error('Error al cargar preguntas recomendadas:', err);
            });
    }

    /**
     * Renderiza los chips de preguntas recomendadas
     */
    function renderizarPreguntasRecomendadas(preguntas) {
        if (!suggestionsWrapper) return;
        suggestionsWrapper.innerHTML = '';

        preguntas.forEach(p => {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'suggestion-chip';
            chip.textContent = p.pregunta;
            chip.addEventListener('click', function () {
                enviarMensaje(p.pregunta);
            });
            suggestionsWrapper.appendChild(chip);
        });
    }

    /**
     * Envía un mensaje al bot y procesa la respuesta
     */
    function enviarMensaje(texto) {
        // 1. Mostrar mensaje del usuario
        agregarMensaje(texto, 'user');

        // 2. Mostrar indicador de tipado ("escribiendo...")
        const typingElem = mostrarIndicadorEscribiendo();
        scrollToBottom();

        // 3. Petición al backend PHP
        fetch(BASE_URL + '/controllers/ChatbotController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'consultar',
                mensaje: texto
            })
        })
        .then(res => res.json())
        .then(data => {
            removerIndicadorEscribiendo(typingElem);
            if (data.ok && data.respuesta) {
                agregarMensaje(data.respuesta, 'bot');
            } else {
                agregarMensaje('Lo siento, ocurrió un error al consultar mi base de conocimiento. Por favor intenta de nuevo.', 'bot');
            }
        })
        .catch(err => {
            removerIndicadorEscribiendo(typingElem);
            agregarMensaje('Hubo un problema de conexión. Por favor verifica tu red e intenta más tarde.', 'bot');
            console.error('Error en Chatbot Fetch:', err);
        });
    }

    /**
     * Agrega una burbuja de mensaje al cuerpo del chat
     */
    function agregarMensaje(htmlText, remitente) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `chat-msg ${remitente}`;

        const bubbleDiv = document.createElement('div');
        bubbleDiv.className = 'msg-bubble';
        bubbleDiv.innerHTML = htmlText;

        const timeSpan = document.createElement('span');
        timeSpan.className = 'msg-time';
        const now = new Date();
        timeSpan.textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        msgDiv.appendChild(bubbleDiv);
        msgDiv.appendChild(timeSpan);

        messagesBody.appendChild(msgDiv);
        scrollToBottom();
    }

    /**
     * Muestra la animación de "escribiendo..."
     */
    function mostrarIndicadorEscribiendo() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'chat-msg bot typing-wrapper';
        typingDiv.innerHTML = `
            <div class="typing-indicator">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        `;
        messagesBody.appendChild(typingDiv);
        return typingDiv;
    }

    /**
     * Remueve la animación de "escribiendo..."
     */
    function removerIndicadorEscribiendo(elem) {
        if (elem && elem.parentNode) {
            elem.parentNode.removeChild(elem);
        }
    }

    /**
     * Desplaza el chat al último mensaje
     */
    function scrollToBottom() {
        messagesBody.scrollTop = messagesBody.scrollHeight;
    }
});
