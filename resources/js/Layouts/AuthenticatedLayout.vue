<template>
  <div class="min-h-screen mesh-gradient flex">
    <!-- Mobile overlay -->
    <div v-if="mobileOpen" @click="mobileOpen = false" class="fixed inset-0 bg-black/60 z-40 lg:hidden"></div>

    <!-- Sidebar -->
    <aside :class="['fixed top-0 left-0 h-screen z-50 flex flex-col transition-all duration-300 ease-out',
      collapsed && !mobileOpen ? 'w-[72px]' : 'w-[240px]',
      mobileOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0']"
      :style="{ background: 'var(--nx-bg-sidebar)', borderRight: '1px solid var(--nx-border-subtle)' }">
      
      <div class="px-4 h-16 flex items-center gap-3" style="border-bottom: 1px solid var(--nx-border-subtle);">
        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-nexus-500 via-nexus-600 to-accent-cyan flex items-center justify-center flex-shrink-0 shadow-lg shadow-nexus-600/20 animate-glow">
          <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
        </div>
        <span v-if="!collapsed || mobileOpen" class="text-lg font-bold tracking-tight text-gradient">NEXUS</span>
        <button @click="mobileOpen = false" class="ml-auto lg:hidden p-1 text-nexus-500"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>

      <nav ref="sidebarNav" class="flex-1 p-3 space-y-1 overflow-y-auto">
        <a v-for="item in navItems" :key="item.href" :href="item.href" @click.prevent="navigate(item)"
          :class="['sidebar-item group', isActive(item.href) ? 'active' : '', item.locked ? 'opacity-50' : '']">
          <div :class="['w-5 h-5 flex-shrink-0', isActive(item.href) ? 'text-nexus-300' : 'text-nexus-600 group-hover:text-nexus-400']" v-html="item.icon"></div>
          <span v-if="!collapsed || mobileOpen" class="flex-1 truncate flex items-center gap-2">
            {{ item.label }}
            <svg v-if="item.locked" class="w-3.5 h-3.5 text-nexus-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
          </span>
          <span v-if="(!collapsed || mobileOpen) && item.badge && !item.locked" class="min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold flex items-center justify-center" :class="item.badgeColor">{{ item.badge }}</span>
        </a>
      </nav>

      <div class="p-3">
        <div v-if="!collapsed || mobileOpen" class="rounded-xl p-3 bg-gradient-to-br from-nexus-600/10 to-accent-cyan/5 border border-nexus-500/10">
          <div class="text-[10px] font-semibold uppercase tracking-wider text-nexus-500 mb-0.5">{{ $t('nav.plan') }}</div>
          <div class="text-sm font-bold text-nexus-200 capitalize">{{ merchant?.subscription_tier || 'Starter' }}</div>
          <div v-if="trialDaysLeft > 0" class="text-[10px] text-accent-cyan mt-1 font-medium">🎁 {{ trialDaysLeft }} {{ $t('nav.trialLeft') }}</div>
        </div>
      </div>

      <button @click="collapsed = !collapsed" class="mx-3 mb-3 p-2 rounded-lg text-nexus-600 hover:text-nexus-400 hover:bg-surface-hover transition-all hidden lg:flex items-center justify-center">
        <svg :class="['w-4 h-4 transition-transform duration-300', collapsed ? 'rotate-180' : '']" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" /></svg>
      </button>
    </aside>

    <!-- Main -->
    <div :class="['flex-1 flex flex-col min-h-screen transition-all duration-300', collapsed ? 'lg:ml-[72px]' : 'lg:ml-[240px]']">
      <!-- Top bar -->
      <header class="h-16 px-4 lg:px-6 flex items-center justify-between sticky top-0 z-30" :style="{ background: 'var(--nx-bg-header)', backdropFilter: 'blur(16px)', borderBottom: '1px solid var(--nx-border-subtle)' }">
        <div class="flex items-center gap-3">
          <button @click="mobileOpen = true" class="lg:hidden p-2 -ml-2 rounded-lg text-nexus-500 hover:text-nexus-300">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
          </button>
          <h1 class="text-base font-semibold text-nexus-100">{{ title || 'Dashboard' }}</h1>
        </div>
        <div class="flex items-center gap-2 lg:gap-4">
          <!-- Search -->
          <div class="relative group hidden sm:block">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-nexus-600 group-focus-within:text-nexus-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input v-model="searchQuery" @keydown.enter="doSearch" @input="onSearchInput" type="text" :placeholder="$t('common.search')" class="w-44 lg:w-52 pl-9 pr-4 py-2 rounded-xl bg-white/[0.03] border border-white/[0.06] text-sm text-nexus-200 placeholder-nexus-700 focus:outline-none focus:border-nexus-500/40 focus:bg-white/[0.05] focus:w-72 transition-all duration-300" />
            <!-- Search results -->
            <div v-if="searchResults.length" class="absolute top-full left-0 right-0 mt-2 glass-strong rounded-xl overflow-hidden shadow-2xl z-50">
              <div v-for="r in searchResults" :key="r.id" @click="goToResult(r)" class="px-4 py-2.5 hover:bg-surface-hover cursor-pointer flex items-center gap-3 text-sm border-b border-white/[0.03] last:border-0">
                <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded" :class="r.type === 'order' ? 'bg-accent-amber/10 text-accent-amber' : r.type === 'contact' ? 'bg-accent-violet/10 text-accent-violet' : 'bg-accent-cyan/10 text-accent-cyan'">{{ r.type }}</span>
                <span class="text-nexus-200">{{ r.label }}</span>
              </div>
            </div>
          </div>

          <!-- Notifications -->
          <div class="relative">
            <button @click="showNotifs = !showNotifs" class="relative p-2 rounded-xl hover:bg-surface-hover transition-all group">
              <svg class="w-5 h-5 text-nexus-500 group-hover:text-nexus-300 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
              <span v-if="unreadCount" class="absolute -top-0.5 -right-0.5 w-4 h-4 rounded-full bg-accent-rose text-white text-[9px] font-bold flex items-center justify-center animate-pulse-soft">{{ unreadCount }}</span>
            </button>
            <!-- Notification panel -->
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95 -translate-y-1" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0 scale-95">
              <div v-if="showNotifs" class="absolute right-0 top-full mt-2 w-80 glass-strong rounded-xl overflow-hidden shadow-2xl z-50">
                <div class="px-4 py-3 border-b border-white/[0.04] flex items-center justify-between">
                  <span class="text-sm font-semibold text-nexus-200">{{ $t('nav.notifications') }}</span>
                  <span v-if="unreadCount" class="badge badge-rose">{{ unreadCount }} {{ $t('common.new') }}</span>
                </div>
                <div class="max-h-72 overflow-y-auto divide-y divide-white/[0.03]">
                  <div v-for="n in notifications" :key="n.id" @click="goToNotif(n)" class="px-4 py-3 hover:bg-surface-hover cursor-pointer">
                    <div class="text-sm text-nexus-200">{{ n.title }}</div>
                    <div class="text-xs text-nexus-600 mt-0.5">{{ n.time }}</div>
                  </div>
                  <div v-if="!notifications.length" class="px-4 py-8 text-center text-sm text-nexus-600">{{ $t('nav.noNotifications') }}</div>
                </div>
              </div>
            </Transition>
          </div>

          <!-- User menu -->
          <div class="relative">
            <button @click="showUserMenu = !showUserMenu" class="flex items-center gap-2.5 pl-2 lg:pl-3 border-l border-white/[0.06] cursor-pointer hover:opacity-80 transition-opacity">
              <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-nexus-500 to-accent-violet flex items-center justify-center text-xs font-bold text-white shadow-md">{{ userInitials }}</div>
              <div class="hidden lg:block text-left">
                <div class="text-xs font-medium text-nexus-200">{{ auth?.user?.name }}</div>
                <div class="text-[10px] text-nexus-600">{{ auth?.user?.email }}</div>
              </div>
              <svg class="w-3 h-3 text-nexus-600 hidden lg:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 scale-95 -translate-y-1" enter-to-class="opacity-100 scale-100" leave-active-class="transition ease-in duration-150" leave-from-class="opacity-100" leave-to-class="opacity-0 scale-95">
              <div v-if="showUserMenu" class="absolute right-0 top-full mt-2 w-52 glass-strong rounded-xl overflow-hidden shadow-2xl z-50">
                <div class="px-4 py-3 border-b border-white/[0.04]">
                  <div class="text-sm font-medium text-nexus-200">{{ auth?.user?.name }}</div>
                  <div class="text-xs text-nexus-600">{{ auth?.user?.email }}</div>
                </div>
                <div class="py-1">
                  <Link v-if="auth?.user?.roles?.includes('admin')" href="/settings" @click="showUserMenu = false" class="block px-4 py-2.5 text-sm text-nexus-400 hover:text-nexus-200 hover:bg-surface-hover transition-all">{{ $t('common.settings') }}</Link>
                  <!-- Theme toggle -->
                  <button @click="toggleThemeMode" class="w-full text-left px-4 py-2.5 text-sm text-nexus-400 hover:text-nexus-200 hover:bg-surface-hover transition-all cursor-pointer flex items-center gap-2">
                    <span v-if="currentTheme === 'dark'">☀️ {{ $t('theme.light') }}</span>
                    <span v-else>🌙 {{ $t('theme.dark') }}</span>
                  </button>
                  <!-- Language picker -->
                  <button @click="cycleLocale" class="w-full text-left px-4 py-2.5 text-sm text-nexus-400 hover:text-nexus-200 hover:bg-surface-hover transition-all cursor-pointer flex items-center gap-2">
                    🌐 {{ currentLocaleName }}
                  </button>
                  <button @click="logout" class="w-full text-left px-4 py-2.5 text-sm text-accent-rose hover:bg-accent-rose/5 transition-all cursor-pointer">{{ $t('common.signOut') }}</button>
                </div>
              </div>
            </Transition>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="flex-1 p-4 lg:p-6 mesh-gradient-subtle">
        <slot />
      </main>
    </div>

    <!-- Close dropdowns on outside click -->
    <div v-if="showNotifs || showUserMenu || searchResults.length" @click="closeAll" class="fixed inset-0 z-20" style="pointer-events: auto;"></div>

    <!-- Toasts -->
    <div class="fixed bottom-6 right-6 z-[100] flex flex-col gap-3 pointer-events-none">
      <TransitionGroup enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 translate-y-4 scale-95" enter-to-class="opacity-100 translate-y-0 scale-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100 scale-100" leave-to-class="opacity-0 scale-95">
        <div v-for="toast in toasts" :key="toast.id" :class="['pointer-events-auto flex items-center gap-3 px-4 py-3.5 rounded-2xl shadow-2xl glass-strong border border-white/10 w-80', toast.type === 'error' ? 'bg-accent-rose/10 border-accent-rose/20' : 'bg-accent-emerald/10 border-accent-emerald/20']">
          <div :class="['w-9 h-9 rounded-full flex items-center justify-center shrink-0 shadow-inner', toast.type === 'error' ? 'bg-gradient-to-br from-accent-rose/30 to-accent-rose/10 text-accent-rose' : 'bg-gradient-to-br from-accent-emerald/30 to-accent-emerald/10 text-accent-emerald']">
            <svg v-if="toast.type === 'success'" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
            <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
          </div>
          <div class="flex-1">
            <div class="text-sm font-bold text-nexus-100">{{ toast.title }}</div>
            <div v-if="toast.message" class="text-xs text-nexus-400 mt-0.5 leading-snug">{{ toast.message }}</div>
          </div>
          <button @click="removeToast(toast.id)" class="ml-2 p-1.5 rounded-lg hover:bg-white/10 text-nexus-500 hover:text-white transition-colors"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
        </div>
      </TransitionGroup>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { usePage, router, Link } from '@inertiajs/vue3';
import { useTheme } from '@/composables/useTheme.js';
import { useI18n } from '@/i18n/index.js';

const { theme: currentTheme, toggleTheme: toggleThemeMode } = useTheme();
const { locale, setLocale, getAvailableLocales } = useI18n();

const availableLocales = getAvailableLocales();
const currentLocaleName = computed(() => {
  const loc = availableLocales.find(l => l.code === locale.value);
  return loc ? loc.name : locale.value;
});

function cycleLocale() {
  const codes = availableLocales.map(l => l.code);
  const idx = codes.indexOf(locale.value);
  const next = codes[(idx + 1) % codes.length];
  setLocale(next);
}

defineProps({ title: String });

const page = usePage();
const collapsed = ref(false);
const mobileOpen = ref(false);
const showNotifs = ref(false);
const showUserMenu = ref(false);
const searchQuery = ref('');
const searchResults = ref([]);
const sidebarNav = ref(null);

const auth = computed(() => page.props.auth);
const merchant = computed(() => page.props.auth?.merchant);
const stats = computed(() => page.props.stats || {});
const unreadCount = computed(() => stats.value.unread_conversations || 0);
const userInitials = computed(() => {
  const name = auth.value?.user?.name || '';
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
});

const trialDaysLeft = computed(() => {
  const exp = merchant.value?.subscription_expires_at;
  if (!exp) return 0;
  const diff = Math.ceil((new Date(exp) - new Date()) / (1000 * 60 * 60 * 24));
  return Math.max(0, diff);
});

// Toast logic
const toasts = ref([]);
let toastId = 0;

function addToast(title, message = '', type = 'success', duration = 4000) {
  const id = ++toastId;
  toasts.value.push({ id, title, message, type });
  setTimeout(() => removeToast(id), duration);
}

function removeToast(id) {
  toasts.value = toasts.value.filter(t => t.id !== id);
}

watch(() => page.props.flash, (flash) => {
  if (flash?.success) addToast('Success', flash.success, 'success');
  if (flash?.error) addToast('Error', flash.error, 'error');
}, { deep: true, immediate: true });

watch(() => page.props.errors, (errors) => {
  if (Object.keys(errors || {}).length > 0) {
    addToast('Validation Error', 'Please check the form for errors.', 'error');
  }
}, { deep: true });

// Build notifications
const notifications = computed(() => {
  const items = [];
  const { t } = useI18n();
  if (stats.value.unread_conversations) {
    items.push({ 
      id: 'inbox', 
      title: t('nav.unreadConv', { count: stats.value.unread_conversations }), 
      time: t('common.now'), 
      href: '/inbox' 
    });
  }
  if (stats.value.pending_orders) {
    items.push({ 
      id: 'orders', 
      title: t('nav.pendingOrders', { count: stats.value.pending_orders }), 
      time: t('common.now'), 
      href: '/orders' 
    });
  }
  if (stats.value.cod_pending) {
    items.push({ 
      id: 'cod', 
      title: t('nav.codToCollect', { amount: Number(stats.value.cod_amount || 0).toFixed(2) }), 
      time: t('common.today'), 
      href: '/orders' 
    });
  }
  return items;
});

// Search - filters orders/contacts from shared props or does navigation
const allOrders = computed(() => page.props.recentOrders || []);
const allContacts = computed(() => page.props.contacts?.data || []);

let searchTimeout = null;
function onSearchInput() {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    const q = searchQuery.value.toLowerCase().trim();
    if (!q) { searchResults.value = []; return; }
    const results = [];
    allOrders.value.filter(o => o.order_number?.toLowerCase().includes(q) || o.customer_name?.toLowerCase().includes(q)).slice(0, 3).forEach(o => {
      results.push({ id: `o-${o.id}`, type: 'order', label: `${o.order_number} — ${o.customer_name}`, href: `/orders/${o.id}` });
    });
    allContacts.value.filter(c => c.name?.toLowerCase().includes(q) || c.phone?.includes(q)).slice(0, 3).forEach(c => {
      results.push({ id: `c-${c.id}`, type: 'contact', label: `${c.name}${c.phone ? ' · ' + c.phone : ''}`, href: `/contacts/${c.id}` });
    });
    // Always offer full search
    if (q.length > 1) results.push({ id: 'search', type: 'search', label: `Search "${searchQuery.value}" in all pages...`, href: `/orders?search=${q}` });
    searchResults.value = results;
  }, 200);
}

function doSearch() {
  if (searchQuery.value.trim()) {
    router.visit(`/orders?search=${searchQuery.value.trim()}`);
    searchResults.value = [];
  }
}

function goToResult(r) { router.visit(r.href); searchResults.value = []; searchQuery.value = ''; }
function goToNotif(n) { router.visit(n.href); showNotifs.value = false; }
function closeAll() { showNotifs.value = false; showUserMenu.value = false; searchResults.value = []; }
function navigate(item) { 
  if (item.locked) {
    if (item.adminOnly) {
      addToast('Access Denied', `${item.label} is restricted to administrators.`, 'error');
    } else {
      if (auth.value?.user?.roles?.includes('admin')) {
        const title = item.lockedTitle || 'Upgrade Required';
        const msg = item.lockedMessage || `${item.label} is locked. Upgrade your plan to unlock this feature.`;
        addToast(title, msg, 'error');
        router.visit('/subscription');
      } else {
        const msg = item.lockedMessage || `${item.label} is locked on this plan. Please ask your administrator to upgrade.`;
        addToast('Upgrade Required', msg, 'error');
      }
    }
    return;
  }
  mobileOpen.value = false; 
  router.visit(item.href, { preserveScroll: true, preserveState: false }); 
}
function logout() { router.post('/logout'); }

function isActive(href) {
  const path = window.location.pathname;
  if (href === '/') return path === '/';
  return path.startsWith(href);
}

// Close mobile sidebar on route change
watch(() => page.url, () => { mobileOpen.value = false; });

onMounted(() => {
  if (sidebarNav.value) {
    const savedScroll = sessionStorage.getItem('nexus_sidebar_scroll');
    if (savedScroll) {
      sidebarNav.value.scrollTop = parseInt(savedScroll, 10);
    }
    
    sidebarNav.value.addEventListener('scroll', () => {
      sessionStorage.setItem('nexus_sidebar_scroll', sidebarNav.value.scrollTop);
    });
  }
});

const navItems = computed(() => {
  const features = merchant.value?.features || [];
  const isAdmin = auth.value?.user?.roles?.includes('admin') || false;
  const { t } = useI18n();
  return [
    { href: '/', label: t('nav.dashboard'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>' },
    { href: '/inbox', label: t('nav.inbox'), locked: !features.includes('inbox'), lockedTitle: t('nav.inbox'), badge: stats.value.unread_conversations || null, badgeColor: 'bg-accent-cyan/15 text-accent-cyan', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" /></svg>' },
    { href: '/orders', label: t('nav.orders'), locked: !features.includes('orders'), lockedTitle: t('nav.orders'), badge: stats.value.pending_orders || null, badgeColor: 'bg-accent-amber/15 text-accent-amber', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>' },
    { href: '/products', label: t('nav.products'), locked: !features.includes('products'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0l-3-3m3 3l3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>' },
    { href: '/contacts', label: t('nav.contacts'), locked: !features.includes('contacts'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>' },
    { href: '/broadcasts', label: t('nav.broadcasts'), locked: !features.includes('broadcasts'), lockedTitle: t('nav.broadcasts'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M10.34 15.84c-.688-.06-1.386-.09-2.09-.09H7.5a4.5 4.5 0 110-9h.75c.704 0 1.402-.03 2.09-.09m0 9.18c.253.962.584 1.892.985 2.783.247.55.06 1.21-.463 1.511l-.657.38c-.551.318-1.26.117-1.527-.461a20.845 20.845 0 01-1.44-4.282m3.102.069a18.03 18.03 0 01-.59-4.59c0-1.586.205-3.124.59-4.59m0 9.18a23.848 23.848 0 018.835 2.535M10.34 6.66a23.847 23.847 0 008.835-2.535m0 0A23.74 23.74 0 0018.795 3m.38 1.125a23.91 23.91 0 011.014 5.395m-1.014-5.395c-.318.55-.117 1.26.461 1.527l.38.657c.301.522.96.709 1.511.463a20.845 20.845 0 00-4.282-1.44m0 0a18.03 18.03 0 014.59-.59c1.586 0 3.124.205 4.59.59" /></svg>' },
    { href: '/templates', label: t('nav.quickReplies'), locked: !features.includes('templates'), lockedTitle: t('nav.quickReplies'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>' },
    { href: '/follow-ups', label: t('nav.followUps'), locked: !features.includes('followups'), lockedTitle: t('nav.followUps'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3" /></svg>' },
    { href: '/coupons', label: t('nav.coupons'), locked: !features.includes('coupons'), lockedTitle: t('nav.coupons'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>' },
    { href: '/invoices', label: t('nav.invoices'), locked: !features.includes('invoices'), lockedTitle: t('nav.invoices'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>' },
    { href: '/returns', label: t('nav.returns'), locked: !features.includes('returns'), lockedTitle: t('nav.returns'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" /></svg>' },
    { href: '/csat', label: t('nav.satisfaction'), locked: !features.includes('csat'), lockedTitle: t('nav.satisfaction'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" /></svg>' },
    { href: '/team-activity', label: t('nav.teamActivity'), locked: !features.includes('team_activity'), lockedTitle: t('nav.teamActivity'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>' },
    { href: '/forecast', label: t('nav.forecast'), locked: !features.includes('forecast'), lockedTitle: t('nav.forecast'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18L9 11.25l4.306 4.307a11.95 11.95 0 015.814-5.519l2.74-1.22m0 0l-5.94-2.28m5.94 2.28l-2.28 5.941" /></svg>' },
    { href: '/storefront', label: t('nav.storefront'), locked: !features.includes('storefront'), lockedTitle: t('nav.storefront'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" /></svg>' },
    { href: '/whatsapp-catalog', label: t('nav.waCatalog'), locked: !features.includes('whatsapp_sync'), lockedTitle: t('nav.waCatalog'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>' },
    { href: '/settings/ai', label: t('nav.aiAssistant'), locked: !features.includes('ai_chat'), lockedTitle: t('nav.aiAssistant'), lockedMessage: 'Auto-replies, order detection, and draft order creation. Your inbox runs itself, 24/7.', icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z" /></svg>' },
    { href: '/analytics', label: t('nav.analytics'), locked: !features.includes('analytics'), lockedTitle: t('nav.analytics'), icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>' },
    { href: '/settings', label: t('nav.settings'), adminOnly: true, locked: !isAdmin, icon: '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>' },
  ];
});
</script>
