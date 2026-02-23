<?php
require_once 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: saioa_hasi.php');
    exit;
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['user_name'] ?? 'Erabiltzailea';

// Erabiltzailearen datuak eta irudia eskuratu
$erabiltzaileStmt = $pdo->prepare("SELECT irudia FROM erabiltzaileak WHERE id = ?");
$erabiltzaileStmt->execute([$userId]);
$erabiltzaileDatuak = $erabiltzaileStmt->fetch();

// Hurrengo hitzordu baieztatua eskuratu
$stmt = $pdo->prepare("
    SELECT a.*, s.izena as zerbitzu_izena, u.izena as langile_izena 
    FROM hitzorduak a 
    JOIN zerbitzuak s ON a.zerbitzu_id = s.id
    JOIN erabiltzaileak u ON a.langile_id = u.id
    WHERE a.bezero_id = ? AND a.data >= CURDATE() AND a.egoera IN ('confirmed', 'pending')
    ORDER BY a.data ASC, a.hasiera ASC
    LIMIT 1
");
$stmt->execute([$userId]);
$nextAppointment = $stmt->fetch();

// Fetch services for "Gure Estiloak"
$servicesStmt = $pdo->query("SELECT * FROM zerbitzuak LIMIT 3");
$services = $servicesStmt->fetchAll();

$pageTitle = 'Tristras - Nire Hasiera';
require_once 'includes/header.php';
?>

<div class="relative flex min-h-screen w-full flex-col pb-24 max-w-md mx-auto">
    <!-- Ambient Glow Background -->
    <div class="fixed top-0 left-0 right-0 h-[500px] w-full pointer-events-none z-0 overflow-hidden">
        <div class="absolute -top-20 -right-20 w-96 h-96 bg-primary/20 rounded-full blur-[100px]"></div>
        <div class="absolute top-40 -left-20 w-72 h-72 bg-teal-500/10 rounded-full blur-[80px]"></div>
    </div>

    <!-- Header -->
    <div class="relative z-10 flex items-center justify-between p-6 pt-12 pb-2">
        <div class="flex items-center gap-4">
            <div class="relative h-12 w-12 rounded-full overflow-hidden border-2 border-primary/30 bg-slate-700 flex items-center justify-center">
                <?php if ($erabiltzaileDatuak['irudia']): ?>
                    <img src="<?= htmlspecialchars($erabiltzaileDatuak['irudia']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="material-symbols-outlined text-white text-[32px]">person</span>
                <?php endif; ?>
            </div>
            <div class="flex flex-col">
                <span class="etiketa-txikia">Ongi etorri berriro,</span>
                <h1 class="titulu-nagusia leading-none">Kaixo, <?= htmlspecialchars(explode(' ', $userName)[0]) ?></h1>
            </div>
        </div>
        <button id="menua-ireki" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800/50 hover:bg-primary/20 text-slate-200 transition-colors backdrop-blur-sm border border-slate-700/50">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
    </div>

    <!-- Next Appointment Section -->
    <div class="relative z-10 px-4 mt-6">
        <div class="relative overflow-hidden panela-premium group">
            <div class="absolute inset-0 bg-gradient-to-r from-primary/20 to-transparent opacity-50"></div>
            <div class="relative p-5 flex flex-col gap-4">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="titulu-ertaina">Hurrengo Hitzordua</h2>
                        <p class="etiketa-txikia mt-1">Tristras Barber Studio</p>
                    </div>
                    <?php if ($nextAppointment): ?>
                    <span class="inline-flex items-center justify-center rounded-full bg-green-500/20 px-2.5 py-0.5 text-green-400">
                        <span class="material-symbols-outlined text-[14px] mr-1">check_circle</span>
                        <span class="text-xs font-bold"><?= ucfirst(htmlspecialchars($nextAppointment['egoera'])) ?></span>
                    </span>
                    <?php endif; ?>
                </div>

                <?php if ($nextAppointment): ?>
                <div class="flex items-center gap-4 mt-2">
                    <div class="h-16 w-16 rounded-xl bg-slate-700 overflow-hidden flex-shrink-0">
                        <img alt="Barbe shop" class="h-full w-full object-cover opacity-80" src="images/hero_barber.png">
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2 text-slate-200">
                            <span class="material-symbols-outlined text-[18px] text-primary">calendar_month</span>
                            <span class="text-sm font-semibold"><?= date('l, M j', strtotime($nextAppointment['data'])) ?></span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-400">
                            <span class="material-symbols-outlined text-[18px]">schedule</span>
                            <span class="text-sm"><?= substr($nextAppointment['hasiera'], 0, 5) ?> - <?= substr($nextAppointment['amaiera'], 0, 5) ?></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-700/50 mt-1">
                    <span class="text-slate-300 text-sm"><?= htmlspecialchars($nextAppointment['zerbitzu_izena']) ?> (<?= htmlspecialchars($nextAppointment['langile_izena']) ?>)</span>
                    <a href="bezero_hitzorduak.php" class="text-primary hover:text-white transition-colors text-sm font-semibold flex items-center gap-1">
                        Xehetasunak <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </a>
                </div>
                <?php else: ?>
                <div class="text-center py-4">
                    <p class="text-slate-400 text-sm">Ez duzu hitzordurik hurbil.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Styles Section (Gure Estiloak) -->
    <div class="relative z-10 mt-8 flex-1">
        <div class="flex items-center justify-between px-6 mb-4">
            <h3 class="titulu-nagusia">Gure Zerbitzuak</h3>
            <a class="text-primary text-sm font-medium hover:text-white transition-colors" href="erreserba.php">Dena ikusi</a>
        </div>
        
        <!-- Zerbitzuak Hautatu (Horizontala) -->
        <div class="flex overflow-x-auto pb-6 px-6 gap-4 korritze-barra-ezkutatu snap-x">
            <?php foreach ($services as $svc): ?>
            <div class="flex-none w-64 snap-center group cursor-pointer" onclick="window.location.href='erreserba.php?service=<?= $svc['id'] ?>'">
                <div class="relative h-80 rounded-2xl overflow-hidden bg-slate-800 border border-slate-700/50 shadow-md">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent z-10 opacity-90"></div>
                    <img alt="<?= htmlspecialchars($svc['izena']) ?>" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?= htmlspecialchars($svc['irudia']) ?>">
                    <div class="absolute bottom-0 left-0 w-full p-4 z-20 flex flex-col">
                        <h4 class="text-white text-lg font-bold uppercase tracking-tight"><?= htmlspecialchars($svc['izena']) ?></h4>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-slate-300 text-sm"><?= htmlspecialchars($svc['iraupena'] ?? '30') ?> min</span>
                            <span class="text-primary font-bold bg-white/10 backdrop-blur-md px-2 py-0.5 rounded text-sm"><?= number_format($svc['prezioa'], 0) ?>€</span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="fixed bottom-24 right-4 z-30">
        <a href="erreserba.php" class="botoi-premium rounded-full shadow-lg group">
            <span class="material-symbols-outlined group-hover:animate-pulse">calendar_add_on</span>
            <span class="font-bold text-base">Erreserbatu Orain</span>
        </a>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="fixed bottom-0 left-0 w-full z-40">
        <div class="bg-slate-900/90 border-t border-slate-800 backdrop-blur-lg pb-6 pt-3 px-4 shadow-[0_-4px_20px_rgba(0,0,0,0.4)]">
            <div class="flex justify-around items-center">
                <a class="flex flex-col items-center gap-1 group" href="bezero_panela.php">
                    <div class="text-primary p-1.5 rounded-full bg-primary/10 transition-colors">
                        <span class="material-symbols-outlined ikono-lg betea">home</span>
                    </div>
                    <span class="text-white text-[10px] font-medium tracking-wide">Hasiera</span>
                </a>
                <a class="flex flex-col items-center gap-1 group" href="bezero_hitzorduak.php">
                    <div class="text-slate-400 group-hover:text-primary p-1.5 transition-colors">
                        <span class="material-symbols-outlined ikono-lg">calendar_month</span>
                    </div>
                    <span class="text-slate-400 group-hover:text-white text-[10px] font-medium tracking-wide transition-colors">Hitzorduak</span>
                </a>
                <a class="flex flex-col items-center gap-1 group" href="bezero_profila.php">
                    <div class="text-slate-400 group-hover:text-primary p-1.5 transition-colors">
                        <span class="material-symbols-outlined ikono-lg">person</span>
                    </div>
                    <span class="text-slate-400 group-hover:text-white text-[10px] font-medium tracking-wide transition-colors">Profila</span>
                </a>
                <a class="flex flex-col items-center gap-1 group" href="ezarpenak.php">
                    <div class="text-slate-400 group-hover:text-primary p-1.5 transition-colors">
                        <span class="material-symbols-outlined ikono-lg">settings</span>
                    </div>
                    <span class="text-slate-400 group-hover:text-white text-[10px] font-medium tracking-wide transition-colors">Ezarpenak</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


