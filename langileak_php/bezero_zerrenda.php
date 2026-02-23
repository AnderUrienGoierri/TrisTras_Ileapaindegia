<?php
$pageTitle = 'Tristras - Nire Bezeroak';
$pageCSS = 'bezero_zerrenda.css';
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'barber' && $_SESSION['user_role'] !== 'admin')) {
    header('Location: ../sesioa/saioa_hasi.php');
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

<div class="pantaila-nagusia">
    <!-- Header Section -->
    <header class="bezeroak-goiburukoa">
        <div class="flex items-center justify-between mb-6">
            <a href="langile_panela.php" class="botoi-itzuli-txikia">
                <span class="material-symbols-outlined text-[24px]">arrow_back</span>
            </a>
            <div class="flex gap-3">
                <button class="botoi-itzuli-txikia">
                    <span class="material-symbols-outlined text-[24px]">notifications</span>
                </button>
            </div>
        </div>
        <h1 class="titulu-nagusia mb-1">Nire Bezeroak</h1>
        <p class="testu-lagungarria">Kudeatu zure bezeroak eta hitzorduak</p>
    </header>

    <!-- Bilaketa barra -->
    <div class="bilaketa-bilgarria">
        <form method="GET" action="bezero_zerrenda.php" class="panela-premium flex items-center h-12 px-4 focus-within:ring-2 focus-within:ring-primary/50 transition-all shadow-none">
            <span class="material-symbols-outlined text-primary mr-3">search</span>
            <input name="search" class="bg-transparent border-none text-white placeholder-slate-400 focus:ring-0 w-full text-base font-medium h-full p-0" 
                   placeholder="Bilatu bezeroa..." type="text" value="<?= htmlspecialchars($bilatu) ?>"/>
        </form>
    </div>

    <!-- Bezeroen zerrenda -->
    <div class="bezero-zerrenda-edukia">
        <?php foreach ($taldekatuta as $letra => $bezeroZerrenda): ?>
        <div class="zerrenda-talde-adierazlea">
            <span class="etiketa-txikia text-primary"><?= $letra ?></span>
            <div class="h-px bg-white/5 w-full"></div>
        </div>
            <?php foreach ($bezeroZerrenda as $bezeroa): ?>
            <div class="bezero-txartela-itxia panela-premium group">
                <div class="flex items-center gap-4">
                    <div class="bezero-irudi-latza">
                        <?php if ($bezeroa['irudia']): ?>
                            <img src="<?= htmlspecialchars($bezeroa['irudia']) ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <span class="material-symbols-outlined text-[32px] text-slate-500">person</span>
                        <?php endif; ?>
                    </div>
                    <div>
                        <h3 class="titulu-ertaina leading-tight group-hover:text-primary transition-colors"><?= htmlspecialchars($bezeroa['izena']) ?></h3>
                        <p class="testu-lagungarria mt-0.5"><?= htmlspecialchars($bezeroa['posta']) ?></p>
                    </div>
                </div>
                <button class="botoi-xehetasunak-txikia">
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
    <nav class="nabigazio-barra-langile">
        <div class="flex justify-around items-center">
            <a class="nabigazio-botoia-langile" href="langile_panela.php">
                <span class="material-symbols-outlined">calendar_month</span>
                <p class="text-[10px] font-medium">Egutegia</p>
            </a>
            <a class="nabigazio-botoia-langile aktiboa" href="bezero_zerrenda.php">
                <span class="material-symbols-outlined betea">group</span>
                <p class="text-[10px] font-bold">Bezeroak</p>
            </a>
            <a class="nabigazio-botoia-langile" href="langile_profila.php">
                <span class="material-symbols-outlined">person</span>
                <p class="text-[10px] font-medium">Profila</p>
            </a>
        </div>
    </nav>
</div>

<?php require_once '../includes/footer.php'; ?>


