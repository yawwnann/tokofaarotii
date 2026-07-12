@extends('layouts.app')

@section('title', 'Kasir Modern - Faa Frozen')

@section('content')
<div class="max-w-[1600px] mx-auto px-4">
    <form action="{{ route('sales.store') }}" method="POST" id="salesForm">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- SISI KIRI: Katalog Produk (Ala Shopee) -->
            <div class="lg:col-span-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-black text-gray-800">KATALOG PRODUK</h2>
                    <div class="relative w-64">
                        <input type="text" placeholder="Cari produk..." class="w-full pl-10 pr-4 py-2 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-indigo-500 outline-none transition-all text-sm">
                        <i class="fas fa-search absolute left-4 top-3 text-gray-400 text-xs"></i>
                    </div>
                </div>

                <!-- Grid Kartu Produk -->
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 overflow-y-auto max-h-[700px] pr-2 custom-scrollbar">
                    @foreach($products as $product)
                    <div onclick="addToCart({{ $product->id }}, '{{ $product->name }}', {{ $product->price }})" 
                         class="bg-white p-3 rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-indigo-400 cursor-pointer transition-all group relative overflow-hidden">
                        
                        <div class="aspect-square bg-gray-50 rounded-xl mb-3 overflow-hidden">
                            @if($product->image)
                                <img src="{{ asset('storage/'.$product->image) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-200">
                                    <i class="fas fa-bread-slice text-3xl"></i>
                                </div>
                            @endif
                        </div>
                        
                        <h3 class="text-sm font-bold text-gray-800 truncate mb-1">{{ $product->name }}</h3>
                        <p class="text-indigo-600 font-black text-sm">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        
                        <!-- Badge Stok (Optional) -->
                        <div class="absolute top-2 right-2 px-2 py-0.5 bg-white/80 backdrop-blur-sm rounded-lg border border-gray-100 text-[9px] font-black text-gray-500 uppercase">
                            Tersedia
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- SISI KANAN: Keranjang & Pembayaran -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-[2.5rem] shadow-xl border border-gray-100 flex flex-col h-[750px] overflow-hidden">
                    
                    <!-- Customer Info -->
                    <div class="p-6 border-b border-gray-50">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600">
                                <i class="fas fa-user text-xs"></i>
                            </div>
                            <input type="text" name="customer_name" placeholder="Nama Pelanggan (Umum)" 
                                   class="flex-1 bg-transparent border-none focus:ring-0 font-bold text-gray-700 placeholder:text-gray-300">
                        </div>
                    </div>

                    <!-- List Keranjang Belanja -->
                    <div id="cartItems" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scrollbar">
                        <!-- Item yang diklik akan muncul di sini via JS -->
                        <div class="flex flex-col items-center justify-center h-full text-gray-300 italic opacity-50">
                            <i class="fas fa-shopping-basket text-5xl mb-4"></i>
                            <p class="text-sm">Klik produk untuk menambah</p>
                        </div>
                    </div>

                    <!-- Ringkasan Bayar -->
                    <div class="p-8 bg-gray-50">
                        <div class="flex justify-between items-end mb-6">
                            <div>
                                <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1 text-left">Total Pembayaran</p>
                                <div class="flex items-baseline text-indigo-600">
                                    <span class="text-lg font-bold mr-1">Rp</span>
                                    <span id="grandTotalDisplay" class="text-4xl font-black tracking-tighter">0</span>
                                </div>
                                <input type="hidden" name="total_price" id="total_price_input" value="0">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl font-black text-lg shadow-lg shadow-indigo-100 transition-all transform hover:-translate-y-1">
                            <i class="fas fa-check-circle mr-2"></i> SELESAIKAN ORDER
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    let cart = [];

    function addToCart(id, name, price) {
        const existing = cart.find(item => item.id === id);
        if (existing) {
            existing.qty += 1;
        } else {
            cart.push({ id, name, price, qty: 1 });
        }
        renderCart();
    }

    function changeQty(id, delta) {
        const item = cart.find(item => item.id === id);
        if (item) {
            item.qty += delta;
            if (item.qty <= 0) {
                cart = cart.filter(i => i.id !== id);
            }
            renderCart();
        }
    }

    function renderCart() {
        const container = document.getElementById('cartItems');
        const grandTotalDisplay = document.getElementById('grandTotalDisplay');
        const totalInput = document.getElementById('total_price_input');
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center h-full text-gray-300 italic opacity-50">
                    <i class="fas fa-shopping-basket text-5xl mb-4"></i>
                    <p class="text-sm text-center">Keranjang masih kosong</p>
                </div>`;
            grandTotalDisplay.innerText = "0";
            totalInput.value = 0;
            return;
        }

        let total = 0;
        container.innerHTML = cart.map(item => {
            const subtotal = item.price * item.qty;
            total += subtotal;
            return `
                <div class="flex items-center justify-between bg-white p-4 rounded-2xl shadow-sm border border-gray-50">
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-gray-800">${item.name}</h4>
                        <p class="text-[10px] font-black text-indigo-500">Rp ${item.price.toLocaleString('id-ID')}</p>
                        <input type="hidden" name="product_id" value="${item.id}">
                        <input type="hidden" name="quantity_sold" value="${item.qty}">
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="changeQty(${item.id}, -1)" class="w-7 h-7 bg-gray-100 rounded-lg text-gray-500 hover:bg-red-50 hover:text-red-600 transition-colors">
                            <i class="fas fa-minus text-[10px]"></i>
                        </button>
                        <span class="text-sm font-black text-gray-700 w-4 text-center">${item.qty}</span>
                        <button type="button" onclick="changeQty(${item.id}, 1)" class="w-7 h-7 bg-indigo-50 rounded-lg text-indigo-600 hover:bg-indigo-600 hover:text-white transition-colors">
                            <i class="fas fa-plus text-[10px]"></i>
                        </button>
                    </div>
                </div>
            `;
        }).join('');

        grandTotalDisplay.innerText = total.toLocaleString('id-ID');
        totalInput.value = total;
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endsection
