<!-- ========================================================
     COMPONENTE CHATBOT FLOTANTE - MARKET PRIMAVERA
     ======================================================== -->
<div class="chatbot-container" id="chatbotContainer">

    <!-- Ventana emergente del Chatbot -->
    <div class="chatbot-window" id="chatbotWindow" aria-label="Asistente Virtual">
        
        <!-- Cabecera del Chat -->
        <div class="chatbot-header">
            <div class="chatbot-header-info">
                <div class="chatbot-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2"></rect>
                        <circle cx="12" cy="5" r="2"></circle>
                        <path d="M12 7v4"></path>
                        <line x1="8" y1="16" x2="8" y2="16.01"></line>
                        <line x1="16" y1="16" x2="16" y2="16.01"></line>
                    </svg>
                </div>
                <div class="chatbot-title">
                    <h4>Asistente Primavera</h4>
                    <div class="chatbot-status">
                        <span class="status-dot"></span>
                        <span>En línea • Respuesta al instante</span>
                    </div>
                </div>
            </div>
            <button type="button" class="chatbot-close-btn" id="chatbotClose" aria-label="Cerrar chat">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Cuerpo del Chat / Mensajes -->
        <div class="chatbot-body" id="chatbotBody">
            <div class="chat-msg bot">
                <div class="msg-bubble">
                    ¡Hola! 👋 Bienvenido a <b>Market Primavera</b>. ¿En qué puedo ayudarte hoy?
                    <br><br>
                    Puedes elegir una de las preguntas recomendadas abajo o escribirme tu consulta.
                </div>
                <span class="msg-time">Ahora</span>
            </div>
        </div>

        <!-- Sección de Preguntas Recomendadas -->
        <div class="chatbot-suggestions">
            <div class="suggestions-title">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                Preguntas Sugeridas
            </div>
            <div class="suggestions-wrapper" id="suggestionsWrapper">
                <!-- Se cargan dinámicamente vía JS -->
            </div>
        </div>

        <!-- Pie de página / Caja de Entrada -->
        <div class="chatbot-footer">
            <form id="chatbotForm" class="chatbot-form" autocomplete="off">
                <input type="text" id="chatbotInput" class="chatbot-input" placeholder="Escribe tu mensaje aquí..." aria-label="Escribe tu consulta">
                <button type="submit" class="chatbot-send-btn" aria-label="Enviar mensaje">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    <!-- Botón Flotante (FAB) -->
    <button type="button" class="chatbot-fab" id="chatbotFab" aria-label="Abrir Asistente Virtual">
        <span class="chatbot-fab-badge"></span>
        <svg class="fab-icon-bot" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <svg class="fab-icon-close" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
    </button>
</div>


