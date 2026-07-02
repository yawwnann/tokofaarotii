<style>
    :root {
        --faa-primary: #004aad;
        --faa-primary-dark: #003580;
        --faa-accent: #f97316;
        --faa-bg: #eef1f5;
    }
    
    /* Wrapper Utama Terluar dikunci di pojok kanan bawah */
    .faa-chatbot-wrapper {
        position: fixed !important;
        bottom: 100px !important;
        right: 25px !important;
        z-index: 9999999 !important;
        pointer-events: none;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-end !important;
        justify-content: flex-end !important;
    }

    .faa-chatbot-wrapper button, 
    .faa-chatbot-wrapper div {
        pointer-events: auto;
    }
    
    @keyframes popUpIn {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    @keyframes msgIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Perbaikan Scrollbar Quick Replies */
    .quick-replies::-webkit-scrollbar { height: 4px; }
    .quick-replies::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    /* Animasi Titik Mengetik AI */
    .typing-dots span {
        width: 6px; height: 6px; background: #94a3b8; border-radius: 50%; display: inline-block;
        animation: typing 1.4s infinite both;
    }
    .typing-dots span:nth-child(2) { animation-delay: .2s; }
    .typing-dots span:nth-child(3) { animation-delay: .4s; }
    @keyframes typing {
        0%, 100% { transform: scale(0.6); opacity: 0.4; }
        50% { transform: scale(1.2); opacity: 1; }
    }
</style>

<div class="faa-chatbot-wrapper">

    <button id="chatbot-trigger" onclick="toggleChatbot()" style="width: 60px !important; height: 60px !important; background: linear-gradient(135deg, var(--faa-primary), var(--faa-primary-dark)) !important; border: none !important; border-radius: 50% !important; color: white !important; font-size: 1.5rem !important; cursor: pointer !important; box-shadow: 0 4px 15px rgba(0, 74, 173, 0.4) !important; display: flex !important; align-items: center !important; justify-content: center !important; transition: all 0.3s ease !important;">
        <i class="bi bi-chat-dots-fill" id="chat-icon"></i>
    </button>

    <div id="chatbot-window" style="width: 380px !important; max-width: 90vw !important; height: 500px !important; background: #fff !important; border-radius: 20px !important; box-shadow: 0 10px 35px rgba(0,0,0,0.2) !important; display: none; flex-direction: column !important; overflow: hidden !important; border: 1px solid #eee !important; transition: all 0.3s ease !important; animation: popUpIn 0.25s ease-out !important;">

        <div class="chat-header" style="padding: 15px 20px; background: linear-gradient(135deg, var(--faa-primary), var(--faa-primary-dark)); color: #fff; display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-shrink: 0;">
            <div class="chat-header-left" style="display: flex; align-items: center; gap: 12px;">
                <div class="chat-avatar rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background: #fff; color: var(--faa-primary); font-size: 1.1rem; flex-shrink: 0;">
                    <i class="bi bi-robot"></i>
                </div>
                <div>
                    <h6 class="m-0 fw-bold" style="font-size: 0.95rem; line-height: 1.2;">FAA AI Assistant</h6>
                    <small class="opacity-75" style="font-size: 0.75rem; display: flex; align-items: center; gap: 4px;">
                        <span class="status-dot" id="statusDot" style="width: 8px; height: 8px; border-radius: 50%; display: inline-block; background: #9ca3af;"></span>
                        <span id="statusText">Menghubungkan...</span>
                    </small>
                </div>
            </div>
            <button onclick="toggleChatbot()" style="background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem; padding: 0 5px;"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="chat-messages" id="chatMessages" style="flex-grow: 1; flex-shrink: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; gap: 14px; background: #f8fafc; scroll-behavior: smooth;">
            <div class="msg msg-ai" style="max-width: 85%; padding: 10px 14px; border-radius: 16px; font-size: 0.9rem; line-height: 1.5; align-self: flex-start; background: #fff; color: #2b2b2b; border-bottom-left-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
                Halo <strong>{{ auth()->check() ? auth()->user()->name : 'Pelanggan' }}</strong>! 👋 Selamat datang di FAA Frozen Food & Bakery. Ada yang bisa saya bantu hari ini?
            </div>
        </div>

        <div class="quick-replies" style="flex-shrink: 0; display: flex; flex-wrap: nowrap; gap: 8px; padding: 12px 15px; overflow-x: auto; background: #f8fafc; border-top: 1px solid #e2e8f0; -webkit-overflow-scrolling: touch;">
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Apa saja produk rekomendasi di Toko FAA?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">🌟 Rekomendasi</button>
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Apakah ada promo atau diskon minggu ini?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">🔥 Promo</button>
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Kapan jam operasional toko FAA?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">🕒 Jam Buka</button>
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Dimana lokasi toko FAA?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">📍 Lokasi</button>
        </div>

        <div class="chat-input-area" style="flex-shrink: 0; padding: 12px 15px; background: #fff; border-top: 1px solid #eee; display: flex; gap: 8px; align-items: center;">
            <input type="text" class="form-control chat-input" id="userInput" placeholder="Ketik pesan Anda..." autocomplete="off" style="border-radius: 50px; padding: 8px 16px; font-size: 0.9rem;">
            <button class="btn btn-primary d-flex align-items-center justify-content-center" id="sendBtn" type="button" style="width: 40px; height: 40px; min-width: 40px; border-radius: 50%; background: var(--faa-primary); border: none;">
                <i class="bi bi-send-fill" style="color: white;"></i>
            </button>
        </div>
    </div>

</div>

<script>
function toggleChatbot() {
    const chatWindow = document.getElementById('chatbot-window');
    const triggerBtn = document.getElementById('chatbot-trigger');
    
    if (chatWindow.style.display === 'none' || chatWindow.style.display === '') {
        chatWindow.style.display = 'flex';
        triggerBtn.style.display = 'none'; // Sembunyikan tombol saat chat terbuka
        setTimeout(() => document.getElementById('userInput').focus(), 50);
    } else {
        chatWindow.style.display = 'none';
        triggerBtn.style.display = 'flex'; // Munculkan kembali tombol saat chat ditutup
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const chatMessages = document.getElementById('chatMessages');
    const userInput     = document.getElementById('userInput');
    const sendBtn       = document.getElementById('sendBtn');
    const statusDot     = document.getElementById('statusDot');
    const statusText    = document.getElementById('statusText');
    const quickReplies  = document.querySelectorAll('.quick-reply-btn');

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function parseMarkdown(text) {
        return text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
    }

    function addMessage(text, type, isHtml = false) {
        const div = document.createElement('div');
        div.className = `msg msg-${type}`;
        div.style = `max-width: 85%; padding: 10px 14px; border-radius: 16px; font-size: 0.9rem; line-height: 1.5; word-wrap: break-word; animation: msgIn 0.25s ease; flex-shrink: 0;`;
        
        if(type === 'user') {
            div.style.cssText += "align-self: flex-end; background: var(--faa-accent); color: #fff; border-bottom-right-radius: 4px; box-shadow: 0 2px 4px rgba(249,115,22,0.15);";
        } else if(type === 'ai') {
            div.style.cssText += "align-self: flex-start; background: #fff; color: #2b2b2b; border-bottom-left-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;";
        } else {
            div.style.cssText += "align-self: flex-start; background: #fdecea; color: #b3261e; border: 1px solid #f6c4c0;";
        }

        div.innerHTML = isHtml ? text : parseMarkdown(escapeHtml(text));
        chatMessages.appendChild(div);
        chatMessages.scrollTop = chatMessages.scrollHeight;
        return div;
    }

    function setSending(isSending) {
        sendBtn.disabled = isSending;
        userInput.disabled = isSending;
    }

    async function handleSend(prefilledText) {
        const text = (prefilledText ?? userInput.value).trim();
        if (!text) return;

        addMessage(text, 'user');
        userInput.value = '';
        setSending(true);

        const loadingDiv = addMessage(
            '<div class="d-flex align-items-center gap-2">' +
                '<span class="typing-dots d-flex gap-1"><span></span><span></span><span></span></span>' +
                '<em class="text-muted" style="font-size:0.85rem;">FAA AI Chatbot sedang mengetik...</em>' +
            '</div>',
            'ai',
            true
        );

        try {
            const response = await fetch("{{ route('chatbot.proxy') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ message: text }),
            });

            const data = await response.json().catch(() => null);
            loadingDiv.remove();

            if (data && data.response) {
                let formattedResponse = parseMarkdown(data.response.replace(/\n/g, '<br>'));
                addMessage(formattedResponse, response.ok ? 'ai' : 'error', true);
            } else {
                addMessage('Maaf, terjadi kesalahan saat memproses jawaban, harap login terlebih dahulu.', 'error');
            }
        } catch (error) {
            loadingDiv.remove();
            addMessage('Maaf, FAA AI Chatbot sedang beristirahat sejenak. Silakan tanya kembali beberapa saat lagi.', 'error');
            console.error('Koneksi Error:', error);
        } finally {
            setSending(false);
            userInput.focus();
        }
    }

    sendBtn.addEventListener('click', () => handleSend());
    userInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') handleSend();
    });

    quickReplies.forEach((btn) => {
        btn.addEventListener('click', () => handleSend(btn.dataset.text));
    });

    async function checkStatus() {
        try {
            const res = await fetch("{{ route('chatbot.status') }}");
            const data = await res.json();

            if (data.model_ready) {
                statusDot.style.backgroundColor = '#22c55e';
                statusText.textContent = 'Online';
            } else {
                statusDot.style.backgroundColor = '#f59e0b';
                statusText.textContent = 'AI Bersiap...';
                setTimeout(checkStatus, 5000);
            }
        } catch (e) {
            statusDot.style.backgroundColor = '#9ca3af';
            statusText.textContent = 'Offline';
        }
    }
    
    checkStatus();
});
</script>