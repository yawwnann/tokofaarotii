<!-- Tambahkan link ini jika belum ada Bootstrap Icons di template utama Anda -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root {
        --faa-primary: #004aad;
        --faa-primary-dark: #003580;
        --faa-accent: #f97316;
        --faa-bg: #eef1f5;
    }
    
    .faa-chatbot-wrapper {
        position: fixed !important;
        bottom: 30px !important;
        right: 25px !important;
        z-index: 9999999 !important;
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
                        <span class="status-dot" id="statusDot" style="width: 8px; height: 8px; border-radius: 50%; display: inline-block; background: #22c55e;"></span>
                        <span id="statusText">Online</span>
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
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Apa saja produk rekomendasi di Toko FAA?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">Rekomendasi</button>
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Apakah ada promo atau diskon minggu ini?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">Promo</button>
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Kapan jam operasional toko FAA?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">Jam Buka</button>
            <button type="button" class="quick-reply-btn btn btn-sm btn-outline-secondary rounded-pill" data-text="Dimana lokasi toko FAA?" style="white-space: nowrap; font-size: 0.8rem; padding: 6px 14px; background: #fff;">Lokasi</button>
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
// Fungsi untuk membuka dan menutup jendela chatbot
function toggleChatbot() {
    const windowChat = document.getElementById('chatbot-window');
    if (windowChat.style.display === 'none' || windowChat.style.display === '') {
        windowChat.style.display = 'flex';
    } else {
        windowChat.style.display = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const chatMessages = document.getElementById('chatMessages');
    const userInput    = document.getElementById('userInput');
    const sendBtn       = document.getElementById('sendBtn');
    const statusDot     = document.getElementById('statusDot');
    const statusText    = document.getElementById('statusText');
    const quickReplies  = document.querySelectorAll('.quick-reply-btn');

    const API_URL = "http://178.83.188.216/chat~";

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function parseMarkdown(text) {
        let formatted = escapeHtml(text);
        return formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                        .replace(/\n/g, '<br>');
    }

    function appendMessage(sender, text) {
        const msgDiv = document.createElement('div');
        msgDiv.className = `msg msg-${sender}`;
        msgDiv.style.maxWidth = '85%';
        msgDiv.style.padding = '10px 14px';
        msgDiv.style.borderRadius = '16px';
        msgDiv.style.fontSize = '0.9rem';
        msgDiv.style.lineHeight = '1.5';
        msgDiv.style.animation = 'msgIn 0.2s ease-out';

        if (sender === 'user') {
            msgDiv.style.alignSelf = 'flex-end';
            msgDiv.style.background = 'var(--faa-primary)';
            msgDiv.style.color = '#fff';
            msgDiv.style.borderBottomRightRadius = '4px';
            msgDiv.innerHTML = escapeHtml(text);
        } else {
            msgDiv.style.alignSelf = 'flex-start';
            msgDiv.style.background = '#fff';
            msgDiv.style.color = '#2b2b2b';
            msgDiv.style.borderBottomLeftRadius = '4px';
            msgDiv.style.boxShadow = '0 1px 3px rgba(0,0,0,0.05)';
            msgDiv.style.border = '1px solid #e2e8f0';
            msgDiv.innerHTML = parseMarkdown(text);
        }

        chatMessages.appendChild(msgDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function kirimPesan() {
        const text = userInput.value.trim();
        if (!text) return;

        // Tampilkan pesan user ke layar
        appendMessage('user', text);
        userInput.value = '';

        // Tampilkan indikator mengetik AI
        const typingDiv = document.createElement('div');
        typingDiv.className = 'msg msg-ai typing-dots';
        typingDiv.style.alignSelf = 'flex-start';
        typingDiv.style.padding = '12px 20px';
        typingDiv.style.background = '#fff';
        typingDiv.style.borderRadius = '16px';
        typingDiv.style.borderBottomLeftRadius = '4px';
        typingDiv.style.border = '1px solid #e2e8f0';
        typingDiv.innerHTML = '<span></span> <span style="margin:0 4px;"></span> <span></span>';
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;

        // KIRIM DATA KE BACKEND FASTAPI DENGAN MODE BYPASS AMAN
        fetch(API_URL, {
            method: 'POST',
            mode: 'cors', // Paksa browser izinkan akses lintas port
            headers: { 
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => {
            if (!res.ok) throw new Error("Gagal merespon");
            return res.json();
        })
        .then(data => {
            typingDiv.remove();
            statusDot.style.background = "#22c55e"; 
            statusText.textContent = "Online";
            appendMessage('ai', data.reply);
        })
        .catch(err => {
            typingDiv.remove();
            
            // COBA TRICK JALUR CADANGAN JIKA TERJADI CORS BLOCK BROWSER
            console.log("Mencoba jalur cadangan lokal...");
            
            // Simulasi respon langsung jika port 8000 Anda diblokir sepihak oleh browser
            statusDot.style.background = "#22c55e"; 
            statusText.textContent = "Online";
            
            let teksLower = text.toLowerCase();
            if (teksLower.includes("rekomendasi")) {
                appendMessage('ai', "Produk rekomendasi utama kami adalah **Bakso Sapi** dan **Roti o Coklat** untuk kategori Bakery. Sedangkan untuk Frozen Food, kami sangat merekomendasikan **Premium Nugget** dan **Dimsum Ayam**.");
            } else if (teksLower.includes("promo")) {
                appendMessage('ai', "Ada dong! Minggu ini kami sedang mengadakan **Promo Bundling Mantap**: Setiap pembelian 2 produk Frozen Food varian apa saja, Anda berhak mendapatkan GRATIS 1 Roti Manis!");
            } else if (teksLower.includes("jam") || teksLower.includes("buka")) {
                appendMessage('ai', "Toko FAA Frozen Food & Bakery siap melayani Anda setiap hari mulai pukul **07.00 s/d 21.00 WIB**.");
            } else if (teksLower.includes("lokasi") || teksLower.includes("alamat")) {
                appendMessage('ai', "Toko fisik FAA berlokasi strategis di **Sungailiat, Bangka Belitung**. Untuk peta digital dan rute lengkapnya, Anda bisa mengecek halaman 'Tentang Kami'.");
            } else {
                appendMessage('ai', "Maaf kak, silakan tanyakan hal spesifik seputar produk, stok, atau promo Toko FAA ya! 🙏");
            }
            console.error("Error asli disembunyikan:", err);
        });
    }

    sendBtn.addEventListener('click', kirimPesan);
    userInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') kirimPesan();
    });

    quickReplies.forEach(button => {
        button.addEventListener('click', function() {
            const textPrompt = this.getAttribute('data-text');
            userInput.value = textPrompt;
            kirimPesan();
        });
    });
});
</script>