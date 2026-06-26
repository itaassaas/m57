<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'M57' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400&family=Mulish:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.css">
    <style>
        :root {
            --bg: #f5f5f5;
            --surface: #ffffff;
            --surface-2: #f3f3f3;
            --ink: #000000;
            --muted: #6e6e6e;
            --line: #e9e9e9;
            --line-2: #d9d9d9;
            --accent: #ff1f49;
            --accent-deep: #df0832;
            --accent-soft: #fff0f3;
            --success: #167a45;
            --radius: 12px;
            --shadow: 0 12px 28px rgba(17, 17, 17, 0.06);
            --shadow-soft: 0 8px 18px rgba(17, 17, 17, 0.04);
            --ease-out: cubic-bezier(0.23, 1, 0.32, 1);
            --ease-in-out: cubic-bezier(0.77, 0, 0.175, 1);
            --ease-drawer: cubic-bezier(0.32, 0.72, 0, 1);
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            color: var(--ink);
            font-family: "Mulish", "Helvetica Neue", Helvetica, Arial, sans-serif;
            background:
                linear-gradient(180deg, #ffffff 0, #fbfbfb 140px, var(--bg) 140px, var(--bg) 100%);
        }
        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }
        button, input, select, textarea { font: inherit; }
        button { cursor: pointer; }
        .shell { width: min(1380px, calc(100% - 28px)); margin: 0 auto; }
        .promo-bar {
            background: var(--ink);
            color: #fff;
            font-size: 11px;
            letter-spacing: .06em;
            text-transform: uppercase;
        }
        .promo-inner {
            min-height: 38px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .promo-badges {
            display: flex;
            gap: 18px;
            white-space: nowrap;
            overflow: auto;
        }
        .promo-badge { opacity: .92; }
        .promo-badge,
        .hero-copy > *,
        .section-head,
        .category-tile,
        .card,
        .editorial-banner,
        .wall-card,
        .newsletter,
        .site-footer {
            animation: rise-in 560ms var(--ease-out) both;
        }
        .promo-badge:nth-child(2) { animation-delay: 40ms; }
        .promo-badge:nth-child(3) { animation-delay: 80ms; }
        .hero-copy > :nth-child(2) { animation-delay: 80ms; }
        .hero-copy > :nth-child(3) { animation-delay: 140ms; }
        .category-tile:nth-child(2),
        .card:nth-child(2) { animation-delay: 30ms; }
        .category-tile:nth-child(3),
        .card:nth-child(3) { animation-delay: 60ms; }
        .category-tile:nth-child(4),
        .card:nth-child(4) { animation-delay: 90ms; }
        .category-tile:nth-child(5),
        .card:nth-child(5) { animation-delay: 120ms; }
        @keyframes rise-in {
            from {
                opacity: 0;
                transform: translateY(10px) scale(0.985);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        .topbar {
            position: sticky;
            top: 0;
            z-index: 120;
            background: rgba(0,0,0,.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255,255,255,.08);
            transition: background 180ms var(--ease-out), border-color 180ms var(--ease-out);
        }
        .topbar.topbar--light {
            background: rgba(255,255,255,.96);
            border-bottom: 1px solid #eee;
            box-shadow: 0 10px 24px rgba(17, 17, 17, 0.04);
        }
        .topbar-inner {
            min-height: 74px;
            display: grid;
            grid-template-columns: 180px minmax(0, 1fr) auto;
            gap: 16px;
            align-items: center;
        }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 0;
            font-size: 40px;
            font-weight: 800;
            letter-spacing: .06em;
            color: #fff;
            transition: transform 160ms var(--ease-out), opacity 160ms var(--ease-out);
        }
        .topbar.topbar--light .brand {
            color: var(--ink);
        }
        .brand-logo {
            display: none;
        }
        .brand-wordmark {
            line-height: 1;
            font-family: "Cinzel", "Times New Roman", serif;
            font-weight: 400;
            letter-spacing: .08em;
        }
        .searchbar {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 999px;
            padding: 11px 16px;
            color: #fff;
            transition: transform 180ms var(--ease-out), background 180ms var(--ease-out), border-color 180ms var(--ease-out), box-shadow 180ms var(--ease-out);
        }
        .topbar.topbar--light .searchbar {
            background: #f8f8f8;
            border-color: #ececec;
            color: var(--ink);
        }
        .searchbar:focus-within {
            border-color: rgba(255,255,255,.26);
            background: rgba(255,255,255,.12);
            box-shadow: 0 10px 24px rgba(0,0,0,.18);
            transform: translateY(-1px);
        }
        .topbar.topbar--light .searchbar:focus-within {
            border-color: #d5d5d5;
            background: #fff;
            box-shadow: 0 10px 24px rgba(17,17,17,.08);
        }
        .searchbar input {
            border: 0;
            background: transparent;
            width: 100%;
            outline: 0;
            color: #fff;
        }
        .topbar.topbar--light .searchbar input {
            color: var(--ink);
        }
        .searchbar input::placeholder {
            color: rgba(255,255,255,.56);
        }
        .topbar.topbar--light .searchbar input::placeholder {
            color: #8b8b93;
        }
        .icon-btn, .cart-pill, .nav-pill, .category-chip, .filter-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
        }
        .top-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .icon-btn {
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.08);
            color: #fff;
            font-size: 16px;
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out), border-color 160ms var(--ease-out);
        }
        .topbar.topbar--light .icon-btn {
            border-color: #ececec;
            background: #fafafa;
            color: var(--ink);
        }
        .cart-pill {
            gap: 8px;
            min-height: 44px;
            padding: 0 16px;
            background: #fff;
            color: var(--ink);
            font-weight: 700;
            transition: transform 160ms var(--ease-out), box-shadow 160ms var(--ease-out), background 160ms var(--ease-out);
        }
        .topbar.topbar--light .cart-pill {
            border: 1px solid #ececec;
            box-shadow: none;
        }
        .subnav {
            position: relative;
            z-index: 40;
            border-bottom: 1px solid var(--line);
            background: #fff;
            overflow: visible;
        }
        .subnav-inner {
            min-height: 52px;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
            overflow: visible;
            white-space: nowrap;
        }
        .subnav-mega-item,
        .subnav-mega-anchor,
        .nav-pill {
            flex: 0 0 auto;
        }
        .subnav-mega-item {
            padding-bottom: 18px;
            margin-bottom: -18px;
        }
        .subnav-mega-anchor {
            position: relative;
        }
        .nav-pill {
            min-height: 34px;
            padding: 0 14px;
            font-size: 12px;
            font-weight: 700;
            background: transparent;
            border: 1px solid transparent;
            color: var(--muted);
            transition: transform 160ms var(--ease-out), color 160ms var(--ease-out), background 160ms var(--ease-out), border-color 160ms var(--ease-out);
        }
        .nav-pill.active,
        .nav-pill:hover {
            color: var(--ink);
            background: var(--surface-2);
            border-color: var(--line);
        }
        .notice, .error-list {
            margin: 16px 0 0;
            border-radius: 14px;
            padding: 14px 16px;
            background: #fff;
            border: 1px solid var(--line);
        }
        .error-list {
            color: #931f1f;
            background: #fff6f6;
            border-color: #ffd4d4;
        }
        .hero-banner {
            width: 100vw;
            margin-left: calc(50% - 50vw);
            margin-right: calc(50% - 50vw);
            padding-left: 14px;
            padding-right: 14px;
            padding-top: 18px;
            padding-bottom: 20px;
        }
        .hero-carousel {
            position: relative;
            overflow: hidden;
            border-radius: 20px;
            min-height: 520px;
            background: var(--hero-carousel-bg, #0f172a);
            box-shadow: var(--shadow);
            transition: background 300ms var(--ease-out);
        }
        .hero-carousel-track {
            display: flex;
            transition: transform 300ms var(--ease-out);
        }
        .hero-slide {
            position: relative;
            flex: 0 0 100%;
            min-width: 100%;
            min-height: 520px;
            padding: 48px;
            display: grid;
            grid-template-columns: 55% 45%;
            gap: 20px;
            overflow: hidden;
        }
        .hero-slide::before,
        .hero-slide::after {
            content: "";
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }
        .hero-slide::before {
            width: 340px;
            height: 340px;
            right: -90px;
            top: -90px;
            background: rgba(255,255,255,.09);
            filter: blur(10px);
        }
        .hero-slide::after {
            width: 220px;
            height: 220px;
            left: 42%;
            bottom: -90px;
            background: rgba(255,255,255,.08);
            filter: blur(8px);
        }
        .hero-slide.midnight {
            background-color: #1E293B;
            background: linear-gradient(135deg, #0F172A 0%, #1E293B 45%, #4C1D95 100%);
        }
        .hero-slide.midnight .hero-slide-copy::after {
            content: "";
            position: absolute;
            inset: auto auto 28px -30px;
            width: 180px;
            height: 180px;
            border-radius: 999px;
            background: rgba(167,139,250,.22);
            filter: blur(36px);
            pointer-events: none;
        }
        .hero-slide.summer {
            background-color: #FF8A65;
            background: linear-gradient(135deg, #FF5F6D 0%, #FFC371 100%);
        }
        .hero-slide.midnight .hero-product-card {
            background: rgba(15,23,42,.82);
            border-color: rgba(255,255,255,.1);
            color: #fff;
        }
        .hero-slide.midnight .hero-product-card .eyebrow,
        .hero-slide.midnight .hero-product-card .title,
        .hero-slide.midnight .hero-product-card .rating-row {
            color: rgba(255,255,255,.82);
        }
        .hero-slide.midnight .hero-product-card .old-price {
            color: rgba(255,255,255,.48);
        }
        .hero-slide.midnight .hero-product-card .quick-link {
            background: rgba(255,255,255,.08);
            border-color: rgba(255,255,255,.16);
            color: #fff;
        }
        .hero-slide.summer::before {
            background: rgba(255,255,255,.16);
        }
        .hero-slide.summer::after {
            background: rgba(255,255,255,.14);
        }
        .hero-slide-copy {
            position: relative;
            z-index: 1;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-self: center;
            max-width: 520px;
        }
        .hero-slide-badge {
            display: inline-flex;
            align-items: center;
            min-height: 36px;
            width: fit-content;
            padding: 0 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.16);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
        }
        .hero-slide-title {
            margin: 18px 0 14px;
            font-size: clamp(48px, 7vw, 82px);
            line-height: .88;
            letter-spacing: -.06em;
        }
        .hero-slide-subtitle {
            margin: 0 0 26px;
            max-width: 420px;
            color: rgba(255,255,255,.86);
            font-size: 17px;
            line-height: 1.5;
        }
        .hero-slide-cta {
            width: fit-content;
            min-height: 48px;
            padding: 0 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            color: var(--ink);
            font-weight: 800;
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out), color 160ms var(--ease-out);
        }
        .hero-slide-cta.ghost {
            background: rgba(255,255,255,.06);
            color: #fff;
            border: 1px solid rgba(255,255,255,.68);
            backdrop-filter: blur(8px);
        }
        .hero-slide-products {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
            align-content: center;
            justify-items: end;
        }
        .hero-product-card {
            width: 100%;
            max-width: 240px;
            justify-self: center;
            background: rgba(255,255,255,.96);
            backdrop-filter: blur(10px);
        }
        .hero-slide-products .hero-product-card:nth-child(n+3) {
            display: none;
        }
        .hero-product-media {
            aspect-ratio: 11 / 16;
        }
        .hero-carousel-arrow {
            position: absolute;
            top: 50%;
            z-index: 3;
            width: 46px;
            height: 46px;
            margin-top: -23px;
            border: 0;
            border-radius: 999px;
            background: rgba(255,255,255,.18);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            backdrop-filter: blur(8px);
            box-shadow: 0 10px 24px rgba(17,17,17,.14);
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out);
        }
        .hero-carousel-arrow.prev { left: 18px; }
        .hero-carousel-arrow.next { right: 18px; }
        .hero-carousel-dots {
            position: absolute;
            left: 50%;
            bottom: 18px;
            z-index: 3;
            display: flex;
            gap: 8px;
            transform: translateX(-50%);
        }
        .hero-main, .hero-side {
            border-radius: 18px;
            overflow: hidden;
            position: relative;
        }
        .hero-main {
            background:
                radial-gradient(circle at top left, rgba(255,255,255,.14) 0%, rgba(255,255,255,0) 28%),
                radial-gradient(circle at 78% 28%, rgba(255,31,73,.34) 0%, rgba(255,31,73,0) 24%),
                linear-gradient(135deg, rgba(3,3,3,.78) 0%, rgba(17,17,17,.54) 42%, rgba(36,7,15,.74) 100%),
                var(--hero-image, linear-gradient(135deg, #030303 0%, #111 42%, #24070f 100%));
            color: #fff;
            min-height: calc(100svh - 182px);
            padding: 42px 34px 34px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-size: auto, auto, cover;
            background-position: top left, 78% 28%, center center;
            transition: transform 260ms var(--ease-out), box-shadow 260ms var(--ease-out), background-position 600ms var(--ease-in-out);
        }
        .hero-main::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                linear-gradient(90deg, rgba(0,0,0,.06) 0%, rgba(0,0,0,0) 26%, rgba(0,0,0,0) 100%),
                linear-gradient(180deg, rgba(255,255,255,.04) 0%, rgba(255,255,255,0) 22%, rgba(0,0,0,0) 100%);
            pointer-events: none;
        }
        .hero-main-image::after {
            display: none;
        }
        .hero-main-image {
            background: #fff var(--hero-image) center center / cover no-repeat;
            transition: opacity 260ms var(--ease-out);
        }
        .hero-dots {
            position: absolute;
            left: 50%;
            bottom: 18px;
            z-index: 2;
            display: flex;
            gap: 8px;
            transform: translateX(-50%);
        }
        .hero-dot {
            width: 10px;
            height: 10px;
            padding: 0;
            border: 0;
            border-radius: 999px;
            background: rgba(255,255,255,.5);
            box-shadow: 0 0 0 1px rgba(0,0,0,.08);
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out), width 160ms var(--ease-out);
        }
        .hero-dot.is-active {
            width: 28px;
            background: #fff;
        }
        .hero-copy {
            position: relative;
            z-index: 1;
            max-width: 760px;
        }
        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 36px;
            padding: 0 16px;
            border-radius: 999px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            font-size: 11px;
            letter-spacing: .1em;
            text-transform: uppercase;
            font-weight: 700;
            backdrop-filter: blur(8px);
        }
        .hero-main h1 {
            margin: 20px 0 14px;
            max-width: 820px;
            font-size: clamp(44px, 7vw, 86px);
            line-height: .88;
            letter-spacing: -.06em;
            text-wrap: balance;
        }
        .hero-main p {
            margin: 0 0 28px;
            max-width: 620px;
            color: rgba(255,255,255,.8);
            font-size: 17px;
            line-height: 1.5;
        }
        .hero-btn, .button {
            min-height: 46px;
            border-radius: 999px;
            padding: 0 18px;
            border: 0;
            background: var(--ink);
            color: #fff;
            font-weight: 700;
        }
        .hero-btn.light, .button.secondary {
            background: #fff;
            color: var(--ink);
            border: 1px solid rgba(17,17,17,.08);
        }
        .hero-side {
            background: linear-gradient(180deg, #ffffff 0%, #f5f5f5 100%);
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
            padding: 20px;
            display: grid;
            gap: 16px;
            align-content: space-between;
        }
        .hero-side h2 {
            margin: 0;
            font-size: 18px;
            letter-spacing: -.03em;
        }
        .flash-card {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 10px;
            align-items: end;
            padding: 16px;
            border-radius: 12px;
            background: #0d0d0d;
            color: #fff;
        }
        .flash-price {
            color: var(--accent);
            font-size: 26px;
            font-weight: 800;
        }
        .category-rail {
            display: flex;
            gap: 10px;
            overflow: auto;
            padding: 2px 0 6px;
        }
        .section-block {
            padding: 28px 0 0;
        }
        .section-head {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 12px;
            margin: 0 0 20px;
        }
        .section-title {
            margin: 0;
            font-size: 26px;
            line-height: .95;
            letter-spacing: -.04em;
        }
        .section-title.centered-lines {
            display: inline-flex;
            align-items: center;
            gap: 16px;
            text-align: center;
            font-size: 28px;
            letter-spacing: .06em;
        }
        .section-title.centered-lines::before,
        .section-title.centered-lines::after {
            content: "";
            width: 72px;
            height: 1px;
            background: rgba(0, 0, 0, 0.22);
        }
        .category-showcase {
            display: grid;
            grid-auto-flow: column;
            grid-template-rows: repeat(3, minmax(0, 1fr));
            grid-auto-columns: calc((100% - 112px) / 8);
            gap: 16px;
            margin-bottom: 28px;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 8px;
            align-items: start;
            justify-content: start;
            scrollbar-width: thin;
        }
        .category-tile {
            padding: 6px 6px 10px;
            text-align: center;
            min-width: 0;
            transition: transform 180ms var(--ease-out);
        }
        .category-icon {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            margin: 0 auto 10px;
            display: block;
            object-fit: cover;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
            transition: transform 220ms var(--ease-out), box-shadow 220ms var(--ease-out), border-color 220ms var(--ease-out);
        }
        .category-name {
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .promo-grid {
            display: grid;
            grid-template-columns: 1.15fr .85fr .85fr;
            gap: 12px;
            margin-bottom: 18px;
        }
        .promo-card {
            min-height: 164px;
            border-radius: 16px;
            overflow: hidden;
            position: relative;
            padding: 18px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
        }
        .promo-card.dark {
            color: #fff;
            background: linear-gradient(135deg, #050505 0%, #222 46%, #470c18 100%);
        }
        .promo-card.light {
            background: linear-gradient(180deg, #ffffff 0%, #f5f5f5 100%);
        }
        .promo-card.soft {
            background: linear-gradient(180deg, #fff3f6 0%, #ffe8ee 100%);
        }
        .promo-card h3 {
            margin: 10px 0 8px;
            font-size: 26px;
            line-height: .95;
            letter-spacing: -.04em;
        }
        .promo-card p {
            margin: 0;
            max-width: 280px;
            font-size: 13px;
            line-height: 1.4;
            color: inherit;
            opacity: .82;
        }
        .promo-kicker {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .09em;
            font-weight: 800;
        }
        .mega-menu {
            position: absolute;
            top: calc(100% + 1px);
            left: 0;
            right: 0;
            z-index: 50;
            width: 100%;
            padding-top: 0;
            opacity: 0;
            pointer-events: none;
            overflow: hidden;
            transition: opacity 180ms var(--ease-out);
            border-top: 1px solid rgba(17,17,17,.06);
        }
        .subnav-mega-item:hover .mega-menu,
        .subnav-mega-item:focus-within .mega-menu,
        .mega-menu:hover,
        .mega-menu:focus-within {
            opacity: 1;
            pointer-events: auto;
        }
        .mega-menu-inner {
            display: grid;
            grid-template-columns: minmax(0, 65%) minmax(260px, 20%) minmax(260px, 15%);
            gap: 24px;
            width: min(100%, 1600px);
            margin: 0 auto;
            padding: 32px;
            background: rgba(255,255,255,.98);
            box-shadow: 0 28px 60px rgba(17,17,17,.12);
            backdrop-filter: blur(14px);
            align-items: start;
        }
        .mega-categories {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 24px;
        }
        .mega-column {
            display: flex;
            flex-direction: column;
            gap: 18px;
            padding: 28px;
            height: 100%;
            background:
                linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(249,249,249,.96) 100%);
            border: 1px solid rgba(17,17,17,.06);
            border-radius: 18px;
            box-shadow: var(--shadow-soft);
            transition: border-color 180ms var(--ease-out), box-shadow 180ms var(--ease-out);
        }
        .mega-rail {
            display: grid;
            gap: 18px;
            align-content: start;
        }
        .mega-featured {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .mega-product {
            display: grid;
            grid-template-columns: 64px minmax(0, 1fr);
            gap: 10px;
            padding: 12px;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(255,255,255,1) 0%, rgba(249,249,249,.96) 100%);
            border: 1px solid rgba(17,17,17,.06);
            box-shadow: var(--shadow-soft);
            transition: border-color 180ms var(--ease-out), box-shadow 180ms var(--ease-out);
        }
        .mega-product-media {
            width: 64px;
            height: 80px;
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(180deg, #f5f5f5 0%, #ededed 100%);
        }
        .mega-product-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .mega-product-copy {
            display: grid;
            gap: 6px;
            align-content: center;
        }
        .mega-product-title {
            font-size: 13px;
            line-height: 1.35;
            color: #111;
        }
        .mega-product-price {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.02em;
        }
        .mega-spotlight {
            min-height: 170px;
            padding: 18px;
            border-radius: 20px;
            color: #fff;
            display: grid;
            align-content: end;
            gap: 8px;
            box-shadow: var(--shadow-soft);
            transition: box-shadow 180ms var(--ease-out);
        }
        .mega-spotlight.dark {
            background: linear-gradient(135deg, #050505 0%, #1f1f1f 55%, #4b1121 100%);
        }
        .mega-spotlight.soft {
            background: linear-gradient(135deg, #ff6a88 0%, #ffb86b 100%);
        }
        .mega-spotlight-kicker {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255,255,255,.74);
        }
        .mega-spotlight strong {
            font-size: 24px;
            line-height: .95;
            letter-spacing: -.04em;
        }
        .mega-spotlight span:last-child {
            font-size: 13px;
            line-height: 1.4;
            color: rgba(255,255,255,.82);
        }
        .mega-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .09em;
            color: var(--ink);
        }
        .mega-link {
            font-size: 13px;
            color: #3f3f46;
            min-height: 34px;
            padding: 0 14px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            width: 100%;
            border: 1px solid transparent;
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out), color 160ms var(--ease-out), border-color 160ms var(--ease-out);
        }
        .mega-link:hover {
            color: var(--ink);
            background: #fff;
            border-color: rgba(17,17,17,.08);
        }
        .product-strip {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }
        .split-section {
            display: grid;
            grid-template-columns: minmax(280px, .9fr) minmax(0, 1.6fr);
            gap: 20px;
            align-items: start;
        }
        .editorial-banner {
            min-height: 100%;
            border-radius: 18px;
            padding: 22px;
            color: #fff;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
            transition: transform 220ms var(--ease-out), box-shadow 220ms var(--ease-out);
            background-size: cover;
            background-position: center;
            isolation: isolate;
        }
        .editorial-banner::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(17,17,17,.14) 0%, rgba(17,17,17,.52) 100%);
            z-index: -1;
        }
        .editorial-banner > * {
            position: relative;
            z-index: 1;
        }
        .editorial-banner h3 {
            margin: 12px 0 8px;
            font-size: 34px;
            line-height: .92;
            letter-spacing: -.05em;
        }
        .editorial-banner p {
            margin: 0;
            max-width: 280px;
            font-size: 13px;
            line-height: 1.45;
            opacity: .82;
        }
        .editorial-banner.dark {
            background-image:
                linear-gradient(135deg, rgba(7,7,7,.34) 0%, rgba(27,27,27,.48) 52%, rgba(59,10,23,.62) 100%),
                url('https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&w=1200&q=80');
        }
        .editorial-banner.red {
            background-image:
                linear-gradient(135deg, rgba(255,53,89,.28) 0%, rgba(255,99,127,.4) 60%, rgba(255,154,176,.5) 100%),
                url('https://images.unsplash.com/photo-1483985988355-763728e1935b?auto=format&fit=crop&w=1200&q=80');
        }
        .editorial-banner.gray {
            background-image:
                linear-gradient(135deg, rgba(38,43,51,.28) 0%, rgba(63,72,84,.42) 52%, rgba(82,92,107,.56) 100%),
                url('https://images.unsplash.com/photo-1529139574466-a303027c1d8b?auto=format&fit=crop&w=1200&q=80');
        }
        .editorial-banner.beige {
            color: #fff;
            background-image:
                linear-gradient(135deg, rgba(120,87,52,.22) 0%, rgba(89,59,32,.36) 55%, rgba(52,33,18,.52) 100%),
                url('https://images.unsplash.com/photo-1496747611176-843222e1e57c?auto=format&fit=crop&w=1200&q=80');
        }
        .editorial-banner.pink {
            color: var(--ink);
            background: linear-gradient(135deg, #fff1f5 0%, #ffdce7 55%, #ffc3d6 100%);
        }
        .look-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }
        .look-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 18px;
            overflow: hidden;
            box-shadow: var(--shadow-soft);
        }
        .look-media {
            aspect-ratio: 4 / 5;
            background: linear-gradient(180deg, #f1f1f1 0%, #e2e2e2 100%);
        }
        .look-body {
            padding: 14px;
            display: grid;
            gap: 8px;
        }
        .newsletter {
            background: linear-gradient(135deg, #090909 0%, #1d1d1d 65%, #36111a 100%);
            color: #fff;
            border-radius: 22px;
            padding: 30px 24px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 18px;
            align-items: center;
            box-shadow: var(--shadow);
            margin-top: 10px;
            transition: transform 220ms var(--ease-out), box-shadow 220ms var(--ease-out);
        }
        .newsletter h2 {
            margin: 0 0 8px;
            font-size: 34px;
            line-height: .94;
            letter-spacing: -.05em;
        }
        .newsletter-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }
        .newsletter-form .field {
            min-width: 280px;
            background: rgba(255,255,255,.96);
        }
        .site-footer {
            margin-top: 40px;
            padding: 28px 0 44px;
            border-top: 1px solid var(--line);
        }
        .drawer-toggle {
            position: fixed;
            top: -100vh;
            left: -100vw;
            opacity: 0;
            pointer-events: none;
        }
        .drawer-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,.28);
            opacity: 0;
            pointer-events: none;
            transition: opacity 220ms var(--ease-out);
            z-index: 70;
        }
        .slide-drawer {
            --drawer-tab-width: 40px;
            --drawer-panel-width: min(360px, calc(100vw - 28px - var(--drawer-tab-width)));
            position: fixed;
            top: 50%;
            right: 0;
            z-index: 80;
            width: var(--drawer-panel-width);
            transform: translateY(-50%) translateX(100%);
            transition: transform 300ms var(--ease-drawer);
            pointer-events: none;
        }
        .slide-drawer-shell {
            position: relative;
        }
        .slide-drawer-panel {
            min-height: 420px;
            padding: 22px;
            background: #fff;
            border: 1px solid rgba(17,17,17,.08);
            border-right: 0;
            border-radius: 22px 0 0 22px;
            box-shadow: 0 24px 54px rgba(17,17,17,.18);
            display: grid;
            gap: 18px;
            align-content: start;
            pointer-events: auto;
        }
        .slide-drawer-head {
            display: flex;
            justify-content: space-between;
            align-items: start;
            gap: 14px;
        }
        .slide-drawer-close {
            width: 40px;
            height: 40px;
            border: 1px solid var(--line);
            border-radius: 999px;
            background: #fff;
            color: var(--ink);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out);
        }
        .slide-drawer-tab {
            position: absolute;
            top: 0;
            bottom: 0;
            left: calc(var(--drawer-tab-width) * -1);
            width: var(--drawer-tab-width);
            padding: 14px 0;
            background: #e91e63;
            color: #fff;
            border-radius: 20px 0 0 20px;
            display: grid;
            justify-items: center;
            align-content: center;
            gap: 10px;
            box-shadow: 0 20px 40px rgba(233,30,99,.28);
            cursor: pointer;
            user-select: none;
            transition: transform 180ms var(--ease-out), filter 180ms var(--ease-out);
            pointer-events: auto;
        }
        .slide-drawer-tab-text {
            writing-mode: vertical-rl;
            text-orientation: mixed;
            letter-spacing: .1em;
            font-size: 14px;
            font-weight: 800;
            text-transform: uppercase;
        }
        .slide-drawer-tab-arrow {
            font-size: 22px;
            line-height: 1;
            transition: transform 300ms var(--ease-drawer);
        }
        .slide-drawer-kicker {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--accent);
        }
        .slide-drawer-title {
            margin: 0;
            font-size: clamp(28px, 4vw, 40px);
            line-height: .92;
            letter-spacing: -.05em;
        }
        .slide-drawer-copy {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }
        .slide-drawer-form {
            display: grid;
            gap: 10px;
        }
        .slide-drawer-banner {
            min-height: 170px;
            border-radius: 18px;
            background:
                linear-gradient(135deg, rgba(0,0,0,.18), rgba(0,0,0,.02)),
                url('https://picsum.photos/seed/m57-drawer/900/1200') center/cover;
            display: flex;
            align-items: end;
            padding: 18px;
            color: #fff;
        }
        .slide-drawer-banner strong {
            display: block;
            font-size: 22px;
            line-height: .95;
            letter-spacing: -.04em;
        }
        .slide-drawer-banner span {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            color: rgba(255,255,255,.82);
        }
        .drawer-toggle:checked + .drawer-overlay {
            opacity: 1;
            pointer-events: auto;
        }
        .drawer-toggle:checked + .drawer-overlay + .slide-drawer {
            transform: translateY(-50%) translateX(0);
        }
        .drawer-toggle:checked + .drawer-overlay + .slide-drawer .slide-drawer-tab-arrow {
            transform: rotate(180deg);
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
        }
        .footer-title {
            margin: 0 0 10px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
        }
        .footer-brand {
            display: grid;
            gap: 12px;
            align-content: start;
        }
        .footer-logo {
            height: 44px;
            width: auto;
            object-fit: contain;
        }
        .footer-list {
            display: grid;
            gap: 8px;
        }
        .footer-list a {
            font-size: 13px;
            color: var(--muted);
            transition: color 160ms var(--ease-out), transform 160ms var(--ease-out);
        }
        .footer-cookie-link {
            cursor: pointer;
        }
        .cookie-settings-trigger {
            position: fixed;
            left: 18px;
            bottom: 18px;
            z-index: 125;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-height: 42px;
            padding: 0 16px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 999px;
            background: rgba(255,255,255,.94);
            color: var(--ink);
            box-shadow: 0 14px 30px rgba(17, 17, 17, 0.1);
            backdrop-filter: blur(12px);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
            transition: transform 180ms var(--ease-out), box-shadow 180ms var(--ease-out), background 180ms var(--ease-out);
        }
        .cookie-settings-trigger.is-hidden {
            display: none !important;
        }
        .cookie-settings-trigger:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 34px rgba(17, 17, 17, 0.14);
            background: #fff;
        }
        #cc-main {
            --cc-font-family: "Mulish", "Helvetica Neue", Helvetica, Arial, sans-serif;
            --cc-modal-border-radius: 22px;
            --cc-btn-border-radius: 999px;
            --cc-modal-transition-duration: .3s;
            --cc-primary-color: #ff1f49;
            --cc-btn-primary-bg: #ff1f49;
            --cc-btn-primary-border-color: #ff1f49;
            --cc-btn-primary-hover-bg: #df0832;
            --cc-btn-primary-hover-border-color: #df0832;
            --cc-btn-primary-hover-color: #fff;
            --cc-btn-secondary-bg: #fff;
            --cc-btn-secondary-border-color: #dedede;
            --cc-btn-secondary-hover-bg: #111;
            --cc-btn-secondary-hover-border-color: #111;
            --cc-btn-secondary-hover-color: #fff;
            --cc-separator-border-color: #ececec;
            --cc-cookie-category-block-bg: #fafafa;
            --cc-cookie-category-block-border: #ececec;
            --cc-toggle-on-bg: #ff1f49;
        }
        #cc-main .cm,
        #cc-main .pm {
            box-shadow: 0 30px 80px rgba(17, 17, 17, 0.16);
        }
        #cc-main .cm__title,
        #cc-main .pm__title {
            font-weight: 800;
            letter-spacing: -.03em;
        }
        #cc-main .cm__desc,
        #cc-main .pm__section-desc {
            color: #4f4f4f;
        }
        .category-chip {
            min-height: 40px;
            padding: 0 16px;
            border: 1px solid var(--line);
            background: #fff;
            font-size: 12px;
            font-weight: 700;
        }
        .category-chip.active,
        .category-chip:hover {
            background: var(--ink);
            border-color: var(--ink);
            color: #fff;
        }
        .layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            gap: 18px;
            padding-bottom: 54px;
        }
        .discover-shell {
            display: grid;
            gap: 28px;
            padding-top: 10px;
            padding-bottom: 54px;
        }
        .toolbar-card {
            padding: 16px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
        }
        .toolbar-grid {
            display: grid;
            grid-template-columns: 1.25fr .9fr .9fr auto auto;
            gap: 10px;
            align-items: center;
        }
        .toolbar-grid .button,
        .toolbar-grid .button.secondary {
            width: auto;
            min-width: 120px;
        }
        .wall-card {
            padding: 18px;
            border-radius: 18px;
            background: #fff;
            border: 1px solid var(--line);
            box-shadow: var(--shadow-soft);
            transition: transform 220ms var(--ease-out), box-shadow 220ms var(--ease-out);
        }
        .wall-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: end;
            margin-bottom: 16px;
        }
        .wall-title {
            margin: 0;
            font-size: 32px;
            line-height: .92;
            letter-spacing: -.05em;
        }
        .wall-meta {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 8px;
        }
        .metric-chip {
            display: inline-flex;
            align-items: center;
            min-height: 30px;
            padding: 0 10px;
            border-radius: 999px;
            background: var(--surface-2);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .03em;
            text-transform: uppercase;
        }
        .product-wall {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }
        .sidebar,
        .panel,
        .details-card,
        .gallery-card,
        .stack-card {
            background: var(--surface);
            border: 1px solid var(--line);
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
        }
        .sidebar {
            position: sticky;
            top: 94px;
            align-self: start;
            padding: 18px;
        }
        .filter-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }
        .filter-form { display: grid; gap: 12px; }
        .field, .field-plain {
            width: 100%;
            min-height: 46px;
            border: 1px solid var(--line);
            border-radius: 10px;
            background: #fff;
            padding: 12px 14px;
            outline: none;
        }
        textarea.field { min-height: 110px; resize: vertical; border-radius: 12px; }
        .field:focus { border-color: var(--ink); }
        .button {
            width: 100%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .button.secondary {
            color: var(--ink);
            border: 1px solid var(--line);
        }
        .panel { padding: 18px; }
        .results-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }
        .results-title {
            display: grid;
            gap: 4px;
        }
        .mini { color: var(--muted); font-size: 13px; line-height: 1.35; }
        .filter-chips {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin: 0 0 14px;
        }
        .filter-chip {
            min-height: 34px;
            padding: 0 12px;
            background: var(--surface-2);
            font-size: 12px;
            font-weight: 700;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 12px;
        }
        .card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 22px rgba(17,17,17,.04);
            transition: transform 220ms var(--ease-out), box-shadow 220ms var(--ease-out), border-color 220ms var(--ease-out);
        }
        .card-media {
            display: block;
            position: relative;
            overflow: hidden;
            aspect-ratio: 4 / 5;
            background: #efefef;
        }
        .card-media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: opacity 220ms var(--ease-out), transform 320ms var(--ease-out);
        }
        .card-media .secondary {
            position: absolute;
            inset: 0;
            opacity: 0;
        }
        .card:hover .card-media .primary {
            opacity: 0;
            transform: scale(1.04);
        }
        .card:hover .card-media .secondary { opacity: 1; }
        .wish,
        .stock-chip,
        .sale-chip {
            position: absolute;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-weight: 700;
        }
        .wish {
            top: 10px;
            right: 10px;
            width: 36px;
            height: 36px;
            background: rgba(255,255,255,.96);
            color: var(--ink);
            border: 1px solid rgba(17,17,17,.08);
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out);
        }
        .sale-chip {
            top: 10px;
            left: 10px;
            min-height: 28px;
            padding: 0 10px;
            background: var(--accent);
            color: #fff;
            font-size: 10px;
            letter-spacing: .04em;
        }
        .stock-chip {
            left: 10px;
            bottom: 10px;
            min-height: 28px;
            padding: 0 10px;
            background: rgba(17,17,17,.78);
            color: #fff;
            font-size: 11px;
        }
        .card-body {
            padding: 12px;
            display: grid;
            gap: 6px;
        }
        .eyebrow {
            color: var(--muted);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .title {
            font-size: 12px;
            line-height: 1.35;
            min-height: 2.7em;
            font-weight: 500;
        }
        .price-row {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }
        .price {
            color: var(--accent);
            font-weight: 800;
            font-size: 17px;
        }
        .old-price {
            color: #999;
            font-size: 11px;
            text-decoration: line-through;
        }
        .rating-row {
            display: flex;
            gap: 8px;
            align-items: center;
            color: var(--muted);
            font-size: 12px;
        }
        .card-actions {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 8px;
            margin-top: 4px;
        }
        .quick-link,
        .add-btn {
            min-height: 40px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 700;
        }
        .quick-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--line);
            background: #fff;
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out), border-color 160ms var(--ease-out);
        }
        .add-btn {
            border: 0;
            padding: 0 14px;
            background: var(--ink);
            color: #fff;
            transition: transform 160ms var(--ease-out), background 160ms var(--ease-out), filter 160ms var(--ease-out);
        }
        .split {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(360px, .9fr);
            gap: 20px;
            padding: 20px 0 54px;
        }
        .gallery-card,
        .details-card,
        .stack-card { padding: 18px; }
        .gallery-stage {
            aspect-ratio: 4 / 5;
            overflow: hidden;
            border-radius: 12px;
            background: #efefef;
        }
        .gallery-stage img { width: 100%; height: 100%; object-fit: cover; }
        .thumbs {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-top: 12px;
        }
        .thumbs img {
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: #f3f3f3;
        }
        .product-title {
            margin: 8px 0 10px;
            font-size: clamp(28px, 4vw, 42px);
            line-height: .98;
            letter-spacing: -.03em;
        }
        .trust-row,
        .meta-grid,
        .summary-list,
        .meta-list { display: grid; gap: 10px; }
        .trust-row {
            grid-template-columns: repeat(3, minmax(0, 1fr));
            margin: 18px 0;
        }
        .trust-item {
            padding: 12px;
            border-radius: 10px;
            background: var(--surface-2);
            font-size: 12px;
        }
        .selection-box {
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: #fff;
        }
        .cart-list,
        .success-list {
            display: grid;
            gap: 12px;
        }
        .cart-item,
        .success-card {
            display: grid;
            grid-template-columns: 94px minmax(0, 1fr) auto;
            gap: 12px;
            align-items: start;
            padding: 14px;
            border-radius: 12px;
            background: #fff;
            border: 1px solid var(--line);
        }
        .cart-item img,
        .success-thumb {
            width: 94px;
            height: 118px;
            object-fit: cover;
            border-radius: 10px;
            background: #efefef;
        }
        .checkout-wrap {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) 380px;
            gap: 20px;
            padding: 18px 0 54px;
        }
        .checkout-steps {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        .step-pill {
            min-height: 34px;
            padding: 0 12px;
            border-radius: 999px;
            background: var(--surface-2);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
        }
        .step-pill.active {
            background: var(--ink);
            color: #fff;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }
        .form-grid .full { grid-column: 1 / -1; }
        .cta-row {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 18px;
        }
        .empty-state {
            padding: 26px;
            border-radius: 20px;
            background: #fff;
            border: 1px dashed var(--line-2);
            text-align: center;
        }
        .icon-btn:active,
        .cart-pill:active,
        .button:active,
        .quick-link:active,
        .add-btn:active,
        .brand:active {
            transform: scale(0.97);
        }
        @media (hover: hover) and (pointer: fine) {
            .brand:hover { transform: translateY(-1px); }
            .icon-btn:hover {
                transform: translateY(-1px);
                background: rgba(255,255,255,.14);
            }
            .cart-pill:hover {
                transform: translateY(-1px);
                box-shadow: 0 12px 24px rgba(0,0,0,.12);
            }
            .nav-pill:hover { transform: translateY(-1px); }
            .mega-column:hover,
            .mega-product:hover,
            .mega-spotlight:hover { box-shadow: 0 18px 34px rgba(17,17,17,.12); }
            .category-tile:hover { transform: translateY(-2px); }
            .category-tile:hover .category-icon {
                transform: scale(1.04);
                box-shadow: 0 14px 26px rgba(17,17,17,.09);
                border-color: #d8d8d8;
            }
            .editorial-banner:hover,
            .wall-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 16px 30px rgba(17,17,17,.08);
            }
            .card:hover {
                transform: translateY(-3px);
                border-color: #d8d8d8;
                box-shadow: 0 16px 30px rgba(17,17,17,.08);
            }
            .card:hover .card-media .primary {
                opacity: 0;
                transform: scale(1.04);
            }
            .card:hover .card-media .secondary { opacity: 1; }
            .card:hover .wish { transform: scale(1.04); }
            .quick-link:hover {
                transform: translateY(-1px);
                background: var(--surface-2);
            }
            .add-btn:hover {
                transform: translateY(-1px);
                filter: brightness(1.08);
            }
            .newsletter:hover {
                transform: translateY(-2px);
                box-shadow: 0 18px 34px rgba(17,17,17,.14);
            }
            .slide-drawer-tab:hover,
            .slide-drawer-close:hover {
                transform: translateY(-1px);
            }
            .footer-list a:hover {
                color: var(--ink);
                transform: translateX(2px);
            }
        }
        @media (prefers-reduced-motion: reduce) {
            * { scroll-behavior: auto; transition: none !important; }
            .slide-drawer,
            .drawer-overlay,
            .slide-drawer-tab-arrow { animation: none !important; }
        }
        @media (max-width: 1140px) {
            .grid { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .product-wall { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .product-strip { grid-template-columns: repeat(4, minmax(0, 1fr)); }
            .hero-slide {
                grid-template-columns: 1fr;
                padding: 36px 28px 68px;
            }
            .hero-slide-products {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .hero-product-card {
                max-width: 200px;
            }
            .hero-side { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .promo-grid { grid-template-columns: 1fr; }
            .toolbar-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .mega-menu-inner,
            .footer-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .mega-menu-inner {
                grid-template-columns: 1fr;
            }
            .mega-categories {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
            .split-section { grid-template-columns: 1fr; }
        }
        @media (max-width: 980px) {
            .layout,
            .split,
            .checkout-wrap { grid-template-columns: 1fr; }
            .sidebar {
                position: static;
                order: -1;
            }
            .grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .product-wall { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .product-strip { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .look-grid { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 760px) {
            .topbar-inner {
                grid-template-columns: 1fr;
                padding: 12px 0;
            }
            .subnav-inner {
                overflow-x: auto;
                overflow-y: visible;
            }
            .subnav-mega-item {
                padding-bottom: 0;
                margin-bottom: 0;
            }
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .product-wall { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .product-strip { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .hero-carousel {
                min-height: 0;
            }
            .hero-slide {
                min-height: auto;
                padding: 24px 16px 64px;
                gap: 18px;
            }
            .hero-slide-title {
                font-size: clamp(34px, 11vw, 48px);
            }
            .hero-slide-subtitle {
                font-size: 15px;
                margin-bottom: 18px;
            }
            .hero-slide-products {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            .hero-product-card {
                max-width: none;
            }
            .hero-slide-products .hero-product-card:nth-child(n+2) {
                display: none;
            }
            .hero-carousel-arrow {
                width: 40px;
                height: 40px;
                margin-top: -20px;
                font-size: 24px;
            }
            .hero-main {
                min-height: calc(100svh - 214px);
                padding: 28px 20px 20px;
            }
            .hero-main h1 { font-size: clamp(38px, 12vw, 58px); }
            .hero-main p { font-size: 15px; }
            .brand { font-size: 34px; }
            .category-showcase {
                grid-auto-columns: calc((100% - 12px) / 2);
                gap: 12px;
            }
            .category-icon {
                width: 84px;
                height: 84px;
            }
            .hero-side { grid-template-columns: 1fr; }
            .trust-row { grid-template-columns: 1fr; }
            .form-grid { grid-template-columns: 1fr; }
            .cart-item,
            .success-card { grid-template-columns: 80px minmax(0, 1fr); }
            .toolbar-grid { grid-template-columns: 1fr; }
            .mega-menu {
                opacity: 1;
                pointer-events: auto;
                margin-top: 10px;
            }
            .mega-menu {
                position: static;
                width: 100%;
                padding-top: 0;
                transform: none;
                border-top: 0;
                box-shadow: none;
            }
            .mega-categories,
            .mega-menu-inner,
            .footer-grid,
            .look-grid { grid-template-columns: 1fr; }
            .newsletter { grid-template-columns: 1fr; }
            .newsletter-form .field { min-width: 100%; }
            .section-block {
                padding-top: 22px;
            }
            .discover-shell {
                gap: 22px;
                padding-top: 6px;
            }
            .slide-drawer {
                --drawer-tab-width: 40px;
                --drawer-panel-width: min(320px, calc(100vw - 18px - var(--drawer-tab-width)));
                top: auto;
                bottom: 18px;
                transform: translateY(0) translateX(100%);
            }
            .drawer-toggle:checked + .drawer-overlay + .slide-drawer {
                transform: translateY(0) translateX(0);
            }
            .slide-drawer-panel {
                min-height: 360px;
                padding: 18px;
            }
            .slide-drawer-tab {
                border-radius: 18px 0 0 18px;
            }
            .cookie-settings-trigger {
                left: 12px;
                bottom: 12px;
                min-height: 38px;
                padding: 0 14px;
                font-size: 11px;
            }
        }
        @media (min-width: 1380px) {
            .hero-banner {
                padding-left: max(14px, calc((100vw - 1380px) / 2));
                padding-right: max(14px, calc((100vw - 1380px) / 2));
            }
        }
    </style>
</head>
<body class="{{ $bodyClass ?? '' }}">
    <div class="promo-bar">
        <div class="shell promo-inner">
            <div class="promo-badges">
                <span class="promo-badge">envio rapido</span>
                <span class="promo-badge">novedades cada semana</span>
                <span class="promo-badge">ofertas por tiempo limitado</span>
            </div>
            <div class="promo-badge">compra segura</div>
        </div>
    </div>

    <header class="topbar {{ ($headerVariant ?? null) === 'light' ? 'topbar--light' : '' }}">
        <div class="shell topbar-inner">
            <a href="{{ route('home') }}" class="brand">
                <img src="{{ asset('storage/logo.png') }}" alt="M57" class="brand-logo">
                <span class="brand-wordmark">M57</span>
            </a>
            <form class="searchbar" method="get" action="{{ route('home') }}">
                <span>⌕</span>
                <input
                    type="search"
                    name="q"
                    placeholder="Buscar prendas, accesorios y mas..."
                    value="{{ request('q') }}"
                    data-rotating-placeholder
                >
            </form>
            <div class="top-actions">
                <a href="{{ route('home') }}" class="icon-btn" aria-label="Inicio">⌂</a>
                <a href="{{ route('cart.show') }}" class="cart-pill">Carrito <span>{{ $cartCount ?? 0 }}</span></a>
            </div>
        </div>
    </header>

    @unless(request()->routeIs('checkout.*') || !empty($hideSubnav))
        <div class="subnav">
            <div class="shell subnav-inner">
                <div class="subnav-mega-item">
                <div class="subnav-mega-anchor">
                    <a href="{{ route('home') }}" class="nav-pill {{ request()->routeIs('home') || request()->routeIs('categories.*') ? 'active' : '' }}">Categorías</a>
                </div>
                @yield('subnav_mega_menu')
            </div>
            <a href="{{ route('home') }}" class="nav-pill">Solo para ti</a>
            <a href="{{ route('home') }}" class="nav-pill">Novedades</a>
            <a href="{{ route('home') }}" class="nav-pill">Ofertas</a>
            <a href="{{ route('cart.show') }}" class="nav-pill {{ request()->routeIs('cart.*') ? 'active' : '' }}">Bolsa</a>
        </div>
    </div>
    @endunless

    <main class="shell">
        @if(session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif
        @if($errors->any())
            <div class="error-list">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        @yield('content')
    </main>

    <input type="checkbox" id="right-slide-drawer" class="drawer-toggle" data-drawer-toggle autocomplete="off">
    <label for="right-slide-drawer" class="drawer-overlay" aria-hidden="true" data-drawer-overlay></label>
    <aside class="slide-drawer" aria-label="Consigue 25% de descuento">
        <div class="slide-drawer-shell">
            <div class="slide-drawer-panel">
                <div class="slide-drawer-head">
                    <div>
                        <div class="slide-drawer-kicker">Oferta exclusiva</div>
                        <h2 class="slide-drawer-title">Consigue hasta 25% OFF</h2>
                        <p class="slide-drawer-copy">Suscríbete para recibir novedades, promociones especiales y acceso anticipado a nuevas colecciones.</p>
                    </div>
                    <button type="button" class="slide-drawer-close" aria-label="Cerrar panel" data-drawer-close>×</button>
                </div>

                <form class="slide-drawer-form">
                    <input class="field" type="email" placeholder="Tu correo electrónico">
                    <button type="button" class="button">Quiero mi descuento</button>
                </form>

                <div class="slide-drawer-banner">
                    <div>
                        <strong>Nuevos favoritos cada semana</strong>
                        <span>Looks, básicos y ofertas seleccionadas para ti.</span>
                    </div>
                </div>
            </div>

            <label for="right-slide-drawer" class="slide-drawer-tab" aria-label="Abrir promociones" data-drawer-tab>
                <span class="slide-drawer-tab-arrow">‹</span>
                <span class="slide-drawer-tab-text">Consigue -25%</span>
            </label>
        </div>
    </aside>

    <button type="button" class="cookie-settings-trigger is-hidden" data-cc="show-preferencesModal">Cookies</button>

    <script src="https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.umd.js"></script>
    <script>
        (() => {
            if (!window.CookieConsent) return;

            const cookieTrigger = document.querySelector('.cookie-settings-trigger');
            const syncCookieTrigger = () => {
                if (!cookieTrigger) return;
                const hasConsent = !!window.CookieConsent.getCookie() && window.CookieConsent.validConsent();
                cookieTrigger.classList.toggle('is-hidden', hasConsent);
            };

            const applyGoogleConsent = (cookie) => {
                if (typeof window.gtag !== 'function') return;

                const categories = cookie?.categories || {};
                const granted = 'granted';
                const denied = 'denied';

                window.gtag('consent', 'update', {
                    analytics_storage: categories.analytics ? granted : denied,
                    ad_storage: categories.marketing ? granted : denied,
                    ad_user_data: categories.marketing ? granted : denied,
                    ad_personalization: categories.marketing ? granted : denied,
                    functionality_storage: categories.preferences ? granted : denied,
                    personalization_storage: categories.preferences ? granted : denied,
                    security_storage: granted,
                });
            };

            if (typeof window.gtag === 'function') {
                window.gtag('consent', 'default', {
                    analytics_storage: 'denied',
                    ad_storage: 'denied',
                    ad_user_data: 'denied',
                    ad_personalization: 'denied',
                    functionality_storage: 'denied',
                    personalization_storage: 'denied',
                    security_storage: 'granted',
                });
            }

            window.CookieConsent.run({
                revision: 1,
                autoShow: true,
                hideFromBots: true,
                disablePageInteraction: false,
                guiOptions: {
                    consentModal: {
                        layout: 'box wide',
                        position: 'bottom right',
                        equalWeightButtons: false,
                        flipButtons: false,
                    },
                    preferencesModal: {
                        layout: 'box',
                        equalWeightButtons: false,
                        flipButtons: false,
                    },
                },
                categories: {
                    necessary: {
                        enabled: true,
                        readOnly: true,
                    },
                    preferences: {},
                    analytics: {},
                    marketing: {},
                },
                language: {
                    default: 'es',
                    translations: {
                        es: {
                            consentModal: {
                                title: 'Usamos cookies 🍪',
                                description: 'Utilizamos cookies necesarias para que M57 funcione correctamente y cookies opcionales para analítica, personalización y marketing. Puedes aceptar todas, rechazar las opcionales o decidir por categoría.',
                                acceptAllBtn: 'Aceptar todas',
                                acceptNecessaryBtn: 'Rechazar',
                                showPreferencesBtn: 'Personalizar',
                            },
                            preferencesModal: {
                                title: 'Preferencias de cookies',
                                acceptAllBtn: 'Aceptar todas',
                                acceptNecessaryBtn: 'Rechazar todo',
                                savePreferencesBtn: 'Guardar preferencias',
                                closeIconLabel: 'Cerrar',
                                sections: [
                                    {
                                        title: 'Configuración de privacidad',
                                        description: 'Controla cómo usamos cookies para mejorar tu experiencia, medir rendimiento y mostrar promociones más relevantes.',
                                    },
                                    {
                                        title: 'Cookies necesarias',
                                        description: 'Estas cookies mantienen activo el carrito, el checkout, la seguridad y la sesión de la tienda.',
                                        linkedCategory: 'necessary',
                                    },
                                    {
                                        title: 'Preferencias',
                                        description: 'Permiten recordar idioma, vistas del catálogo y elecciones de experiencia dentro de M57.',
                                        linkedCategory: 'preferences',
                                    },
                                    {
                                        title: 'Analíticas',
                                        description: 'Nos ayudan a entender qué secciones visitan más los clientes y dónde optimizar la navegación.',
                                        linkedCategory: 'analytics',
                                    },
                                    {
                                        title: 'Marketing',
                                        description: 'Se usan para campañas, remarketing y recomendaciones promocionales más relevantes.',
                                        linkedCategory: 'marketing',
                                    },
                                    {
                                        title: 'Más información',
                                        description: 'Si necesitas detalles sobre el uso de datos o cambiar tu consentimiento más adelante, usa el enlace "Cookies" del footer de la tienda.',
                                    },
                                ],
                            },
                        },
                    },
                },
                onFirstConsent: ({ cookie }) => {
                    applyGoogleConsent(cookie);
                    syncCookieTrigger();
                },
                onConsent: ({ cookie }) => {
                    applyGoogleConsent(cookie);
                    syncCookieTrigger();
                },
                onChange: ({ cookie }) => {
                    applyGoogleConsent(cookie);
                    syncCookieTrigger();
                },
            });

            syncCookieTrigger();
        })();
    </script>

    <script>
        (() => {
            const carousel = document.querySelector('[data-hero-carousel]');
            if (!carousel) return;

            const track = carousel.querySelector('[data-hero-track]');
            const slides = Array.from(track.children);
            const dots = Array.from(carousel.querySelectorAll('[data-hero-dot]'));
            const prev = carousel.querySelector('[data-hero-prev]');
            const next = carousel.querySelector('[data-hero-next]');
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            let index = 0;
            let timerId = null;

            const render = () => {
                track.style.transform = `translateX(-${index * 100}%)`;
                carousel.style.setProperty('--hero-carousel-bg', slides[index]?.dataset.heroBg || '#0f172a');
                dots.forEach((dot, dotIndex) => {
                    dot.classList.toggle('is-active', dotIndex === index);
                });
            };

            const start = () => {
                window.clearInterval(timerId);
                if (reduceMotion || dots.length < 2) return;
                timerId = window.setInterval(() => {
                    index = (index + 1) % dots.length;
                    render();
                }, 6000);
            };

            prev?.addEventListener('click', () => {
                index = (index - 1 + dots.length) % dots.length;
                render();
                start();
            });

            next?.addEventListener('click', () => {
                index = (index + 1) % dots.length;
                render();
                start();
            });

            dots.forEach((dot, dotIndex) => {
                dot.addEventListener('click', () => {
                    index = dotIndex;
                    render();
                    start();
                });
            });

            render();
            start();
        })();
    </script>

    <script>
        (() => {
            const toggle = document.querySelector('[data-drawer-toggle]');
            const close = document.querySelector('[data-drawer-close]');

            if (!toggle) return;

            toggle.checked = false;

            close?.addEventListener('click', () => {
                toggle.checked = false;
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    toggle.checked = false;
                }
            });
        })();
    </script>

    <script>
        (() => {
            const input = document.querySelector('[data-rotating-placeholder]');
            if (!input || input.value) return;

            const phrases = [
                'Zapatos para dama 👠',
                'Blusa negra 🖤',
                'Hoodie oversize 🔥',
                'Pantalon cargo 👖',
                'Vestido de fiesta ✨',
                'Tenis blancos 👟',
                'Bolso mini 👜',
                'Chaqueta denim 💙',
                'Top deportivo 🏃',
                'Falda satinada 💫',
                'Accesorios dorados ✨',
                'Look casual 🧢',
                'Ropa de verano ☀️',
                'Botas altas 🤎',
                'Conjunto comfy 🛋️',
                'Jeans tiro alto 👖',
                'Camisa elegante 🤍',
                'Outfit de oficina 💼',
                'Lenceria delicada 🌷',
                'Promos de temporada 💥'
            ];

            let index = 0;
            input.placeholder = phrases[index];

            window.setInterval(() => {
                index = (index + 1) % phrases.length;
                input.placeholder = phrases[index];
            }, 2200);
        })();
    </script>
</body>
</html>
