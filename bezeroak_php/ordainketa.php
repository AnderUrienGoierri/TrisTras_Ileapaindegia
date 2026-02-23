<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../sesioa/saioa_hasi.php');
    exit;
}

$pageTitle = 'Tristras - Ordainketa';
$pageCSS = 'ordainketa.css';
require_once '../includes/header.php';

// Erreserba datuak saioatik edo POST-etik lortu
$zerbitzuId = $_POST['service_id'] ?? ($_SESSION['last_booking']['service_id'] ?? null);
$langileId = $_POST['barber_id'] ?? ($_SESSION['last_booking']['barber_id'] ?? null);
$data = $_POST['date'] ?? ($_SESSION['last_booking']['date'] ?? null);
$ordua = $_POST['time'] ?? ($_SESSION['last_booking']['time'] ?? null);

if (!$zerbitzuId || !$langileId || !$data || !$ordua) {
    header('Location: erreserba.php');
    exit;
}

// Xehetasunak eskuratu
$zerbitzuStmt = $pdo->prepare("SELECT izena, prezioa FROM zerbitzuak WHERE id = ?");
$zerbitzuStmt->execute([$zerbitzuId]);
$zerbitzua = $zerbitzuStmt->fetch();

// Data formateatu
$dataObj = new DateTime($data);
$formatuData = $dataObj->format('M j');
?>

<div class="pantaila-nagusia">
    <!-- Header -->
    <header class="ordainketa-goiburukoa">
        <button onclick="history.back()" class="botoi-itzuli-txikia">
            <span class="material-symbols-outlined text-[24px]">arrow_back_ios_new</span>
        </button>
        <h1 class="titulu-ertaina text-center flex-1 pr-10">Ordainketa</h1>
    </header>

    <main class="ordainketa-edukia">
        <!-- Service Summary -->
        <section>
            <h3 class="etiketa-txikia mb-3 px-1">Zerbitzuaren Laburpena</h3>
            <div class="panela-premium p-5 space-y-4">
                <div class="flex justify-between items-start gap-4">
                    <div>
                        <p class="testu-lagungarria mb-1">Zerbitzua</p>
                        <p class="titulu-txikia"><?= htmlspecialchars($zerbitzua['izena']) ?></p>
                    </div>
                    <div class="text-right">
                        <p class="testu-lagungarria mb-1">Data</p>
                        <p class="titulu-txikia"><?= $formatuData ?></p>
                    </div>
                </div>
                <div class="h-px bg-white/5 w-full"></div>
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2 text-primary dark:text-primary-light">
                        <span class="material-symbols-outlined text-lg">schedule</span>
                        <span class="testu-lagungarria font-medium"><?= $ordua ?></span>
                    </div>
                    <div class="flex items-baseline gap-1">
                        <span class="testu-lagungarria">Guztira</span>
                        <span class="titulu-ertaina"><?= number_format($zerbitzua['prezioa'], 2) ?>€</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Payment Method -->
        <section>
            <h3 class="etiketa-txikia mb-3 px-1">Ordainketa metodoa</h3>
            <div class="metodo-hautatzailea">
                <label class="flex-1 cursor-pointer">
                    <input checked="" class="peer sr-only" name="payment_method" type="radio" value="card"/>
                    <div class="metodo-elementua">
                        <span class="material-symbols-outlined text-[20px]">credit_card</span>
                        <span>Txartela</span>
                    </div>
                </label>
                <label class="flex-1 cursor-pointer">
                    <input class="peer sr-only" name="payment_method" type="radio" value="paypal"/>
                    <div class="metodo-elementua">
                        <span class="material-symbols-outlined text-[20px]">account_balance_wallet</span>
                        <span>PayPal</span>
                    </div>
                </label>
            </div>
        </section>

        <!-- Card Details Form -->
        <form id="paymentForm" class="space-y-4">
            <input type="hidden" name="service_id" value="<?= $zerbitzuId ?>">
            <input type="hidden" name="barber_id" value="<?= $langileId ?>">
            <input type="hidden" name="date" value="<?= $data ?>">
            <input type="hidden" name="time" value="<?= $ordua ?>">

            <div class="space-y-1.5">
                <label class="etiketa-txikia ml-1" for="cardName">Txartelaren izena</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-400 group-focus-within:text-primary dark:group-focus-within:text-primary-light">person</span>
                    </div>
                    <input class="input-testu pl-10" id="cardName" placeholder="Adib. Jon Etxeberria" type="text" required>
                </div>
            </div>

            <div class="space-y-1.5">
                <label class="etiketa-txikia ml-1" for="cardNumber">Zenbakia</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="material-symbols-outlined text-slate-400 group-focus-within:text-primary dark:group-focus-within:text-primary-light">numbers</span>
                    </div>
                    <input class="input-testu pl-10" id="cardNumber" inputmode="numeric" placeholder="0000 0000 0000 0000" type="text" required>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="space-y-1.5 flex-1">
                    <label class="etiketa-txikia ml-1" for="expiry">Iraungitze-data</label>
                    <input class="input-testu" id="expiry" inputmode="numeric" placeholder="HH/UU" type="text" required>
                </div>
                <div class="space-y-1.5 flex-1">
                    <label class="etiketa-txikia ml-1" for="cvv">CVV</label>
                    <input class="input-testu" id="cvv" inputmode="numeric" placeholder="123" type="text" required>
                </div>
            </div>
            
            <div class="flex items-center justify-center gap-2 py-2 opacity-60">
                <span class="material-symbols-outlined text-green-500 text-sm">lock</span>
                <span class="text-xs text-slate-500 dark:text-slate-400">SSL bidezko ordainketa segurua</span>
            </div>
        </form>
    </main>

    <!-- Bottom Action Bar -->
    <div class="ordainketa-beheko-barra">
        <div class="max-w-md mx-auto w-full">
            <button type="button" id="payButton" class="w-full botoi-premium shadow-lg">
                <span>Ordaindu orain</span>
                <span class="material-symbols-outlined text-lg">arrow_forward</span>
            </button>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#payButton').click(function() {
        // In a real app, we would process payment here.
        // For this demo, we'll just submit the booking data to bezero_dashboard or similar.
        // We'll use erreserba.php's save logic but directed to a save endpoint or just simulated.
        
        const erreserbaDatuak = {
            submit_booking_ajax: 1,
            service_id: $('input[name="service_id"]').val(),
            barber_id: $('input[name="barber_id"]').val(),
            date: $('input[name="date"]').val(),
            time: $('input[name="time"]').val()
        };

        $(this).prop('disabled', true).html('Prozesatzen...');

        $.post('erreserba.php', erreserbaDatuak, function(response) {
            try {
                const res = JSON.parse(response);
                if (res.status === 'success') {
                    window.location.href = 'berrespena.php?id=' + res.appointment_id;
                } else {
                    alert('Akats bat gertatu da: ' + res.message);
                    $('#payButton').prop('disabled', false).html('<span>Ordaindu orain</span><span class="material-symbols-outlined text-lg">arrow_forward</span>');
                }
            } catch (e) {
                console.error(response);
                alert('Zerbitzariaren erantzun okerra.');
                $('#payButton').prop('disabled', false).html('Ordaindu orain');
            }
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>


