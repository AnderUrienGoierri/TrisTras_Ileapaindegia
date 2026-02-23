<?php
$pageTitle = 'Barber App - Hasiera';
require_once 'includes/header.php';
?>

<!-- Edukiontzi Nagusia -->
<div class="relative min-h-screen flex flex-col w-full max-w-md mx-auto shadow-2xl overflow-hidden bg-[#0f172a]">
    
    <!-- Giroko dirdirak -->
    <div class="giro-dirdira-nagusia"></div>
    <div class="giro-dirdira-sekundarioa"></div>
    
    <!-- Hero Atala -->
    <div class="relative w-full h-[60vh] z-10">
        <!-- Irudia gradientearekin behean desager dadin -->
        <div class="w-full h-full relative hero-irudia">
            <div class="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#0f172a]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#0f172a] via-transparent to-black/20"></div>
        </div>
        
        <!-- Goi Nabigazioa (Leuna) -->
        <div class="absolute top-0 left-0 w-full p-6 flex justify-between items-center text-white/80">
            <span class="material-symbols-outlined cursor-pointer">menu</span>
            <span class="text-xs font-bold tracking-widest uppercase opacity-70">Barber Studio</span>
            <span class="material-symbols-outlined cursor-pointer">notifications</span>
        </div>
    </div>
    
    <!-- Eduki Atala (Heroaren gainean) -->
    <div class="relative z-20 flex-1 flex flex-col -mt-16 px-6 pb-8">
        <!-- Tipografia Blokea -->
        <div class="mb-8 text-center md:text-left">
            <h1 class="titulu-nagusia text-[40px] leading-[1.1] mb-3 drop-shadow-lg">
                Gure Estiloa,<br/>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-white to-gray-400">Zure Nortasuna</span>
            </h1>
            <p class="testu-lagungarria text-lg font-medium leading-relaxed max-w-[90%] mx-auto md:mx-0">
                Premium esperientzia zure ile eta bizarrentzat.
            </p>
        </div>
        
        <!-- Beheko edukia bultzatzeko espazioa -->
        <div class="flex-grow"></div>
        
        <!-- Akzio Eremua -->
        <div class="flex flex-col gap-4 w-full">
            <!-- Erreserba Botoi Nagusia -->
            <a href="saioa_hasi.php" class="group relative w-full h-14 overflow-hidden rounded-xl shadow-[0_0_20px_rgba(20,184,166,0.3)] transition-all hover:scale-[1.02] active:scale-[0.98] block">
                <div class="absolute inset-0 bg-gradient-to-r from-teal-500 to-emerald-500 transition-all group-hover:bg-gradient-to-r group-hover:from-teal-400 group-hover:to-emerald-400"></div>
                <div class="relative flex items-center justify-center gap-2 h-full w-full">
                    <span class="text-white text-lg font-bold tracking-wide">Erreserbatu Nire Hitzordua</span>
                    <span class="material-symbols-outlined text-white ikono-md">calendar_month</span>
                </div>
            </a>
            
            <!-- Saioa Hasi/Erregistratu Panela -->
            <div class="panela-premium w-full p-5 flex flex-col sm:flex-row items-center justify-between gap-4 mt-2">
                <div class="flex flex-col gap-0.5 text-center sm:text-left">
                    <span class="titulu-txikia shadow-none">Kontua</span>
                    <span class="testu-lagungarria text-xs">Kudeatu zure erreserbak erraz</span>
                </div>
                <div class="flex items-center gap-4 text-sm font-semibold tracking-wide">
                    <a href="saioa_hasi.php" class="testu-lagungarria hover:text-white transition-colors py-2">Hasi saioa</a>
                    <div class="h-4 w-[1px] bg-slate-600"></div>
                    <a href="erregistratu.php" class="text-accent-teal hover:text-teal-300 transition-colors py-2 flex items-center gap-1 group/link">
                        Erregistratu
                        <span class="material-symbols-outlined transition-transform group-hover/link:translate-x-1 ikono-sm">arrow_forward</span>
                    </a>
                </div>
            </div>
            
            <!-- Oin-oharra / Bertsioa (Leuna) -->
            <div class="text-center mt-4">
                <p class="etiketa-txikia">V 2.0.4 • Euskara</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>


