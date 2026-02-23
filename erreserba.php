<?php
require_once 'includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: saioa_hasi.php');
    exit;
}

// Erreserba bidalketa kudeatu POST bidez
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
    $bezeroId = $_SESSION['user_id'];
    $zerbitzuId = $_POST['service_id'];
    $langileId = $_POST['barber_id'];
    $data = $_POST['date'];
    $ordua = $_POST['time'];
    
    // Zerbitzuaren prezioa eta iraupena lortu
    $zerbStmt = $pdo->prepare("SELECT prezioa, iraupena FROM zerbitzuak WHERE id = ?");
    $zerbStmt->execute([$zerbitzuId]);
    $zerb = $zerbStmt->fetch();
    
    if (!$zerb) {
        if (isset($_POST['submit_booking_ajax'])) {
            echo json_encode(['status' => 'error', 'message' => 'Zerbitzua ez da aurkitu.']);
            exit;
        }
        $error = "Zerbitzua ez da aurkitu.";
    } else {
        $prezioa = $zerb['prezioa'];
        $iraupena = $zerb['iraupena'];
        $amaieraOrdua = date('H:i:s', strtotime("$ordua + $iraupena minutes"));

        $stmt = $pdo->prepare("INSERT INTO hitzorduak (bezero_id, langile_id, zerbitzu_id, data, hasiera, amaiera, egoera, prezioa) VALUES (?, ?, ?, ?, ?, ?, 'confirmed', ?)");
        
        if ($stmt->execute([$bezeroId, $langileId, $zerbitzuId, $data, $ordua, $amaieraOrdua, $prezioa])) {
            $appointmentId = $pdo->lastInsertId();
            if (isset($_POST['submit_booking_ajax'])) {
                echo json_encode(['status' => 'success', 'appointment_id' => $appointmentId]);
                exit;
            }
            header("Location: berrespena.php?id=$appointmentId");
            exit;
        } else {
            if (isset($_POST['submit_booking_ajax'])) {
                echo json_encode(['status' => 'error', 'message' => 'Ezin izan da erreserba egin.']);
                exit;
            }
            $error = "Ezin izan da erreserba egin. Saiatu berriro.";
        }
    }
}

// Datuak eskuratu bistaratzeko
$zerbitzuakStmt = $pdo->query("SELECT * FROM zerbitzuak");
$zerbitzuak = $zerbitzuakStmt->fetchAll();

$langileakStmt = $pdo->query("SELECT id, izena, irudia FROM erabiltzaileak WHERE rola = 'barber'");
$langileak = $langileakStmt->fetchAll();

$hautatutakoZerbitzuId = $_GET['service'] ?? null;

$pageTitle = 'Tristras - Erreserba';
require_once 'includes/header.php';
?>

<header class="fixed top-0 z-50 w-full bg-background-light/90 dark:bg-background-dark/90 backdrop-blur-md border-b border-white/5 transition-all duration-300">
    <div class="flex items-center justify-between px-4 h-14 max-w-md mx-auto">
        <a href="bezero_panela.php" class="flex items-center justify-center size-10 rounded-full hover:bg-white/10 transition-colors text-white">
            <span class="material-symbols-outlined">arrow_back_ios_new</span>
        </a>
        <h1 class="titulu-ertaina">Erreserba</h1>
        <div class="size-10"></div>
    </div>
</header>

<main class="w-full pt-20 px-4 flex flex-col gap-8 max-w-md mx-auto pb-40">
    <form id="erreserbaInprimakia" method="POST" action="ordainketa.php">
        <input type="hidden" name="submit_booking" value="1">
        <input type="hidden" name="service_id" id="service_id" value="<?= $hautatutakoZerbitzuId ?>">
        <input type="hidden" name="barber_id" id="barber_id" value="">
        <input type="hidden" name="date" id="date" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="time" id="time" value="">

        <!-- Atala: Zerbitzuak -->
        <section class="mb-8">
            <h2 class="titulu-nagusia mb-4">Aukeratu Zerbitzua</h2>
            <div class="flex flex-col gap-4">
                <?php foreach ($zerbitzuak as $zerb): ?>
                <div class="zerbitzu-txartela beira-txartela rounded-xl p-4 flex gap-4 items-center cursor-pointer transition-all duration-200 hover:bg-white/5 relative overflow-hidden group <?= ($hautatutakoZerbitzuId == $zerb['id']) ? 'border-primary shadow-glow' : '' ?>" 
                     data-id="<?= $zerb['id'] ?>" data-price="<?= $zerb['prezioa'] ?>">
                    <?php if ($hautatutakoZerbitzuId == $zerb['id']): ?>
                        <div class="absolute inset-0 bg-primary/10"></div>
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>
                    <?php endif; ?>
                    <div class="size-16 rounded-lg bg-cover bg-center shrink-0 shadow-lg" style="background-image: url('<?= htmlspecialchars($zerb['irudia']) ?>');"></div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start mb-1">
                            <h3 class="titulu-txikia truncate pr-2"><?= htmlspecialchars($zerb['izena']) ?></h3>
                            <span class="check-icon material-symbols-outlined text-primary text-[20px] font-bold <?= ($hautatutakoZerbitzuId == $zerb['id']) ? '' : 'hidden' ?>">check_circle</span>
                        </div>
                        <p class="testu-lagungarria line-clamp-2 mb-2"><?= htmlspecialchars($zerb['deskribapena'] ?? '') ?></p>
                        <div class="flex items-center gap-3 etiketa-txikia">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined ikono-sm">schedule</span> <?= $zerb['iraupena'] ?> min</span>
                            <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                            <span class="text-white font-bold"><?= number_format($zerb['prezioa'], 0) ?>€</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Atala: Barberoa hautatu -->
        <section class="mb-8">
            <h2 class="titulu-nagusia mb-4">Aukeratu Barberoa</h2>
            <div class="flex gap-6 overflow-x-auto korritze-barra-ezkutatu pb-2 px-1">
                <?php foreach ($langileak as $langilea): ?>
                <div class="langile-txartela flex flex-col items-center gap-2 cursor-pointer group min-w-[70px]" data-id="<?= $langilea['id'] ?>">
                    <div class="relative avatar-ingurua">
                        <div class="avatar-eraztuna size-16 rounded-full p-0.5 bg-transparent border border-slate-700 transition-all">
                            <?php if ($langilea['irudia']): ?>
                                <img alt="<?= htmlspecialchars($langilea['izena']) ?>" class="w-full h-full rounded-full object-cover opacity-70 group-hover:opacity-100 transition-opacity" 
                                     src="<?= htmlspecialchars($langilea['irudia']) ?>">
                            <?php else: ?>
                                <div class="w-full h-full rounded-full bg-slate-700 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-500">person</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="baieztapen-txapa absolute bottom-0 right-0 bg-primary text-white rounded-full p-0.5 border-2 border-background-dark hidden">
                            <span class="material-symbols-outlined text-[12px] block">check</span>
                        </div>
                    </div>
                    <span class="langile-izena etiketa-txikia group-hover:text-white transition-colors"><?= htmlspecialchars(explode(' ', $langilea['izena'])[0]) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Atala: Eguna hautatu -->
        <section class="mb-8">
            <h2 class="titulu-nagusia mb-4">Aukeratu Eguna</h2>
            <div class="beira-txartela rounded-xl p-4 mb-6">
                <div class="flex justify-between items-center text-center">
                    <?php 
                    $today = new DateTime();
                    for($i=0; $i<6; $i++): 
                        $curr = clone $today;
                        $curr->modify("+$i days");
                    ?>
                    <div class="data-hautatzailea flex flex-col gap-1 w-10 cursor-pointer <?= ($i==0)?'active-date':'' ?>" data-date="<?= $curr->format('Y-m-d') ?>">
                        <span class="etiketa-txikia"><?= $curr->format('D') ?></span>
                        <div class="data-zenbakia w-10 h-10 flex items-center justify-center rounded-full transition-all <?= ($i==0)?'bg-primary text-white shadow-glow':'text-slate-300 hover:bg-white/5' ?>">
                            <?= $curr->format('d') ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <h3 class="titulu-txikia mb-3">Ordua</h3>
            <div class="grid grid-cols-4 gap-3 mb-6">
                <?php 
                $slots = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '15:00', '15:30', '16:00', '16:30'];
                foreach($slots as $slot): ?>
                <button type="button" class="ordu-tarte-botoia py-2 px-1 rounded-lg border border-slate-700 text-slate-400 text-sm font-medium hover:border-slate-500 hover:text-white transition-all" 
                        data-time="<?= $slot ?>:00">
                    <?= $slot ?>
                </button>
                <?php endforeach; ?>
            </div>
        </section>
    </form>
</main>

<!-- Fixed Bottom Bar -->
<div class="fixed bottom-0 left-0 w-full bg-slate-900/80 backdrop-blur-xl border-t border-white/5 p-4 safe-area-bottom pb-8 z-50">
    <div class="max-w-md mx-auto flex items-center justify-between gap-6">
        <div class="flex flex-col">
            <span class="etiketa-txikia">Guztira</span>
            <div class="flex items-baseline gap-1">
                <span id="prezioaErakutsi" class="titulu-nagusia">0,00€</span>
            </div>
        </div>
        <button type="button" id="erreserbaBaieztatuBotoia" class="flex-1 botoi-premium shadow-glow opacity-50 cursor-not-allowed">
            <span>Erreserbatu</span>
            <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
        </button>
    </div>
</div>

<script>
$(document).ready(function() {
    let hautatutakoZerbitzua = $('#service_id').val();
    let hautatutakoLangilea = '';
    let hautatutakoData = $('#date').val();
    let hautatutakoOrdua = '';

    // Hasierako prezioa eguneratu
    if (hautatutakoZerbitzua) {
        let txartelAktiboa = $(`.zerbitzu-txartela[data-id="${hautatutakoZerbitzua}"]`);
        $('#prezioaErakutsi').text(txartelAktiboa.data('price') + ',00€');
    }

    // Zerbitzua hautatzea
    $('.zerbitzu-txartela').click(function() {
        $('.zerbitzu-txartela').removeClass('border-primary shadow-glow').find('.absolute').remove();
        $('.zerbitzu-txartela .check-icon').addClass('hidden');
        
        $(this).addClass('border-primary shadow-glow').prepend('<div class="absolute inset-0 bg-primary/10"></div><div class="absolute left-0 top-0 bottom-0 w-1 bg-primary"></div>');
        $(this).find('.check-icon').removeClass('hidden');
        
        hautatutakoZerbitzua = $(this).data('id');
        $('#service_id').val(hautatutakoZerbitzua);
        $('#prezioaErakutsi').text($(this).data('price') + ',00€');
        balidatu();
    });

    // Barberoa hautatzea
    $('.langile-txartela').click(function() {
        $('.avatar-eraztuna').removeClass('bg-gradient-to-br from-primary to-purple-400 ring-2 ring-primary ring-offset-2 ring-offset-background-dark shadow-glow border-none');
        $('.avatar-eraztuna img').addClass('opacity-70');
        $('.baieztapen-txapa').addClass('hidden');
        $('.langile-izena').removeClass('text-white').addClass('text-slate-400');

        $(this).find('.avatar-eraztuna').addClass('bg-gradient-to-br from-primary to-purple-400 ring-2 ring-primary ring-offset-2 ring-offset-background-dark shadow-glow border-none');
        $(this).find('img').removeClass('opacity-70');
        $(this).find('.baieztapen-txapa').removeClass('hidden');
        $(this).find('.langile-izena').removeClass('text-slate-400').addClass('text-white');

        hautatutakoLangilea = $(this).data('id');
        $('#barber_id').val(hautatutakoLangilea);
        balidatu();
    });

    // Data hautatzea
    $('.data-hautatzailea').click(function() {
        $('.data-hautatzailea .data-zenbakia').removeClass('bg-primary text-white shadow-glow').addClass('text-slate-300');
        $(this).find('.data-zenbakia').addClass('bg-primary text-white shadow-glow').removeClass('text-slate-300');
        
        hautatutakoData = $(this).data('date');
        $('#date').val(hautatutakoData);
        balidatu();
    });

    // Ordua hautatzea
    $('.ordu-tarte-botoia').click(function() {
        $('.ordu-tarte-botoia').removeClass('bg-primary border-transparent text-white shadow-glow ring-2 ring-primary ring-offset-1 ring-offset-background-dark').addClass('border-slate-700 text-slate-400');
        $(this).removeClass('border-slate-700 text-slate-400').addClass('bg-primary border-transparent text-white shadow-glow ring-2 ring-primary ring-offset-1 ring-offset-background-dark');
        
        hautatutakoOrdua = $(this).data('time');
        $('#time').val(hautatutakoOrdua);
        balidatu();
    });

    function balidatu() {
        if (hautatutakoZerbitzua && hautatutakoLangilea && hautatutakoData && hautatutakoOrdua) {
            $('#erreserbaBaieztatuBotoia').removeClass('opacity-50 cursor-not-allowed').addClass('hover:bg-[#5a32b6]');
        } else {
            $('#erreserbaBaieztatuBotoia').addClass('opacity-50 cursor-not-allowed').removeClass('hover:bg-[#5a32b6]');
        }
    }

    $('#erreserbaBaieztatuBotoia').click(function() {
        if (hautatutakoZerbitzua && hautatutakoLangilea && hautatutakoData && hautatutakoOrdua) {
            $('#erreserbaInprimakia').submit();
        }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>


