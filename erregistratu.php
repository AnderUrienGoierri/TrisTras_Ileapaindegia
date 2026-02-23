<?php
require_once 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $izena = $_POST['name'] ?? '';
    $posta = $_POST['email'] ?? '';
    $pasahitza = $_POST['password'] ?? '';
    $telefonoa = $_POST['phone'] ?? '';

    if ($izena && $posta && $pasahitza) {
        $stmt = $pdo->prepare('SELECT id FROM erabiltzaileak WHERE posta = ?');
        $stmt->execute([$posta]);
        if ($stmt->fetch()) {
            $error = 'Posta elektroniko hau lehendik erregistratuta dago.';
        } else {
            $pasahitzHaxeatua = password_hash($pasahitza, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO erabiltzaileak (izena, posta, pasahitza, telefonoa, rola) VALUES (?, ?, ?, ?, "client")');
            if ($stmt->execute([$izena, $posta, $pasahitzHaxeatua, $telefonoa])) {
                $success = 'Kontua ondo sortu da! Orain hasi zaitezke saioa.';
            } else {
                $error = 'Akats bat gertatu da kontua sortzerakoan.';
            }
        }
    } else {
        $error = 'Mesedez, bete beharrezko eremu guztiak (Izena, Posta eta Pasahitza).';
    }
}

$pageTitle = 'Tristras - Erregistratu';
require_once 'includes/header.php';
?>

<!-- Ambient Glow Effects -->
<div class="fixed top-[-20%] left-[-10%] w-[500px] h-[500px] bg-primary/20 rounded-full blur-[120px] pointer-events-none z-0"></div>

<div class="relative w-full max-w-[480px] min-h-screen flex flex-col z-10 p-4 mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between py-4">
        <a href="saioa_hasi.php" class="text-white hover:text-primary transition-colors flex size-10 shrink-0 items-center justify-center rounded-full active:bg-surface-dark">
            <span class="material-symbols-outlined text-[24px]">arrow_back</span>
        </a>
        <h1 class="titulu-ertaina">Erregistratu</h1>
        <div class="size-10"></div>
    </div>

    <!-- Register Form -->
    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="bg-green-500/10 border border-green-500/50 text-green-500 p-4 rounded-xl mb-6 text-sm">
            <?= htmlspecialchars($success) ?>
            <div class="mt-2">
                <a href="saioa_hasi.php" class="font-bold underline">Hasi saioa hemen</a>
            </div>
        </div>
    <?php endif; ?>

    <form method="POST" action="erregistratu.php" class="flex flex-col gap-5">
        <div class="space-y-1.5">
            <label class="etiketa-txikia ml-1" for="name">Izena eta Abizena</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors">person</span>
                </div>
                <input class="input-testu pl-12" 
                       id="name" name="name" placeholder="Zure izena" type="text" required>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="etiketa-txikia ml-1" for="email">Posta elektronikoa</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors">mail</span>
                </div>
                <input class="input-testu pl-12" 
                       id="email" name="email" placeholder="zure@posta.com" type="email" required>
            </div>
        </div>

        <div class="space-y-1.5">
            <label class="etiketa-txikia ml-1" for="phone">Telefonoa (Aukerakoa)</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors">call</span>
                </div>
                <input class="input-testu pl-12" 
                       id="phone" name="phone" placeholder="600 000 000" type="tel">
            </div>
        </div>
        
        <div class="space-y-1.5">
            <label class="etiketa-txikia ml-1" for="password">Pasahitza</label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors">lock</span>
                </div>
                <input class="input-testu pl-12 pr-12" 
                       id="password" name="password" placeholder="••••••••" type="password" required>
            </div>
        </div>

        <button type="submit" class="w-full mt-4 botoi-premium shadow-glow group">
            <span>Kontua Sortu</span>
            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">how_to_reg</span>
        </button>
    </form>

    <div class="mt-8 text-center">
        <p class="testu-lagungarria">
            Dagoeneko baduzu kontua? 
            <a class="text-white font-medium hover:text-primary-light transition-colors ml-1 underline" href="saioa_hasi.php">Hasi saioa</a>
        </p>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


