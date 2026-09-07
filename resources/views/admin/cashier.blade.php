<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-coffee-800 leading-tight">Kasir</h2>
    </x-slot>

    <div
        x-data="cashierApp(@json($categories), {{ (float) $taxRate }})"
        class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8"
    >
        {{-- ============ TAB NAVIGATION ============ --}}
        <div class="flex gap-2 rounded-cozy bg-cream-200/70 p-1.5" role="tablist">
            <button
                type="button"
                @click="tab = 'pos'"
                :class="tab === 'pos' ? 'bg-coffee-900 text-cream-50 shadow-warm' : 'text-coffee-700 hover:bg-cream-100'"
                class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition sm:text-base"
            >
                Kasir POS
            </button>
            <button
                type="button"
                @click="tab = 'queue'; fetchOrders()"
                :class="tab === 'queue' ? 'bg-coffee-900 text-cream-50 shadow-warm' : 'text-coffee-700 hover:bg-cream-100'"
                class="flex-1 rounded-lg px-4 py-2.5 text-sm font-semibold transition sm:text-base"
            >
                Antrean Pesanan
                <span
                    x-show="activeOrders.length"
                    class="ml-1 inline-grid h-5 min-w-5 place-items-center rounded-full bg-terracotta-500 px-1 text-xs font-bold text-cream-50"
                    x-text="activeOrders.length"
                ></span>
            </button>
        </div>

        {{-- ============ TAB: KASIR POS ============ --}}
        <section x-show="tab === 'pos'" x-transition class="mt-6 grid grid-cols-1 items-start gap-6 lg:grid-cols-[minmax(0,1fr)_minmax(0,380px)]">
            {{-- ---- KIRI: GRID MENU (70%) ---- --}}
            <div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <input
                            type="search"
                            x-model="search"
                            placeholder="Cari menu..."
                            class="w-full rounded-cozy border-coffee-100 bg-cream-50 py-3 pl-10 pr-4 text-sm text-coffee-800 placeholder:text-coffee-400 focus:border-terracotta-500 focus:ring-terracotta-500"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-coffee-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>

                    <div class="flex gap-2 overflow-x-auto pb-1">
                        <button
                            type="button"
                            @click="selectedCategory = null"
                            :class="!selectedCategory ? 'bg-terracotta-500 text-cream-50' : 'bg-cream-50 text-coffee-700 hover:bg-cream-100'"
                            class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium shadow-warm transition"
                        >Semua</button>
                        <template x-for="cat in categories" :key="cat.id">
                            <button
                                type="button"
                                @click="selectedCategory = cat.id"
                                :class="selectedCategory === cat.id ? 'bg-terracotta-500 text-cream-50' : 'bg-cream-50 text-coffee-700 hover:bg-cream-100'"
                                class="whitespace-nowrap rounded-full px-4 py-2 text-sm font-medium shadow-warm transition"
                                x-text="cat.name"
                            ></button>
                        </template>
                    </div>
                </div>

                {{-- Grid menu per kategori --}}
                <template x-for="cat in categories" :key="cat.id">
                    <div class="mt-8" x-show="!selectedCategory || selectedCategory === cat.id">
                        <h3 class="font-serif text-lg font-bold text-coffee-900" x-text="cat.name"></h3>

                        <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-3 xl:grid-cols-4">
                            <template x-for="item in cat.items" :key="item.id">
                                <article
                                    @click="addToCart(item)"
                                    :class="!search || item.name.toLowerCase().includes(search.toLowerCase()) ? '' : 'hidden'"
                                    class="card group cursor-pointer overflow-hidden select-none"
                                >
                                    <div class="aspect-[4/3] overflow-hidden bg-cream-200">
                                        <img
                                            x-show="item.image"
                                            :src="item.image"
                                            :alt="item.name"
                                            loading="lazy"
                                            class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                                        />
                                    </div>
                                    <div class="flex items-start justify-between gap-2 p-3">
                                        <h4 class="line-clamp-1 text-sm font-semibold text-coffee-900" x-text="item.name"></h4>
                                        <button
                                            type="button"
                                            @click.stop="addToCart(item)"
                                            class="grid h-8 w-8 shrink-0 place-items-center rounded-full bg-terracotta-500 text-cream-50 shadow-warm transition hover:bg-terracotta-600"
                                            aria-label="Tambah ke keranjang"
                                        >+</button>
                                    </div>
                                    <div class="px-3 pb-3 flex items-center justify-between">
                                        <span class="text-sm font-bold text-terracotta-500" x-text="formatRupiah(item.price)"></span>
                                        <span
                                            x-show="qtyOf(item.id)"
                                            class="rounded-full bg-sage-100 px-2 py-0.5 text-xs font-bold text-sage-600"
                                            x-text="qtyOf(item.id) + 'x'"
                                        ></span>
                                    </div>
                                </article>
                            </template>
                        </div>

                        <p x-show="!(cat.items.length > 0)" class="mt-4 text-sm text-coffee-500">Belum ada menu pada kategori ini.</p>
                    </div>
                </template>

                <p x-show="categories.length === 0" class="mt-10 rounded-cozy bg-cream-50 p-8 text-center text-coffee-600">
                    Belum ada menu tersedia. Silakan isi data menu dari panel admin.
                </p>
            </div>

            {{-- ---- KANAN: KERANJANG (30%) ---- --}}
            <aside class="card sticky top-24 bg-cream-50 p-5 lg:max-h-[calc(100vh-8rem)] lg:overflow-y-auto">
                <div class="flex items-center justify-between">
                    <h3 class="font-serif text-lg font-bold text-coffee-900">Keranjang Pesanan</h3>
                    <span x-show="cart.length" class="rounded-full bg-coffee-900 px-2.5 py-0.5 text-xs font-bold text-cream-50" x-text="cart.length + ' item'"></span>
                </div>

                {{-- List keranjang --}}
                <div class="mt-4 space-y-3">
                    <template x-for="item in cart" :key="item.id">
                        <div class="flex items-center gap-3 rounded-cozy bg-cream-100 p-3">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-coffee-900" x-text="item.name"></p>
                                <p class="text-xs text-coffee-500" x-text="formatRupiah(item.price)"></p>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <button type="button" @click="decrease(item.id)"
                                    class="grid h-8 w-8 place-items-center rounded-lg bg-cream-200 text-coffee-800 transition hover:bg-cream-300">−</button>
                                <span class="w-6 text-center text-sm font-bold text-coffee-900" x-text="item.quantity"></span>
                                <button type="button" @click="increase(item.id)"
                                    class="grid h-8 w-8 place-items-center rounded-lg bg-cream-200 text-coffee-800 transition hover:bg-cream-300">+</button>
                            </div>
                            <button type="button" @click="removeItem(item.id)"
                                class="grid h-8 w-8 place-items-center rounded-lg text-coffee-500 transition hover:bg-terracotta-100 hover:text-terracotta-600"
                                aria-label="Hapus item">✕</button>
                        </div>
                    </template>
                </div>

                <p x-show="!cart.length" class="mt-6 rounded-cozy border border-dashed border-coffee-100 p-6 text-center text-sm text-coffee-500">
                    Keranjang masih kosong. Ketuk menu untuk menambah pesanan.
                </p>

                {{-- Ringkasan --}}
                <div x-show="cart.length" class="mt-5 space-y-2 border-t border-coffee-100 pt-4 text-sm">
                    <div class="flex justify-between text-coffee-700">
                        <span>Subtotal</span>
                        <span class="font-medium text-coffee-800" x-text="formatRupiah(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-coffee-700">
                        <span>Pajak (10%)</span>
                        <span class="font-medium text-coffee-800" x-text="formatRupiah(tax)"></span>
                    </div>
                    <div class="flex justify-between border-t border-coffee-100 pt-3">
                        <span class="font-serif text-base font-bold text-coffee-900">Grand Total</span>
                        <span class="font-serif text-xl font-bold text-terracotta-500" x-text="formatRupiah(grossTotal)"></span>
                    </div>
                </div>

                {{-- Form pembayaran tunai --}}
                <form x-show="cart.length" @submit.prevent="submitCashOrder()" class="mt-5 space-y-3">
                    <div>
                        <label class="text-xs font-medium text-coffee-600" for="customer-name">Nama Pelanggan</label>
                        <input id="customer-name" type="text" x-model="customerName" placeholder="Pelanggan"
                            class="mt-1 w-full rounded-cozy border-coffee-100 bg-cream-100 px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    </div>
                    <div>
                        <label class="text-xs font-medium text-coffee-600" for="customer-phone">No. HP (opsional)</label>
                        <input id="customer-phone" type="tel" x-model="customerPhone" placeholder="08xxxxxxxxxx"
                            class="mt-1 w-full rounded-cozy border-coffee-100 bg-cream-100 px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    </div>
                    <div>
                        <label class="text-xs font-medium text-coffee-600" for="cart-table-number">Nomor Meja</label>
                        <input id="cart-table-number" name="table_number" type="text" x-model="cartTableNumber" placeholder="Contoh: Meja 12"
                            class="mt-1 w-full rounded-cozy border-coffee-100 bg-cream-100 px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                    </div>
                    <div>
                        <label class="text-xs font-medium text-coffee-600" for="cart-notes">Catatan</label>
                        <textarea id="cart-notes" name="notes" x-model="cartNotes" rows="2" placeholder="Contoh: Esnya dikit aja, jangan terlalu manis"
                            class="mt-1 w-full resize-none rounded-cozy border-coffee-100 bg-cream-100 px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500"></textarea>
                    </div>
                    <div>
                        <label class="text-xs font-medium text-coffee-600" for="amount-paid">Dibayar Tunai (Rp)</label>
                        <input id="amount-paid" type="number" min="0" x-model="amountPaid" placeholder="Jumlah uang"
                            class="mt-1 w-full rounded-cozy border-coffee-100 bg-cream-100 px-3 py-2.5 text-sm text-coffee-800 focus:border-terracotta-500 focus:ring-terracotta-500" />
                        <p x-show="amountPaid !== ''" class="mt-1 text-xs"
                            :class="hasEnoughCash ? 'text-sage-600' : 'text-terracotta-600'"
                            x-text="hasEnoughCash ? 'Kembalian: ' + formatRupiah(change) : 'Uang kurang ' + formatRupiah(-change)"></p>
                    </div>

                    <div x-show="feedback" x-transition
                        class="rounded-cozy px-4 py-3 text-sm font-medium"
                        :class="feedback && feedback.type === 'success' ? 'bg-sage-100 text-sage-600' : 'bg-terracotta-100 text-terracotta-700'"
                        x-text="feedback && feedback.text"></div>

                    <button type="submit" :disabled="submitting || !hasEnoughCash || grossTotal <= 0"
                        class="btn-primary w-full text-base disabled:cursor-not-allowed disabled:opacity-50"
                        x-text="submitting ? 'Menyimpan...' : 'Bayar Tunai'"></button>
                </form>
            </aside>
        </section>

        {{-- ============ TAB: ANTREAN PESANAN ============ --}}
        <section x-show="tab === 'queue'" x-transition class="mt-6">
            <div class="flex items-center justify-between gap-3">
                <h3 class="font-serif text-lg font-bold text-coffee-900">Antrean Pesanan</h3>
                <div class="flex items-center gap-2 text-xs text-coffee-500">
                    <span class="relative flex h-2 w-2">
                        <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-sage-500 opacity-75"></span>
                        <span class="relative inline-flex h-2 w-2 rounded-full bg-sage-500"></span>
                    </span>
                    Memperbarui otomatis tiap 10 detik
                </div>
            </div>

            <template x-if="tab === 'queue'">
                <div>
                    <div class="mt-5 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        <template x-for="order in activeOrders" :key="order.id">
                            <article class="card bg-cream-50 p-5">
                                <header class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-mono text-xs text-coffee-500" x-text="'#' + order.id.slice(0, 8).toUpperCase()"></p>
                                        <h4 class="mt-0.5 truncate font-serif text-base font-bold text-coffee-900" x-text="order.customer_name"></h4>
                                    </div>
                                    <div class="flex shrink-0 flex-col items-end gap-1.5">
                                        <div x-show="order.table_number"
                                            class="rounded-full bg-terracotta-500 px-3 py-1 text-sm font-bold text-cream-50 shadow-warm">
                                            Meja: <span x-text="order.table_number"></span>
                                        </div>
                                        <span class="whitespace-nowrap rounded-full px-3 py-1 text-xs font-bold" :class="statusMeta(order.payment_status).cls" x-text="statusMeta(order.payment_status).label"></span>
                                    </div>
                                </header>

                                <p class="mt-1 text-xs text-coffee-500" x-text="'Masuk ' + order.time + ' WIB'"></p>

                                <ul class="mt-4 space-y-2 border-t border-coffee-100 pt-3">
                                    <template x-for="(line, idx) in order.items" :key="idx">
                                        <li class="flex items-center justify-between gap-3 text-sm">
                                            <span class="min-w-0 truncate text-coffee-800">
                                                <b class="font-semibold text-terracotta-500" x-text="line.quantity + 'x'"></b>
                                                <span class="ml-1" x-text="line.name"></span>
                                            </span>
                                            <span class="whitespace-nowrap text-xs text-coffee-500" x-text="line.price"></span>
                                        </li>
                                    </template>
                                </ul>

                                <div x-show="order.notes" x-transition
                                    class="mt-4 rounded-r-cozy border-l-4 border-yellow-400 bg-yellow-100/80 px-4 py-2.5">
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-yellow-700">Catatan</p>
                                    <p class="mt-0.5 text-sm leading-relaxed text-coffee-800" x-text="order.notes"></p>
                                </div>

                                <footer class="mt-4 flex items-center justify-between border-t border-coffee-100 pt-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold" :class="methodMeta(order.payment_method).cls" x-text="methodMeta(order.payment_method).label"></span>
                                    <span class="font-serif text-lg font-bold text-terracotta-500" x-text="order.total"></span>
                                </footer>
                            </article>
                        </template>
                    </div>

                    <p x-show="!activeOrders.length" class="mt-8 rounded-cozy bg-cream-50 p-10 text-center text-coffee-600">
                        Tidak ada pesanan aktif saat ini. Pesanan dari website akan muncul otomatis di sini.
                    </p>
                </div>
            </template>
        </section>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('cashierApp', (categories, taxRate) => ({
                /* ---------- State: tab default aktif adalah 'pos' ---------- */
                tab: 'pos',
                cart: [],
                activeOrders: [],
                categories: Array.isArray(categories) ? categories : [],
                taxRate: typeof taxRate === 'number' ? taxRate : 0.1,
                selectedCategory: null,
                search: '',
                customerName: '',
                customerPhone: '',
                cartTableNumber: '',
                cartNotes: '',
                amountPaid: '',
                submitting: false,
                feedback: null,
                polling: null,
                fetching: false,

                /* ---------- Cart computed ---------- */
                get subtotal() {
                    return this.cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
                },
                get tax() {
                    return Math.round(this.subtotal * this.taxRate);
                },
                get grossTotal() {
                    return this.subtotal + this.tax;
                },
                get change() {
                    return (parseFloat(this.amountPaid) || 0) - this.grossTotal;
                },
                get hasEnoughCash() {
                    return this.change >= 0;
                },

                /* ---------- Cart actions ---------- */
                addToCart(item) {
                    const existing = this.cart.find((c) => c.id === item.id);
                    if (existing) {
                        existing.quantity += 1;
                    } else {
                        this.cart.push({ id: item.id, name: item.name, price: Number(item.price), quantity: 1 });
                    }
                },
                increase(id) {
                    const item = this.cart.find((c) => c.id === id);
                    if (item) item.quantity += 1;
                },
                decrease(id) {
                    const index = this.cart.findIndex((c) => c.id === id);
                    if (index === -1) return;
                    if (this.cart[index].quantity > 1) {
                        this.cart[index].quantity -= 1;
                    } else {
                        this.cart.splice(index, 1);
                    }
                },
                removeItem(id) {
                    this.cart = this.cart.filter((c) => c.id !== id);
                },
                qtyOf(id) {
                    return this.cart.find((c) => c.id === id)?.quantity || 0;
                },
                resetCart() {
                    this.cart = [];
                    this.customerName = '';
                    this.customerPhone = '';
                    this.cartTableNumber = '';
                    this.cartNotes = '';
                    this.amountPaid = '';
                    this.feedback = null;
                },

                /* ---------- Kasir submit ---------- */
                async submitCashOrder() {
                    if (!this.cart.length || this.submitting) return;
                    this.submitting = true;
                    this.feedback = null;

                    try {
                        const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                        const res = await fetch('{{ route('admin.kasir.order') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': token,
                            },
                            body: JSON.stringify({
                                customer_name: this.customerName,
                                customer_phone: this.customerPhone,
                                table_number: this.cartTableNumber,
                                notes: this.cartNotes,
                                amount_paid: this.amountPaid !== '' ? Number(this.amountPaid) : null,
                                items: this.cart.map((item) => ({ menu_item_id: item.id, quantity: item.quantity })),
                            }),
                        });

                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) throw new Error(data.message || 'Gagal menyimpan pesanan.');

                        this.feedback = { type: 'success', text: data.message };
                        this.resetCart();
                        this.tab = 'queue';
                        this.fetchOrders();
                    } catch (error) {
                        this.feedback = { type: 'error', text: error.message || 'Terjadi kesalahan.' };
                    } finally {
                        this.submitting = false;
                    }
                },

                /* ---------- Queue: muat & polling antrean ---------- */
                fetchOrders() {
                    if (this.fetching) return;
                    this.fetching = true;

                    return fetch('{{ route('admin.api.orders.active') }}', {
                        headers: { 'Accept': 'application/json' },
                    })
                        .then((res) => {
                            if (!res.ok) {
                                console.error('Gagal memuat antrean (HTTP ' + res.status + ')');
                                return null;
                            }
                            return res.json();
                        })
                        .then((data) => {
                            if (data) {
                                this.activeOrders = Array.isArray(data) ? data : (data.orders || []);
                                console.log('Antrean dimuat:', this.activeOrders.length, 'pesanan');
                            }
                        })
                        .catch((error) => {
                            console.error('Error saat memuat antrean:', error);
                        })
                        .finally(() => {
                            this.fetching = false;
                        });
                },

                /* ---------- Helpers ---------- */
                formatRupiah(value) {
                    return 'Rp ' + Number(value || 0).toLocaleString('id-ID');
                },
                statusMeta(status) {
                    return {
                        pending: { label: 'Menunggu', cls: 'bg-terracotta-100 text-terracotta-700' },
                        success: { label: 'Lunas', cls: 'bg-sage-100 text-sage-600' },
                        failed: { label: 'Gagal', cls: 'bg-terracotta-200 text-terracotta-700' },
                        expired: { label: 'Kedaluwarsa', cls: 'bg-coffee-100 text-coffee-600' },
                    }[status] || { label: status, cls: 'bg-cream-200 text-coffee-600' };
                },
                methodMeta(method) {
                    return {
                        tunai: { label: 'Tunai', cls: 'bg-sage-100 text-sage-600' },
                        online: { label: 'Online', cls: 'bg-terracotta-100 text-terracotta-700' },
                    }[method] || { label: method || '-', cls: 'bg-cream-200 text-coffee-600' };
                },

                /* ---------- Lifecycle: render konten langsung saat dibuka ---------- */
                init() {
                    this.fetchOrders();
                    this.polling = setInterval(() => this.fetchOrders(), 10000);
                },
                destroy() {
                    if (this.polling) clearInterval(this.polling);
                    this.polling = null;
                },
            }));
        });
    </script>
</x-admin-layout>