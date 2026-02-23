<?php
require_once '../includes/db.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../sesioa/saioa_hasi.php');
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
$pageCSS = 'erreserba.css';
require_once '../includes/header.php';
?>

<header class="erreserba-goiburukoa shadow-lg">
    <div class="erreserba-goiburuko-edukia">
        <a href="bezero_panela.php" class="botoi-itzuli-txikia text-white">
            <span class="material-symbols-outlined">arrow_back_ios_new</span>
        </a>
        <h1 class="titulu-ertaina">Erreserba</h1>
        <div class="size-10"></div>
    </div>
</header>

<main class="erreserba-nagusia">
    <form id="erreserbaInprimakia" method="POST" action="ordainketa.php">
        <input type="hidden" name="submit_booking" value="1">
        <input type="hidden" name="service_id" id="service_id" value="<?= $hautatutakoZerbitzuId ?>">
        <input type="hidden" name="barber_id" id="barber_id" value="">
        <input type="hidden" name="date" id="date" value="<?= date('Y-m-d') ?>">
        <input type="hidden" name="time" id="time" value="">

        <!-- Atala: Zerbitzuak -->
        <section class="atala-bilgarria">
            <h2 class="titulu-nagusia mb-4">Aukeratu Zerbitzua</h2>
            <div class="zerbitzu-zerrend">
                <?php foreach ($zerbitzuak as $zerb): ?>
                <div class="zerbitzu-hautagai-txartela beira-txartela rounded-xl p-4 <?= ($hautatutakoZerbitzuId == $zerb['id']) ? 'aktiboa' : '' ?>" 
                     data-id="<?= $zerb['id'] ?>" data-price="<?= $zerb['prezioa'] ?>">
                    <?php if ($hautatutakoZerbitzuId == $zerb['id']): ?>
                        <div class="txartela-aktiboa-bilgarria"></div>
                        <div class="txartela-aktiboa-marra"></div>
                    <?php endif; ?>
                    <div class="txartel-irudi-txikia" style="background-image: url('../<?= htmlspecialchars($zerb['irudia']) ?>');"></div>
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
        <section class="atala-bilgarria">
            <h2 class="titulu-nagusia mb-4">Aukeratu Barberoa</h2>
            <div class="barber-zerrenda-horizontala korritze-barra-ezkutatu px-1">
                <?php foreach ($langileak as $langilea): ?>
                <div class="barber-elementua group" data-id="<?= $langilea['id'] ?>">
                    <div class="relative">
                        <div class="avatar-eraztun-barber">
                            <?php if ($langilea['irudia']): ?>
                                <img alt="<?= htmlspecialchars($langilea['izena']) ?>" class="w-full h-full rounded-full object-cover opacity-70 group-hover:opacity-100 transition-opacity" 
                                     src="../<?= htmlspecialchars($langilea['irudia']) ?>">
                            <?php else: ?>
                                <div class="w-full h-full rounded-full bg-slate-700 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-slate-500">person</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="baieztapen-etiketa-txikia hidden">
                            <span class="material-symbols-outlined text-[12px] block">check</span>
                        </div>
                    </div>
                    <span class="langile-izena etiketa-txikia group-hover:text-white transition-colors"><?= htmlspecialchars(explode(' ', $langilea['izena'])[0]) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Atala: Eguna hautatu -->
        <section class="atala-bilgarria">
            <h2 class="titulu-nagusia mb-4">Aukeratu Eguna</h2>
            <div class="beira-txartela data-hautatzaile-txartela rounded-xl p-4">
                <div class="flex justify-between items-center text-center">
                    <?php 
                    $today = new DateTime();
                    for($i=0; $i<6; $i++): 
                        $curr = clone $today;
                        $curr->modify("+$i days");
                    ?>
                    <div class="data-elementua <?= ($i==0)?'aktiboa':'' ?>" data-date="<?= $curr->format('Y-m-d') ?>">
                        <span class="etiketa-txikia"><?= $curr->format('D') ?></span>
                        <div class="data-txapela <?= ($i==0)?'aktiboa':'' ?>">
                            <?= $curr->format('d') ?>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <h3 class="titulu-txikia mb-3">Ordua</h3>
            <div class="ordu-sareta">
                <?php 
                $slots = ['09:00', '09:30', '10:00', '10:30', '11:00', '11:30', '12:00', '12:30', '15:00', '15:30', '16:00', '16:30'];
                foreach($slots as $slot): ?>
                <button type="button" class="ordu-botoia" data-time="<?= $slot ?>:00">
                    <?= $slot ?>
                </button>
                <?php endforeach; ?>
            </div>
        </section>
    </form>
</main>

<!-- Fixed Bottom Bar -->
<div class="erreserba-beheko-barra safe-area-bottom">
    <div class="max-w-md mx-auto flex items-center justify-between gap-6">
        <div class="flex flex-col">
            <span class="etiketa-txikia">Guztira</span>
            <div class="flex items-baseline gap-1">
                <span id="prezioaErakutsi" class="titulu-nagusia">0,00€</span>
            </div>
        </div>
        <button type="button" id="erreserbaBaieztatuBotoia" class="flex-1 botoi-premium shadow-lg opacity-50 cursor-not-allowed">
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
    $('.zerbitzu-hautagai-txartela').click(function() {
        $('.zerbitzu-hautagai-txartela').removeClass('aktiboa').find('.txartela-aktiboa-bilgarria, .txartela-aktiboa-marra').remove();
        $('.zerbitzu-hautagai-txartela .check-icon').addClass('hidden');
        
        $(this).addClass('aktiboa').prepend('<div class="txartela-aktiboa-bilgarria"></div><div class="txartela-aktiboa-marra"></div>');
        $(this).find('.check-icon').removeClass('hidden');
        
        hautatutakoZerbitzua = $(this).data('id');
        $('#service_id').val(hautatutakoZerbitzua);
        $('#prezioaErakutsi').text($(this).data('price') + ',00€');
        balidatu();
    });

    // Barberoa hautatzea
    $('.barber-elementua').click(function() {
        $('.barber-elementua').removeClass('aktiboa');
        $('.baieztapen-etiketa-txikia').addClass('hidden');
        $('.langile-izena').removeClass('text-white').addClass('text-slate-400');

        $(this).addClass('aktiboa');
        $(this).find('.baieztapen-etiketa-txikia').removeClass('hidden');
        $(this).find('.langile-izena').removeClass('text-slate-400').addClass('text-white');

        hautatutakoLangilea = $(this).data('id');
        $('#barber_id').val(hautatutakoLangilea);
        balidatu();
    });

    // Data hautatzea
    $('.data-elementua').click(function() {
        $('.data-elementua, .data-txapela').removeClass('aktiboa');
        $(this).addClass('aktiboa').find('.data-txapela').addClass('aktiboa');
        
        hautatutakoData = $(this).data('date');
        $('#date').val(hautatutakoData);
        balidatu();
    });

    // Ordua hautatzea
    $('.ordu-botoia').click(function() {
        $('.ordu-botoia').removeClass('aktiboa');
        $(this).addClass('aktiboa');
        
        hautatutakoOrdua = $(this).data('time');
        $('#time').val(hautatutakoOrdua);
        balidatu();
    });

    function balidatu() {
        if (hautatutakoZerbitzua && hautatutakoLangilea && hautatutakoData && hautatutakoOrdua) {
            $('#erreserbaBaieztatuBotoia').removeClass('opacity-50 cursor-not-allowed');
        } else {
            $('#erreserbaBaieztatuBotoia').addClass('opacity-50 cursor-not-allowed');
        }
    }

    $('#erreserbaBaieztatuBotoia').click(function() {
        if (hautatutakoZerbitzua && hautatutakoLangilea && hautatutakoData && hautatutakoOrdua) {
            $('#erreserbaInprimakia').submit();
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>


