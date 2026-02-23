<?php
require_once 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: saioa_hasi.php');
    exit;
}

$pageTitle = 'Tristras - Nire Profila';
require_once 'includes/header.php';

$erabiltzaileStmt = $pdo->prepare("SELECT * FROM erabiltzaileak WHERE id = ?");
$erabiltzaileStmt->execute([$_SESSION['user_id']]);
$erabiltzaileDatuak = $erabiltzaileStmt->fetch();
?>

<div class="relative flex min-h-screen w-full flex-col pb-24 max-w-md mx-auto">
    <header class="p-6 pt-12 flex items-center justify-between">
        <a href="bezero_panela.php" class="size-10 rounded-full bg-slate-800/50 flex items-center justify-center text-white">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="titulu-nagusia">Nire Profila</h1>
        <div class="w-10"></div>
    </header>

    <div class="px-6 flex flex-col items-center mt-4">
        <div class="size-24 rounded-full bg-primary/20 border-4 border-primary/30 flex items-center justify-center mb-4 overflow-hidden">
            <?php if ($erabiltzaileDatuak['irudia']): ?>
                <img src="<?= htmlspecialchars($erabiltzaileDatuak['irudia']) ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <span class="material-symbols-outlined text-[64px] text-white">person</span>
            <?php endif; ?>
        </div>
        <h2 class="titulu-ertaina"><?= htmlspecialchars($erabiltzaileDatuak['izena']) ?></h2>
        <p class="testu-lagungarria"><?= htmlspecialchars($erabiltzaileDatuak['posta']) ?></p>
        
        <div class="w-full mt-10 space-y-6">
            <div class="space-y-4">
                <h3 class="etiketa-txikia px-1">Kontuaren informazioa</h3>
                <div class="panela-premium p-4 divide-y divide-white/5 divide-y divide-white/5">
                    <div class="py-3 flex justify-between">
                        <span class="testu-lagungarria">Telefonoa</span>
                        <span class="titulu-txikia font-medium"><?= htmlspecialchars($erabiltzaileDatuak['telefonoa'] ?: 'Ez dago zehaztuta') ?></span>
                    </div>
                    <div class="py-3 flex justify-between">
                        <span class="testu-lagungarria">Rola</span>
                        <span class="titulu-txikia capitalize font-medium"><?= htmlspecialchars($erabiltzaileDatuak['rola']) ?></span>
                    </div>
                </div>
            </div>

            <a href="saioa_itxi.php" class="w-full flex items-center justify-center gap-2 bg-red-500/10 text-red-500 border border-red-500/20 py-4 rounded-xl font-bold hover:bg-red-500/20 transition-colors">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                Saioa Itxi
            </a>
        </div>
    </div>

    <!-- Bottom Nav -->
    <div class="fixed bottom-0 left-0 w-full z-40 max-w-md mx-auto">
        <div class="bg-slate-900/90 border-t border-slate-800 backdrop-blur-lg pb-6 pt-3 px-4">
            <div class="flex justify-around items-center">
                <a class="flex flex-col items-center gap-1" href="bezero_panela.php">
                    <span class="material-symbols-outlined text-slate-400">home</span>
                    <span class="text-slate-400 text-[10px]">Hasiera</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="bezero_hitzorduak.php">
                    <span class="material-symbols-outlined text-slate-400">calendar_month</span>
                    <span class="text-slate-400 text-[10px]">Hitzorduak</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="bezero_profila.php">
                    <span class="material-symbols-outlined text-primary" style="font-variation-settings: 'FILL' 1;">person</span>
                    <span class="text-white text-[10px]">Profila</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="ezarpenak.php">
                    <span class="material-symbols-outlined text-slate-400">settings</span>
                    <span class="text-slate-400 text-[10px]">Ezarpenak</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


