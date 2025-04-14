<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion du Musée - Version Ultime</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        /* Variables CSS */
        :root {
            --primary-color: #6366f1;
            --secondary-color: #4f46e5;
            --accent-color: #ec4899;
            --background: #0f172a;
            --card-bg: #1e293b;
            --card-hover: #2a3b55;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;

            /* Nouvelles variables */
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --gradient-accent: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --border-radius-sm: 8px;
            --border-radius-md: 12px;
            --border-radius-lg: 16px;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Reset et styles de base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            font-family: 'Poppins', sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
            background-image: 
                radial-gradient(at 80% 0%, hsla(225, 39%, 30%, 0.4) 0px, transparent 50%),
                radial-gradient(at 20% 100%, hsla(256, 39%, 30%, 0.4) 0px, transparent 50%);
            background-attachment: fixed;
        }

        /* Header stylisé */
        .header {
            background: var(--gradient-primary);
            color: white;
            padding: 2.5rem 2rem;
            text-align: center;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(99, 102, 241, 0.2) 0%, rgba(79, 70, 229, 0) 70%);
            z-index: 1;
        }

        .main-title {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: float 6s ease-in-out infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            position: relative;
            z-index: 2;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .icon {
            font-size: 1.4em;
            animation: pulse 3s infinite;
            display: inline-block;
        }

        .subtitle {
            font-size: 1.4rem;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 300;
            position: relative;
            z-index: 2;
        }

        /* Navigation moderne */
        nav {
            background: var(--card-bg);
            padding: 1rem;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 100;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        nav:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 2.5rem;
            max-width: 1200px;
            margin: 0 auto;
            flex-wrap: wrap;
        }

        nav a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 500;
            padding: 0.7rem 1.5rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
        }

        nav a::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: var(--accent-color);
            transition: width 0.3s ease;
        }

        nav a:hover::before {
            width: 100%;
        }

        nav a:hover, nav a.active {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Sections */
        section {
            max-width: 1200px;
            margin: 2.5rem auto;
            padding: 2.5rem;
            background: var(--card-bg);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-lg);
            animation: fadeIn 0.8s ease-out;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        h2 {
            color: var(--text-primary);
            margin-bottom: 1.8rem;
            font-size: 2rem;
            text-align: center;
            position: relative;
            padding-bottom: 0.8rem;
            font-family: 'Playfair Display', serif;
        }

        h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: var(--gradient-primary);
            border-radius: 3px;
        }

        h3 {
            color: var(--text-primary);
            margin-bottom: 1.2rem;
            font-size: 1.6rem;
            font-weight: 600;
        }

        /* Table d'historique */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-md);
            overflow: hidden;
        }

        thead {
            background-color: rgba(99, 102, 241, 0.2);
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        th {
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-size: 0.9rem;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.08);
            transition: background-color 0.2s ease;
        }

        /* Recherche */
        .search-form {
            display: flex;
            margin-bottom: 1.8rem;
            gap: 10px;
        }

        .search-form input {
            flex: 1;
            padding: 12px 16px;
            border-radius: var(--border-radius-sm);
            border: 2px solid rgba(255, 255, 255, 0.1);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .search-form input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
            outline: none;
        }

        .search-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0 20px;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .status {
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 30px;
            display: inline-block;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }

        .status.present {
            background-color: var(--success-color);
            color: white;
        }

        .status.left {
            background-color: var(--warning-color);
            color: white;
        }

        /* Messages */
        .success-message, .error-message {
            padding: 1.2rem;
            border-radius: var(--border-radius-md);
            margin: 1.2rem 0;
            text-align: center;
            animation: fadeIn 0.3s ease-out;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: var(--shadow-md);
        }

        .success-message {
            background-color: var(--success-color);
            color: white;
        }

        .error-message {
            background-color: var(--danger-color);
            color: white;
        }

        .success-message::before {
            content: '✓';
            font-size: 1.2rem;
            font-weight: bold;
        }

        .error-message::before {
            content: '✕';
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Tableau de bord */
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        .card {
            background: var(--card-bg);
            border-radius: var(--border-radius-md);
            padding: 1.8rem;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.05);
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            background: var(--card-hover);
        }

        .card h3 {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.5rem;
            margin-bottom: 1.2rem;
            font-family: 'Playfair Display', serif;
            color: var(--text-primary);
        }

        .visitor-count {
            font-size: 2.5rem;
            font-weight: bold;
            text-align: center;
            margin: 1.2rem 0;
            color: var(--primary-color);
            text-shadow: 0 0 15px rgba(99, 102, 241, 0.4);
        }

        .progress-bar {
            width: 100%;
            height: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 5px;
            overflow: hidden;
            margin-top: 1rem;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.2);
        }

        .progress {
            height: 100%;
            background: var(--gradient-primary);
            transition: width 0.5s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .progress::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.3) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            animation: shimmer 2s infinite;
            background-size: 200% 100%;
        }

        /* Formulaire de gestion */
        .add-visitor-form {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.8rem;
            margin-bottom: 2.5rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 2.5rem;
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .form-group {
            margin-bottom: 1.2rem;
        }

        label {
            color: var(--text-secondary);
            font-weight: 500;
            margin-bottom: 0.6rem;
            display: block;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        input, select {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--border-radius-sm);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary);
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }

        input:focus, select:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }

        button, .submit-btn {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0.9rem 1.5rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.95rem;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        button::after, .submit-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            transform: translateX(-100%);
            transition: transform 0.6s ease;
        }

        button:hover::after, .submit-btn:hover::after {
            transform: translateX(100%);
        }

        button:hover, .submit-btn:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        /* Liste des visiteurs */
        .visitors-list {
            margin-top: 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.8rem;
        }

        .visitor-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: var(--border-radius-md);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-sm);
        }

        .visitor-card:hover {
            background: rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-md);
        }

        .visitor-info {
            margin-bottom: 1.2rem;
        }

        .visitor-info p {
            margin-bottom: 0.6rem;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .visitor-info p strong {
            color: var(--text-primary);
            min-width: 100px;
        }

        /* Bouton de suppression */
        .delete-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            padding: 0.7rem 1rem;
            font-size: 0.9rem;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 10px;
        }

        .delete-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        /* Statistiques */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.8rem;
            margin-bottom: 2.5rem;
        }

        .stats-container .card {
            text-align: center;
            padding: 2rem 1.5rem;
        }

        .stats-container .card h3 {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1.5rem;
            font-size: 1.3rem;
        }

        .stats-container .card p {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--accent-color);
            text-shadow: 0 0 15px rgba(236, 72, 153, 0.4);
        }

        /* Canvas pour les graphiques */
        .chart-container {
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-md);
            padding: 1.8rem;
            margin-top: 2rem;
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .chart-container:hover {
            box-shadow: var(--shadow-lg);
        }

        canvas {
            margin-top: 1rem;
            max-width: 100%;
            height: auto;
        }

        /* Images */
        .card img {
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: var(--border-radius-sm);
            margin-bottom: 1rem;
            box-shadow: var(--shadow-sm);
        }

        /* Bouton pour éditer */
        .edit-button {
            display: inline-block;
            background: var(--gradient-primary);
            color: white;
            padding: 0.8rem 1.2rem;
            border-radius: var(--border-radius-sm);
            text-decoration: none;
            font-weight: 600;
            margin-top: 1rem;
            transition: all 0.3s ease;
            text-align: center;
            box-shadow: var(--shadow-sm);
        }

        .edit-button:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Aucun visiteur */
        .no-visitors {
            text-align: center;
            padding: 2rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-md);
            font-style: italic;
            color: var(--text-secondary);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .header {
                padding: 2rem 1rem;
            }

            .main-title {
                font-size: 2.2rem;
            }

            section {
                padding: 1.5rem;
                margin: 1.5rem 1rem;
            }

            .dashboard-container {
                grid-template-columns: 1fr;
            }

            .add-visitor-form {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }

            nav ul {
                flex-direction: column;
                gap: 0.8rem;
            }

            .visitors-list {
                grid-template-columns: 1fr;
            }

            .stats-container {
                grid-template-columns: 1fr;
            }

            .search-form {
                flex-direction: column;
            }

            .search-btn {
                padding: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .main-title {
                font-size: 1.8rem;
            }

            h2 {
                font-size: 1.6rem;
            }

            .card {
                padding: 1.2rem;
            }

            .visitor-count {
                font-size: 2rem;
            }

            .add-visitor-form {
                padding: 1.2rem;
            }

            button, .submit-btn, input, select {
                padding: 0.8rem;
            }
        }

        /* Animation des cartes */
        .card {
            animation: fadeIn 0.5s ease-out;
        }

        /* Accessibilité */
        button:focus, a:focus {
            outline: 3px solid var(--accent-color);
            outline-offset: 2px;
        }

        /* Select options */
        select option {
            background-color: var(--card-bg);
            color: var(--text-primary);
            padding: 0.5rem;
        }

        /* Animation des messages */
        .success-message, .error-message {
            animation: fadeIn 0.3s ease-out;
        }

        /* Contenu sans visiteurs */
        .no-content {
            text-align: center;
            padding: 3rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: var(--border-radius-md);
            margin: 2rem 0;
        }

        .no-content p {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .pagination a {
            color: var(--text-primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-sm);
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
        }

        .pagination a:hover {
            background: var(--primary-color);
            color: white;
        }

        .pagination .current {
            background: var(--primary-color);
            color: white;
        }

        /* Nouvelles classes spécifiques */
        .capacity-gauge {
            width: 100%;
            height: 30px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .capacity-gauge-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #6366f1);
            border-radius: 15px;
            transition: width 1s ease;
        }

        .capacity-gauge-danger .capacity-gauge-fill {
            background: linear-gradient(90deg, #ef4444, #f59e0b);
        }

        .capacity-gauge-warning .capacity-gauge-fill {
            background: linear-gradient(90deg, #f59e0b, #10b981);
        }

        .gauge-label {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-weight: bold;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
            z-index: 5;
        }

        .visitor-badge {
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--accent-color);
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            box-shadow: var(--shadow-md);
            animation: pulse 2s infinite;
        }

        .highlight-card {
            border: 2px solid var(--accent-color);
            position: relative;
            overflow: hidden;
        }

        .highlight-card::before {
            content: 'En cours';
            position: absolute;
            top: 10px;
            right: -30px;
            background: var(--accent-color);
            color: white;
            padding: 5px 40px;
            transform: rotate(45deg);
            font-size: 0.8rem;
            font-weight: bold;
            z-index: 10;
        }

        .expo-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
            border-radius: var(--border-radius-sm);
            transition: transform 0.5s ease;
        }

        .card:hover .expo-image {
            transform: scale(1.05);
        }

        .expo-date {
            background: rgba(99, 102, 241, 0.2);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-bottom: 10px;
        }

        .pill {
            background: rgba(99, 102, 241, 0.2);
            color: var(--text-primary);
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            display: inline-block;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        /* Animation de notification */
        @keyframes notificationBlink {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.1); }
        }

        .notification-alert {
            display: inline-block;
            background: var(--danger-color);
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            text-align: center;
            line-height: 20px;
            font-size: 0.8rem;
            animation: notificationBlink 2s infinite;
            margin-left: 5px;
        }

        /* Animation de changement */
        @keyframes highlight {
            0% { background-color: rgba(99, 102, 241, 0.3); }
            100% { background-color: transparent; }
        }

        .highlight-effect {
            animation: highlight 2s ease;
        }

        /* Loaders et Spinners */
        .loader {
            width: 30px;
            height: 30px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            border-top: 4px solid var(--primary-color);
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .ripple {
            position: absolute;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.4);
            transform: scale(0);
            animation: ripple 1s linear;
            pointer-events: none;
        }

        @keyframes ripple {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }

        /* Amélioration des boutons */
        .action-button {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 8px 12px;
            border-radius: var(--border-radius-sm);
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .action-button:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.3);
        }

        .action-button i {
            font-size: 0.8rem;
        }

        .floating-action-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .floating-action-button:hover {
            transform: translateY(-5px) rotate(45deg);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
        }

        .floating-action-button i {
            font-size: 1.5rem;
        }

        /* Notification toast */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--card-bg);
            color: var(--text-primary);
            padding: 15px 20px;
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 10px;
            transform: translateX(120%);
            transition: transform 0.5s ease;
            border-left: 4px solid var(--primary-color);
            max-width: 300px;
        }

        .toast.visible {
            transform: translateX(0);
        }

        .toast.success {
            border-left-color: var(--success-color);
        }

        .toast.error {
            border-left-color: var(--danger-color);
        }

        .toast.info {
            border-left-color: var(--primary-color);
        }

        .toast i {
            font-size: 1.2rem;
        }

        .toast .close {
            cursor: pointer;
            margin-left: auto;
            opacity: 0.7;
        }

        .toast .close:hover {
            opacity: 1;
        }

        /* Animation du badge de capacité */
        @keyframes pulsate {
            0% { box-shadow: 0 0 0 0 rgba(236, 72, 153, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(236, 72, 153, 0); }
            100% { box-shadow: 0 0 0 0 rgba(236, 72, 153, 0); }
        }

        .capacity-badge {
            background: var(--accent-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            display: inline-block;
            margin-left: 10px;
            animation: pulsate 2s infinite;
        }

        /* Animation d'onglet actif */
        .tab-button {
            padding: 10px 20px;
            background: transparent;
            border: none;
            border-bottom: 2px solid transparent;
            color: var(--text-secondary);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            color: var(--text-primary);
        }

        .tab-button.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .tab-content {
            display: none;
            padding: 20px 0;
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        /* Styles d'infobulles */
        .tooltip {
            position: relative;
            display: inline-block;
        }

        .tooltip:hover .tooltip-text {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .tooltip-text {
            visibility: hidden;
            width: 200px;
            background: var(--card-bg);
            color: var(--text-primary);
            text-align: center;
            border-radius: var(--border-radius-sm);
            padding: 10px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            margin-left: -100px;
            opacity: 0;
            transition: opacity 0.3s, transform 0.3s;
            transform: translateY(10px);
            box-shadow: var(--shadow-md);
            border: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.85rem;
        }

        .tooltip-text::after {
            content: "";
            position: absolute;
            top: 100%;
            left: 50%;
            margin-left: -5px;
            border-width: 5px;
            border-style: solid;
            border-color: var(--card-bg) transparent transparent transparent;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1 class="main-title">
            <span class="icon">🏛️</span> Système de Gestion du Musée
        </h1>
        <p class="subtitle">Une expérience culturelle unique</p>
    </header>

    <nav>
        <ul>
            <li><a href="#dashboard" onclick="scrollToSection('dashboard')"><i class="fas fa-chart-pie"></i> Tableau de bord</a></li>
            <li><a href="#visitors" onclick="scrollToSection('visitors')"><i class="fas fa-users"></i> Visiteurs</a></li>
            <li><a href="#statistics" onclick="scrollToSection('statistics')"><i class="fas fa-chart-bar"></i> Statistiques</a></li>
            <li><a href="#historique" onclick="scrollToSection('historique')"><i class="fas fa-history"></i> Historique</a></li>
        </ul>
    </nav>

    <div id="message-container"></div>

    <section id="dashboard">
        <h2>🎨 Tableau de bord</h2>
        
        <div class="capacity-gauge" id="capacity-gauge">
            <div class="capacity-gauge-fill" id="capacity-gauge-fill" style="width: 0%"></div>
            <span class="gauge-label">0/50 visiteurs</span>
        </div>
        
        <div class="dashboard-container">
            <div class="card">
                <h3><i class="fas fa-users"></i> Visiteurs actuels</h3>
                <p class="visitor-count" id="total-visitors">0 / 50</p>
                <div class="progress-bar">
                    <div class="progress" id="visitor-progress" style="width: 0%;"></div>
                </div>
                </br>
                <h3><i class="fas fa-clipboard-list"></i> Résumé des activités</h3>
                <p><i class="far fa-calendar-alt"></i> <strong>Aujourd'hui :</strong> <span id="current-date"></span></p>
                <p><i class="fas fa-users"></i> <strong>Visiteurs actuels :</strong> <span id="current-visitors">0</span> / 50</p>
                <p><i class="fas fa-door-open"></i> <strong>Visites terminées aujourd'hui :</strong> <span id="completed-visits">0</span></p>
            </div>
            
            <div class="card">
                <div class="tooltip" style="position: absolute; top: 15px; right: 15px;">
                    <i class="fas fa-info-circle" style="color: rgba(255,255,255,0.5);"></i>
                    <span class="tooltip-text">Exposition permanente du musée présentant des œuvres emblématiques du XXe siècle.</span>
                </div>
                <h3><i class="fas fa-paint-brush"></i> Exposition Permanente</h3>
                <div style="overflow: hidden; border-radius: var(--border-radius-sm);">
                    <img src="images/musee 20e siecle.jpg" 
                         class="expo-image" alt="Exposition d'Art Moderne">
                </div>
                <div class="expo-date">Depuis Jan 2025 - Permanente</div>
                <p>
                    Découvrez des œuvres emblématiques du XXe siècle, incluant des artistes comme Picasso et Matisse. 
                    Cette exposition offre un aperçu fascinant de l'évolution de l'art moderne.
                </p>
                <div style="margin-top: 10px;">
                    <span class="pill"><i class="fas fa-palette"></i> Art moderne</span>
                    <span class="pill"><i class="fas fa-star"></i> Collection permanente</span>
                    <span class="pill"><i class="fas fa-users"></i> <span id="permanent-visitors">0</span> visiteurs</span>
                </div>
            </div>

            <div class="card highlight-card">
                <div class="tooltip" style="position: absolute; top: 15px; right: 15px;">
                    <i class="fas fa-info-circle" style="color: rgba(255,255,255,0.5);"></i>
                    <span class="tooltip-text">Exposition temporaire actuelle présentant une installation immersive.</span>
                </div>
                <h3><i class="fas fa-clock"></i> Exposition Temporaire</h3>
                <div style="overflow: hidden; border-radius: var(--border-radius-sm);">
                    <img src="images/musee temps.jpg" 
                         class="expo-image" alt="Exposition Echos du Temps">
                </div>
                <div class="expo-date" id="temp-expo-dates">Mai 2025 - Août 2023</div>
                <p>
                    "Échos du Temps" est une installation immersive qui explore la notion de passage du temps 
                    à travers des éléments en constante évolution. Les visiteurs sont invités à interagir avec l'œuvre, 
                    créant une expérience unique à chaque visite.
                </p>
                <div style="margin-top: 10px;">
                    <span class="pill"><i class="fas fa-lightbulb"></i> Art contemporain</span>
                    <span class="pill"><i class="fas fa-sync-alt"></i> Installation interactive</span>
                    <span class="pill"><i class="fas fa-users"></i> <span id="temporary-visitors">0</span> visiteurs</span>
                </div>
                <a href="#edit_exposition.php" class="edit-button" onclick="showToast('Modification de l\'exposition temporaire')">
                    <i class="fas fa-edit"></i> Modifier l'Exposition
                </a>
            </div>
        </div>
    </section>

    <section id="visitors">
        <h2>👥 Gestion des Visiteurs</h2>
        <form id="add-visitor-form" class="add-visitor-form">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" name="nom" id="nom" required>
            </div>
            
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" name="prenom" id="prenom" required>
            </div>
            
            <div class="form-group">
                <label for="age">Âge :</label>
                <input type="number" name="age" id="age" required min="0" max="120">
            </div>
            
            <div class="form-group">
                <label for="mail">Email :</label>
                <input type="email" name="mail" id="mail" required>
            </div>
            
            <div class="form-group">
                <label for="tel">Téléphone :</label>
                <input type="tel" name="tel" id="tel">
            </div>
            
            <div class="form-group">
                <label for="exposition">Exposition :</label>
                <select name="exposition" id="exposition" required>
                    <option value="1">Exposition Permanente</option>
                    <option value="2">Exposition Temporaire</option>
                    <option value="3">Les deux</option>
                </select>
            </div>
            
            <button type="submit" name="add_visitor" class="submit-btn">
                <i class="fas fa-user-plus"></i> Ajouter un visiteur
            </button>
        </form>

        <h3>Liste des visiteurs actuels</h3>
        <div class="visitors-list" id="visitors-list">
            <!-- Les visiteurs seront ajoutés dynamiquement ici -->
        </div>

        <div class="search-container">
            <h3>Rechercher un visiteur</h3>
            <form id="search-form" class="search-form">
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="text" name="search" id="search-input" placeholder="Nom, prénom ou email">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                </div>
            </form>
            
            <div id="search-results" style="display: none;">
                <h3>Résultats de recherche</h3>
                <div class="visitors-list" id="search-results-list">
                    <!-- Les résultats de recherche seront ajoutés ici -->
                </div>
            </div>
        </div>
    </section>

    <section id="statistics">
        <h2>📊 Statistiques</h2>
        <div class="stats-container">
            <div class="card">
                <h3>Exposition Permanente</h3>
                <p id="permanent-percentage">0%</p>
            </div>
            <div class="card">
                <h3>Exposition Temporaire</h3>
                <p id="temporary-percentage">0%</p>
            </div>
            <div class="card">
                <h3>Les deux expositions</h3>
                <p id="both-percentage">0%</p>
            </div>
            <div class="card">
                <h3>Taux d'occupation</h3>
                <p id="occupation-rate">0%</p>
            </div>
        </div>

        <div class="chart-container">
            <h3>Fréquentation hebdomadaire</h3>
            <canvas id="weeklyChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3>Distribution par âge</h3>
            <canvas id="ageChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3>Durée moyenne des visites</h3>
            <canvas id="durationChart"></canvas>
        </div>
        
        <div class="chart-container">
            <h3>Heures d'affluence</h3>
            <canvas id="hourlyChart"></canvas>
        </div>
    </section>

    <section id="historique">
        <h2>📜 Historique des visites</h2>
        
        <div class="tab-buttons" style="display: flex; justify-content: center; margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1);">
            <button class="tab-button active" data-tab="today">Aujourd'hui</button>
            <button class="tab-button" data-tab="week">Cette semaine</button>
            <button class="tab-button" data-tab="month">Ce mois</button>
            <button class="tab-button" data-tab="all">Tout l'historique</button>
        </div>
        
        <div class="tab-content active" id="today-tab">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Exposition</th>
                        <th>Arrivée</th>
                        <th>Départ</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody id="today-history">
                    <!-- Les données seront ajoutées dynamiquement ici -->
                </tbody>
            </table>
        </div>
        
        <div class="tab-content" id="week-tab">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Exposition</th>
                        <th>Date</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody id="week-history">
                    <!-- Les données seront ajoutées dynamiquement ici -->
                </tbody>
            </table>
        </div>
        
        <div class="tab-content" id="month-tab">
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Exposition</th>
                        <th>Date</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody id="month-history">
                    <!-- Les données seront ajoutées dynamiquement ici -->
                </tbody>
            </table>
        </div>
        
        <div class="tab-content" id="all-tab">
            <div class="search-form" style="margin-bottom: 20px;">
                <input type="text" id="history-search" placeholder="Rechercher dans l'historique...">
                <button class="search-btn"><i class="fas fa-search"></i></button>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Exposition</th>
                        <th>Date</th>
                        <th>Durée</th>
                    </tr>
                </thead>
                <tbody id="all-history">
                    <!-- Les données seront ajoutées dynamiquement ici -->
                </tbody>
            </table>
        </div>
    </section>

    <div class="toast" id="toast">
        <i class="fas fa-info-circle"></i>
        <span id="toast-message"></span>
        <span class="close" onclick="hideToast()">×</span>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Données de simulation
        const museumData = {
            maxCapacity: 50,
            currentVisitors: 0,
            completedVisits: 0,
            visitors: [],
            history: [],
            expositions: {
                permanent: {
                    id: 1,
                    title: "Exposition Permanente",
                    visitors: 0,
                    percentage: 0
                },
                temporary: {
                    id: 2,
                    title: "Exposition Temporaire",
                    visitors: 0,
                    percentage: 0
                },
                both: {
                    visitors: 0,
                    percentage: 0
                }
            }
        };

        // Génération de données aléatoires initiales
        function generateInitialData() {
            // Date actuelle
            const currentDate = new Date();
            document.getElementById('current-date').textContent = currentDate.toLocaleDateString('fr-FR', { 
                day: '2-digit', month: '2-digit', year: 'numeric' 
            });
            
            // Dates pour l'exposition temporaire
            const startDate = new Date();
            startDate.setMonth(startDate.getMonth() - 1);
            const endDate = new Date();
            endDate.setMonth(endDate.getMonth() + 2);
            document.getElementById('temp-expo-dates').textContent = 
                `${startDate.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })} - ${endDate.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' })}`;
            
            // Génération de visiteurs aléatoires
            const numVisitors = Math.floor(Math.random() * 15) + 5; // 5 à 20 visiteurs
            
            for (let i = 0; i < numVisitors; i++) {
                addRandomVisitor();
            }
            
            // Génération d'historique
            generateHistoryData();
            
            // Mise à jour des affichages
            updateDisplays();
        }

        // Ajout d'un visiteur aléatoire
        function addRandomVisitor() {
            const firstName = firstNames[Math.floor(Math.random() * firstNames.length)];
            const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
            const age = Math.floor(Math.random() * 70) + 10; // 10 à 80 ans
            const email = `${firstName.toLowerCase()}.${lastName.toLowerCase()}@email.com`;
            const phone = `0${Math.floor(Math.random() * 9) + 1}${Math.random().toString().slice(2, 10)}`;
            const exposition = Math.floor(Math.random() * 3) + 1; // 1, 2 ou 3
            
            // Heure d'arrivée aléatoire aujourd'hui
            const arrivalTime = new Date();
            arrivalTime.setHours(9 + Math.floor(Math.random() * 8)); // Entre 9h et 17h
            arrivalTime.setMinutes(Math.floor(Math.random() * 60));
            
            const visitor = {
                id: Date.now() + Math.floor(Math.random() * 1000),
                firstName,
                lastName,
                age,
                email,
                phone,
                exposition,
                arrivalTime,
                departureTime: null
            };
            
            museumData.visitors.push(visitor);
            museumData.currentVisitors++;
            
            // Mise à jour des compteurs d'exposition
            if (exposition === 1) {
                museumData.expositions.permanent.visitors++;
            } else if (exposition === 2) {
                museumData.expositions.temporary.visitors++;
            } else {
                museumData.expositions.both.visitors++;
                museumData.expositions.permanent.visitors++;
                museumData.expositions.temporary.visitors++;
            }
            
            // Calcul des pourcentages
            calculateExpositionPercentages();
            
            return visitor;
        }

        // Calcul des pourcentages d'exposition
        function calculateExpositionPercentages() {
            const totalVisitors = museumData.currentVisitors;
            
            if (totalVisitors > 0) {
                // Visiteurs uniques par expo
                const permanentOnly = museumData.expositions.permanent.visitors - museumData.expositions.both.visitors;
                const temporaryOnly = museumData.expositions.temporary.visitors - museumData.expositions.both.visitors;
                const both = museumData.expositions.both.visitors;
                
                museumData.expositions.permanent.percentage = Math.round((permanentOnly / totalVisitors) * 100);
                museumData.expositions.temporary.percentage = Math.round((temporaryOnly / totalVisitors) * 100);
                museumData.expositions.both.percentage = Math.round((both / totalVisitors) * 100);
            } else {
                museumData.expositions.permanent.percentage = 0;
                museumData.expositions.temporary.percentage = 0;
                museumData.expositions.both.percentage = 0;
            }
        }

        // Génération de données d'historique
        function generateHistoryData() {
            // Créer un historique sur les 30 derniers jours
            const today = new Date();
            
            for (let i = 0; i < 100; i++) {
                const randomDate = new Date();
                randomDate.setDate(today.getDate() - Math.floor(Math.random() * 30));
                
                // Heure d'arrivée aléatoire
                randomDate.setHours(9 + Math.floor(Math.random() * 8)); // Entre 9h et 17h
                randomDate.setMinutes(Math.floor(Math.random() * 60));
                
                // Heure de départ (1 à 3 heures plus tard)
                const departureTime = new Date(randomDate);
                departureTime.setMinutes(departureTime.getMinutes() + 30 + Math.floor(Math.random() * 150));
                
                const firstName = firstNames[Math.floor(Math.random() * firstNames.length)];
                const lastName = lastNames[Math.floor(Math.random() * lastNames.length)];
                const age = Math.floor(Math.random() * 70) + 10;
                const email = `${firstName.toLowerCase()}.${lastName.toLowerCase()}@email.com`;
                const exposition = Math.floor(Math.random() * 3) + 1;
                
                const visitRecord = {
                    id: Date.now() + Math.floor(Math.random() * 1000) + i,
                    firstName,
                    lastName,
                    age,
                    email,
                    exposition,
                    arrivalTime: randomDate,
                    departureTime: departureTime,
                    duration: Math.floor((departureTime - randomDate) / (1000 * 60)) // en minutes
                };
                
                museumData.history.push(visitRecord);
                
                // Si c'est aujourd'hui, compter comme visite terminée
                if (randomDate.toDateString() === today.toDateString()) {
                    museumData.completedVisits++;
                }
            }
        }

        // Mise à jour des affichages
        function updateDisplays() {
            // Compteurs
            document.getElementById('total-visitors').textContent = `${museumData.currentVisitors} / ${museumData.maxCapacity}`;
            document.getElementById('current-visitors').textContent = museumData.currentVisitors;
            document.getElementById('completed-visits').textContent = museumData.completedVisits;
            
            // Barres de progression
            const visitorPercentage = (museumData.currentVisitors / museumData.maxCapacity) * 100;
            document.getElementById('visitor-progress').style.width = `${visitorPercentage}%`;
            
            // Jauge de capacité
            const capacityGauge = document.getElementById('capacity-gauge');
            const capacityGaugeFill = document.getElementById('capacity-gauge-fill');
            const gaugeLabel = capacityGauge.querySelector('.gauge-label');
            
            capacityGaugeFill.style.width = `${visitorPercentage}%`;
            gaugeLabel.textContent = `${museumData.currentVisitors}/${museumData.maxCapacity} visiteurs`;
            
            // Coloration de la jauge en fonction du remplissage
            capacityGauge.classList.remove('capacity-gauge-warning', 'capacity-gauge-danger');
            if (visitorPercentage >= 80) {
                capacityGauge.classList.add('capacity-gauge-danger');
            } else if (visitorPercentage >= 50) {
                capacityGauge.classList.add('capacity-gauge-warning');
            }
            
            // Compteurs d'exposition
            document.getElementById('permanent-visitors').textContent = museumData.expositions.permanent.visitors;
            document.getElementById('temporary-visitors').textContent = museumData.expositions.temporary.visitors;
            
            // Pourcentages dans les statistiques
            document.getElementById('permanent-percentage').textContent = `${museumData.expositions.permanent.percentage}%`;
            document.getElementById('temporary-percentage').textContent = `${museumData.expositions.temporary.percentage}%`;
            document.getElementById('both-percentage').textContent = `${museumData.expositions.both.percentage}%`;
            document.getElementById('occupation-rate').textContent = `${Math.round(visitorPercentage)}%`;
            
            // Liste des visiteurs
            renderVisitorsList();
            
            // Historique
            renderHistory();
            
            // Graphiques
            renderCharts();
        }

        // Rendu de la liste des visiteurs
        function renderVisitorsList() {
            const visitorsListElement = document.getElementById('visitors-list');
            visitorsListElement.innerHTML = '';
            
            if (museumData.visitors.length === 0) {
                visitorsListElement.innerHTML = '<p class="no-visitors">Aucun visiteur actuellement dans le musée.</p>';
                return;
            }
            
            // Trier les visiteurs par heure d'arrivée (plus récent en premier)
            const sortedVisitors = [...museumData.visitors]
                .filter(visitor => visitor.departureTime === null)
                .sort((a, b) => b.arrivalTime - a.arrivalTime);
            
            sortedVisitors.forEach(visitor => {
                const card = document.createElement('div');
                card.className = 'visitor-card';
                card.dataset.id = visitor.id;
                
                const expoName = visitor.exposition === 1 ? 'Exposition Permanente' : 
                                 visitor.exposition === 2 ? 'Exposition Temporaire' : 'Les deux expositions';
                
                card.innerHTML = `
                    <p><strong>Nom :</strong> ${visitor.lastName} ${visitor.firstName}</p>
                    <p><strong>Âge :</strong> ${visitor.age} ans</p>
                    <p><strong>Email :</strong> ${visitor.email}</p>
                    <p><strong>Téléphone :</strong> ${visitor.phone}</p>
                    <p><strong>Exposition :</strong> ${expoName}</p>
                    <p><strong>Heure d'arrivée :</strong> ${formatTime(visitor.arrivalTime)}</p>
                    <button class="delete-btn" onclick="registerDeparture(${visitor.id})">
                        <i class="fas fa-door-open"></i> Enregistrer le départ
                    </button>
                `;
                
                visitorsListElement.appendChild(card);
            });
        }

        // Enregistrement du départ d'un visiteur
        function registerDeparture(visitorId) {
            const visitorIndex = museumData.visitors.findIndex(v => v.id === visitorId);
            
            if (visitorIndex !== -1) {
                const visitor = museumData.visitors[visitorIndex];
                visitor.departureTime = new Date();
                
                // Calculer la durée de la visite en minutes
                visitor.duration = Math.floor((visitor.departureTime - visitor.arrivalTime) / (1000 * 60));
                
                // Ajouter à l'historique
                museumData.history.push({...visitor});
                
                // Décrémenter les compteurs
                museumData.currentVisitors--;
                
                if (visitor.exposition === 1) {
                    museumData.expositions.permanent.visitors--;
                } else if (visitor.exposition === 2) {
                    museumData.expositions.temporary.visitors--;
                } else {
                    museumData.expositions.both.visitors--;
                    museumData.expositions.permanent.visitors--;
                    museumData.expositions.temporary.visitors--;
                }
                
                // Incrémenter le compteur de visites terminées
                museumData.completedVisits++;
                
                // Recalculer les pourcentages
                calculateExpositionPercentages();
                
                // Mettre à jour l'affichage
                updateDisplays();
                
                // Afficher un message
                showToast(`Départ de ${visitor.firstName} ${visitor.lastName} enregistré.`, 'success');
            }
        }

        // Formatage de l'heure
        function formatTime(date) {
            return date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        }

        // Formatage de la date
        function formatDate(date) {
            return date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        // Rendu de l'historique
        function renderHistory() {
            const todayHistory = document.getElementById('today-history');
            const weekHistory = document.getElementById('week-history');
            const monthHistory = document.getElementById('month-history');
            const allHistory = document.getElementById('all-history');
            
            todayHistory.innerHTML = '';
            weekHistory.innerHTML = '';
            monthHistory.innerHTML = '';
            allHistory.innerHTML = '';
            
            const today = new Date();
            const todayStart = new Date(today.getFullYear(), today.getMonth(), today.getDate());
            
            const weekStart = new Date(todayStart);
            weekStart.setDate(weekStart.getDate() - 7);
            
            const monthStart = new Date(todayStart);
            monthStart.setMonth(monthStart.getMonth() - 1);
            
            // Trier l'historique par date d'arrivée (plus récent en premier)
            const sortedHistory = [...museumData.history].sort((a, b) => b.arrivalTime - a.arrivalTime);
            
            sortedHistory.forEach(record => {
                const expoName = record.exposition === 1 ? 'Exposition Permanente' : 
                                record.exposition === 2 ? 'Exposition Temporaire' : 'Les deux expositions';
                
                // Pour l'onglet "Aujourd'hui"
                if (record.arrivalTime >= todayStart) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.lastName} ${record.firstName}</td>
                        <td>${expoName}</td>
                        <td>${formatTime(record.arrivalTime)}</td>
                        <td>${record.departureTime ? formatTime(record.departureTime) : 'En cours'}</td>
                        <td>${record.duration ? record.duration + ' min' : 'En cours'}</td>
                    `;
                    todayHistory.appendChild(row);
                }
                
                // Pour l'onglet "Cette semaine"
                if (record.arrivalTime >= weekStart) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.lastName} ${record.firstName}</td>
                        <td>${expoName}</td>
                        <td>${formatDate(record.arrivalTime)}</td>
                        <td>${record.duration ? record.duration + ' min' : 'En cours'}</td>
                    `;
                    weekHistory.appendChild(row);
                }
                
                // Pour l'onglet "Ce mois"
                if (record.arrivalTime >= monthStart) {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${record.lastName} ${record.firstName}</td>
                        <td>${expoName}</td>
                        <td>${formatDate(record.arrivalTime)}</td>
                        <td>${record.duration ? record.duration + ' min' : 'En cours'}</td>
                    `;
                    monthHistory.appendChild(row);
                }
                
                // Pour l'onglet "Tout l'historique"
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${record.lastName} ${record.firstName}</td>
                    <td>${expoName}</td>
                    <td>${formatDate(record.arrivalTime)}</td>
                    <td>${record.duration ? record.duration + ' min' : 'En cours'}</td>
                `;
                allHistory.appendChild(row);
            });
            
            // Afficher un message si aucune donnée n'est disponible
            if (todayHistory.children.length === 0) {
                todayHistory.innerHTML = '<tr><td colspan="5" class="text-center">Aucune visite enregistrée aujourd\'hui</td></tr>';
            }
            if (weekHistory.children.length === 0) {
                weekHistory.innerHTML = '<tr><td colspan="4" class="text-center">Aucune visite enregistrée cette semaine</td></tr>';
            }
            if (monthHistory.children.length === 0) {
                monthHistory.innerHTML = '<tr><td colspan="4" class="text-center">Aucune visite enregistrée ce mois</td></tr>';
            }
            if (allHistory.children.length === 0) {
                allHistory.innerHTML = '<tr><td colspan="4" class="text-center">Aucune visite enregistrée</td></tr>';
            }
        }

        // Rendu des graphiques
        function renderCharts() {
            // Graphique de fréquentation hebdomadaire
            renderWeeklyChart();
            
            // Graphique de distribution par âge
            renderAgeChart();
            
            // Graphique de durée moyenne des visites
            renderDurationChart();
            
            // Graphique d'heures d'affluence
            renderHourlyChart();
        }

        // Graphique de fréquentation hebdomadaire
        function renderWeeklyChart() {
            const ctx = document.getElementById('weeklyChart').getContext('2d');
            
            // Calcul des données pour chaque jour de la semaine
            const dayLabels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
            const dayData = [0, 0, 0, 0, 0, 0, 0];
            
            // Récupérer les 7 derniers jours
            const today = new Date();
            const weekStart = new Date(today);
            weekStart.setDate(weekStart.getDate() - 7);
            
            // Compter les visites par jour
            museumData.history.forEach(record => {
                if (record.arrivalTime >= weekStart) {
                    // Obtenir le jour de la semaine (0 = dimanche, 1 = lundi, etc.)
                    let dayIndex = record.arrivalTime.getDay() - 1;
                    if (dayIndex < 0) dayIndex = 6; // Transformer dimanche (0) en 6
                    
                    dayData[dayIndex]++;
                }
            });
            
            // Création ou mise à jour du graphique
            if (window.weeklyChart) {
                window.weeklyChart.data.datasets[0].data = dayData;
                window.weeklyChart.update();
            } else {
                window.weeklyChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: dayLabels,
                        datasets: [{
                            label: 'Visiteurs',
                            data: dayData,
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.2)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#f1f5f9'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }
        }

        // Graphique de distribution par âge
        function renderAgeChart() {
            const ctx = document.getElementById('ageChart').getContext('2d');
            
            // Catégories d'âge
            const ageLabels = ['<18', '18-25', '26-40', '41-60', '>60'];
            const ageData = [0, 0, 0, 0, 0];
            
            // Compter les visiteurs par tranche d'âge
            const allVisitorsData = [...museumData.visitors, ...museumData.history];
            
            allVisitorsData.forEach(visitor => {
                const age = visitor.age;
                
                if (age < 18) {
                    ageData[0]++;
                } else if (age <= 25) {
                    ageData[1]++;
                } else if (age <= 40) {
                    ageData[2]++;
                } else if (age <= 60) {
                    ageData[3]++;
                } else {
                    ageData[4]++;
                }
            });
            
            // Création ou mise à jour du graphique
            if (window.ageChart) {
                window.ageChart.data.datasets[0].data = ageData;
                window.ageChart.update();
            } else {
                window.ageChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ageLabels,
                        datasets: [{
                            label: 'Nombre de visiteurs',
                            data: ageData,
                            backgroundColor: [
                                'rgba(99, 102, 241, 0.7)',
                                'rgba(79, 70, 229, 0.7)',
                                'rgba(67, 56, 202, 0.7)',
                                'rgba(55, 48, 163, 0.7)',
                                'rgba(49, 46, 129, 0.7)'
                            ],
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#f1f5f9'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }
        }

        // Graphique de durée moyenne des visites
        function renderDurationChart() {
            const ctx = document.getElementById('durationChart').getContext('2d');
            
            // Initialiser les données
            const durationLabels = ['Expo Permanente', 'Expo Temporaire', 'Les deux'];
            const durationData = [0, 0, 0];
            const durationCounts = [0, 0, 0];
            
            // Calculer la durée moyenne pour chaque type d'exposition
            museumData.history.forEach(record => {
                if (record.duration) {
                    if (record.exposition === 1) {
                        durationData[0] += record.duration;
                        durationCounts[0]++;
                    } else if (record.exposition === 2) {
                        durationData[1] += record.duration;
                        durationCounts[1]++;
                    } else {
                        durationData[2] += record.duration;
                        durationCounts[2]++;
                    }
                }
            });
            
            // Calculer les moyennes
            for (let i = 0; i < 3; i++) {
                if (durationCounts[i] > 0) {
                    durationData[i] = Math.round(durationData[i] / durationCounts[i]);
                } else {
                    // Valeurs par défaut si aucune donnée
                    durationData[i] = i === 0 ? 45 : i === 1 ? 35 : 90;
                }
            }
            
            // Création ou mise à jour du graphique
            if (window.durationChart) {
                window.durationChart.data.datasets[0].data = durationData;
                window.durationChart.update();
            } else {
                window.durationChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: durationLabels,
                        datasets: [{
                            label: 'Durée moyenne (minutes)',
                            data: durationData,
                            backgroundColor: [
                                'rgba(99, 102, 241, 0.7)',
                                'rgba(16, 185, 129, 0.7)',
                                'rgba(239, 68, 68, 0.7)'
                            ],
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#f1f5f9'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }
        }

        // Graphique d'heures d'affluence
        function renderHourlyChart() {
            const ctx = document.getElementById('hourlyChart').getContext('2d');
            
            // Heures d'ouverture (9h à 18h)
            const hourLabels = ['9h', '10h', '11h', '12h', '13h', '14h', '15h', '16h', '17h', '18h'];
            const hourData = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
            
            // Compter les visites par heure d'arrivée
            const allVisitorsData = [...museumData.visitors, ...museumData.history];
            
            allVisitorsData.forEach(visitor => {
                const hour = visitor.arrivalTime.getHours();
                if (hour >= 9 && hour <= 18) {
                    hourData[hour - 9]++;
                }
            });
            
            // Création ou mise à jour du graphique
            if (window.hourlyChart) {
                window.hourlyChart.data.datasets[0].data = hourData;
                window.hourlyChart.update();
            } else {
                window.hourlyChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: hourLabels,
                        datasets: [{
                            label: 'Nombre de visiteurs',
                            data: hourData,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.2)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                labels: {
                                    color: '#f1f5f9'
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    color: 'rgba(255, 255, 255, 0.1)'
                                },
                                ticks: {
                                    color: '#f1f5f9'
                                }
                            }
                        }
                    }
                });
            }
        }

        // Ajout d'un visiteur via le formulaire
        document.getElementById('add-visitor-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Vérifier si le musée n'est pas plein
            if (museumData.currentVisitors >= museumData.maxCapacity) {
                showToast('Le musée est à capacité maximale. Impossible d\'ajouter un nouveau visiteur.', 'error');
                return;
            }
            
            // Récupérer les valeurs du formulaire
            const lastName = document.getElementById('nom').value;
            const firstName = document.getElementById('prenom').value;
            const age = parseInt(document.getElementById('age').value);
            const email = document.getElementById('mail').value;
            const phone = document.getElementById('tel').value;
            const exposition = parseInt(document.getElementById('exposition').value);
            
            // Valider les données
            if (!lastName || !firstName || isNaN(age) || !email || !exposition) {
                showToast('Veuillez remplir tous les champs obligatoires.', 'error');
                return;
            }
            
            // Vérifier si l'email existe déjà parmi les visiteurs actuels
            const existingVisitor = museumData.visitors.find(v => v.email === email && v.departureTime === null);
            
            if (existingVisitor) {
                showToast('Un visiteur avec cet email est déjà présent dans le musée.', 'error');
                return;
            }
            
            // Créer le nouveau visiteur
            const visitor = {
                id: Date.now(),
                firstName,
                lastName,
                age,
                email,
                phone,
                exposition,
                arrivalTime: new Date(),
                departureTime: null
            };
            
            // Ajouter à la liste des visiteurs
            museumData.visitors.push(visitor);
            museumData.currentVisitors++;
            
            // Mettre à jour les compteurs d'exposition
            if (exposition === 1) {
                museumData.expositions.permanent.visitors++;
            } else if (exposition === 2) {
                museumData.expositions.temporary.visitors++;
            } else {
                museumData.expositions.both.visitors++;
                museumData.expositions.permanent.visitors++;
                museumData.expositions.temporary.visitors++;
            }
            
            // Recalculer les pourcentages
            calculateExpositionPercentages();
            
            // Mettre à jour l'affichage
            updateDisplays();
            
            // Réinitialiser le formulaire
            this.reset();
            
            // Afficher un message de succès
            showToast(`Visiteur ${firstName} ${lastName} ajouté avec succès !`, 'success');
        });

        // Fonction de recherche
        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
            
            if (!searchTerm) {
                showToast('Veuillez entrer un terme de recherche.', 'info');
                return;
            }
            
            // Rechercher dans la liste complète des visiteurs (actuels et historiques)
            const allVisitors = [...museumData.visitors, ...museumData.history];
            
            const results = allVisitors.filter(visitor => 
                visitor.firstName.toLowerCase().includes(searchTerm) ||
                visitor.lastName.toLowerCase().includes(searchTerm) ||
                visitor.email.toLowerCase().includes(searchTerm)
            );
            
            // Afficher les résultats
            const resultsContainer = document.getElementById('search-results');
            const resultsList = document.getElementById('search-results-list');
            
            resultsContainer.style.display = 'block';
            resultsList.innerHTML = '';
            
            if (results.length === 0) {
                resultsList.innerHTML = '<p class="no-visitors">Aucun visiteur trouvé pour cette recherche.</p>';
                return;
            }
            
            // Afficher jusqu'à 10 résultats
            const limitedResults = results.slice(0, 10);
            
            limitedResults.forEach(visitor => {
                const card = document.createElement('div');
                card.className = 'visitor-card';
                
                const expoName = visitor.exposition === 1 ? 'Exposition Permanente' : 
                                visitor.exposition === 2 ? 'Exposition Temporaire' : 'Les deux expositions';
                
                const status = visitor.departureTime ? 'A quitté le musée' : 'Actuellement présent';
                const statusClass = visitor.departureTime ? 'left' : 'present';
                
                card.innerHTML = `
                    <p><strong>Nom :</strong> ${visitor.lastName} ${visitor.firstName}</p>
                    <p><strong>Âge :</strong> ${visitor.age} ans</p>
                    <p><strong>Email :</strong> ${visitor.email}</p>
                    <p><strong>Téléphone :</strong> ${visitor.phone || 'Non renseigné'}</p>
                    <p><strong>Exposition :</strong> ${expoName}</p>
                    <p><strong>Arrivée :</strong> ${formatTime(visitor.arrivalTime)}</p>
                    <p class="status ${statusClass}"><strong>Statut :</strong> ${status}</p>
                `;
                
                resultsList.appendChild(card);
            });
        });

        // Recherche dans l'historique
        document.getElementById('history-search').addEventListener('input', function(e) {
            const searchTerm = this.value.toLowerCase().trim();
            const allHistoryRows = document.querySelectorAll('#all-history tr');
            
            allHistoryRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm) || searchTerm === '') {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Gestion des onglets
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function() {
                // Retirer la classe active de tous les boutons et contenus
                document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
                
                // Ajouter la classe active au bouton cliqué
                this.classList.add('active');
                
                // Afficher le contenu correspondant
                const tabId = this.dataset.tab + '-tab';
                document.getElementById(tabId).classList.add('active');
            });
        });

        // Fonction pour faire défiler jusqu'à une section
        function scrollToSection(sectionId) {
            const section = document.getElementById(sectionId);
            if (section) {
                section.scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Afficher un toast (notification)
        function showToast(message, type = 'info') {
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toast-message');
            
            toast.className = 'toast visible ' + type;
            toastMessage.textContent = message;
            
            setTimeout(hideToast, 5000);
        }

        // Masquer le toast
        function hideToast() {
            const toast = document.getElementById('toast');
            toast.className = 'toast';
        }

        // Effet de clic pour les boutons
        document.addEventListener('click', function(e) {
            if (e.target.tagName === 'BUTTON' || e.target.classList.contains('edit-button')) {
                const ripple = document.createElement('span');
                ripple.className = 'ripple';
                e.target.appendChild(ripple);
                
                const rect = e.target.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            }
        });

        // Initialisation au chargement de la page
        window.addEventListener('load', function() {
            generateInitialData();
            showToast('Système de gestion du musée initialisé. Bienvenue !', 'info');
        });
    </script>
</body>
</html>