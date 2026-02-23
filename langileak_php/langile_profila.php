<?php
$pageTitle = 'Tristras - Barber Profila';
$pageCSS = 'langile_profila.css';
require_once '../includes/db.php';
require_once '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'barber') {
    header('Location: ../sesioa/saioa_hasi.php');
    exit;
}

$erabiltzaileStmt = $pdo->prepare("SELECT * FROM erabiltzaileak WHERE id = ?");
$erabiltzaileStmt->execute([$_SESSION['user_id']]);
$erabiltzaileDatuak = $erabiltzaileStmt->fetch();
?>

<div class="pantaila-nagusia">
    <header class="profil-goiburukoa">
        <a href="langile_panela.php" class="botoi-itzuli-txikia">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <h1 class="titulu-nagusia">Nire Profila</h1>
        <div class="w-10"></div>
    </header>

    <div class="profil-edukia-langile">
        <div class="profil-irudi-bilgarria">
            <div class="profil-irudi-zirkulua">
                <?php if ($erabiltzaileDatuak['irudia']): ?>
                    <img src="<?= htmlspecialchars($erabiltzaileDatuak['irudia']) ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <span class="material-symbols-outlined text-[64px] text-white">person</span>
                <?php endif; ?>
            </div>
            <div class="egoera-puntu-txikia"></div>
        </div>
        <h2 class="titulu-ertaina mt-4"><?= htmlspecialchars($erabiltzaileDatuak['izena']) ?></h2>
        <p class="testu-lagungarria">Barber Profesionala</p>
        
        <div class="profil-info-zerrenda">
            <div class="space-y-4">
                <h3 class="profil-atal-titulua">Laneko informazioa</h3>
                <div class="panela-premium p-4 divide-y divide-white/5">
                    <div class="py-3 flex justify-between">
                        <span class="testu-lagungarria">Emaila</span>
                        <span class="titulu-txikia font-medium"><?= htmlspecialchars($erabiltzaileDatuak['posta']) ?></span>
                    </div>
                    <div class="py-3 flex justify-between">
                        <span class="testu-lagungarria">Telefonoa</span>
                        <span class="titulu-txikia font-medium"><?= htmlspecialchars($erabiltzaileDatuak['telefonoa'] ?: 'Ez dago') ?></span>
                    </div>
                </div>
            </div>

            <a href="saioa_itxi.php" class="botoi-saioa-itxi-txikia">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                Saioa Itxi
            </a>
        </div>
    </div>

    <!-- Bottom Nav -->
    <nav class="nabigazio-barra-langile">
        <div class="flex justify-around items-center">
            <a class="nabigazio-botoia-langile" href="langile_panela.php">
                <span class="material-symbols-outlined">calendar_month</span>
                <p class="text-[10px] font-medium">Egutegia</p>
            </a>
            <a class="nabigazio-botoia-langile" href="bezero_zerrenda.php">
                <span class="material-symbols-outlined">group</span>
                <p class="text-[10px] font-medium">Bezeroak</p>
            </a>
            <a class="nabigazio-botoia-langile aktiboa" href="langile_profila.php">
                <span class="material-symbols-outlined betea">person</span>
                <p class="text-[10px] font-bold">Profila</p>
            </a>
        </div>
    </nav>
</div>

<?php require_once '../includes/footer.php'; ?>


