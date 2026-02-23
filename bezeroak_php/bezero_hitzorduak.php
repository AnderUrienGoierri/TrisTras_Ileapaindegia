<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../sesioa/saioa_hasi.php');
    exit;
}

$pageTitle = 'Tristras - Nire Hitzorduak';
$pageCSS = 'bezero_hitzorduak.css';
require_once '../includes/header.php';

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

<div class="pantaila-nagusia">
    <!-- Header -->
    <div class="panela-goiburukoa">
        <a href="bezero_panela.php" class="botoi-menua-txikia">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="titulu-nagusia">Hitzorduen Historia</h1>
        <div class="w-10"></div>
    </div>

    <div class="px-6 space-y-8">
        <!-- Upcoming Section -->
        <section>
            <h2 class="etiketa-txikia mb-4">Hurrengoak</h2>
            <div class="space-y-4">
                <?php foreach($hurrengoak as $appt): ?>
                <div class="panela-premium hitzordu-txartela">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center gap-4">
                            <div class="hitzordu-ikono-bilgarria">
                                <span class="material-symbols-outlined text-[28px]">content_cut</span>
                            </div>
                            <div>
                                <p class="titulu-ertaina"><?= htmlspecialchars($appt['zerbitzu_izena']) ?></p>
                                <p class="testu-lagungarria"><?= htmlspecialchars($appt['langile_izena']) ?> barberoa</p>
                            </div>
                        </div>
                        <span class="egoera-txikitua-baieztatua">
                            <span class="material-symbols-outlined text-[14px] mr-1">check_circle</span>
                            Baieztatuta
                        </span>
                    </div>
                    <div class="txartel-oina">
                        <div class="data-lerroa">
                            <span class="material-symbols-outlined ikono-sm">calendar_today</span>
                            <?= date('M d', strtotime($appt['data'])) ?>
                        </div>
                        <div class="ordu-lerroa">
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
                <div class="iraganeko-hitzordu-lerroa group">
                    <div class="flex flex-col flex-1">
                        <h4 class="titulu-txikia group-hover:text-primary transition-colors"><?= htmlspecialchars($appt['zerbitzu_izena']) ?></h4>
                        <p class="testu-lagungarria"><?= date('Y-m-d', strtotime($appt['data'])) ?> • <?= substr($appt['hasiera'], 0, 5) ?></p>
                    </div>
                    <div class="flex flex-col items-end gap-1">
                        <span class="egoera-testu-txikia"><?= htmlspecialchars($appt['langile_izena']) ?></span>
                        <span class="text-[10px] font-bold uppercase <?= $appt['egoera'] == 'completed' ? 'text-blue-400' : 'text-slate-500' ?>">
                            <?= htmlspecialchars($appt['egoera']) ?>
                        </span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="beheko-nabigazio-barra">
        <div class="nabigazio-edukiontzia">
            <div class="flex justify-around items-center">
                <a class="nabigazio-elementua" href="bezero_panela.php">
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">home</span>
                    </div>
                    <span class="text-slate-400 text-[10px] font-medium tracking-wide">Hasiera</span>
                </a>
                <a class="nabigazio-elementua" href="bezero_hitzorduak.php">
                    <div class="nabigazio-ikonoa-aktiboa">
                        <span class="material-symbols-outlined ikono-lg betea">calendar_month</span>
                    </div>
                    <span class="text-white text-[10px] font-medium tracking-wide">Hitzorduak</span>
                </a>
                <a class="nabigazio-elementua" href="bezero_profila.php">
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">person</span>
                    </div>
                    <span class="text-slate-400 text-[10px] font-medium tracking-wide">Profila</span>
                </a>
                <a class="nabigazio-elementua" href="ezarpenak.php">
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">settings</span>
                    </div>
                    <span class="text-slate-400 text-[10px] font-medium tracking-wide">Ezarpenak</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>


