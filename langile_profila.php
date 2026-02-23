<?php
$pageTitle = 'Tristras - Barber Profila';
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barber') {
    header('Location: saioa_hasi.php');
    exit;
}

$erabiltzaileStmt = $pdo->prepare("SELECT * FROM erabiltzaileak WHERE id = ?");
$erabiltzaileStmt->execute([$_SESSION['user_id']]);
$erabiltzaileDatuak = $erabiltzaileStmt->fetch();
?>

<div class="relative flex min-h-screen w-full flex-col pb-24 max-w-md mx-auto">
    <header class="p-6 pt-12 flex items-center justify-between">
        <a href="langile_panela.php" class="size-10 rounded-full bg-slate-800/50 flex items-center justify-center text-white">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="titulu-nagusia">Nire Profila</h1>
        <div class="w-10"></div>
    </header>

    <div class="px-6 flex flex-col items-center mt-4">
        <div class="relative">
            <div class="size-24 rounded-full border-4 border-primary/30 overflow-hidden ring-4 ring-primary/10 flex items-center justify-center bg-slate-800">
                <?php if ($erabiltzaileDatuak['irudia']): ?>
                    <img src="<?= htmlspecialchars($erabiltzaileDatuak['irudia']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="material-symbols-outlined text-[64px] text-white">person</span>
                <?php endif; ?>
            </div>
            <div class="absolute bottom-0 right-0 size-6 bg-green-500 border-2 border-[#0f172a] rounded-full"></div>
        </div>
        <h2 class="titulu-ertaina mt-4"><?= htmlspecialchars($erabiltzaileDatuak['izena']) ?></h2>
        <p class="testu-lagungarria">Barber Profesionala</p>
        
        <div class="w-full mt-10 space-y-6">
            <div class="space-y-4">
                <h3 class="etiketa-txikia px-1">Laneko informazioa</h3>
                <div class="panela-premium p-4 divide-y divide-white/5 divide-y divide-white/5">
                    <div class="py-3 flex justify-between">
                        <span class="testu-lagungarria">Emaila</span>
                        <span class="titulu-txikia font-medium"><?= htmlspecialchars($erabiltzaileDatuak['posta']) ?></span>
                    </div>
                    <div class="py-3 flex justify-between">
                        <span class="testu-lagungarria">Telefonoa</span>
                        <span class="titulu-txikia font-medium"><?= htmlspecialchars($erabiltzaileDatuak['telefonoa'] ?: 'Ez dago') ?></span>
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
    <nav class="fixed bottom-0 left-0 w-full border-t border-white/5 bg-slate-900 px-2 pb-5 pt-3 max-w-md mx-auto z-40">
        <div class="flex justify-around items-center">
            <a class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl py-1 text-slate-400" href="langile_panela.php">
                <span class="material-symbols-outlined">calendar_month</span>
                <p class="text-[10px] font-medium">Egutegia</p>
            </a>
            <a class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl py-1 text-slate-400" href="bezero_zerrenda.php">
                <span class="material-symbols-outlined">group</span>
                <p class="text-[10px] font-medium">Bezeroak</p>
            </a>
            <a class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl py-1 text-primary" href="langile_profila.php">
                <span class="material-symbols-outlined fill">person</span>
                <p class="text-[10px] font-bold">Profila</p>
            </a>
        </div>
    </nav>
</div>

<?php require_once 'includes/footer.php'; ?>


