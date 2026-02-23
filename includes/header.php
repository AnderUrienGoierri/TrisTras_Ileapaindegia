<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$pageTitle = $pageTitle ?? 'Barber App';

// Kalkulatu bidea erroarekiko (root)
$isSubdir = strpos($_SERVER['REQUEST_URI'], '_php') !== false || strpos($_SERVER['REQUEST_URI'], 'sesioa') !== false;
$basePath = $isSubdir ? '../' : '';

// Rolak eta bideak
$userRole = $_SESSION['user_role'] ?? '';
$dashboardLink = 'index.php';
if ($userRole === 'client') {
    $dashboardLink = $basePath . 'bezeroak_php/bezero_panela.php';
} elseif ($userRole === 'barber' || $userRole === 'admin') {
    $dashboardLink = $basePath . 'langileak_php/langile_panela.php';
}
?>
<!DOCTYPE html>
<html lang="eu" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    
    <!-- Fuenteak eta Ikonoak -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    
    <!-- Gaiaren Konfigurazioa -->
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#472796",
                        "primary-light": "#6d4dbd", 
                        "primary-dark": "#361d73",
                        "background-light": "#f6f6f8",
                        "background-dark": "#0f172a", // Consistent dark slate
                        "surface-dark": "#1e293b",
                        "surface-input": "#221d2f",
                        "surface-highlight": "#334155",
                        "border-input": "#453b5e",
                        "text-secondary": "#94a3b8",
                        "accent-teal": "#14b8a6",
                        "accent-emerald": "#10b981",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "2xl": "1rem", "full": "9999px"
                    },
                    boxShadow: {
                        'glow': '0 0 20px rgba(71, 39, 150, 0.5)',
                        'input-focus': '0 0 0 2px rgba(71, 39, 150, 0.3)',
                    }
                },
            },
        }
    </script>
    
    <!-- Estilo Pertsonalizatuak -->
    <link rel="stylesheet" href="<?= $basePath ?>css/estiloak.css">
    <?php if (isset($pageCSS)): ?>
    <link rel="stylesheet" href="<?= $basePath ?>css/orriak/<?= $pageCSS ?>">
    <?php endif; ?>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="<?= $basePath ?>js/scripts.js"></script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased selection:bg-primary selection:text-white pb-24 min-h-screen overflow-x-hidden">

<!-- Nabigazio Menua (Slide-out) -->
<div id="menua-estaldura" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[60] hidden transition-opacity duration-300 opacity-0"></div>
<div id="alboko-menua" class="fixed top-0 right-0 h-full w-[280px] bg-background-dark z-[70] transform translate-x-full transition-transform duration-300 ease-in-out border-l border-white/10 shadow-2xl">
    <div class="p-6 flex flex-col h-full">
        <div class="flex justify-between items-center mb-10">
            <h2 class="text-xl font-bold text-white">Menua</h2>
            <button id="menua-itxi" class="p-2 text-slate-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        
        <nav class="flex flex-col gap-6 flex-1">
            <a href="<?= $dashboardLink ?>" class="flex items-center gap-4 text-slate-300 hover:text-primary transition-colors group">
                <span class="material-symbols-outlined group-hover:scale-110 transition-transform">home</span>
                <span class="font-medium">Hasiera</span>
            </a>
            <?php if ($userRole === 'client'): ?>
                <a href="<?= $basePath ?>bezeroak_php/bezero_hitzorduak.php" class="flex items-center gap-4 text-slate-300 hover:text-primary transition-colors group">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">calendar_month</span>
                    <span class="font-medium">Nire Hitzorduak</span>
                </a>
                <a href="<?= $basePath ?>bezeroak_php/bezero_profila.php" class="flex items-center gap-4 text-slate-300 hover:text-primary transition-colors group">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">person</span>
                    <span class="font-medium">Profila</span>
                </a>
                <a href="<?= $basePath ?>bezeroak_php/ezarpenak.php" class="flex items-center gap-4 text-slate-300 hover:text-primary transition-colors group">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">settings</span>
                    <span class="font-medium">Ezarpenak</span>
                </a>
            <?php elseif ($userRole === 'barber' || $userRole === 'admin'): ?>
                <a href="<?= $basePath ?>langileak_php/bezero_zerrenda.php" class="flex items-center gap-4 text-slate-300 hover:text-primary transition-colors group">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">group</span>
                    <span class="font-medium">Bezeroak</span>
                </a>
                <a href="<?= $basePath ?>langileak_php/langile_profila.php" class="flex items-center gap-4 text-slate-300 hover:text-primary transition-colors group">
                    <span class="material-symbols-outlined group-hover:scale-110 transition-transform">person</span>
                    <span class="font-medium">Profila</span>
                </a>
            <?php endif; ?>
            <div class="h-px bg-white/5 my-2"></div>
            <a href="<?= $basePath ?>sesioa/saioa_itxi.php" class="flex items-center gap-4 text-red-400 hover:text-red-300 transition-colors group">
                <span class="material-symbols-outlined group-hover:scale-110 transition-transform">logout</span>
                <span class="font-medium">Saioa itxi</span>
            </a>
        </nav>
        
        <!-- Hizkuntza Hautatzailea -->
        <div class="mt-auto">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-3">Hizkuntza</p>
            <div class="grid grid-cols-2 gap-2">
                <button class="flex items-center justify-center gap-2 p-2 rounded-lg bg-primary/20 border border-primary/40 text-white text-xs font-bold">
                    <span>⚡️</span> Euskara
                </button>
                <button class="flex items-center justify-center gap-2 p-2 rounded-lg bg-white/5 border border-white/10 text-slate-400 text-xs hover:bg-white/10 transition-colors">
                    <span>🇪🇸</span> ES
                </button>
            </div>
        </div>
    </div>
</div>

