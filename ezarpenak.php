<?php
require_once 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: saioa_hasi.php');
    exit;
}

$pageTitle = 'Tristras - Ezarpenak';
require_once 'includes/header.php';

// Hizkuntza aldaketa kudeatu
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$erabiltzaileStmt = $pdo->prepare("SELECT izena, posta, irudia FROM erabiltzaileak WHERE id = ?");
$erabiltzaileStmt->execute([$_SESSION['user_id']]);
$erabiltzaileDatuak = $erabiltzaileStmt->fetch();
?>

<div class="relative flex h-full min-h-screen w-full flex-col mx-auto max-w-md bg-background-light dark:bg-background-dark shadow-2xl overflow-hidden">
    <!-- Header -->
    <header class="flex items-center justify-between px-4 pt-6 pb-4 bg-background-light dark:bg-background-dark z-10">
        <button onclick="history.back()" class="flex items-center justify-center w-10 h-10 rounded-full hover:bg-black/5 dark:hover:bg-white/10 transition-colors text-slate-900 dark:text-white">
            <span class="material-symbols-outlined ikono-lg">arrow_back_ios_new</span>
        </button>
        <h1 class="titulu-ertaina">Ezarpenak</h1>
        <div class="w-10"></div> 
    </header>

    <main class="flex-1 overflow-y-auto px-4 pb-24 relative z-0">
        <!-- Giroko dirdira -->
        <div class="giro-dirdira-nagusia opacity-50"></div>
        
        <!-- Erabiltzailearen informazioa -->
        <div class="flex flex-col items-center justify-center py-6 mb-6">
            <div class="relative w-24 h-24 mb-3 group cursor-pointer">
                <?php if ($erabiltzaileDatuak['irudia']): ?>
                    <img src="<?= htmlspecialchars($erabiltzaileDatuak['irudia']) ?>" class="w-full h-full object-cover rounded-full border-2 border-primary shadow-lg">
                <?php else: ?>
                    <div class="w-full h-full rounded-full bg-slate-800 border-2 border-primary flex items-center justify-center">
                        <span class="material-symbols-outlined ikono-xxl text-white">person</span>
                    </div>
                <?php endif; ?>
                <div class="absolute bottom-0 right-0 bg-primary text-white p-1.5 rounded-full border-2 border-background-dark flex items-center justify-center shadow-md">
                    <span class="material-symbols-outlined ikono-sm">edit</span>
                </div>
            </div>
            <h2 class="titulu-ertaina"><?= htmlspecialchars($erabiltzaileDatuak['izena']) ?></h2>
            <p class="testu-lagungarria"><?= htmlspecialchars($erabiltzaileDatuak['posta']) ?></p>
        </div>

        <!-- Settings Groups -->
        <div class="mb-6">
            <h3 class="etiketa-txikia px-2 mb-2">Orokorra</h3>
            <div class="panela-premium overflow-hidden divide-y divide-white/5 shadow-sm">
                <button class="w-full flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-800 hover:bg-white/5 transition-colors group">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary">
                            <span class="material-symbols-outlined ikono-md">language</span>
                        </div>
                        <span class="titulu-txikia font-medium">Hizkuntza</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="testu-lagungarria group-hover:text-primary transition-colors">Euskara</span>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                    </div>
                </button>
                <button class="w-full flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-800 hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-pink-500/10 text-pink-500">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                        </div>
                        <span class="titulu-txikia font-medium">Jakinarazpenak</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                    </div>
                </button>
                <button class="w-full flex items-center justify-between p-4 hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500">
                            <span class="material-symbols-outlined text-[20px]">dark_mode</span>
                        </div>
                        <span class="titulu-txikia font-medium">Itxura</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="testu-lagungarria">Iluna</span>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                    </div>
                </button>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="etiketa-txikia px-2 mb-2">Segurtasuna</h3>
            <div class="panela-premium overflow-hidden divide-y divide-white/5 shadow-sm">
                <button class="w-full flex items-center justify-between p-4 border-b border-slate-200 dark:border-slate-800 hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-500">
                            <span class="material-symbols-outlined text-[20px]">lock</span>
                        </div>
                        <span class="titulu-txikia font-medium">Pasahitza</span>
                    </div>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                </button>
                <button class="w-full flex items-center justify-between p-4 hover:bg-white/5 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-500/10 text-orange-500">
                            <span class="material-symbols-outlined text-[20px]">privacy_tip</span>
                        </div>
                        <span class="titulu-txikia font-medium">Pribatutasuna</span>
                    </div>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                </button>
            </div>
        </div>

        <a href="saioa_itxi.php" class="w-full block py-4 text-center text-red-500 text-sm font-bold hover:bg-red-500/10 rounded-xl transition-colors mb-8 border border-red-500/20">
            Saioa itxi
        </a>
    </main>

    <!-- Beheko nabigazioa -->
    <div class="fixed bottom-0 left-0 w-full z-40 max-w-md mx-auto">
        <div class="bg-slate-900/90 border-t border-slate-800 backdrop-blur-lg pb-6 pt-3 px-4">
            <div class="flex justify-around items-center">
                <a class="flex flex-col items-center gap-1" href="bezero_panela.php">
                    <span class="material-symbols-outlined ikono-lg text-slate-400">home</span>
                    <span class="text-slate-400 text-[10px]">Hasiera</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="bezero_hitzorduak.php">
                    <span class="material-symbols-outlined ikono-lg text-slate-400">calendar_month</span>
                    <span class="text-slate-400 text-[10px]">Hitzorduak</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="bezero_profila.php">
                    <span class="material-symbols-outlined ikono-lg text-slate-400">person</span>
                    <span class="text-slate-400 text-[10px]">Profila</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="ezarpenak.php">
                    <span class="material-symbols-outlined ikono-lg text-primary betea">settings</span>
                    <span class="text-white text-[10px]">Ezarpenak</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


