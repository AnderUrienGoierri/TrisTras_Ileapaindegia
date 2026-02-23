<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../sesioa/saioa_hasi.php');
    exit;
}

$pageTitle = 'Tristras - Ezarpenak';
$pageCSS = 'ezarpenak.css';
require_once '../includes/header.php';

// Hizkuntza aldaketa kudeatu
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
}

$erabiltzaileStmt = $pdo->prepare("SELECT izena, posta, irudia FROM erabiltzaileak WHERE id = ?");
$erabiltzaileStmt->execute([$_SESSION['user_id']]);
$erabiltzaileDatuak = $erabiltzaileStmt->fetch();
?>

<div class="pantaila-nagusia">
    <!-- Header -->
    <header class="ezarpenak-goiburukoa">
        <button onclick="history.back()" class="botoi-itzuli-txikia">
            <span class="material-symbols-outlined ikono-lg">arrow_back_ios_new</span>
        </button>
        <h1 class="titulu-ertaina">Ezarpenak</h1>
        <div class="w-10"></div> 
    </header>

    <main class="flex-1 overflow-y-auto px-4 pb-24 relative z-0">
        <!-- Giroko dirdira -->
        <div class="giro-dirdira-nagusia opacity-50"></div>
        
        <!-- Erabiltzailearen informazioa -->
        <div class="profil-atal-txikia">
            <div class="relative w-24 h-24 mb-3 group cursor-pointer">
                <?php if ($erabiltzaileDatuak['irudia']): ?>
                    <img src="../<?= htmlspecialchars($erabiltzaileDatuak['irudia']) ?>" class="w-full h-full object-cover rounded-full border-2 border-primary shadow-lg">
                <?php else: ?>
                    <div class="w-full h-full rounded-full bg-slate-800 border-2 border-primary flex items-center justify-center">
                        <span class="material-symbols-outlined ikono-xxl text-white">person</span>
                    </div>
                <?php endif; ?>
                <div class="editatu-txapa">
                    <span class="material-symbols-outlined ikono-sm">edit</span>
                </div>
            </div>
            <h2 class="titulu-ertaina"><?= htmlspecialchars($erabiltzaileDatuak['izena']) ?></h2>
            <p class="testu-lagungarria"><?= htmlspecialchars($erabiltzaileDatuak['posta']) ?></p>
        </div>

        <!-- Settings Groups -->
        <div class="mb-6">
            <h3 class="etiketa-txikia px-2 mb-2">Orokorra</h3>
            <div class="panela-premium overflow-hidden divide-y divide-white/5">
                <button class="ezarpen-botoia border-b border-white/5 group">
                    <div class="flex items-center gap-3">
                        <div class="ezarpen-ikono-bilgarria bg-primary/10 text-primary">
                            <span class="material-symbols-outlined ikono-md">language</span>
                        </div>
                        <span class="titulu-txikia font-medium">Hizkuntza</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="testu-lagungarria group-hover:text-primary transition-colors">Euskara</span>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                    </div>
                </button>
                <button class="ezarpen-botoia border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="ezarpen-ikono-bilgarria bg-pink-500/10 text-pink-500">
                            <span class="material-symbols-outlined text-[20px]">notifications</span>
                        </div>
                        <span class="titulu-txikia font-medium">Jakinarazpenak</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                    </div>
                </button>
                <button class="ezarpen-botoia">
                    <div class="flex items-center gap-3">
                        <div class="ezarpen-ikono-bilgarria bg-blue-500/10 text-blue-500">
                            <span class="material-symbols-outlined text-[20px]">dark_mode</span>
                        </div>
                        <span class="titulu-txikia font-medium">Itxura</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="testu-lagungarria">Iluna</span>
                        <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                    </div>
                </button>
            </div>
        </div>

        <div class="mb-6">
            <h3 class="etiketa-txikia px-2 mb-2">Segurtasuna</h3>
            <div class="panela-premium overflow-hidden divide-y divide-white/5">
                <button class="ezarpen-botoia border-b border-white/5">
                    <div class="flex items-center gap-3">
                        <div class="ezarpen-ikono-bilgarria bg-emerald-500/10 text-emerald-500">
                            <span class="material-symbols-outlined text-[20px]">lock</span>
                        </div>
                        <span class="titulu-txikia font-medium">Pasahitza</span>
                    </div>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                </button>
                <button class="ezarpen-botoia">
                    <div class="flex items-center gap-3">
                        <div class="ezarpen-ikono-bilgarria bg-orange-500/10 text-orange-500">
                            <span class="material-symbols-outlined text-[20px]">privacy_tip</span>
                        </div>
                        <span class="titulu-txikia font-medium">Pribatutasuna</span>
                    </div>
                    <span class="material-symbols-outlined text-slate-400 text-[18px]">chevron_right</span>
                </button>
            </div>
        </div>

        <a href="../sesioa/saioa_itxi.php" class="botoi-saioa-itxi-lineala">
            Saioa itxi
        </a>
    </main>

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
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">calendar_month</span>
                    </div>
                    <span class="text-slate-400 text-[10px] font-medium tracking-wide">Hitzorduak</span>
                </a>
                <a class="nabigazio-elementua" href="bezero_profila.php">
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">person</span>
                    </div>
                    <span class="text-slate-400 text-[10px] font-medium tracking-wide">Profila</span>
                </a>
                <a class="nabigazio-elementua" href="ezarpenak.php">
                    <div class="nabigazio-ikonoa-aktiboa">
                        <span class="material-symbols-outlined ikono-lg betea">settings</span>
                    </div>
                    <span class="text-white text-[10px] font-medium tracking-wide">Ezarpenak</span>
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>


