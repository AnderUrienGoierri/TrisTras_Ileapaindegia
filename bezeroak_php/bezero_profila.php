<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../sesioa/saioa_hasi.php');
    exit;
}

$pageTitle = 'Tristras - Nire Profila';
$pageCSS = 'bezero_profila.css';
require_once '../includes/header.php';

$erabiltzaileStmt = $pdo->prepare("SELECT * FROM erabiltzaileak WHERE id = ?");
$erabiltzaileStmt->execute([$_SESSION['user_id']]);
$erabiltzaileDatuak = $erabiltzaileStmt->fetch();
?>

<div class="pantaila-nagusia">
    <header class="panela-goiburukoa">
        <a href="bezero_panela.php" class="botoi-itzuli">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="titulu-nagusia">Nire Profila</h1>
        <div class="w-10"></div>
    </header>

    <div class="profil-edukia">
        <div class="size-24 rounded-full bg-primary/20 border-4 border-primary/30 flex items-center justify-center mb-4 overflow-hidden">
            <?php if ($erabiltzaileDatuak['irudia']): ?>
                <img src="<?= htmlspecialchars($erabiltzaileDatuak['irudia']) ?>" class="w-full h-full object-cover">
            <?php else: ?>
                <span class="material-symbols-outlined text-[64px] text-white">person</span>
            <?php endif; ?>
        </div>
        <h2 class="titulu-ertaina"><?= htmlspecialchars($erabiltzaileDatuak['izena']) ?></h2>
        <p class="testu-lagungarria"><?= htmlspecialchars($erabiltzaileDatuak['posta']) ?></p>
        
        <div class="erabiltzaile-informazio-bilgarria">
            <div class="space-y-4">
                <h3 class="etiketa-txikia px-1">Kontuaren informazioa</h3>
                <div class="panela-premium profil-txartel-premium">
                    <div class="profil-errenkada">
                        <span class="testu-lagungarria">Telefonoa</span>
                        <span class="titulu-txikia font-medium"><?= htmlspecialchars($erabiltzaileDatuak['telefonoa'] ?: 'Ez dago zehaztuta') ?></span>
                    </div>
                    <div class="profil-errenkada">
                        <span class="testu-lagungarria">Rola</span>
                        <span class="titulu-txikia capitalize font-medium"><?= htmlspecialchars($erabiltzaileDatuak['rola']) ?></span>
                    </div>
                </div>
            </div>

            <a href="../sesioa/saioa_itxi.php" class="botoi-saioa-itxi">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                Saioa Itxi
            </a>
        </div>
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
                    <div class="nabigazio-ikonoa-ez-aktiboa">
                        <span class="material-symbols-outlined ikono-lg">calendar_month</span>
                    </div>
                    <span class="text-slate-400 text-[10px] font-medium tracking-wide">Hitzorduak</span>
                </a>
                <a class="nabigazio-elementua" href="bezero_profila.php">
                    <div class="nabigazio-ikonoa-aktiboa">
                        <span class="material-symbols-outlined ikono-lg betea">person</span>
                    </div>
                    <span class="text-white text-[10px] font-medium tracking-wide">Profila</span>
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


