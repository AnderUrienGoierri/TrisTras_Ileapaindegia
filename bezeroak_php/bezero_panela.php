<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../sesioa/saioa_hasi.php');
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
$pageCSS = 'bezero_panela.css';
require_once '../includes/header.php';
?>

<div class="pantaila-nagusia">
    <!-- Ambient Glow Background -->
    <div class="giro-efektu-bilgarria">
        <div class="giro-argia-1"></div>
        <div class="giro-argia-2"></div>
    </div>

    <!-- Header -->
    <div class="panela-goiburukoa">
        <div class="flex items-center gap-4">
            <div class="avatar-bilgarria-txikia">
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
        <button id="menua-ireki" class="botoi-menua-txikia">
            <span class="material-symbols-outlined text-[24px]">menu</span>
        </button>
    </div>

    <!-- Next Appointment Section -->
    <div class="hitzordu-atala">
        <div class="relative overflow-hidden panela-premium group">
            <div class="txartel-gradientea"></div>
            <div class="txartel-edukia">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="titulu-ertaina">Hurrengo Hitzordua</h2>
                        <p class="etiketa-txikia mt-1">Tristras Barber Studio</p>
                    </div>
                    <?php if ($nextAppointment): ?>
                    <span class="egoera-etiketa-baieztatua">
                        <span class="material-symbols-outlined text-[14px] mr-1">check_circle</span>
                        <span class="text-xs font-bold"><?= ucfirst(htmlspecialchars($nextAppointment['egoera'])) ?></span>
                    </span>
                    <?php endif; ?>
                </div>

                <?php if ($nextAppointment): ?>
                <div class="flex items-center gap-4 mt-2">
                    <div class="hitzordu-irudi-bilgarria">
                        <img alt="Barbe shop" class="h-full w-full object-cover opacity-80" src="../irudiak/hero_barber.png">
                    </div>
                    <div class="flex flex-col gap-1">
                        <div class="data-lerroa">
                            <span class="material-symbols-outlined text-[18px] text-primary">calendar_month</span>
                            <span class="text-sm font-semibold"><?= date('l, M j', strtotime($nextAppointment['data'])) ?></span>
                        </div>
                        <div class="ordu-lerroa">
                            <span class="material-symbols-outlined text-[18px]">schedule</span>
                            <span class="text-sm"><?= substr($nextAppointment['hasiera'], 0, 5) ?> - <?= substr($nextAppointment['amaiera'], 0, 5) ?></span>
                        </div>
                    </div>
                </div>
                <div class="txartel-oina">
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
    <div class="zerbitzu-atala-nagusia">
        <div class="flex items-center justify-between px-6 mb-4">
            <h3 class="titulu-nagusia">Gure Zerbitzuak</h3>
            <a class="text-primary text-sm font-medium hover:text-white transition-colors" href="erreserba.php">Dena ikusi</a>
        </div>
        
        <!-- Zerbitzuak Hautatu (Horizontala) -->
        <div class="zerbitzu-zerrenda-horizontala">
            <?php foreach ($services as $svc): ?>
            <div class="zerbitzu-elementua" onclick="window.location.href='erreserba.php?service=<?= $svc['id'] ?>'">
                <div class="zerbitzu-txartel-txikia">
                    <div class="zerbitzu-txartel-gradientea"></div>
                    <img alt="<?= htmlspecialchars($svc['izena']) ?>" class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-110" src="../<?= htmlspecialchars($svc['irudia']) ?>">
                    <div class="zerbitzu-txartel-testua">
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
    <div class="akzio-botoi-bilgarria">
        <a href="erreserba.php" class="botoi-premium rounded-full shadow-lg group">
            <span class="material-symbols-outlined group-hover:animate-pulse">calendar_add_on</span>
            <span class="font-bold text-base">Erreserbatu Orain</span>
        </a>
    </div>

    <!-- Bottom Navigation Bar -->
    <div class="beheko-nabigazio-barra">
        <div class="nabigazio-edukiontzia">
            <div class="flex justify-around items-center">
                <a class="nabigazio-elementua" href="bezero_panela.php">
                    <div class="nabigazio-ikonoa-aktiboa">
                        <span class="material-symbols-outlined ikono-lg betea">home</span>
                    </div>
                    <span class="text-white text-[10px] font-medium tracking-wide">Hasiera</span>
                </a>
                <a class="nabigazio-elementua" href="bezero_hitzorduak.php">
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">calendar_month</span>
                    </div>
                    <span class="text-slate-400 group-hover:text-white text-[10px] font-medium tracking-wide transition-colors">Hitzorduak</span>
                </a>
                <a class="nabigazio-elementua" href="bezero_profila.php">
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">person</span>
                    </div>
                    <span class="text-slate-400 group-hover:text-white text-[10px] font-medium tracking-wide transition-colors">Profila</span>
                </a>
                <a class="nabigazio-elementua" href="ezarpenak.php">
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">settings</span>
                    </div>
                    <span class="text-slate-400 group-hover:text-white text-[10px] font-medium tracking-wide transition-colors">Ezarpenak</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>


