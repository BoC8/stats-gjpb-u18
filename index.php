 

  <?php /* page d’accueil */ ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats GJPB</title>
    <link rel="apple-touch-icon" href="pages/logo-gjpb.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>

    <style>
        /* === VARIABLES CSS === */
        :root {
            --color-sky-blue: #6EC1FF;
            --color-light-blue: #9FD6FF;
            --color-white: #ffffff;
            --color-navy: #1e3a5f;
            --color-gold: #FFD700;
            --color-text-dark: #2c3e50;
            --color-text-light: #7f8c8d;
            --color-success: #10b981;
            --color-info: #3b82f6;
            --color-danger: #ef4444;
            --color-warning: #f59e0b;

            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --max-width: 1200px;
            --padding-mobile: 1rem;
            --padding-desktop: 2rem;
            --border-radius: 16px;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --shadow-hover: 0 8px 40px rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
            --gradient-primary: linear-gradient(135deg, var(--color-sky-blue) 0%, var(--color-navy) 100%);
            --gradient-card: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            --gradient-success: linear-gradient(135deg, #10b981, #059669);
            --gradient-danger: linear-gradient(135deg, #ef4444, #dc2626);
            --gradient-warning: linear-gradient(135deg, #f59e0b, #d97706);
        }

        /* === RESET & BASE === */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-family);
            line-height: 1.6;
            color: var(--color-text-dark);
            background: linear-gradient(135deg, var(--color-white) 0%, var(--color-light-blue) 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* === CONTENEUR PRINCIPAL === */
        .container {
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 0 var(--padding-mobile);
        }

        /* === HEADER === */
        header {
            background: var(--color-white);
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-content {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            align-items: center;
            height: 70px;
            max-width: var(--max-width);
            margin: 0 auto;
            padding: 0 var(--padding-mobile);
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--color-navy);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo img {
            width: 40px;
            height: 40px;
            object-fit: contain;
        }

        /* Navigation desktop */
        .nav-desktop {
            display: none;
            justify-content: center;
            align-items: center;
            gap: 70px;
        }

        .login-desktop {
            justify-self: end;
            display: none;
        }

        .nav-links {
            display: flex;
            gap: 50px;
            justify-content: center;
            list-style: none;
        }

        .nav-links a {
            text-decoration: none;
            color: var(--color-text-dark);
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: var(--transition);
            position: relative;
        }

        .nav-links a:hover,
        .nav-links a:focus {
            background: var(--color-light-blue);
            color: var(--color-navy);
            outline: 2px solid var(--color-sky-blue);
        }

        .login-btn {
            justify-self: end;
            background: var(--color-sky-blue);
            color: var(--color-white);
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
        }

        .login-btn:hover,
        .login-btn:focus {
            background: var(--color-navy);
            transform: translateY(-1px);
            outline: 2px solid var(--color-gold);
        }

        /* Menu mobile */
        .mobile-menu-btn {
            display: block;
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: var(--color-navy);
            padding: 0.5rem;
            border-radius: 6px;
            transition: var(--transition);
        }

        .mobile-menu-btn:hover,
        .mobile-menu-btn:focus {
            background: var(--color-light-blue);
            outline: 2px solid var(--color-sky-blue);
        }

        .mobile-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--color-white);
            box-shadow: var(--shadow);
            padding: 1rem;
            text-align: center;
        }

        .mobile-menu.open {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .mobile-nav-links {
            list-style: none;
            margin-bottom: 1rem;
        }

        .mobile-nav-links li {
            margin-bottom: 0.5rem;
        }

        .mobile-nav-links a {
            display: block;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: var(--color-text-dark);
            font-weight: 500;
            border-radius: 6px;
            transition: var(--transition);
        }

        .mobile-nav-links a:hover,
        .mobile-nav-links a:focus {
            background: var(--color-light-blue);
            color: var(--color-navy);
        }

        /* === HERO SECTION === */
        .hero {
            background: var(--gradient-primary);
            padding: 6rem 0 4rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="20" cy="20" r="2" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="1" fill="white" opacity="0.15"/><circle cx="40" cy="80" r="1.5" fill="white" opacity="0.1"/><circle cx="60" cy="60" r="1" fill="white" opacity="0.2"/><circle cx="30" cy="70" r="1.2" fill="white" opacity="0.12"/></svg>');
            animation: float 20s ease-in-out infinite;
            z-index: 1;
            pointer-events: none;
        }

        /* Calques diaporama d'arrière-plan pour la hero */
        .hero-bg, .hero-bg-next {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.2s ease-in-out;
            z-index: 0;
            filter: brightness(0.55);
            pointer-events: none;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-15px) rotate(120deg);
            }

            66% {
                transform: translateY(-10px) rotate(240deg);
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            color: var(--color-white);
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            animation: titleGlow 3s ease-in-out infinite alternate;
        }

        @keyframes titleGlow {
            0% {
                text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
            }

            100% {
                text-shadow: 0 4px 20px rgba(255, 215, 0, 0.4), 0 4px 8px rgba(0, 0, 0, 0.3);
            }
        }

        .hero p {
            color: rgba(255, 255, 255, 0.95);
            font-size: 1.3rem;
            max-width: 700px;
            margin: 0 auto 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .hero-emoji {
            font-size: 4rem;
            display: inline-block;
            animation: bounce 2s ease-in-out infinite;
            margin: 0 0.5rem;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px) rotate(10deg);
            }

            60% {
                transform: translateY(-5px) rotate(-5deg);
            }
        }

        /* === SEASON INFO === */
        .season-info {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: 20px;
            margin: 2rem auto;
            max-width: 600px;
            position: relative;
            z-index: 2;
        }

        .season-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--color-white);
            margin-bottom: 1rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .divisions {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        /* === STATS SECTION === */
        .stats-section {
            background: var(--color-white);
            padding: 2rem 0.5rem;
            box-shadow: var(--shadow);
            margin: 2rem 0;
            border-radius: var(--border-radius);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .stat-card {
            /* background: var(--gradient-card); */
            background-color: #d7ecfb;
            padding: 2rem;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
            border-color: var(--color-sky-blue);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: var(--color-navy);
            display: block;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: var(--color-text-light);
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* === RECORD SECTION === */
        .record-section {
            background: var(--gradient-card);
            padding: 3rem 0.5rem;
            border-radius: var(--border-radius);
            margin: 2rem 0;
            box-shadow: var(--shadow);
        }

        /* Styles pour les filtres de compétition */
        .competition-filters {
            display: flex;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .comp-filter-btn {
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            border: 2px solid var(--color-sky-blue);
            background: white;
            color: var(--color-navy);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            font-family: var(--font-family);
        }

        .comp-filter-btn:hover {
            background: var(--color-light-blue);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .comp-filter-btn.active {
            background: var(--gradient-primary);
            color: white;
            border-color: var(--color-navy);
            box-shadow: 0 4px 15px rgba(30, 58, 95, 0.3);
        }

        .record-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 600;
            color: var(--color-navy);
            margin-bottom: 2rem;
        }

        .record-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .record-item {
            text-align: center;
            padding: 1.5rem 0;
            border-radius: var(--border-radius);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .record-item.victories {
            background: var(--gradient-success);
            color: white;

        }

        .record-item.draws {
            background: var(--gradient-warning);
            color: white;
        }

        .record-item.defeats {
            background: var(--gradient-danger);
            color: white;
        }

        .record-item:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: var(--shadow-hover);
        }

        .record-number {
            font-size: 2.5rem;
            font-weight: 700;
            display: block;
            margin-bottom: 0.5rem;
        }

        .record-label {
            font-size: 1rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-align: center;
        }

        .results-btn {
            display: block;
            background: var(--gradient-primary);
            color: var(--color-white);
            padding: 1rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            margin: 2rem auto 0;
            width: fit-content;
            box-shadow: var(--shadow);
        }

        .results-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-hover);
        }

        /* === CARDS SECTION === */
        .cards-section {
            padding: 4rem 0;
            position: relative;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 600;
            color: var(--color-navy);
            margin-bottom: 3rem;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: var(--gradient-primary);
            border-radius: 2px;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
            max-width: 1100px;
            margin: 0 auto;
        }

        #latest-results-card {
            padding: 2rem 0.2rem;
        }

        .card {
            background: var(--gradient-card);
            border-radius: var(--border-radius);
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: var(--shadow);
            transition: var(--transition);
            text-decoration: none;
            color: inherit;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: var(--transition);
        }

        .card:hover::before {
            left: 100%;
            transition: left 0.6s ease;
        }

        .card:hover,
        .card:focus {
            transform: translateY(-10px) scale(1.02);
            box-shadow: var(--shadow-hover);
            border-color: var(--color-sky-blue);
            outline: none;
        }

        .card-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 2rem;
            fill: var(--color-sky-blue);
            transition: var(--transition);
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }

        .card:hover .card-icon {
            fill: var(--color-navy);
            transform: scale(1.15) rotate(5deg);
            filter: drop-shadow(0 8px 16px rgba(0, 0, 0, 0.2));
        }

        .card h3 {
            font-size: 1.8rem;
            font-weight: 600;
            color: var(--color-navy);
            margin-bottom: 1rem;
            transition: var(--transition);
        }

        .card:hover h3 {
            color: var(--color-sky-blue);
        }

        .card p {
            color: var(--color-text-light);
            font-size: 1.1rem;
            line-height: 1.7;
            transition: var(--transition);
        }

        .card:hover p {
            color: var(--color-text-dark);
        }

        .card-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: var(--gradient-primary);
            color: var(--color-white);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.9;
            }
        }

        .results-grid {
            display: grid;
            gap: 1rem;
            margin-top: 1rem;
        }

        .result-item {
            display: grid;
            grid-template-columns: auto 1fr auto 1fr;
            align-items: center;
            gap: 8px;
            padding: .55rem 0.4rem;
            border-bottom: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #f2f8fc;
        }

        .compdate {
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.05;
            gap: 2px;
            min-width: 90px;
        }

        .date {
            color: #6b7280;
            font-size: .9rem;
        }

        .result-item.victory {
            border-left-color: var(--color-success);
        }

        .result-item.draw {
            border-left-color: var(--color-warning);
        }

        .result-item.defeat {
            border-left-color: var(--color-danger);
        }

        .result-teams {
            font-weight: 600;
            color: var(--color-navy);
            display: flex;
            flex-direction: column;
            justify-content: center;
            line-height: 1.1;
            gap: 2px;
        }

        .result-score {
            display: flex;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .competition {
            color: rgb(168, 132, 132);
            font-weight: 700;
            margin-right: 0.5rem;
        }

        /* === STYLES SÉPARÉS POUR ÉQUIPES GJPB ET ADVERSAIRES === */
        .team-part {
            display: block;
            font-size: 0.85rem;
        }

        /* Styles pour les équipes GJPB */
        .team-gjpb {
            color: #2f80f2;
            font-weight: 700;
            padding: 0.2rem 0.4rem;
            border-radius: 6px;
            margin: 0.2rem;
        }

        /* Styles pour les équipes adversaires */
        .team-adversaire {
            color: #4d525c;
            font-weight: 400;
            padding: 0.2rem 0.4rem;
            border-radius: 6px;
            margin: 0.2rem;
        }

        /* Pastille "mini card" du score */
        .score-chip {
            display: inline-block;
            padding: .22rem .6rem;
            border-radius: 9999px;
            background: #e5eef9;
            border: 1px solid rgba(0, 0, 0, .06);
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            font-weight: 700;
            min-width: 72px;
            text-align: center;
            line-height: 1.1;
            white-space: nowrap;
        }


        /* === FOOTER === */
        .footer {
            background: var(--color-navy);
            color: var(--color-white);
            text-align: center;
            padding: 3rem 0;
            margin-top: auto;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .footer-content {
            position: relative;
            z-index: 2;
        }

        .footer a {
            color: var(--color-light-blue);
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
        }

        .footer a:hover,
        .footer a:focus {
            color: var(--color-gold);
            background: rgba(255, 215, 0, 0.1);
            transform: translateY(-2px);
            outline: none;
        }

        /* === RESPONSIVE === */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }

            .hero p {
                font-size: 1.1rem;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .stats-grid {
                font-size: 0.5rem;
            }

            .stat-number {
                font-size: 1.5rem;
            }

            .stat-label {
                font-size: 0.65rem;
            }

            .record-title {
                font-size: 1.5rem;
            }

            #res-title {
                font-size: 1.5rem;
            }

            /* Optimisations spécifiques pour les résultats sur mobile */
            .result-item {
                grid-template-columns: auto 1fr 60px 1fr;
                gap: 4px;
                padding: .4rem .5rem;
                font-size: 0.85rem;
            }

            .compdate {
                min-width: 70px;
                gap: 1px;
            }

            .date {
                font-size: 0.7rem;
            }

            .competition {
                font-size: 0.7rem;
            }

            .score-chip {
                font-size: 0.75rem;
                padding: .15rem .4rem;
                min-width: 50px;
            }

            .team-part {
                font-size: 0.75rem;
            }

            .team-gjpb,
            .team-adversaire {
                margin: 0.1rem;
                font-size: 0.75rem;
                padding: 0.1rem 0.3rem;
            }

            .record-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 1rem;
            }

            .record-number {
                font-size: 1.6rem;
            }

            .record-label {
                font-size: 0.9rem;
            }

            .card {
                padding: 2rem 1.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .season-title {
                font-size: 1.5rem;
            }

            .divisions {
                font-size: 1rem;
            }

            .competition-filters {
                gap: 0.5rem;
            }

            .comp-filter-btn {
                padding: 0.6rem 1.2rem;
                font-size: 0.60rem;
            }
        }

        @media (min-width: 769px) {
            .container {
                padding: 0 var(--padding-desktop);
            }

            .header-content {
                padding: 0 var(--padding-desktop);
            }

            .nav-desktop {
                display: flex;
            }

            .login-desktop {
                display: inline-flex;
            }

            .mobile-menu-btn {
                display: none;
            }

            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .record-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* === ANIMATIONS === */
        .card {
            opacity: 0;
            animation: fadeInUp 0.6s ease forwards;
        }

        .card:nth-child(2) {
            animation-delay: 0.2s;
        }

        .card:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* === LOADING STATES === */
        .loading {
            opacity: 0.5;
            pointer-events: none;
        }

        .loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 20px;
            height: 20px;
            border: 2px solid #ccc;
            border-top: 2px solid var(--color-sky-blue);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }

            100% {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }
    </style>
</head>

<body>
    <!-- Header avec navigation -->
    <header class="header">
        <div class="header-content">
            <a href="index.html" class="logo" aria-label="Accueil du club de foot jeunesse">
                <img src="pages/logo-gjpb.png" alt="Logo GJPB">
            </a>

            <nav class="nav-desktop" aria-label="Navigation principale">
                <ul class="nav-links">
                    <li><a href="18A.html" aria-label="Équipe 18 ans A">18A</a></li>
                    <li><a href="17.html" aria-label="Équipe 17 ans">17</a></li>
                    <li><a href="18B.html" aria-label="Équipe 18 ans B">18B</a></li>
                </ul>
            </nav>
            <a href="login.html" class="login-btn login-desktop" aria-label="Se connecter à votre compte">Se
                connecter</a>

            <button class="mobile-menu-btn" aria-label="Ouvrir le menu de navigation" aria-expanded="false"
                aria-controls="mobile-menu">
                <span aria-hidden="true">☰</span>
            </button>
        </div>

        <nav id="mobile-menu" class="mobile-menu" aria-label="Navigation mobile">
            <ul class="mobile-nav-links">
                <li><a href="18A.html" aria-label="Équipe 18 ans A">18A</a></li>
                <li><a href="17.html" aria-label="Équipe 17 ans">17</a></li>
                <li><a href="18B.html" aria-label="Équipe 18 ans B">18B</a></li>
            </ul>
            <a href="login.html" class="login-btn" aria-label="Se connecter à votre compte">Se connecter</a>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-bg" aria-hidden="true"></div>
        <div class="hero-bg-next" aria-hidden="true"></div>
        <div class="container">
            <div class="hero-content">
                <h1>
                    <span class="hero-emoji">⚽</span>
                    DataU18
                    <span class="hero-emoji">⚽</span>
                </h1>
                <p>

                </p>

                <!-- Info saison -->
                <div class="season-info">
                    <div class="season-title">📅 Saison 2025-2026 📅</div>
                    <div class="divisions">
                        <a href="18A.html" style="text-decoration: none; color: #1e3a5f; font-weight: bold;">U18A</a>
                        •
                        <a href="17.html" style="text-decoration: none; color: #1e3a5f; font-weight: bold;">U17</a>
                        •
                        <a href="18B.html" style="text-decoration: none; color: #1e3a5f; font-weight: bold;">U18B</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <main class="container">
        <br>
        <h2 class="section-title">Nos équipes</h2>
        <!-- Section statistiques -->
        <section class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <span class="stat-number" id="stat-teams">0</span>
                    <span class="stat-label">Équipes actives</span>
                </div>
                <div class="stat-card">
                    <span class="stat-number" id="stat-matches">0</span>
                    <span class="stat-label">Matchs joués</span>
                </div>
            </div>
        </section>

        <!-- Section bilan avec filtres -->
        <section class="record-section">
            <!-- Filtres de compétition -->
            <div class="competition-filters">
                <button class="comp-filter-btn" data-filter="tous">Tous les matchs</button>
                <button class="comp-filter-btn" data-filter="championnat">Championnat</button>
                <button class="comp-filter-btn" data-filter="coupe">Coupe</button>
                <button class="comp-filter-btn" data-filter="amical">Amicaux</button>
            </div>

            <h2 class="record-title">Bilan de la saison</h2>
            <div class="record-grid">
                <div class="record-item victories">
                    <span class="record-number" id="victories">0</span>
                    <span class="record-label">Victoires</span>
                </div>
                <div class="record-item draws">
                    <span class="record-number" id="draws">0</span>
                    <span class="record-label">Nuls</span>
                </div>
                <div class="record-item defeats">
                    <span class="record-number" id="defeats">0</span>
                    <span class="record-label">Défaites</span>
                </div>
            </div>
        </section>

        <!-- Card Derniers résultats -->
        <div class="card" id="latest-results-card">
            <div class="card-badge">LIVE 🔥</div>
            <svg class="card-icon" viewBox="0 0 100 100" aria-hidden="true">
                <rect x="15" y="20" width="70" height="60" fill="currentColor" stroke="#333" stroke-width="2" rx="8" />
                <rect x="25" y="30" width="50" height="40" fill="var(--color-navy)" rx="4" />
                <text x="35" y="50" fill="var(--color-gold)" font-size="12" font-weight="bold">3</text>
                <text x="50" y="50" fill="white" font-size="8">-</text>
                <text x="60" y="50" fill="var(--color-gold)" font-size="12" font-weight="bold">1</text>
            </svg>
            <h3 id="res-title">Affichage des derniers résultats</h3>
            <div class="results-grid" id="latest-results">
                <!-- Sera rempli par JavaScript -->
            </div>
            <a href="resultats.html" class="results-btn">📊 Voir tous les résultats</a>
        </div>

        <!-- Section cards -->
        <section class="cards-section">
            <h2 class="section-title">Explorer les données</h2>

            <div class="cards-grid">
                <!-- Card Calendrier -->
                <a href="calendrier.html" class="card" aria-label="Consulter le calendrier des matchs">
                    <div class="card-badge">LIVE 📅</div>
                    <svg class="card-icon" viewBox="0 0 100 100" aria-hidden="true">
                        <rect x="20" y="25" width="60" height="50" fill="currentColor" stroke="#333" stroke-width="2"
                            rx="5" />
                        <rect x="20" y="25" width="60" height="15" fill="var(--color-navy)" rx="5 5 0 0" />
                        <rect x="30" y="15" width="4" height="20" fill="#666" rx="2" />
                        <rect x="66" y="15" width="4" height="20" fill="#666" rx="2" />
                        <line x1="30" y1="47" x2="70" y2="47" stroke="white" stroke-width="1" />
                        <line x1="30" y1="57" x2="70" y2="57" stroke="white" stroke-width="1" />
                        <line x1="30" y1="67" x2="70" y2="67" stroke="white" stroke-width="1" />
                        <line x1="40" y1="40" x2="40" y2="70" stroke="white" stroke-width="1" />
                        <line x1="50" y1="40" x2="50" y2="70" stroke="white" stroke-width="1" />
                        <line x1="60" y1="40" x2="60" y2="70" stroke="white" stroke-width="1" />
                        <circle cx="45" cy="52" r="2" fill="var(--color-gold)" />
                        <circle cx="65" cy="62" r="2" fill="#dc3545" />
                    </svg>
                    <h3>Voir le calendrier des matchs</h3>
                    <p>Consultez tous les matchs à venir, les horaires et les adversaires. Mais aussi les matchs joués
                        et leurs scores !</p>
                </a>

                <!-- Card Statistiques -->
                <a href="stats.html" class="card" aria-label="Consulter les statistiques">
                    <div class="card-badge">HOT 🔥</div>
                    <svg class="card-icon" viewBox="0 0 100 100" aria-hidden="true">
                        <circle cx="50" cy="50" r="45" fill="#dc3545" stroke="#333" stroke-width="2" />
                        <circle cx="50" cy="50" r="35" fill="white" stroke="#333" stroke-width="1.5" />
                        <circle cx="50" cy="50" r="25" fill="#dc3545" stroke="#333" stroke-width="1.5" />
                        <circle cx="50" cy="50" r="15" fill="white" stroke="#333" stroke-width="1.5" />
                        <circle cx="50" cy="50" r="8" fill="var(--color-gold)" stroke="#333" stroke-width="1" />
                    </svg>
                    <h3>Voir les classements individuels</h3>
                    <p>Découvrez les classements des buteurs, passeurs décisifs et joueurs les plus décisifs.</p>
                </a>

                <!-- Card Videos -->
                <a href="videos.html" class="card" aria-label="Voir les videos des matchs">
                    <div class="card-badge">NEW</div>
                    <svg class="card-icon" viewBox="0 0 100 100" aria-hidden="true">
                        <rect x="18" y="30" width="54" height="38" rx="8" fill="currentColor" stroke="#333" stroke-width="2"/>
                        <rect x="24" y="36" width="42" height="26" rx="4" fill="#0f2547"/>
                        <polygon points="72,40 88,48 88,52 72,60" fill="#dc3545" stroke="#333" stroke-width="1.5"/>
                        <circle cx="32" cy="49" r="3" fill="var(--color-gold)"/>
                    </svg>
                    <h3>Voir les videos</h3>
                    <p>Accedez aux videos et resumes des matchs (si disponibles).</p>
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <p>
                    <a href="https://www.gjpb44.com/" aria-label="Visiter le site officiel du club">🌐 Site GJPB</a>
                    | © 2025 - By Clément Bolomey
                </p>
            </div>
        </div>
    </footer>

    <script>
        // === SUPABASE CONFIG ===
        const SUPABASE_URL = 'https://lgnwgbctaqaeawzjnuqh.supabase.co';
        const SUPABASE_ANON_KEY = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImxnbndnYmN0YXFhZWF3empudXFoIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTM4MjYzNjUsImV4cCI6MjA2OTQwMjM2NX0.E0ug5jpRQVh55aq1ewToqol7h7aRPkg7YvwbKpQtVHo';
        const supabase = window.supabase.createClient(SUPABASE_URL, SUPABASE_ANON_KEY, {
            auth: { persistSession: false, autoRefreshToken: false }
        });

        // Variable globale pour stocker le filtre actif
        let activeCompetitionFilter = 'tous';

        // === MENU MOBILE ===
        document.addEventListener('DOMContentLoaded', function () {
            const menuBtn = document.querySelector('.mobile-menu-btn');
            const mobileMenu = document.querySelector('.mobile-menu');

            if (menuBtn && mobileMenu) {
                menuBtn.addEventListener('click', function () {
                    const isOpen = mobileMenu.classList.contains('open');

                    if (isOpen) {
                        mobileMenu.classList.remove('open');
                        menuBtn.setAttribute('aria-expanded', 'false');
                        menuBtn.setAttribute('aria-label', 'Ouvrir le menu de navigation');
                    } else {
                        mobileMenu.classList.add('open');
                        menuBtn.setAttribute('aria-expanded', 'true');
                        menuBtn.setAttribute('aria-label', 'Fermer le menu de navigation');
                    }
                });
            }

            // Fermer le menu mobile au clic sur un lien
            const mobileLinks = document.querySelectorAll('.mobile-nav-links a, .mobile-menu .login-btn');
            mobileLinks.forEach(link => {
                link.addEventListener('click', function () {
                    if (mobileMenu.classList.contains('open')) {
                        mobileMenu.classList.remove('open');
                        menuBtn.setAttribute('aria-expanded', 'false');
                        menuBtn.setAttribute('aria-label', 'Ouvrir le menu de navigation');
                    }
                });
            });

            // Fermer le menu mobile avec Escape
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape' && mobileMenu.classList.contains('open')) {
                    mobileMenu.classList.remove('open');
                    menuBtn.setAttribute('aria-expanded', 'false');
                    menuBtn.setAttribute('aria-label', 'Ouvrir le menu de navigation');
                    menuBtn.focus();
                }
            });

            // Gestion des filtres de compétition
            const filterButtons = document.querySelectorAll('.comp-filter-btn');

            filterButtons.forEach(button => {
                button.addEventListener('click', function () {
                    // Retirer la classe active de tous les boutons
                    filterButtons.forEach(btn => btn.classList.remove('active'));

                    // Ajouter la classe active au bouton cliqué
                    this.classList.add('active');

                    // Mettre à jour le filtre actif
                    activeCompetitionFilter = this.dataset.filter;

                    // Recharger les statistiques avec le nouveau filtre
                    loadRecordStats();
                });
            });

            // Diaporama d'arrière-plan de la hero
            startHeroSlideshow();

            // Charger les données
            loadAllData();
        });

        // Fonction pour déterminer si un match correspond au filtre
        function matchesCompetitionFilter(competition) {
            if (activeCompetitionFilter === 'tous') {
                return true;
            }

            const comp = (competition || '').toLowerCase();

            if (activeCompetitionFilter === 'championnat') {
                // Inclut Phase 1, Phase 2, Phase 3
                return comp.includes('phase');
            }

            if (activeCompetitionFilter === 'coupe') {
                return comp.includes('coupe');
            }

            if (activeCompetitionFilter === 'amical') {
                return comp.includes('amical');
            }

            return false;
        }

        // === ANIMATION DES COMPTEURS ===
        function animateCounter(element, finalValue, duration = 2000) {
            let startValue = 0;
            const increment = finalValue / (duration / 16);

            const counter = setInterval(() => {
                startValue += increment;
                if (startValue >= finalValue) {
                    element.textContent = finalValue;
                    clearInterval(counter);
                } else {
                    element.textContent = Math.floor(startValue);
                }
            }, 16);
        }

        // === CHARGEMENT DES DONNÉES ===
        async function loadAllData() {
            try {
                await Promise.all([
                    loadBasicStats(),
                    loadRecordStats(),
                    loadLatestResults()
                ]);
                startCounterAnimations();
            } catch (error) {
                console.error('Erreur lors du chargement des données:', error);
            }
        }

        // === STATISTIQUES DE BASE ===
        async function loadBasicStats() {
            // Équipes actives
            const { data: teamsData, error: teamsError } = await supabase
                .from('matchs')
                .select('equipe');

            if (!teamsError && teamsData) {
                const uniqueTeams = new Set(teamsData.map(match => match.equipe));
                document.getElementById('stat-teams').textContent = uniqueTeams.size;
            }

            // Matchs joués
            const { count: matchesCount, error: matchesError } = await supabase
                .from('matchs')
                .select('id', { count: 'exact', head: true });

            if (!matchesError) {
                document.getElementById('stat-matches').textContent = matchesCount || 0;
            }
        }

        // === STATISTIQUES DE BILAN ===
        async function loadRecordStats() {
            const { data: matchsData, error } = await supabase
                .from('matchs')
                .select('resultat, competition');

            if (error || !matchsData) {
                console.error('Erreur lors du chargement du bilan:', error);
                return;
            }

            let victories = 0, draws = 0, defeats = 0;

            matchsData.forEach(match => {
                // Vérifier si le match correspond au filtre
                if (!matchesCompetitionFilter(match.competition)) {
                    return;
                }

                switch (match.resultat?.toLowerCase()) {
                    case 'victoire':
                    case 'v':
                        victories++;
                        break;
                    case 'nul':
                    case 'n':
                        draws++;
                        break;
                    case 'defaite':
                    case 'défaite':
                    case 'd':
                        defeats++;
                        break;
                }
            });

            // Animer les compteurs
            animateCounter(document.getElementById('victories'), victories, 1000);
            animateCounter(document.getElementById('draws'), draws, 1000);
            animateCounter(document.getElementById('defeats'), defeats, 1000);
        }

        function formatScoreLine(match) {
            const teamLabel = formatTeamName(`GJPB ${match.equipe}`, true); // true = équipe GJPB
            const adv = formatTeamName(match.adversaire || 'N/A', false); // false = adversaire
            const x = Number(match.buts_gjpb ?? 0);
            const y = Number(match.buts_adv ?? 0);
            const lieu = (match.lieu || '').toLowerCase();
            const dateStr = match.date ? new Date(match.date).toLocaleDateString('fr-FR') : '';
            const competition = match.competition ? `<span class="competition">(${match.competition})</span>` : '';

            const compDate = `<div class="compdate">${competition}<span class="date">${dateStr}</span></div>`;

            if (lieu === 'dom' || lieu === 'domicile') {
                return {
                    left: teamLabel,
                    score: `<span class="score-chip">${x} - ${y}</span>`,
                    right: adv,
                    date: compDate
                };
            }
            return {
                left: adv,
                score: `<span class="score-chip">${y} - ${x}</span>`,
                right: teamLabel,
                date: compDate
            };
        }

        function formatTeamName(teamName, isGjpb = false) {
            const baseClass = isGjpb ? 'team-gjpb' : 'team-adversaire';

            if (teamName.length > 20) {
                // Chercher un espace pour couper intelligemment
                const spaceIndex = teamName.indexOf(' ', 5);
                if (spaceIndex !== -1 && spaceIndex <= 12) {
                    const firstPart = teamName.substring(0, spaceIndex);
                    const secondPart = teamName.substring(spaceIndex + 1);
                    return `
                    <span class="team-part ${baseClass}">${firstPart}</span>
                    <span class="team-part ${baseClass}">${secondPart}</span>`;
                } else {
                    // Si pas d'espace approprié, couper à 10 caractères
                    const firstPart = teamName.substring(0, 10);
                    const secondPart = teamName.substring(10);
                    return `<span class="team-part ${baseClass}">${firstPart}</span><span class="team-part ${baseClass}">${secondPart}</span>`;
                }
            }
            return `<span class="${baseClass}">${teamName}</span>`;
        }

        // === DERNIERS RÉSULTATS ===
        async function loadLatestResults() {
            const { data: matchsData, error } = await supabase
                .from('matchs')
                .select('*')
                .not('resultat', 'is', null)
                .order('date', { ascending: false });

            if (error || !matchsData) {
                console.error('Erreur lors du chargement des résultats:', error);
                return;
            }

            // Grouper par équipe et prendre le dernier match de chaque équipe
            const latestByTeam = {};
            matchsData.forEach(match => {
                if (!latestByTeam[match.equipe]) {
                    latestByTeam[match.equipe] = match;
                }
            });

            const resultsContainer = document.getElementById('latest-results');
            resultsContainer.innerHTML = '';

            Object.values(latestByTeam).forEach(match => {
                const resultClass = getResultClass(match.resultat);
                const view = formatScoreLine(match);

                const resultItem = document.createElement('div');
                resultItem.className = `result-item ${resultClass}`;

                resultItem.innerHTML = `
                    <div class="result-date">${view.date}</div>
                    <div class="result-teams">${view.left}</div>
                    <div class="result-score">${view.score}</div>
                    <div class="result-teams">${view.right}</div>
                `;

                resultsContainer.appendChild(resultItem);
            });
        }

        function getResultClass(resultat) {
            if (!resultat) return '';

            const result = resultat.toLowerCase();
            if (result === 'victoire' || result === 'v') return 'victory';
            if (result === 'nul' || result === 'n') return 'draw';
            if (result === 'defaite' || result === 'défaite' || result === 'd') return 'defeat';
            return '';
        }

        // === ANIMATIONS ===
        function startCounterAnimations() {
            // Animation des compteurs avec un délai
            setTimeout(() => {
                const counters = document.querySelectorAll('.stat-number, .record-number');
                counters.forEach((counter, index) => {
                    const finalValue = parseInt(counter.textContent) || 0;
                    counter.textContent = '0';

                    setTimeout(() => {
                        animateCounter(counter, finalValue, 1500);
                    }, index * 200);
                });
            }, 500);
        }

        // === OBSERVER POUR LES ANIMATIONS AU SCROLL ===
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animationPlayState = 'running';
                }
            });
        }, observerOptions);

        // Observer les éléments animés
        document.addEventListener('DOMContentLoaded', () => {
            const animatedElements = document.querySelectorAll('.card, .stat-card, .record-item');
            animatedElements.forEach(el => {
                observer.observe(el);
            });
        });

        // === GESTION DES ERREURS RÉSEAU ===
        window.addEventListener('online', () => {
            console.log('Connexion rétablie');
            loadAllData();
        });

        window.addEventListener('offline', () => {
            console.log('Connexion perdue');
        });

        // === DIAPORAMA HERO ===
        const HERO_SLIDES = [
            'pages/PHOTO-2025-10-29-18-03-13.jpg',
            'pages/PHOTO-2025-10-29-18-03-14_1.jpg',
            'pages/PHOTO-2025-10-29-18-03-14.jpg',
            'pages/PHOTO-2025-10-29-18-03-15_1.jpg',
            'pages/PHOTO-2025-10-29-18-03-15.jpg',
            'pages/PHOTO-2025-10-29-18-10-52.jpg'
        ];

        // Préchargement des images pour éviter les flashs
        HERO_SLIDES.forEach(src => { const img = new Image(); img.src = src; });

        function startHeroSlideshow() {
            const el1 = document.querySelector('.hero-bg');
            const el2 = document.querySelector('.hero-bg-next');
            if (!el1 || !el2 || HERO_SLIDES.length === 0) return;

            let index = 0;
            let showFirst = true;

            const setBg = (el, src) => { el.style.backgroundImage = `url('${src}')`; };

            // Image initiale
            setBg(el1, HERO_SLIDES[0]);
            el1.style.opacity = '1';

            const swap = () => {
                const next = (index + 1) % HERO_SLIDES.length;
                const showEl = showFirst ? el2 : el1;
                const hideEl = showFirst ? el1 : el2;

                setBg(showEl, HERO_SLIDES[next]);
                showEl.style.opacity = '1';
                hideEl.style.opacity = '0';

                showFirst = !showFirst;
                index = next;
            };

            // Première transition après 1s, puis toutes les 6s
            setTimeout(swap, 1000);
            setInterval(swap, 6000);
        }
    </script>
</body>

</html>

