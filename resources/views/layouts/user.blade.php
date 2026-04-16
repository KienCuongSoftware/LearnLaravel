<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'NovaShop')</title>
<link rel="icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    @livewireStyles
    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; padding-top: 100px; color: #111827; }
        main { color: #111827; }
        .text-muted { color: #5b6472 !important; }
        small, .small { color: inherit; }
        .page-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .page-header h2 { margin: 0; font-size: 1.5rem; }
        .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); border-radius: 0.75rem; overflow: hidden; }
        main .form-control { border-radius: 0.5rem; }
        .form-control:focus {
            outline: none !important;
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        main { flex: 1; }
        footer { margin-top: auto; }

        .alert-toast-container {
            position: fixed;
            top: 100px;
            left: 0;
            right: 0;
            z-index: 9999;
            padding: 1rem 15px;
            pointer-events: none;
        }
        .alert-toast-container .alert {
            pointer-events: auto;
            max-width: 720px;
            margin: 0 auto 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        main .btn-primary {
            background: #dc3545;
            border-color: #dc3545;
            border-radius: 0.5rem;
        }
        main .btn-primary:hover {
            background: #c82333;
            border-color: #bd2130;
        }

        header .navbar-main {
            background: #dc3545;
            padding: 0;
            flex-wrap: wrap;
        }
        .navbar-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            padding: 0.35rem 0;
        }
        .navbar-top-row-left { flex-shrink: 0; }
        .navbar-top-row-right {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-shrink: 0;
        }
        .navbar-top-row-left a,
        .navbar-top-row-right > a,
        .navbar-top-row-left a:hover,
        .navbar-top-row-right > a:hover,
        header .navbar-main .navbar-top-row-left a,
        header .navbar-main .navbar-top-row-right > a {
            color: #fff !important;
        }
        .navbar-top-row-left a:hover,
        .navbar-top-row-right > a:hover { opacity: 0.9; }
        .navbar-top-row span { color: #fff !important; }
        .navbar-top-row-nav {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 0.35rem 1.35rem;
        }
        .navbar-top-row-nav a {
            font-size: 0.9375rem;
            font-weight: 600;
            letter-spacing: 0.03em;
            white-space: nowrap;
            opacity: 0.95;
        }
        .navbar-top-row-nav a:hover {
            opacity: 1;
            text-decoration: underline;
            text-underline-offset: 3px;
        }
        /* User menu dropdown - hiển thị khi hover */
        .user-menu-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            padding: 0.25rem 0 0.5rem 0;
        }
        .user-menu-wrap:hover .user-menu-dropdown { display: block; }
        .user-menu-dropdown {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            margin-top: 0;
            padding-top: 12px;
            min-width: 180px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1050;
            overflow: visible;
        }
        .user-menu-dropdown::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 11px solid transparent;
            border-right: 11px solid transparent;
            border-bottom: 11px solid #dee2e6;
        }
        .user-menu-dropdown::after {
            content: '';
            position: absolute;
            top: -9px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 10px solid transparent;
            border-right: 10px solid transparent;
            border-bottom: 10px solid #fff;
        }
        .user-menu-dropdown a {
            display: block;
            padding: 0.6rem 1rem;
            color: #212529 !important;
            text-decoration: none;
            font-size: 0.95rem;
            border: none;
            transition: background 0.15s;
        }
        .user-menu-dropdown a,
        .user-menu-dropdown a:visited,
        .navbar-top-row .user-menu-dropdown a {
            color: #212529 !important;
        }
        .user-menu-dropdown a:hover {
            background: #fff5f5;
            color: #dc3545 !important;
        }
        .user-menu-dropdown a:not(:last-child) {
            border-bottom: 1px solid #eee;
        }
        .navbar-top {
            width: 100%;
            padding: 0.5rem 0;
        }
        .navbar-top .navbar-row {
            display: flex;
            align-items: center;
            gap: 1rem;
            width: 100%;
        }
        .navbar-top .navbar-brand-wrap {
            flex-shrink: 0;
        }
        .navbar-top .navbar-spacer {
            flex: 1;
            min-width: 0.5rem;
        }
        .navbar-top .navbar-search-wrap {
            flex-shrink: 0;
            width: 100%;
            max-width: 520px;
            margin: 0 0.5rem;
        }
        @media (max-width: 991px) {
            .navbar-top .navbar-row { flex-wrap: wrap; }
            .navbar-top .navbar-spacer { display: none; }
            .navbar-top .navbar-search-wrap { order: 3; width: 100%; max-width: none; margin: 0.5rem 0 0; }
        }
        .navbar-top .navbar-brand,
        .navbar-top .nav-link {
            color: #fff !important;
        }
        .navbar-brand-logo {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            color: #fff !important;
            font-size: 2.1rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            padding-top: 0.25rem;
        }
        .navbar-brand-logo:hover {
            color: #fff !important;
            text-decoration: none;
            opacity: 0.95;
        }
        .navbar-brand-logo .navbar-brand-icon {
            width: 2.6rem;
            height: 2.6rem;
            margin-right: 0.5rem;
            flex-shrink: 0;
        }
        .navbar-brand-logo .navbar-brand-icon svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        .navbar-top .nav-link.active {
            font-weight: 600;
            text-decoration: underline;
        }
        .navbar-search-wrap.has-dropdown {
            position: relative;
        }
        .search-history-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            margin-top: 4px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-height: 280px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
        }

        /* Search dropdown: history + typeahead suggestions */
        .search-history-dropdown.show {
            display: block;
        }
        .search-history-dropdown .dropdown-title {
            padding: 0.5rem 1rem;
            font-size: 0.8rem;
            color: #6c757d;
            border-bottom: 1px solid #dee2e6;
        }
        .search-history-dropdown .dropdown-item {
            display: block;
            width: 100%;
            padding: 0.6rem 1rem;
            color: #212529;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.95rem;
        }
        .search-history-dropdown .dropdown-item:hover {
            background: #f8f9fa;
        }
        .search-history-dropdown .history-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 1rem;
            color: #212529;
            font-size: 0.95rem;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        .search-history-dropdown .history-item-row:hover {
            background: #f8f9fa;
        }
        .search-history-dropdown .history-item-row .history-item-text {
            flex: 1;
            min-width: 0;
        }
        .search-history-dropdown .history-item-row .history-item-delete {
            color: #dc3545;
            font-size: 0.9rem;
            margin-left: 0.5rem;
            flex-shrink: 0;
            cursor: pointer;
        }
        .search-history-dropdown .history-item-row .history-item-delete:hover {
            text-decoration: underline;
        }
        .search-history-dropdown .dropdown-item-clear {
            color: #6c757d;
            font-size: 0.85rem;
            border-top: 1px solid #dee2e6;
        }
        .search-history-dropdown .suggestion-item-row {
            width: 100%;
            border: 0;
            background: transparent;
            text-align: left;
            padding: 0.55rem 0.9rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            cursor: pointer;
        }
        .search-history-dropdown .suggestion-item-row:hover,
        .search-history-dropdown .suggestion-item-row.is-active {
            background: #fff5f5;
        }
        .search-history-dropdown .suggestion-item-main {
            min-width: 0;
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }
        .search-history-dropdown .suggestion-item-name {
            font-size: 0.94rem;
            color: #1f2937;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 360px;
        }
        .search-history-dropdown .suggestion-item-price {
            font-size: 0.82rem;
            color: #dc3545;
            font-weight: 700;
            margin-left: 0.5rem;
            flex-shrink: 0;
        }

        .navbar-search-wrap {
            position: relative;
            width: 100%;
        }
        .navbar-search-wrap .form-control {
            border-radius: 999px;
            padding-left: 1.25rem;
            padding-right: 5rem;
            height: 44px;
            font-size: 1rem;
            border-color: #dee2e6;
        }
        .navbar-search-wrap .form-control:focus {
            outline: none !important;
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .navbar-search-inner {
            display: flex;
            align-items: center;
            position: relative;
            border: 1px solid #dee2e6;
            border-radius: 999px;
            background: #fff;
            overflow: hidden;
            transition: border-color 0.15s, box-shadow 0.15s;
        }
        .navbar-search-inner:focus-within {
            outline: none;
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .navbar-search-inner .form-control {
            flex: 1;
            min-width: 0;
            border: none !important;
            border-radius: 0;
            padding-left: 0.75rem;
            padding-right: 3rem;
        }
        .navbar-search-inner .form-control:focus {
            box-shadow: none !important;
        }
        .navbar-search-wrap .btn-search-inside {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            padding: 0;
            border: none;
            background: transparent;
            color: #666;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .navbar-search-wrap .btn-search-inside:hover {
            background: rgba(0,0,0,0.06);
            color: #333;
        }
        .navbar-search-wrap .btn-search-inside svg {
            width: 18px;
            height: 18px;
        }
        .navbar-cart-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            position: relative;
            flex-shrink: 0;
        }
        .navbar-cart-link:hover {
            color: #fff;
            background: rgba(255,255,255,0.15);
        }
        .navbar-cart-link svg {
            width: 24px;
            height: 24px;
        }
        .navbar-cart-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            min-width: 18px;
            height: 18px;
            padding: 0 5px;
            font-size: 0.7rem;
            font-weight: 700;
            line-height: 18px;
            text-align: center;
            background: #fff;
            color: #dc3545;
            border-radius: 999px;
        }

        footer.bg-novashop {
            background: #c62828;
            color: #fff;
        }

        .btn-view-detail {
            padding: 0.6rem 1.5rem;
            border-radius: 999px;
            font-weight: 500;
        }
        .btn-primary.btn-view-detail {
            background: #dc3545;
            border-color: #dc3545;
        }
        .btn-primary.btn-view-detail:hover {
            background: #c82333;
            border-color: #bd2130;
        }
        .btn-outline-primary.btn-view-detail {
            color: #dc3545;
            border-color: #dc3545;
        }
        .btn-outline-primary.btn-view-detail:hover {
            background: #dc3545;
            color: #fff;
        }

        .product-card-actions {
            gap: 0.5rem;
            justify-content: space-between;
        }
        .product-card-add-form {
            min-width: 0;
            margin-left: auto;
        }
        .product-card-actions .btn-view-detail {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
        .product-card-img {
            height: 180px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8f9fa;
        }
        .product-card-img img {
            max-height: 100%;
            max-width: 100%;
            object-fit: contain;
        }
        .product-card-content {
            margin-bottom: 0.35rem;
        }
        .product-card-title {
            height: 2.6em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            line-height: 1.3;
            margin-bottom: 0.4rem;
            font-size: 1rem;
            font-weight: 700;
            color: #0d0d0d;
        }
        .product-card-desc {
            height: 4.05em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            line-height: 1.35;
            margin-bottom: 0;
            font-weight: 500;
            color: #5c5c5c;
        }
        .product-card-category {
            font-weight: 500;
            color: #0d0d0d;
        }
        .product-card-price-old {
            font-size: 0.85rem;
            color: #888;
            text-decoration: line-through;
            margin-right: 0.35rem;
        }
        .product-card-price-new {
            font-size: 1.15rem;
            font-weight: 700;
            color: #dc3545;
        }
        /* Sidebar - danh mục bên trái */
        .products-with-sidebar {
            display: flex;
            gap: 1rem;
            align-items: flex-start;
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
        }
        /* Trang welcome không sidebar */
        .products-with-sidebar.products-no-sidebar {
            max-width: 1140px;
        }
        /* Trang có sidebar: container rộng hơn */
        .products-container-wide {
            max-width: 1320px !important;
        }
        /* Trang welcome không sidebar */
        .products-container-narrow {
            max-width: 1140px !important;
        }
        .products-sidebar {
            width: 220px;
            min-width: 220px;
            flex-shrink: 0;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0.0625rem 0.125rem rgba(0,0,0,0.08);
            overflow: hidden;
        }
        .products-sidebar-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.9rem 1rem;
            font-size: 0.95rem;
            font-weight: 700;
            color: #212529;
            background: #fff;
            border-bottom: 1px solid #eee;
        }
        .products-sidebar-title svg {
            width: 1.1rem;
            height: 1.1rem;
            color: #666;
        }
        .products-sidebar-title-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #212529;
            text-decoration: none;
        }
        .products-sidebar-title-link:hover {
            color: #dc3545;
        }
        .products-sidebar-list {
            padding: 0.5rem 0;
        }
        .products-sidebar-list a {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.6rem 1rem;
            color: #212529;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.15s, color 0.15s;
        }
        .products-sidebar-list a:hover {
            background: #fff5f5;
            color: #dc3545;
        }
        .products-sidebar-list a.active {
            color: #dc3545;
            font-weight: 600;
        }
        .products-sidebar-list a.active::after {
            content: '';
            width: 0;
            height: 0;
            border-top: 5px solid transparent;
            border-bottom: 5px solid transparent;
            border-left: 5px solid #dc3545;
        }
        .products-sidebar-price {
            padding: 1rem;
            border-top: 1px solid #eee;
        }
        .products-sidebar-price-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 0.75rem;
        }
        .products-sidebar-price-form {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        .products-sidebar-price-inputs {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .products-sidebar-price-inputs .form-control {
            flex: 1;
            padding: 0.5rem 0.6rem;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
            border-radius: 6px;
        }
        .products-sidebar-price-inputs .form-control:focus {
            outline: none !important;
            border-color: #dc3545 !important;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
        }
        .products-sidebar-price-sep {
            color: #999;
            flex-shrink: 0;
        }
        .products-sidebar-price-btn {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
        }
        .products-sidebar-price-btn:hover {
            background: #c82333;
            border-color: #bd2130;
            color: #fff;
        }
        /* Sidebar - Thương hiệu */
        .products-sidebar-brands {
            padding: 1rem;
            border-top: 1px solid #eee;
        }
        .products-sidebar-brands-list {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .products-sidebar-brand-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0.75rem;
            color: #212529;
            text-decoration: none;
            font-size: 0.9rem;
            border-radius: 6px;
            transition: background 0.15s, color 0.15s;
        }
        .products-sidebar-brand-item:hover {
            background: #fff5f5;
            color: #dc3545;
        }
        .products-sidebar-brand-item.active {
            background: #fff5f5;
            color: #dc3545;
            font-weight: 600;
        }
        .products-sidebar-brand-item .brand-check {
            width: 1.1rem;
            flex-shrink: 0;
            color: #dc3545;
            font-weight: 700;
        }
        .products-sidebar-brand-item .brand-logo-thumb {
            width: 24px;
            height: 24px;
            object-fit: contain;
            flex-shrink: 0;
        }
        /* Brands grid - đầu trang category */
        .brands-section {
            background: #fff;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 0.0625rem 0.125rem rgba(0,0,0,0.08);
        }
        .brands-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #212529;
        }
        .brands-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }
        @media (min-width: 576px) {
            .brands-grid { grid-template-columns: repeat(4, 1fr); }
        }
        @media (min-width: 768px) {
            .brands-grid { grid-template-columns: repeat(6, 1fr); }
        }
        /* Carousel thương hiệu (một dòng, mũi tên, không scrollbar) */
        .brands-track-wrap { position: relative; }
        .brands-track {
            display: flex;
            flex-wrap: nowrap;
            gap: 0.75rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding: 0.25rem 44px;
        }
        .brands-track::-webkit-scrollbar { display: none; }
        .brands-track-item.brands-grid-item {
            flex: 0 0 auto;
            width: 100px;
            min-width: 100px;
            max-width: 100px;
        }
        @media (min-width: 576px) {
            .brands-track-item.brands-grid-item { width: 108px; min-width: 108px; max-width: 108px; }
        }
        .brands-track-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #dee2e6;
            box-shadow: 0 1px 4px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 5;
            cursor: pointer;
            color: #333;
            font-size: 1.55rem;
            line-height: 1;
            transition: background 0.2s, color 0.2s, border-color 0.2s;
        }
        .brands-track-arrow:hover { background: #c62828; color: #fff; border-color: #c62828; }
        .brands-track-arrow:focus,
        .brands-track-arrow:focus-visible { outline: none !important; border-color: #c62828 !important; box-shadow: 0 0 0 2px rgba(198,40,40,0.35); }
        .brands-track-arrow.prev { left: 0; }
        .brands-track-arrow.next { right: 0; }
        .brands-grid-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 0.5rem;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            text-decoration: none;
            color: #212529;
            transition: all 0.15s;
        }
        .brands-grid-item:hover {
            border-color: #dc3545;
            background: #fff5f5;
            color: #dc3545;
        }
        .brands-grid-item.active {
            border-color: #dc3545;
            background: #fff5f5;
            color: #dc3545;
        }
        .brands-grid-logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            margin-bottom: 0.35rem;
        }
        .brands-grid-name {
            font-size: 0.8rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.2;
        }
        .products-main {
            flex: 1;
            min-width: 0;
        }
        .products-no-sidebar .products-main {
            flex: 1 1 100%;
            max-width: 100%;
        }
        .products-sort-bar {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.75rem 0;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .products-sort-bar .sort-label {
            font-size: 0.9rem;
            color: #666;
            margin-right: 0.25rem;
        }
        .products-sort-btn {
            padding: 0.4rem 0.9rem;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #fff;
            color: #212529;
            text-decoration: none;
            transition: all 0.15s;
        }
        .products-sort-btn:hover {
            border-color: #dc3545;
            color: #dc3545;
        }
        .products-sort-btn.active {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .products-sort-price {
            margin-left: auto;
            position: relative;
        }
        .products-sort-price select {
            padding: 0.4rem 1.75rem 0.4rem 0.75rem;
            font-size: 0.9rem;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            background: #fff;
            color: #212529;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23666' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
        }
        .products-sort-price select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        @media (max-width: 767px) {
            .products-with-sidebar { flex-direction: column; }
            .products-sidebar { width: 100%; min-width: 0; }
        }
        /* Trang chủ - DANH MỤC */
        .home-categories-section {
            max-width: 1140px;
            margin-left: auto;
            margin-right: auto;
            background: #fff;
            border-radius: 8px;
            padding: 1rem 0;
            box-shadow: 0 0.0625rem 0.125rem rgba(0,0,0,0.08);
        }
        .home-categories-title {
            font-size: 1.1rem;
            color: #212529;
            padding: 0 1rem;
        }
        .home-categories-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.5rem;
            padding: 0 1rem;
        }
        @media (min-width: 576px) {
            .home-categories-grid { grid-template-columns: repeat(6, 1fr); }
        }
        @media (min-width: 768px) {
            .home-categories-grid { grid-template-columns: repeat(8, 1fr); }
        }
        .home-categories-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #212529;
            padding: 0.75rem 0.5rem;
            background: #fff;
            border-radius: 8px;
            transition: background 0.15s, color 0.15s;
        }
        .home-categories-item:hover {
            background: #fff5f5;
            color: #dc3545;
        }
        .home-categories-icon {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            flex-shrink: 0;
        }
        .home-categories-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .home-categories-icon-placeholder {
            font-size: 1.25rem;
            font-weight: 700;
            color: #adb5bd;
        }
        .home-categories-name {
            font-size: 0.8rem;
            text-align: center;
            line-height: 1.25;
            font-weight: 500;
        }
        /* Trang tất cả danh mục - lưới danh mục */
        .all-categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 1.5rem;
        }
        .all-categories-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #212529;
            padding: 1rem 0.5rem;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 12px;
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .all-categories-item:hover {
            border-color: #dc3545;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.15);
            color: #212529;
        }
        .all-categories-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            overflow: hidden;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.75rem;
        }
        .all-categories-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .all-categories-icon-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #adb5bd;
        }
        .all-categories-name {
            font-size: 0.9rem;
            font-weight: 500;
            text-align: center;
            line-height: 1.3;
        }
        .breadcrumb-item + .breadcrumb-item::before { content: '›'; }
        .breadcrumb-item a { color: #dc3545; }
        .breadcrumb-item a:hover { text-decoration: underline; }
        /* Div bọc danh mục - nền trắng, mép trái/phải thẳng hàng với lưới sản phẩm (cùng canh với .row) */
        .categories-wrapper {
            background: #fff;
            margin-left: -15px;
            margin-right: -15px;
            padding: 1.25rem 15px;
            border-radius: 0.5rem;
            box-shadow: 0 0.0625rem 0.125rem rgba(0,0,0,0.05);
        }
        /* Danh mục (categories) - dưới header, trên sản phẩm */
        .categories-section {
            width: 100%;
        }
        .categories-section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #212529;
            letter-spacing: 0.02em;
            text-align: center;
        }
        .categories-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem 0.75rem;
            justify-content: space-between;
            width: 100%;
        }
        .category-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 7.5rem;
            min-height: 6.5rem;
            padding: 0.75rem 0.5rem;
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            color: #212529;
            text-decoration: none;
            text-align: center;
            transition: box-shadow 0.2s, border-color 0.2s;
        }
        .category-item:hover {
            border-color: #dc3545;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.15);
            color: #212529;
        }
        .category-item.active {
            border-color: #dc3545;
            background: #fff5f5;
            color: #c82333;
        }
        .category-item-icon {
            width: 2.75rem;
            height: 2.75rem;
            margin-bottom: 0.4rem;
            color: #6c757d;
        }
        .category-item.active .category-item-icon {
            color: #dc3545;
        }
        .category-item-icon svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        .category-item-icon-img {
            overflow: hidden;
            border-radius: 4px;
        }
        .category-item-icon-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .category-item-icon-placeholder {
            display: block;
            width: 100%;
            height: 100%;
            background: #e9ecef;
            border-radius: 4px;
        }
        .category-item-name {
            font-size: 0.8rem;
            font-weight: 500;
            line-height: 1.2;
            word-break: break-word;
        }
        /* Phân trang: căn giữa, màu đỏ */
        main .pagination {
            justify-content: center;
            flex-wrap: wrap;
        }
        main .pagination .page-link {
            color: #dc3545;
            border-color: #dc3545;
            background: #fff;
        }
        main .pagination .page-link:hover {
            color: #fff;
            background: #dc3545;
            border-color: #dc3545;
        }
        main .pagination .page-item.active .page-link {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        main .pagination .page-item.disabled .page-link {
            color: #dc3545;
            border-color: #dee2e6;
            background: #fff;
            opacity: 0.6;
        }
        main .pagination .page-link {
            padding: 0.5rem 0.85rem;
            font-size: 1rem;
        }
        #user-livechat {
            position: fixed;
            right: 1rem;
            bottom: 5.25rem;
            z-index: 1060;
        }
        #user-chat-toggle {
            min-width: 108px;
            height: 54px;
            border-radius: 999px;
            font-size: 0.95rem;
            font-weight: 600;
            box-shadow: 0 10px 24px rgba(220, 53, 69, 0.28);
            background: #dc3545;
            border-color: #dc3545;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.1rem;
        }
        #user-chat-toggle .chat-unread-badge {
            position: absolute;
            top: -6px;
            right: -4px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid #dc3545;
            color: #dc3545;
            font-weight: 800;
            font-size: 0.72rem;
            line-height: 20px;
            text-align: center;
            display: none;
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.2);
        }
        #user-chat-popup {
            width: min(370px, calc(100vw - 1.5rem));
            max-height: 70vh;
            border-radius: 22px;
            border: 1px solid #f4c7cd;
            overflow: hidden;
            display: none;
            flex-direction: column;
        }
        #user-chat-popup .card-header {
            background: #dc3545;
            color: #fff;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            border-top-left-radius: 22px;
            border-top-right-radius: 22px;
        }
        #user-chat-messages {
            padding: 0.8rem;
            background: #fff8f8;
            overflow-y: auto;
            min-height: 260px;
            max-height: calc(70vh - 110px);
        }
        #user-chat-messages .msg-row {
            margin-bottom: 0.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }
        #user-chat-messages .msg-row > small {
            color: #7a2b32 !important;
            font-weight: 600;
            line-height: 1.2;
            padding: 0 0.38rem;
            margin-bottom: 0.08rem;
        }
        #user-chat-messages .msg-bubble {
            max-width: 85%;
            padding: 0.55rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.92rem;
            line-height: 1.35;
            word-break: break-word;
        }
        #user-chat-messages .msg-row.user-msg { align-items: flex-end; }
        #user-chat-messages .msg-row.user-msg > small { margin-right: 0.1rem; }
        #user-chat-messages .msg-row.user-msg .msg-bubble {
            background: #dc3545;
            color: #fff;
            border-bottom-right-radius: 0.45rem;
        }
        #user-chat-messages .msg-row.admin-msg { align-items: flex-start; }
        #user-chat-messages .msg-row.admin-msg > small { margin-left: 0.1rem; }
        #user-chat-messages .msg-row.admin-msg .msg-bubble {
            background: #ffffff;
            color: #721c24;
            border: 1px solid #f1ccd1;
            border-bottom-left-radius: 0.45rem;
        }
        #user-chat-input {
            border-radius: 999px 0 0 999px;
            border-color: #f1ccd1;
            height: 42px;
        }
        #user-chat-send-btn {
            border-radius: 0 999px 999px 0;
            background: #dc3545;
            border-color: #dc3545;
            font-weight: 600;
            padding: 0 1rem;
        }
        #user-chat-send-btn:hover {
            background: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <header>
    <nav class="navbar navbar-main navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <div class="navbar-top-row d-none d-md-flex">
                <nav class="navbar-top-row-left navbar-top-row-nav" aria-label="Điều hướng nhanh">
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.dashboard') }}">Quản trị</a>
                        @endif
                    @endauth
                    <a href="{{ route('welcome') }}">Trang chủ</a>
                    <a href="{{ route('ai-chat.index') }}">Trợ lý AI</a>
                    <a href="{{ route('all.categories') }}">Danh mục</a>
                    <a href="{{ url('/#welcome-flash-section') }}">Flash Sale</a>
                    @auth
                        <a href="{{ route('orders.index') }}">Đơn của tôi</a>
                    @else
                        <a href="{{ route('login') }}">Theo dõi đơn</a>
                    @endauth
                    <a href="{{ url('/#site-footer') }}">Liên hệ</a>
                </nav>
                <div class="navbar-top-row-right">
                    @guest
                    <a href="{{ route('login') }}">Đăng nhập</a>
                    <a href="{{ route('register') }}">Đăng ký</a>
                    @else
                    <div class="user-menu-wrap">
                        <x-user-avatar :user="auth()->user()" :size="28" class="rounded-circle" extra-style="border: 2px solid rgba(255,255,255,0.8);" />
                        <span class="text-white">{{ auth()->user()->name }}</span>
                        <div class="user-menu-dropdown">
                            <a href="{{ route('profile') }}">Quản lý tài khoản</a>
                            @if(!auth()->user()->is_admin)
                            <a href="{{ route('orders.index') }}">Đơn mua</a>
                            <a href="{{ route('addresses.index') }}">Sổ địa chỉ</a>
                            @endif
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Đăng xuất</a>
                        </div>
                    </div>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
                    @endguest
                </div>
            </div>
        </div>
        <div class="navbar-top w-100">
            <div class="container">
                <div class="navbar-row">
                    <div class="navbar-brand-wrap">
                        <a class="navbar-brand navbar-brand-logo py-0 mb-0" href="{{ url('/') }}" title="Về trang chủ">
                            <span class="navbar-brand-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                            </span>
                            <span class="navbar-brand-text">NovaShop</span>
                        </a>
                    </div>
                    <div class="navbar-spacer" aria-hidden="true"></div>
                    <div class="navbar-search-wrap has-dropdown">
                        <form action="{{ route('search') }}" method="GET" id="search-form">
                            <div class="navbar-search-inner">
                                <input type="text" name="q" id="search-input" class="form-control" placeholder="Tìm sản phẩm..." value="{{ request('q') }}" aria-label="Tìm kiếm" autocomplete="off">
                                <button type="submit" class="btn-search-inside" aria-label="Tìm kiếm">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                                </button>
                            </div>
                        </form>

                        <div class="search-history-dropdown" id="search-history-dropdown" role="listbox">
                            <div id="search-history-section">
                                <div class="dropdown-title">Lịch sử tìm kiếm</div>
                                <div id="search-history-list"></div>
                                <button type="button" class="dropdown-item dropdown-item-clear" id="search-history-clear">Xóa lịch sử</button>
                            </div>
                            <div id="search-suggest-section" style="display:none;">
                                <div class="dropdown-title">Gợi ý sản phẩm</div>
                                <div id="search-suggest-list"></div>
                            </div>
                        </div>
                    </div>
                    @auth
                    <a href="{{ route('wishlist.index') }}" class="navbar-cart-link mr-1" title="Yêu thích">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        @if(($navWishlistCount ?? 0) > 0)
                        <span class="navbar-cart-badge">{{ ($navWishlistCount ?? 0) > 99 ? '99+' : $navWishlistCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('compare.index') }}" class="navbar-cart-link mr-1" title="So sánh">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="7" height="14" rx="1"/><rect x="14" y="5" width="7" height="14" rx="1"/><path d="M10 9h4M10 15h4"/></svg>
                        @if(($navCompareCount ?? 0) > 0)
                        <span class="navbar-cart-badge">{{ $navCompareCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('stock-alerts.index') }}" class="navbar-cart-link mr-1" title="Thông báo có hàng">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                        @if(($navStockAlertUnread ?? 0) > 0)
                        <span class="navbar-cart-badge">{{ ($navStockAlertUnread ?? 0) > 99 ? '99+' : $navStockAlertUnread }}</span>
                        @endif
                    </a>
                    @endauth
                    <a href="{{ auth()->check() ? route('cart.index') : route('login') }}" class="navbar-cart-link" title="Giỏ hàng">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        @auth
                        @php $cartCount = auth()->user()->cart?->items()->sum('quantity') ?? 0; @endphp
                        @if($cartCount > 0)
                        <span class="navbar-cart-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                        @endif
                        @endauth
                    </a>
                    <div class="navbar-spacer d-none d-lg-block" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </nav>
    </header>

    @php
        $successMessage = session()->pull('success');
        $errorMessage = session()->pull('error');
        $infoMessage = session()->pull('info');
    @endphp
    @if ($successMessage || $errorMessage || $infoMessage)
    <div class="alert-toast-container">
        @if ($successMessage)
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $successMessage }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
        @if ($infoMessage)
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ $infoMessage }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
        @if ($errorMessage)
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ $errorMessage }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
        @endif
    </div>
    @endif

    <main class="py-4">
        <div class="@yield('containerClass', 'container')">
            @yield('content')
        </div>
    </main>

    @auth
    <div id="user-livechat">
        <button id="user-chat-toggle" class="btn btn-danger" type="button" aria-label="Mở chat hỗ trợ">
            Chat
            <span id="user-chat-unread-badge" class="chat-unread-badge">0</span>
        </button>
        <div id="user-chat-popup" class="card shadow-lg mt-2">
            <div class="card-header">
                <span>Hỗ trợ khách hàng</span>
                <button id="user-chat-close" class="btn btn-sm btn-outline-light rounded-pill px-3" type="button">Đóng</button>
            </div>
            <div id="user-chat-messages" class="card-body p-0">
                <div class="text-center text-muted py-3"><small>Đang tải lịch sử...</small></div>
            </div>
            <div class="card-footer bg-white">
                <div class="input-group">
                    <input type="text" id="user-chat-input" class="form-control" placeholder="Nhập tin nhắn..." autocomplete="off">
                    <div class="input-group-append">
                        <button id="user-chat-send-btn" class="btn btn-danger" type="button">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endauth

    <footer class="bg-novashop py-4 mt-4" id="site-footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-left">
                    <a href="{{ url('/') }}" class="text-white">NovaShop</a>
                </div>
                <div class="col-md-6 text-center text-md-right small">
                    &copy; {{ date('Y') }} NovaShop. Tất cả quyền được bảo lưu.
                </div>
            </div>
        </div>
    </footer>

    @include('partials.ai-chat-widget')

    <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="globalConfirmModalLabel">Xác nhận</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="globalConfirmModalBody">Bạn có chắc muốn thực hiện thao tác này?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="globalConfirmModalOk">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script src="https://unpkg.com/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <script src="{{ asset('js/image-preview.js') }}"></script>
    <script>
        $(function() {
            setTimeout(function() { $('.alert').alert('close'); }, 3000);
        });
        (function() {
            var pendingResolve = null;
            function cleanupResolve(value) {
                if (pendingResolve) {
                    pendingResolve(value);
                    pendingResolve = null;
                }
            }
            window.bsConfirm = function(message) {
                return new Promise(function(resolve) {
                    var modal = document.getElementById('globalConfirmModal');
                    var body = document.getElementById('globalConfirmModalBody');
                    var okBtn = document.getElementById('globalConfirmModalOk');
                    if (!modal || !body || !okBtn || typeof $ === 'undefined' || !$.fn.modal) {
                        resolve(window.confirm(message || 'Bạn có chắc muốn thực hiện thao tác này?'));
                        return;
                    }
                    body.textContent = message || 'Bạn có chắc muốn thực hiện thao tác này?';
                    pendingResolve = resolve;
                    okBtn.onclick = function() {
                        cleanupResolve(true);
                        $('#globalConfirmModal').modal('hide');
                    };
                    $('#globalConfirmModal')
                        .off('hidden.bs.modal.globalConfirm')
                        .on('hidden.bs.modal.globalConfirm', function() {
                            cleanupResolve(false);
                        })
                        .modal('show');
                });
            };
            window.bsConfirmSubmit = function(formEl, message) {
                if (!formEl) return false;
                window.bsConfirm(message).then(function(ok) {
                    if (ok) formEl.submit();
                });
                return false;
            };
        })();
        (function() {
            var STORAGE_KEY = 'novashop_search_history';
            var MAX_ITEMS = 10;
            var input = document.getElementById('search-input');
            var form = document.getElementById('search-form');
            var dropdown = document.getElementById('search-history-dropdown');
            var historySection = document.getElementById('search-history-section');
            var suggestSection = document.getElementById('search-suggest-section');
            var historyListEl = document.getElementById('search-history-list');
            var suggestListEl = document.getElementById('search-suggest-list');
            var clearBtn = document.getElementById('search-history-clear');
            var hideTimeout = null;
            var debounceTimer = null;
            var activeSuggestIndex = -1;
            var currentSuggestions = [];

            function getHistory() {
                try {
                    var raw = localStorage.getItem(STORAGE_KEY);
                    return raw ? JSON.parse(raw) : [];
                } catch (e) { return []; }
            }
            function setHistory(arr) {
                try {
                    localStorage.setItem(STORAGE_KEY, JSON.stringify(arr.slice(0, MAX_ITEMS)));
                } catch (e) {}
            }
            function addToHistory(q) {
                q = (q || '').trim();
                if (!q) return;
                var arr = getHistory();
                arr = arr.filter(function(item) { return item !== q; });
                arr.unshift(q);
                setHistory(arr);
            }
            function removeFromHistory(q) {
                var arr = getHistory().filter(function(item) { return item !== q; });
                setHistory(arr);
                renderHistory();
            }
            function formatPrice(v) {
                var n = Number(v || 0);
                return n.toLocaleString('vi-VN') + '₫';
            }
            function openDropdown() { dropdown.classList.add('show'); }
            function closeDropdown() { dropdown.classList.remove('show'); }
            function showHistorySection() {
                suggestSection.style.display = 'none';
                historySection.style.display = '';
            }
            function showSuggestSection() {
                historySection.style.display = 'none';
                suggestSection.style.display = '';
            }
            function renderHistory() {
                var arr = getHistory();
                historyListEl.innerHTML = '';
                if (arr.length === 0) {
                    historyListEl.innerHTML = '<div class="dropdown-item" style="color:#999;cursor:default;">Chưa có lịch sử</div>';
                    return;
                }
                arr.forEach(function(text) {
                    var row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'history-item-row';
                    row.setAttribute('role', 'option');
                    var spanText = document.createElement('span');
                    spanText.className = 'history-item-text';
                    spanText.textContent = text;
                    var spanDelete = document.createElement('span');
                    spanDelete.className = 'history-item-delete';
                    spanDelete.textContent = 'Xóa';
                    spanDelete.setAttribute('aria-label', 'Xóa mục này');
                    row.appendChild(spanText);
                    row.appendChild(spanDelete);
                    row.addEventListener('mousedown', function(e) {
                        if (e.target === spanDelete || spanDelete.contains(e.target)) return;
                        e.preventDefault();
                        input.value = text;
                        closeDropdown();
                        window.location.href = form.action + '?q=' + encodeURIComponent(text);
                    });
                    spanDelete.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        removeFromHistory(text);
                    });
                    historyListEl.appendChild(row);
                });
            }
            function renderSuggestions(rows) {
                currentSuggestions = rows || [];
                activeSuggestIndex = -1;
                suggestListEl.innerHTML = '';
                if (!currentSuggestions.length) {
                    suggestListEl.innerHTML = '<div class="dropdown-item" style="color:#999;cursor:default;">Không có gợi ý phù hợp</div>';
                    return;
                }
                currentSuggestions.forEach(function(item, index) {
                    var row = document.createElement('button');
                    row.type = 'button';
                    row.className = 'suggestion-item-row';
                    row.setAttribute('data-index', String(index));
                    row.setAttribute('role', 'option');
                    row.innerHTML =
                        '<span class="suggestion-item-main">' +
                            '<span class="suggestion-item-name"></span>' +
                        '</span>' +
                        '<span class="suggestion-item-price"></span>';
                    row.querySelector('.suggestion-item-name').textContent = item.name || '';
                    row.querySelector('.suggestion-item-price').textContent = formatPrice(item.price || 0);
                    row.addEventListener('mousedown', function(e) {
                        e.preventDefault();
                        if (item.url) {
                            window.location.href = item.url;
                            return;
                        }
                        if (item.slug) {
                            window.location.href = '/products/' + encodeURIComponent(item.slug);
                        }
                    });
                    suggestListEl.appendChild(row);
                });
            }
            function updateActiveSuggestion() {
                var rows = suggestListEl.querySelectorAll('.suggestion-item-row');
                rows.forEach(function(row, idx) {
                    if (idx === activeSuggestIndex) row.classList.add('is-active');
                    else row.classList.remove('is-active');
                });
            }
            function performSuggest() {
                var q = (input.value || '').trim();
                if (!q) {
                    showHistorySection();
                    renderHistory();
                    if (getHistory().length > 0) openDropdown(); else closeDropdown();
                    return;
                }
                fetch("{{ route('search.suggestions') }}?q=" + encodeURIComponent(q) + "&limit=8", {
                    headers: { 'Accept': 'application/json' }
                })
                .then(function(res) { return res.ok ? res.json() : { data: [] }; })
                .then(function(payload) {
                    showSuggestSection();
                    renderSuggestions((payload && payload.data) || []);
                    openDropdown();
                })
                .catch(function() {
                    showHistorySection();
                    renderHistory();
                    if (getHistory().length > 0) openDropdown(); else closeDropdown();
                });
            }
            function debounceSuggest() {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(performSuggest, 220);
            }
            function showDropdownByInput() {
                clearTimeout(hideTimeout);
                var q = (input.value || '').trim();
                if (q) {
                    debounceSuggest();
                } else {
                    // Khi chưa nhập gì: chỉ hiển thị lịch sử (không hiện gợi ý sản phẩm).
                    showHistorySection();
                    renderHistory();
                    if (getHistory().length > 0) openDropdown(); else closeDropdown();
                }
            }
            function hideDropdown() {
                hideTimeout = setTimeout(function() {
                    closeDropdown();
                }, 200);
            }

            if (input) {
                input.addEventListener('focus', showDropdownByInput);
                input.addEventListener('blur', hideDropdown);
                input.addEventListener('input', showDropdownByInput);
                input.addEventListener('keydown', function(e) {
                    if (!dropdown.classList.contains('show') || suggestSection.style.display === 'none') return;
                    if (!currentSuggestions.length) return;
                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        activeSuggestIndex = (activeSuggestIndex + 1) % currentSuggestions.length;
                        updateActiveSuggestion();
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        activeSuggestIndex = activeSuggestIndex <= 0 ? currentSuggestions.length - 1 : activeSuggestIndex - 1;
                        updateActiveSuggestion();
                    } else if (e.key === 'Enter' && activeSuggestIndex >= 0 && currentSuggestions[activeSuggestIndex]) {
                        e.preventDefault();
                        var item = currentSuggestions[activeSuggestIndex];
                        if (item.url) window.location.href = item.url;
                    } else if (e.key === 'Escape') {
                        closeDropdown();
                    }
                });
            }
            if (form) {
                form.addEventListener('submit', function() {
                    addToHistory(input.value);
                });
            }
            if (clearBtn) {
                clearBtn.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                    setHistory([]);
                    renderHistory();
                });
            }
            if (dropdown) {
                dropdown.addEventListener('mousedown', function(e) {
                    e.preventDefault();
                });
            }
            if (input && input.value.trim()) {
                addToHistory(input.value.trim());
            }
        })();
        (function() {
            var toggleBtn = document.getElementById('user-chat-toggle');
            if (!toggleBtn) return;
            var popup = document.getElementById('user-chat-popup');
            var closeBtn = document.getElementById('user-chat-close');
            var sendBtn = document.getElementById('user-chat-send-btn');
            var input = document.getElementById('user-chat-input');
            var messagesEl = document.getElementById('user-chat-messages');
            var unreadBadge = document.getElementById('user-chat-unread-badge');
            var authId = {{ (int) auth()->id() }};
            var channel = null;
            var seenMessageIds = new Set();
            function isPopupOpen() {
                return popup && popup.style.display === 'flex';
            }

            function escapeHtml(text) {
                var div = document.createElement('div');
                div.textContent = text || '';
                return div.innerHTML;
            }

            function setUnreadBadge(count) {
                if (!unreadBadge) return;
                var total = Number(count || 0);
                if (total <= 0) {
                    unreadBadge.style.display = 'none';
                    return;
                }
                unreadBadge.textContent = total > 99 ? '99+' : String(total);
                unreadBadge.style.display = 'inline-block';
            }

            function loadUnreadCount() {
                fetch("{{ route('user.chat.unread-count') }}", { headers: { 'Accept': 'application/json' } })
                    .then(function(res) { return res.ok ? res.json() : { unread: 0 }; })
                    .then(function(data) { setUnreadBadge(data.unread || 0); })
                    .catch(function() {});
            }

            function renderMessages(messages) {
                if (!messages || messages.length === 0) {
                    messagesEl.innerHTML = "<div class='text-center text-muted py-3'><small>Bắt đầu cuộc trò chuyện với Admin</small></div>";
                    return;
                }
                var html = '';
                seenMessageIds = new Set();
                messages.forEach(function(msg) {
                    seenMessageIds.add(Number(msg.id));
                    var isMe = Number(msg.sender_id) === authId;
                    var sender = isMe ? 'Bạn' : 'Admin';
                    var rowClass = isMe ? 'user-msg' : 'admin-msg';
                    html += '<div class="msg-row ' + rowClass + '">';
                    html += '<small class="text-muted">' + escapeHtml(sender) + '</small>';
                    html += '<div class="msg-bubble">' + escapeHtml(msg.content || '') + '</div>';
                    html += '</div>';
                });
                messagesEl.innerHTML = html;
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function appendMessage(msg) {
                var msgId = Number(msg.id);
                if (msgId > 0 && seenMessageIds.has(msgId)) {
                    return;
                }
                var isMe = Number(msg.sender_id) === authId;
                if (!isMe && isPopupOpen()) {
                    // If user is already reading chat, refresh to mark incoming message as read.
                    loadMessages();
                    return;
                }
                if (msgId > 0) {
                    seenMessageIds.add(msgId);
                }
                if (messagesEl.querySelector('.text-center')) {
                    messagesEl.innerHTML = '';
                }
                var sender = isMe ? 'Bạn' : 'Admin';
                var rowClass = isMe ? 'user-msg' : 'admin-msg';
                var wrapper = document.createElement('div');
                wrapper.className = 'msg-row ' + rowClass;
                wrapper.innerHTML = '<small class="text-muted">' + escapeHtml(sender) + '</small>'
                    + '<div class="msg-bubble">' + escapeHtml(msg.content || '') + '</div>';
                messagesEl.appendChild(wrapper);
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function loadMessages() {
                fetch("{{ route('user.chat.messages') }}", {
                    headers: { 'Accept': 'application/json' }
                })
                    .then(function(res) { return res.ok ? res.json() : []; })
                    .then(function(messages) {
                        renderMessages(messages);
                        loadUnreadCount();
                    })
                    .catch(function() {
                        messagesEl.innerHTML = "<div class='text-center text-danger py-3'><small>Không tải được tin nhắn.</small></div>";
                    });
            }

            function sendMessage() {
                var message = (input.value || '').trim();
                if (!message) return;
                input.disabled = true;
                sendBtn.disabled = true;
                fetch("{{ route('user.chat.send') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ message: message })
                })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.error) {
                            throw new Error(data.error);
                        }
                        input.value = '';
                        input.disabled = false;
                        sendBtn.disabled = false;
                        input.focus();
                        appendMessage(data);
                    })
                    .catch(function() {
                        input.disabled = false;
                        sendBtn.disabled = false;
                    });
            }

            function initEcho() {
                if (typeof window.Echo === 'undefined' || typeof window.Pusher === 'undefined') {
                    return;
                }
                if (!window.chatEchoInstance) {
                    var csrf = document.querySelector('meta[name="csrf-token"]');
                    var broadcaster = @json(config('broadcasting.default'));
                    if (broadcaster !== 'pusher' && broadcaster !== 'reverb') {
                        return;
                    }
                    var opts;
                    if (broadcaster === 'pusher') {
                        opts = {
                            broadcaster: 'pusher',
                            key: @json(config('broadcasting.connections.pusher.key')),
                            cluster: @json(config('broadcasting.connections.pusher.options.cluster')),
                            wsHost: @json(config('broadcasting.connections.pusher.options.host')),
                            wsPort: @json((int) config('broadcasting.connections.pusher.options.port', 80)),
                            wssPort: @json((int) config('broadcasting.connections.pusher.options.port', 443)),
                            forceTLS: @json((bool) config('broadcasting.connections.pusher.options.useTLS', true)),
                            enabledTransports: ['ws', 'wss'],
                            authEndpoint: '/broadcasting/auth',
                            auth: { headers: { 'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '' } }
                        };
                    } else {
                        opts = {
                            broadcaster: 'reverb',
                            key: @json(config('broadcasting.connections.reverb.key')),
                            wsHost: @json(config('broadcasting.connections.reverb.options.host', '127.0.0.1')),
                            wsPort: @json((int) config('broadcasting.connections.reverb.options.port', 8080)),
                            wssPort: @json((int) config('broadcasting.connections.reverb.options.port', 8080)),
                            forceTLS: @json(config('broadcasting.connections.reverb.options.scheme', 'http') === 'https'),
                            enabledTransports: ['ws', 'wss'],
                            authEndpoint: '/broadcasting/auth',
                            auth: { headers: { 'X-CSRF-TOKEN': csrf ? csrf.getAttribute('content') : '' } }
                        };
                    }

                    var EchoCtor = window.Echo;
                    window.chatEchoInstance = new EchoCtor(opts);
                }
                channel = window.chatEchoInstance.private('chat.user.' + authId);
                channel.listen('.message.sent', function(payload) {
                    appendMessage(payload);
                    loadUnreadCount();
                });
            }

            toggleBtn.addEventListener('click', function() {
                popup.style.display = 'flex';
                toggleBtn.style.display = 'none';
                loadMessages();
            });
            closeBtn.addEventListener('click', function() {
                popup.style.display = 'none';
                toggleBtn.style.display = 'inline-flex';
            });
            sendBtn.addEventListener('click', sendMessage);
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });

            initEcho();
            loadUnreadCount();
        })();
    </script>
    @livewireScripts
    @stack('scripts')
</body>
</html>
