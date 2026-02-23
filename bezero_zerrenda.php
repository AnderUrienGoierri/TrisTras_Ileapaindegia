<?php
$pageTitle = 'Tristras - Nire Bezeroak';
require_once 'includes/db.php';
require_once 'includes/header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'barber' && $_SESSION['user_role'] !== 'admin')) {
    header('Location: saioa_hasi.php');
    exit;
}

$bilatu = $_GET['search'] ?? '';

$sql = "SELECT id, izena, posta, telefonoa, irudia FROM erabiltzaileak WHERE rola = 'client'";
if ($bilatu) {
    $sql .= " AND (izena LIKE ? OR posta LIKE ?)";
}
$sql .= " ORDER BY izena ASC";

$stmt = $pdo->prepare($sql);
if ($bilatu) {
    $stmt->execute(["%$bilatu%", "%$bilatu%"]);
} else {
    $stmt->execute();
}
$bezeroak = $stmt->fetchAll();

// Bezeroak lehen letraren arabera taldekatu
$taldekatuta = [];
foreach ($bezeroak as $bezeroa) {
    $lehenLetra = strtoupper(substr($bezeroa['izena'], 0, 1));
    $taldekatuta[$lehenLetra][] = $bezeroa;
}
?>

<div class="relative flex h-screen w-full flex-col overflow-hidden max-w-md mx-auto shadow-2xl bg-background-light dark:bg-background-dark">
    <!-- Header Section -->
    <header class="flex-none px-6 pt-12 pb-4 z-20">
        <div class="flex items-center justify-between mb-6">
            <a href="langile_panela.php" class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 text-slate-100 hover:bg-slate-800 transition-colors">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div class="flex gap-3">
                <button class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 text-slate-100 hover:bg-slate-800 transition-colors">
                    <span class="material-symbols-outlined text-[24px]">notifications</span>
                </button>
            </div>
        </div>
        <h1 class="titulu-nagusia mb-1">Nire Bezeroak</h1>
        <p class="testu-lagungarria">Kudeatu zure bezeroak eta hitzorduak</p>
    </header>

    <!-- Bilaketa barra -->
    <div class="flex-none px-6 pb-4 z-20">
        <form method="GET" action="bezero_zerrenda.php" class="panela-premium flex items-center h-12 px-4 group focus-within:ring-2 focus-within:ring-accent-teal/50 transition-all shadow-none">
            <span class="material-symbols-outlined text-accent-teal mr-3">search</span>
            <input name="search" class="bg-transparent border-none text-white placeholder-slate-400 focus:ring-0 w-full text-base font-medium h-full p-0" 
                   placeholder="Bilatu bezeroa..." type="text" value="<?= htmlspecialchars($bilatu) ?>"/>
        </form>
    </div>

    <!-- Bezeroen zerrenda -->
    <div class="flex-1 overflow-y-auto px-6 pb-24 space-y-4">
        <?php foreach ($taldekatuta as $letra => $bezeroZerrenda): ?>
        <div class="flex items-center gap-4 pt-2">
            <span class="etiketa-txikia text-accent-teal"><?= $letra ?></span>
            <div class="h-[1px] bg-slate-800 w-full"></div>
        </div>
            <?php foreach ($bezeroZerrenda as $bezeroa): ?>
            <div class="panela-premium p-4 flex items-center justify-between border border-transparent hover:border-primary/30 active:scale-[0.99] group cursor-pointer transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-full bg-slate-700 overflow-hidden flex items-center justify-center">
                        <?php if ($bezeroa['irudia']): ?>
                            <img src="<?= htmlspecialchars($bezeroa['irudia']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-[32px] text-slate-500">person</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="titulu-ertaina leading-tight group-hover:text-accent-teal transition-colors"><?= htmlspecialchars($bezeroa['izena']) ?></h3>
                        <p class="testu-lagungarria mt-0.5"><?= htmlspecialchars($bezeroa['posta']) ?></p>
                    </div>
                </div>
                <button class="flex items-center justify-center w-10 h-10 rounded-lg bg-primary/10 text-accent-teal hover:bg-primary hover:text-white transition-all">
                    <span class="material-symbols-outlined text-[20px]">sticky_note_2</span>
                </button>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        
        <?php if(empty($taldekatuta)): ?>
        <div class="text-center py-10">
            <p class="text-slate-500">Ez da bezerorik aurkitu.</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="fixed bottom-0 left-0 w-full border-t border-white/5 bg-slate-900 px-2 pb-5 pt-3 max-w-md mx-auto z-40">
        <div class="flex justify-around items-center">
            <a class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl py-1 text-slate-400" href="langile_panela.php">
                <span class="material-symbols-outlined">calendar_month</span>
                <p class="text-[10px] font-medium">Egutegia</p>
            </a>
            <a class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl py-1 text-accent-teal" href="bezero_zerrenda.php">
                <span class="material-symbols-outlined fill">group</span>
                <p class="text-[10px] font-bold">Bezeroak</p>
            </a>
            <a class="flex flex-1 flex-col items-center justify-center gap-1 rounded-xl py-1 text-slate-400" href="langile_profila.php">
                <span class="material-symbols-outlined">person</span>
                <p class="text-[10px] font-medium">Profila</p>
            </a>
        </div>
    </nav>
</div>

<?php require_once 'includes/footer.php'; ?>


