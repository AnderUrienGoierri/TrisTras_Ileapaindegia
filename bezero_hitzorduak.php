<?php
require_once 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: saioa_hasi.php');
    exit;
}

$pageTitle = 'Tristras - Nire Hitzorduak';
require_once 'includes/header.php';

$bezeroId = $_SESSION['user_id'];

// Hurrengo hitzorduak
$hurrengoakStmt = $pdo->prepare("
    SELECT a.*, s.izena as zerbitzu_izena, u.izena as langile_izena 
    FROM hitzorduak a 
    JOIN zerbitzuak s ON a.zerbitzu_id = s.id
    JOIN erabiltzaileak u ON a.langile_id = u.id
    WHERE a.bezero_id = ? AND a.data >= CURDATE() AND a.egoera IN ('confirmed', 'pending')
    ORDER BY a.data ASC, a.hasiera ASC
");
$hurrengoakStmt->execute([$bezeroId]);
$hurrengoak = $hurrengoakStmt->fetchAll();

// Pasa diren hitzorduak
$pasaDirenakStmt = $pdo->prepare("
    SELECT a.*, s.izena as zerbitzu_izena, u.izena as langile_izena 
    FROM hitzorduak a 
    JOIN zerbitzuak s ON a.zerbitzu_id = s.id
    JOIN erabiltzaileak u ON a.langile_id = u.id
    WHERE a.bezero_id = ? AND (a.data < CURDATE() OR a.egoera IN ('completed', 'cancelled'))
    ORDER BY a.data DESC, a.hasiera DESC
    LIMIT 10
");
$pasaDirenakStmt->execute([$bezeroId]);
$pasaDirenak = $pasaDirenakStmt->fetchAll();
?>

<div class="relative flex min-h-screen w-full flex-col pb-24 max-w-md mx-auto">
    <!-- Header -->
    <header class="flex items-center justify-between p-6 pt-12">
        <a href="bezero_panela.php" class="flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 text-white">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="titulu-nagusia">Hitzorduen Historia</h1>
        <div class="w-10"></div>
    </header>

    <div class="px-6 space-y-8">
        <!-- Upcoming Section -->
        <section>
            <h2 class="etiketa-txikia mb-4">Hurrengoak</h2>
            <div class="space-y-4">
                <?php foreach($hurrengoak as $appt): ?>
                <div class="panela-premium p-4 border border-primary/20 bg-primary/5">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <p class="titulu-ertaina"><?= htmlspecialchars($appt['zerbitzu_izena']) ?></p>
                            <p class="testu-lagungarria"><?= htmlspecialchars($appt['langile_izena']) ?> barberoa</p>
                        </div>
                        <span class="etiketa-txikia bg-green-500/20 text-green-400 px-2 py-1 rounded-full">Baieztatuta</span>
                    </div>
                    <div class="flex items-center gap-4 testu-lagungarria">
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined ikono-sm">calendar_today</span>
                            <?= date('M d', strtotime($appt['data'])) ?>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined ikono-sm">schedule</span>
                            <?= substr($appt['hasiera'], 0, 5) ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($hurrengoak)): ?>
                    <p class="text-slate-500 text-sm">Ez dago hitzordu berririk.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Past Section -->
        <section>
            <h2 class="etiketa-txikia mb-4">Pasa direnak</h2>
            <div class="space-y-4">
                <?php foreach($pasaDirenak as $appt): ?>
                <div class="panela-premium p-4 opacity-80">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="titulu-txikia"><?= htmlspecialchars($appt['zerbitzu_izena']) ?></p>
                            <p class="etiketa-txikia"><?= htmlspecialchars($appt['langile_izena']) ?></p>
                        </div>
                        <span class="text-[10px] font-bold uppercase <?= $appt['egoera'] == 'completed' ? 'text-blue-400' : 'text-slate-500' ?>">
                            <?= htmlspecialchars($appt['egoera']) ?>
                        </span>
                    </div>
                    <p class="testu-lagungarria"><?= date('Y-m-d', strtotime($appt['data'])) ?> • <?= substr($appt['hasiera'], 0, 5) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Bottom Nav -->
    <div class="fixed bottom-0 left-0 w-full z-40 max-w-md mx-auto">
        <div class="bg-slate-900/90 border-t border-slate-800 backdrop-blur-lg pb-6 pt-3 px-4">
            <div class="flex justify-around items-center">
                <a class="flex flex-col items-center gap-1" href="bezero_panela.php">
                    <span class="material-symbols-outlined ikono-lg text-slate-400">home</span>
                    <span class="text-slate-400 text-[10px]">Hasiera</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="bezero_hitzorduak.php">
                    <span class="material-symbols-outlined ikono-lg text-primary betea">calendar_month</span>
                    <span class="text-white text-[10px]">Hitzorduak</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="bezero_profila.php">
                    <span class="material-symbols-outlined ikono-lg text-slate-400">person</span>
                    <span class="text-slate-400 text-[10px]">Profila</span>
                </a>
                <a class="flex flex-col items-center gap-1" href="ezarpenak.php">
                    <span class="material-symbols-outlined ikono-lg text-slate-400">settings</span>
                    <span class="text-slate-400 text-[10px]">Ezarpenak</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


