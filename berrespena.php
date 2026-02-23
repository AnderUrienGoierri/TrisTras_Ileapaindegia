<?php
require_once 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$hitzorduId = $_GET['id'] ?? null;

if (!$hitzorduId) {
    header('Location: bezero_panela.php');
    exit;
}

$pageTitle = 'Tristras - Erreserba Berretsia';
require_once 'includes/header.php';

$stmt = $pdo->prepare("
    SELECT a.*, s.izena as zerbitzu_izena, u.izena as langile_izena, u.posta as langile_posta, u.irudia as langile_irudia
    FROM hitzorduak a 
    JOIN zerbitzuak s ON a.zerbitzu_id = s.id
    JOIN erabiltzaileak u ON a.langile_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$hitzorduId]);
$hitzordua = $stmt->fetch();

if (!$hitzordua) {
    header('Location: bezero_panela.php');
    exit;
}
?>



<!-- Top App Bar -->
<header class="flex items-center justify-between p-4 sticky top-0 z-50 bg-background-light/80 dark:bg-background-dark/80 backdrop-blur-md border-b border-white/5 max-w-md mx-auto">
    <a href="bezero_panela.php" class="flex size-10 shrink-0 items-center justify-center rounded-full text-slate-400 hover:bg-white/10 transition-colors">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>
    <h2 class="titulu-ertaina">Erreserba Berretsia</h2>
    <div class="size-10"></div>
</header>

<!-- Main Content -->
<main class="flex-1 flex flex-col items-center justify-start px-4 pt-6 pb-24 w-full max-w-md mx-auto relative">
    <!-- Success Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center size-16 rounded-full bg-emerald-500/20 text-emerald-500 mb-4 ring-1 ring-emerald-500/50 shadow-[0_0_40px_-5px_rgba(16,185,129,0.4)]">
            <span class="material-symbols-outlined text-[32px] font-bold">check</span>
        </div>
        <h1 class="titulu-nagusia mb-2">Hitzordua baieztatuta!</h1>
        <p class="testu-lagungarria">Zure erreserba sisteman gorde da.</p>
    </div>

    <!-- Digital Ticket Card -->
    <div class="w-full panela-premium overflow-hidden relative group">
        <!-- Top Section: QR Code Simulation -->
        <div class="p-8 flex flex-col items-center justify-center relative bg-gradient-to-b from-transparent to-black/20">
            <div class="absolute inset-0 bg-primary/5 opacity-50"></div>
            <div class="relative z-10 p-4 bg-white rounded-xl shadow-lg border-4 border-white/10">
                <div class="absolute inset-0 bg-emerald-500/30 blur-xl rounded-full scale-110 -z-10 animate-pulse"></div>
                <!-- Imitatutako QR -->
                <div class="size-36 bg-slate-100 flex items-center justify-center p-2 rounded">
                    <span class="material-symbols-outlined text-slate-800 ikono-erraldoi">qr_code_2</span>
                </div>
            </div>
            <p class="mt-4 etiketa-txikia">Hitzordu ID: #<?= str_pad($hitzordua['id'], 6, "0", STR_PAD_LEFT) ?></p>
        </div>

        <!-- Ebaki banatzailea -->
        <div class="txartel-ebakidura relative h-8 w-full flex items-center justify-center">
            <div class="h-px w-full mx-6 linea-etena opacity-30 dark:opacity-20"></div>
        </div>

        <!-- Bottom Section: Details -->
        <div class="p-6 pt-2 space-y-5">
            <!-- Barber Row -->
            <div class="flex items-center gap-3">
                <div class="relative">
                    <?php if ($hitzordua['langile_irudia']): ?>
                        <img alt="<?= htmlspecialchars($hitzordua['langile_izena']) ?>" class="size-12 rounded-full object-cover border-2 border-primary/30" 
                             src="<?= htmlspecialchars($hitzordua['langile_irudia']) ?>">
                    <?php else: ?>
                        <div class="size-12 rounded-full bg-slate-700 flex items-center justify-center border-2 border-primary/30">
                            <span class="material-symbols-outlined text-slate-500">person</span>
                        </div>
                    <?php endif; ?>
                    <div class="absolute bottom-0 right-0 size-3 bg-emerald-500 border-2 border-white dark:border-[#1f1b29] rounded-full"></div>
                </div>
                <div>
                    <p class="etiketa-txikia mb-0.5">Barberoa</p>
                    <p class="titulu-txikia"><?= htmlspecialchars($hitzordua['langile_izena']) ?></p>
                </div>
            </div>
                <button class="p-2 text-primary hover:bg-primary/10 rounded-full transition-colors">
                    <span class="material-symbols-outlined ikono-md">chat</span>
                </button>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-background-light dark:bg-slate-900/50 p-3 rounded-xl border border-gray-100 dark:border-white/5">
                    <div class="flex items-center gap-2 mb-1 text-slate-500 dark:text-slate-400">
                        <span class="material-symbols-outlined ikono-sm">calendar_today</span>
                        <span class="text-xs font-medium uppercase">Data</span>
                    </div>
                    <p class="font-mono text-sm font-semibold text-slate-900 dark:text-white tracking-tight"><?= date('M j', strtotime($hitzordua['data'])) ?></p>
                    <p class="text-xs text-slate-500"><?= date('l', strtotime($hitzordua['data'])) ?></p>
                </div>
                <div class="bg-background-light dark:bg-slate-900/50 p-3 rounded-xl border border-gray-100 dark:border-white/5">
                    <div class="flex items-center gap-2 mb-1 text-slate-500 dark:text-slate-400">
                        <span class="material-symbols-outlined ikono-sm">schedule</span>
                        <span class="text-xs font-medium uppercase">Ordua</span>
                    </div>
                    <p class="font-mono text-sm font-semibold text-slate-900 dark:text-white tracking-tight"><?= substr($hitzordua['hasiera'], 0, 5) ?></p>
                    <p class="text-xs text-emerald-500 font-medium">Garaiz</p>
                </div>
            </div>

            <!-- Zerbitzu ilara -->
            <div class="flex justify-between items-center py-2 border-t border-gray-100 dark:border-white/5">
                <p class="text-sm text-slate-500 dark:text-slate-400">Zerbitzua</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-slate-200"><?= htmlspecialchars($hitzordua['zerbitzu_izena']) ?></p>
            </div>

            <!-- Guztira ilara -->
            <div class="flex justify-between items-center pt-2">
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Guztira</p>
                <p class="text-lg font-bold text-primary dark:text-primary-400"><?= number_format($hitzordua['prezioa'], 2) ?>€</p>
            </div>
        </div>
    </div>
</main>

<!-- Footer Actions -->
<div class="fixed bottom-0 left-0 w-full p-4 bg-background-light/95 dark:bg-background-dark/95 backdrop-blur border-t border-white/5 z-40">
    <div class="max-w-md mx-auto space-y-3">
        <button class="w-full botoi-premium shadow-glow">
            <span class="material-symbols-outlined">calendar_add_on</span>
            Egutegian Gehitu
        </button>
        <a href="bezero_panela.php" class="w-full bg-transparent hover:bg-white/5 text-slate-600 dark:text-slate-300 border border-slate-300 dark:border-white/20 font-medium h-12 rounded-xl flex items-center justify-center gap-2 transition-colors">
            Hasierara Itzuli
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


