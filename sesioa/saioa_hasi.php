<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($email && $password) {
        $stmt = $pdo->prepare('SELECT * FROM erabiltzaileak WHERE posta = ?');
        $stmt->execute([$email]);
        $erabiltzailea = $stmt->fetch();

        if ($erabiltzailea && password_verify($password, $erabiltzailea['pasahitza'])) {
            $_SESSION['user_id'] = $erabiltzailea['id'];
            $_SESSION['user_name'] = $erabiltzailea['izena'];
            $_SESSION['user_role'] = $erabiltzailea['rola'];

            if ($erabiltzailea['rola'] === 'barber' || $erabiltzailea['rola'] === 'admin') {
                header('Location: ../langileak_php/langile_panela.php');
            } else {
                header('Location: ../bezeroak_php/bezero_panela.php');
            }
            exit;
        } else {
            $error = 'Posta elektronikoa edo pasahitza okerrak dira.';
        }
    } else {
        $error = 'Mesedez, bete eremu guztiak.';
    }
}

$pageTitle = 'Tristras - Saio Hasiera';
require_once '../includes/header.php';
?>

<!-- Ambient Glow Effects -->
<div class="fixed top-[-20%] left-[-10%] w-[500px] h-[500px] bg-primary/20 rounded-full blur-[120px] pointer-events-none z-0"></div>
<div class="fixed bottom-[-10%] right-[-10%] w-[400px] h-[400px] bg-primary/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

<div class="relative w-full max-w-[480px] min-h-screen flex flex-col z-10 p-4 mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between py-4">
        <a href="../index.php" class="text-white hover:text-primary transition-colors flex size-10 shrink-0 items-center justify-center rounded-full active:bg-surface-dark">
            <span class="material-symbols-outlined text-[24px]">arrow_back</span>
        </a>
        <h1 class="titulu-ertaina">Hasi saioa</h1>
        <div class="size-10"></div> <!-- Spacer for alignment -->
    </div>

    <!-- Hero Section -->
    <div class="mt-4 mb-8">
        <div class="w-full relative overflow-hidden rounded-2xl aspect-[16/9] shadow-2xl ring-1 ring-white/10 group">
            <div class="absolute inset-0 bg-gradient-to-t from-background-dark via-transparent to-transparent opacity-80 z-10"></div>
            <div class="w-full h-full bg-center bg-cover bg-no-repeat transition-transform duration-700 group-hover:scale-105" 
                 style="background-image: url('../irudiak/hero_barber.png');">
            </div>
            <div class="absolute bottom-4 left-4 z-20">
                <div class="etiketa-txikia mb-1">Ongi etorri</div>
                <h2 class="titulu-nagusia leading-tight">Barber Shop Premium</h2>
            </div>
        </div>
    </div>

    <!-- Login Form -->
    <?php if ($error): ?>
        <div class="bg-red-500/10 border border-red-500/50 text-red-500 p-4 rounded-xl mb-6 text-sm">
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="saioa_hasi.php" class="flex flex-col gap-5">
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
            <div class="flex justify-between items-center ml-1">
                <label class="etiketa-txikia" for="password">Pasahitza</label>
            </div>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <span class="material-symbols-outlined text-text-secondary group-focus-within:text-primary transition-colors">lock</span>
                </div>
                <input class="input-testu pl-12 pr-12" 
                       id="password" name="password" placeholder="••••••••" type="password" required>
                <button class="absolute inset-y-0 right-0 pr-4 flex items-center text-text-secondary hover:text-white transition-colors cursor-pointer focus:outline-none" type="button" id="togglePassword">
                    <span class="material-symbols-outlined text-[20px]">visibility_off</span>
                </button>
            </div>
            <div class="flex justify-end mt-1">
                <a class="text-sm font-medium text-primary-light hover:text-white transition-colors" href="#">
                    Pasahitza ahaztu duzu?
                </a>
            </div>
        </div>

        <!-- Main Button -->
        <button type="submit" class="w-full mt-2 botoi-premium shadow-glow group">
            <span>Sartu</span>
            <span class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
        </button>

        <!-- Divider -->
        <div class="relative my-4">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-border-input"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="px-3 bg-background-dark text-text-secondary uppercase tracking-widest text-[10px] font-bold">Edo jarraitu honekin...</span>
            </div>
        </div>

        <!-- Social Login -->
        <div class="grid grid-cols-2 gap-4">
            <button type="button" class="flex items-center justify-center gap-3 bg-surface-input border border-border-input hover:border-primary/50 hover:bg-surface-dark text-white py-3 px-4 rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"></path>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"></path>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"></path>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"></path>
                </svg>
                <span class="font-medium text-sm">Google</span>
            </button>
            <button type="button" class="flex items-center justify-center gap-3 bg-surface-input border border-border-input hover:border-primary/50 hover:bg-surface-dark text-white py-3 px-4 rounded-xl transition-all duration-200 group">
                <svg class="w-5 h-5 fill-current group-hover:scale-110 transition-transform" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M17.05 20.28c-.98.95-2.05.88-3.08.4-1.09-.5-2.08-.48-3.24 0-1.44.62-2.2.44-3.06-.4C2.79 15.25 3.51 7.59 9.05 7.31c1.35.07 2.29.74 3.08.8 1.18-.24 2.31-.93 3.57-.84 1.51.12 2.65.72 3.4 1.8-3.12 1.87-2.38 5.98.48 7.13-.24.65-.42 1.28-.73 1.94-.52 1.08-1.12 2.22-1.8 2.14zM12.03 7.25c-.15-2.23 1.66-4.07 3.74-4.25.29 2.58-2.34 4.5-3.74 4.25z"></path>
                </svg>
                <span class="font-medium text-sm">Apple</span>
            </button>
        </div>
    </form>

    <!-- Footer -->
    <div class="mt-auto pt-8 pb-4 text-center">
        <p class="testu-lagungarria">
            Ez duzu konturik? 
            <a class="text-white font-medium hover:text-primary-light transition-colors ml-1 underline decoration-primary/50 decoration-2 underline-offset-4" href="erregistratu.php">Erregistratu</a>
        </p>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#togglePassword').click(function() {
        const passwordInput = $('#password');
        const icon = $(this).find('.material-symbols-outlined');
        
        if (passwordInput.attr('type') === 'password') {
            passwordInput.attr('type', 'text');
            icon.text('visibility');
        } else {
            passwordInput.attr('type', 'password');
            icon.text('visibility_off');
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>


