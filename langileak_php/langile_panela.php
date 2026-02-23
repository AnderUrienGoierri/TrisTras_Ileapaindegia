<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'barber' && $_SESSION['user_role'] !== 'admin')) {
    header('Location: ../sesioa/saioa_hasi.php');
    exit;
}

$langileId = $_SESSION['user_id'];

// Egoera eguneratzeak kudeatu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $apptId = $_POST['appointment_id'];
    $egoeraBerria = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE hitzorduak SET egoera = ? WHERE id = ? AND langile_id = ?");
    $stmt->execute([$egoeraBerria, $apptId, $langileId]);
}

// Gaurko hitzorduak eskuratu
$stmt = $pdo->prepare("
    SELECT a.*, s.izena as zerbitzu_izena, s.iraupena, u.izena as bezero_izena 
    FROM hitzorduak a 
    JOIN zerbitzuak s ON a.zerbitzu_id = s.id
    JOIN erabiltzaileak u ON a.bezero_id = u.id
    WHERE a.langile_id = ? AND a.data = CURDATE()
    ORDER BY a.hasiera ASC
");
$stmt->execute([$langileId]);
$gaurkoHitzorduak = $stmt->fetchAll();

// Estatistikak
$geratzenDirenKopurua = 0;
foreach($gaurkoHitzorduak as $a) if($a['egoera'] != 'completed' && $a['egoera'] != 'cancelled') $geratzenDirenKopurua++;

$pageTitle = 'Tristras - Lan-eremua';
$pageCSS = 'langile_panela.css';
require_once '../includes/header.php';
?>

<div class="pantaila-nagusia">
    <!-- Header -->
    <header class="flex items-center justify-between px-6 py-5 bg-background-light dark:bg-background-dark shrink-0 z-10">
        <div class="flex items-center gap-3">
            <button id="menua-ireki" class="botoi-itzuli-txikia">
                <span class="material-symbols-outlined text-[24px]">menu</span>
            </button>
            <h1 class="titulu-ertaina">Lan-eremua</h1>
        </div>
        <button class="text-sm font-semibold text-primary dark:text-primary-light hover:opacity-80 transition-opacity">
            Laguntza
        </button>
    </header>

    <!-- Scrollable Content -->
    <main class="flex-1 overflow-y-auto px-4 pb-24 scroll-smooth">
        <!-- Status Toggle Card -->
        <div class="egoera-kontrola panela-premium">
            <div class="flex items-center justify-between p-5">
                <div class="flex flex-col gap-1">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                        </span>
                        <span class="titulu-txikia">Egoera</span>
                    </div>
                    <p class="testu-lagungarria">Aktibatu lanean zaudenean</p>
                </div>
                <label class="egoera-aldatzailea group">
                    <input checked="" class="sr-only peer" type="checkbox"/>
                    <div class="w-14 h-8 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-primary"></div>
                </label>
            </div>
        </div>

        <!-- Dashboard Summary Widgets -->
        <div class="grid grid-cols-2 gap-4 mb-8">
            <div class="estatistika-txartela-nagusia panela-premium shadow-lg">
                <div class="flex items-start justify-between mb-4 relative z-10">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <span class="material-symbols-outlined text-white text-[20px]">content_cut</span>
                    </div>
                    <span class="etiketa-txikia bg-white/20 px-2 py-0.5 rounded-full backdrop-blur-sm">Gaur</span>
                </div>
                <div class="relative z-10">
                    <span class="block text-3xl font-bold mb-1"><?= $geratzenDirenKopurua ?></span>
                    <span class="text-sm font-medium text-white/90 leading-tight block">Hitzordu geratzen dira</span>
                </div>
            </div>
            <div class="estatistika-txartela panela-premium">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-2 bg-primary/20 rounded-lg text-primary-light">
                        <span class="material-symbols-outlined text-[20px]">schedule</span>
                    </div>
                </div>
                <div>
                    <span class="block text-3xl font-bold text-white mb-1">13:30</span>
                    <span class="testu-lagungarria leading-tight block">Hurrengo atsedenaldia</span>
                </div>
            </div>
        </div>

        <!-- Agenda Section -->
        <div class="mb-4 flex items-center justify-between">
            <h2 class="titulu-ertaina">Gaurko Agenda</h2>
            <button class="text-xs font-semibold text-primary dark:text-primary-light hover:underline">Ikusi dena</button>
        </div>

        <div class="space-y-4">
            <?php foreach($gaurkoHitzorduak as $appt): ?>
            <div class="hitzordu-txartela-langile <?= $appt['egoera'] == 'completed' ? 'osatuta' : '' ?>">
                <div class="flex justify-between items-start">
                    <div class="flex gap-4">
                        <div class="ordu-adierazlea">
                            <span class="text-lg font-bold <?= $appt['egoera'] == 'completed' ? 'text-slate-500 line-through' : 'text-primary' ?>"><?= substr($appt['hasiera'], 0, 5) ?></span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-white <?= $appt['egoera'] == 'completed' ? 'text-slate-500 line-through' : '' ?>"><?= htmlspecialchars($appt['bezero_izena']) ?></h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-sm text-slate-400"><?= htmlspecialchars($appt['zerbitzu_izena']) ?></span>
                                <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                                <span class="text-xs font-medium px-2 py-0.5 rounded-full bg-amber-900/30 text-amber-400"><?= $appt['iraupena'] ?> min</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2 border-t border-white/5 mt-1">
                    <div class="text-xs text-slate-500">
                        <?php if($appt['egoera'] == 'completed'): ?>
                            <span class="text-green-500 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-sm">check_circle</span> Eginda</span>
                        <?php else: ?>
                            <span class="etiketa-txikia">Zain</span>
                        <?php endif; ?>
                    </div>
                    <?php if($appt['egoera'] != 'completed'): ?>
                    <form method="POST" action="langile_panela.php">
                        <input type="hidden" name="action" value="update_status">
                        <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                        <input type="hidden" name="status" value="completed">
                        <button type="submit" class="botoi-premium shadow-lg">
                            <span class="material-symbols-outlined text-[18px]">check</span>
                            Osatuta
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($gaurkoHitzorduak)): ?>
            <div class="text-center py-10">
                <span class="material-symbols-outlined text-slate-600 text-[48px] mb-2">event_busy</span>
                <p class="text-slate-500">Ez duzu hitzordurik gaurko.</p>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="h-24"></div>
    </main>

    <!-- Bottom Navigation Bar -->
    <nav class="nabigazio-barra-langile">
        <div class="flex justify-around items-center">
            <a class="nabigazio-botoia-langile aktiboa" href="langile_panela.php">
                <span class="material-symbols-outlined betea">home</span>
                <p class="text-[10px] font-medium">Hasiera</p>
            </a>
            <a class="nabigazio-botoia-langile" href="langile_panela.php">
                <span class="material-symbols-outlined">calendar_month</span>
                <p class="text-[10px] font-medium">Agenda</p>
            </a>
            <a class="nabigazio-botoia-langile" href="bezero_zerrenda.php">
                <span class="material-symbols-outlined">group</span>
                <p class="text-[10px] font-medium">Bezeroak</p>
            </a>
            <a class="nabigazio-botoia-langile" href="langile_profila.php">
                <span class="material-symbols-outlined">person</span>
                <p class="text-[10px] font-medium">Profila</p>
            </a>
        </div>
    </nav>
</div>

<?php require_once '../includes/footer.php'; ?>


