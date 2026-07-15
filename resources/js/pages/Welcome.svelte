<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login } from '@/routes';
    import { register } from '@/routes';
    import type { Team } from '@/types';

    const auth = $derived(page.props.auth);
    const currentTeam = $derived(page.props.currentTeam as Team | null);
    const dashboardUrl = $derived(
        currentTeam ? dashboard(currentTeam.slug) : '/',
    );

    const features = [
        {
            title: 'Catatan Pekerjaan & Proyek',
            description: 'Kelola rencana kerja, notulen rapat, daftar tugas, dan dokumentasi proyek dalam satu tempat.',
            icon: '📋',
        },
        {
            title: 'Jurnal & Pengembangan Diri',
            description: 'Tulis jurnal harian, tetapkan tujuan, catat gratitude, dan lacak perkembangan diri.',
            icon: '📝',
        },
        {
            title: 'Manajemen Keuangan',
            description: 'Pantau pengeluaran, anggaran bulanan, investasi, dan aset pribadi Anda.',
            icon: '💰',
        },
        {
            title: 'Keluarga & Sosial',
            description: 'Rencanakan acara keluarga, catat ulang tangga, dan kelola kontak penting.',
            icon: '👨‍👩‍👧‍👦',
        },
        {
            title: 'Kreatif & Teknis',
            description: 'Simpan skrip video, naskah cerita, kode snippet, dan ide kreatif lainnya.',
            icon: '🎨',
        },
        {
            title: 'Kolaborasi Tim',
            description: 'Bagikan catatan dengan tim, berikan komentar, dan kerja sama secara real-time.',
            icon: '🤝',
        },
    ];
</script>

<AppHead title="Welcome">
    <link rel="preconnect" href="https://rsms.me/" />
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
</AppHead>

<div class="flex min-h-screen flex-col bg-white dark:bg-[#0a0a0a]">
    <!-- Navigation -->
    <header class="sticky top-0 z-50 border-b border-gray-200/60 bg-white/80 backdrop-blur-md dark:border-gray-800/60 dark:bg-[#0a0a0a]/80">
        <nav class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <div class="flex items-center gap-3">
                <img src="/logo.jpeg" alt="Noteds" class="h-9 w-9 rounded-lg object-cover" />
                <span class="text-xl font-bold text-gray-900 dark:text-white">Noteds</span>
            </div>
            <div class="flex items-center gap-3">
                {#if auth.user}
                    <Link
                        href={toUrl(dashboardUrl)}
                        class="inline-flex items-center rounded-lg bg-[#2563EB] px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-[#1D4ED8] hover:shadow-md active:scale-[0.98]"
                    >
                        Dashboard
                    </Link>
                {:else}
                    <Link
                        href={toUrl(login())}
                        class="inline-flex items-center rounded-lg px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Masuk
                    </Link>
                    <Link
                        href={toUrl(register())}
                        class="inline-flex items-center rounded-lg bg-[#2563EB] px-5 py-2.5 text-sm font-medium text-white shadow-sm transition-all hover:bg-[#1D4ED8] hover:shadow-md active:scale-[0.98]"
                    >
                        Daftar Gratis
                    </Link>
                {/if}
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-[#2563EB]/5 via-transparent to-[#1E3A8A]/5 dark:from-[#2563EB]/10 dark:to-[#1E3A8A]/10"></div>
        <div class="relative mx-auto max-w-6xl px-6 py-24 text-center lg:py-32">
            <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-[#2563EB]/20 bg-[#2563EB]/5 px-4 py-1.5 text-sm font-medium text-[#2563EB] dark:border-[#60A5FA]/20 dark:bg-[#60A5FA]/10 dark:text-[#60A5FA]">
                <span class="h-1.5 w-1.5 rounded-full bg-[#2563EB] dark:bg-[#60A5FA]"></span>
                Platform Catatan All-in-One
            </div>
            <h1 class="mb-6 text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-5xl lg:text-6xl">
                Satu Tempat untuk
                <span class="bg-gradient-to-r from-[#2563EB] to-[#1E3A8A] bg-clip-text text-transparent dark:from-[#60A5FA] dark:to-[#3B82F6]">
                    Semua Catatan Anda
                </span>
            </h1>
            <p class="mx-auto mb-10 max-w-2xl text-lg text-gray-600 dark:text-gray-400">
                Kelola catatan pekerjaan, jurnal pribadi, keuangan, keluarga, dan proyek kreatif — semuanya dalam satu platform yang terintegrasi.
            </p>
            <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                {#if !auth.user}
                    <Link
                        href={toUrl(register())}
                        class="inline-flex items-center gap-2 rounded-xl bg-[#2563EB] px-8 py-3.5 text-base font-semibold text-white shadow-lg shadow-[#2563EB]/25 transition-all hover:bg-[#1D4ED8] hover:shadow-xl hover:shadow-[#2563EB]/30 active:scale-[0.98]"
                    >
                        Mulai Gratis
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                    <Link
                        href={toUrl(login())}
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-8 py-3.5 text-base font-semibold text-gray-700 transition-all hover:bg-gray-50 hover:shadow-md active:scale-[0.98] dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Sudah punya akun? Masuk
                    </Link>
                {:else}
                    <Link
                        href={toUrl(dashboardUrl)}
                        class="inline-flex items-center gap-2 rounded-xl bg-[#2563EB] px-8 py-3.5 text-base font-semibold text-white shadow-lg shadow-[#2563EB]/25 transition-all hover:bg-[#1D4ED8] hover:shadow-xl active:scale-[0.98]"
                    >
                        Buka Dashboard
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                {/if}
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="border-t border-gray-200/60 bg-gray-50/50 py-20 dark:border-gray-800/60 dark:bg-[#111111]">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-14 text-center">
                <h2 class="mb-3 text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                    Segala Jenis Catatan, Satu Platform
                </h2>
                <p class="mx-auto max-w-2xl text-gray-600 dark:text-gray-400">
                    Dari catatan kerja hingga jurnal pribadi, Noteds dirancang untuk mengakomodasi semua kebutuhan catatan Anda.
                </p>
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                {#each features as feature}
                    <div class="group rounded-2xl border border-gray-200/60 bg-white p-6 transition-all hover:border-[#2563EB]/30 hover:shadow-lg hover:shadow-[#2563EB]/5 dark:border-gray-800/60 dark:bg-[#161616] dark:hover:border-[#3B82F6]/30">
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-xl bg-[#2563EB]/10 text-2xl transition-colors group-hover:bg-[#2563EB]/15 dark:bg-[#60A5FA]/10">
                            {feature.icon}
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
                            {feature.title}
                        </h3>
                        <p class="text-sm leading-relaxed text-gray-600 dark:text-gray-400">
                            {feature.description}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="border-t border-gray-200/60 py-20 dark:border-gray-800/60">
        <div class="mx-auto max-w-6xl px-6">
            <div class="mb-14 text-center">
                <h2 class="mb-3 text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                    Mulai dalam 3 Langkah
                </h2>
                <p class="mx-auto max-w-2xl text-gray-600 dark:text-gray-400">
                    Daftar, buat tim, dan mulai mencatat — semudah itu.
                </p>
            </div>
            <div class="grid gap-8 md:grid-cols-3">
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#2563EB] text-xl font-bold text-white shadow-lg shadow-[#2563EB]/25">1</div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Buat Akun</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Daftar gratis dalam hitungan detik. Verifikasi email untuk keamanan ekstra.</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#2563EB] text-xl font-bold text-white shadow-lg shadow-[#2563EB]/25">2</div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Buat Tim</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Buat tim pribadi atau undang rekan untuk berkolaborasi bersama.</p>
                </div>
                <div class="text-center">
                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-[#2563EB] text-xl font-bold text-white shadow-lg shadow-[#2563EB]/25">3</div>
                    <h3 class="mb-2 text-lg font-semibold text-gray-900 dark:text-white">Mulai Mencatat</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Buat catatan pertama Anda dan rasakan produktivitas yang berbeda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Security Section -->
    <section class="border-t border-gray-200/60 bg-gray-50/50 py-20 dark:border-gray-800/60 dark:bg-[#111111]">
        <div class="mx-auto max-w-6xl px-6">
            <div class="flex flex-col items-center gap-12 lg:flex-row">
                <div class="flex-1">
                    <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                        Keamanan adalah Prioritas
                    </h2>
                    <p class="mb-8 text-gray-600 dark:text-gray-400">
                        Catatan Anda dilindungi dengan berbagai lapisan keamanan terbaik.
                    </p>
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#2563EB]/10">
                                <svg class="h-3.5 w-3.5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Two-Factor Authentication (2FA)</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Lapisan keamanan tambahan dengan TOTP via Google Authenticator.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#2563EB]/10">
                                <svg class="h-3.5 w-3.5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Passkey / WebAuthn</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Login tanpa password menggunakan biometrik atau hardware key.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#2563EB]/10">
                                <svg class="h-3.5 w-3.5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Enkripsi & CSRF Protection</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Semua data terenkripsi dengan proteksi CSRF, XSS, dan SQL Injection.</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#2563EB]/10">
                                <svg class="h-3.5 w-3.5 text-[#2563EB]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 dark:text-white">Role-Based Access Control</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Atur izin akses untuk setiap anggota tim — admin, editor, viewer.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="relative rounded-2xl border border-gray-200/60 bg-white p-8 shadow-xl dark:border-gray-800/60 dark:bg-[#161616]">
                        <div class="mb-6 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-xl">
                                <img src="/logo.jpeg" alt="Noteds" class="h-10 w-10 object-cover" />
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 dark:text-white">Noteds Security</h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Enterprise-grade protection</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-[#1a1a1a]">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Password</span>
                                <span class="flex items-center gap-1.5 text-sm font-medium text-green-600 dark:text-green-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Aktif
                                </span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-[#1a1a1a]">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Two-Factor Auth</span>
                                <span class="flex items-center gap-1.5 text-sm font-medium text-green-600 dark:text-green-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Tersedia
                                </span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-[#1a1a1a]">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Passkey</span>
                                <span class="flex items-center gap-1.5 text-sm font-medium text-green-600 dark:text-green-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Tersedia
                                </span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-gray-50 px-4 py-3 dark:bg-[#1a1a1a]">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Email Verified</span>
                                <span class="flex items-center gap-1.5 text-sm font-medium text-green-600 dark:text-green-400">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="border-t border-gray-200/60 py-20 dark:border-gray-800/60">
        <div class="mx-auto max-w-4xl px-6 text-center">
            <h2 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                Siap untuk Mulai?
            </h2>
            <p class="mb-8 text-gray-600 dark:text-gray-400">
                Bergabung dengan Noteds dan rasakan pengalaman mencatat yang berbeda.
            </p>
            {#if !auth.user}
                <Link
                    href={toUrl(register())}
                    class="inline-flex items-center gap-2 rounded-xl bg-[#2563EB] px-8 py-3.5 text-base font-semibold text-white shadow-lg shadow-[#2563EB]/25 transition-all hover:bg-[#1D4ED8] hover:shadow-xl active:scale-[0.98]"
                >
                    Daftar Gratis Sekarang
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
            {:else}
                <Link
                    href={toUrl(dashboardUrl)}
                    class="inline-flex items-center gap-2 rounded-xl bg-[#2563EB] px-8 py-3.5 text-base font-semibold text-white shadow-lg shadow-[#2563EB]/25 transition-all hover:bg-[#1D4ED8] hover:shadow-xl active:scale-[0.98]"
                >
                    Buka Dashboard
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
            {/if}
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-gray-200/60 bg-gray-50/50 py-10 dark:border-gray-800/60 dark:bg-[#111111]">
        <div class="mx-auto flex max-w-6xl flex-col items-center justify-between gap-4 px-6 sm:flex-row">
            <div class="flex items-center gap-2">
                <img src="/logo.jpeg" alt="Noteds" class="h-6 w-6 rounded object-cover" />
                <span class="text-sm font-semibold text-gray-900 dark:text-white">Noteds</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                &copy; {new Date().getFullYear()} Noteds. All rights reserved.
            </p>
        </div>
    </footer>
</div>
