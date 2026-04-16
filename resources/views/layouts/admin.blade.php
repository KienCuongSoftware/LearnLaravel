<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Trang quản trị') - NovaShop</title>
<link rel="icon" href="{{ url('/favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ url('/favicon.ico') }}" type="image/x-icon">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
    @stack('styles')
    <style>
        body { overflow-x: hidden; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 250px;
            min-width: 250px;
            background: linear-gradient(180deg, #c62828, #b71c1c);
            color: #fff;
            flex-shrink: 0;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            overflow-y: auto;
        }
        .admin-sidebar .brand {
            padding: 1.25rem;
            font-size: 1.25rem;
            font-weight: 600;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }
        .admin-sidebar .brand a { color: #fff; text-decoration: none; }
        .admin-sidebar .brand a:hover { color: #fff; opacity: 0.9; }
        .admin-sidebar .nav { flex-direction: column; padding: 1rem 0; }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.9);
            padding: 0.6rem 1.25rem;
            border-left: 3px solid transparent;
        }
        .admin-sidebar .nav-link:hover { color: #fff; background: rgba(255,255,255,0.1); }
        .admin-sidebar .nav-link.active {
            color: #fff;
            background: rgba(255,255,255,0.15);
            border-left-color: #fff;
        }
        .admin-sidebar .nav-divider {
            height: 1px;
            margin: 0.5rem 1rem;
            background: rgba(255,255,255,0.15);
        }
        .admin-main {
            flex: 1;
            margin-left: 250px;
            padding: 1.5rem 2rem;
            background: #ffffff;
            min-height: 100vh;
        }
        .alert-toast-container {
            position: fixed;
            top: 1rem;
            left: 270px;
            right: 1rem;
            z-index: 9999;
            pointer-events: none;
        }
        .alert-toast-container .alert {
            pointer-events: auto;
            max-width: 600px;
            margin-bottom: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .admin-main .card { box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075); }
        .page-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.5rem; }
        .page-header h2 { margin: 0; font-size: 1.5rem; }
        .page-header .admin-toolbar { display: flex; align-items: center; flex-wrap: wrap; gap: 0.75rem; }
        .page-header .admin-search-form .input-group { border-radius: 0.5rem; overflow: hidden; }
        .page-header .admin-search-form .form-control { border-radius: 0.5rem 0 0 0.5rem; border-right: 0; }
        .page-header .admin-search-form .input-group-append .btn { border-radius: 0 0.5rem 0.5rem 0; background: #dc3545; border-color: #dc3545; color: #fff; }
        .page-header .admin-search-form .input-group-append .btn:hover { background: #c82333; border-color: #bd2130; color: #fff; }
        .page-header .admin-toolbar .btn-success { border-radius: 0.5rem; }
        /* Phần tìm kiếm trong toàn bộ trang admin: bo góc + nút đỏ */
        .admin-main .admin-search-form .form-control,
        .admin-main .admin-search-form input.form-control { border-radius: 0.5rem; }
        .admin-main .admin-search-form .input-group .form-control:first-child { border-radius: 0.5rem 0 0 0.5rem; }
        .admin-main .admin-search-form .input-group-append .btn,
        .admin-main .admin-search-form button[type="submit"].btn {
            border-radius: 0 0.5rem 0.5rem 0;
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .admin-main .admin-search-form .input-group-append .btn:hover,
        .admin-main .admin-search-form button[type="submit"].btn:hover {
            background: #c82333;
            border-color: #bd2130;
            color: #fff;
        }
        .admin-main .admin-search-form.d-flex .form-control { border-radius: 0.5rem; }
        .admin-main .admin-search-form.d-flex button[type="submit"].btn {
            border-radius: 0.5rem;
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .admin-main .admin-search-form.d-flex button[type="submit"].btn:hover {
            background: #c82333;
            border-color: #bd2130;
            color: #fff;
        }
        /* Phân trang: căn giữa, màu đỏ */
        .admin-main .card-footer {
            display: flex;
            justify-content: center;
        }
        .admin-main .pagination {
            justify-content: center;
            flex-wrap: wrap;
        }
        .admin-main .pagination .page-link {
            color: #dc3545;
            border-color: #dc3545;
            background: #fff;
        }
        .admin-main .pagination .page-link:hover {
            color: #fff;
            background: #dc3545;
            border-color: #dc3545;
        }
        .admin-main .pagination .page-item.active .page-link {
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
        }
        .admin-main .pagination .page-item.disabled .page-link {
            color: #dc3545;
            border-color: #dee2e6;
            background: #fff;
            opacity: 0.6;
        }
        .admin-main .pagination .page-link {
            padding: 0.5rem 0.85rem;
            font-size: 1rem;
        }
        .admin-main .badge-role {
            min-width: 4.5rem;
            padding: 0.45rem 0.75rem;
            font-size: 0.95rem;
            display: inline-block;
            text-align: center;
        }
        #admin-livechat {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 1060;
        }
        #admin-chat-toggle {
            border-radius: 999px;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.25);
            position: relative;
            background: #dc3545;
            border-color: #dc3545;
            color: #fff;
            font-weight: 600;
            padding: 0.55rem 1rem;
        }
        #admin-chat-toggle .chat-unread-badge {
            position: absolute;
            top: -6px;
            right: -6px;
            min-width: 20px;
            height: 20px;
            padding: 0 5px;
            border-radius: 999px;
            background: #dc3545;
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 20px;
            text-align: center;
            display: none;
        }
        #admin-chat-popup {
            width: min(760px, calc(100vw - 2rem));
            height: min(560px, calc(100vh - 2rem));
            background: #fff;
            border-radius: 22px;
            border: 1px solid #f3ccd1;
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        #admin-chat-body {
            display: flex;
            flex: 1;
            min-height: 0;
        }
        #admin-chat-users {
            width: 230px;
            border-right: 1px solid #f3d9dd;
            overflow-y: auto;
            background: #fff8f8;
        }
        #admin-chat-users .user-item {
            padding: 0.7rem 0.85rem;
            border-bottom: 1px solid #f5e1e4;
            cursor: pointer;
            font-size: 0.92rem;
            border-radius: 12px;
            margin: 0.3rem 0.35rem;
        }
        #admin-chat-users .user-item:hover {
            background: #fff5f5;
        }
        #admin-chat-users .user-item.active {
            background: #ffe8eb;
            color: #b71c1c;
            font-weight: 600;
        }
        #admin-chat-users .user-unread {
            display: inline-block;
            min-width: 18px;
            height: 18px;
            border-radius: 999px;
            padding: 0 5px;
            margin-left: 0.35rem;
            background: #dc3545;
            color: #fff;
            font-size: 0.7rem;
            line-height: 18px;
            text-align: center;
            font-weight: 700;
        }
        #admin-chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 0.8rem;
            background: #fff8f8;
        }
        #admin-chat-messages .msg-row {
            margin-bottom: 0.55rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }
        #admin-chat-messages .msg-row > small {
            color: #7a2b32 !important;
            font-weight: 600;
            line-height: 1.2;
            padding: 0 0.38rem;
            margin-bottom: 0.08rem;
        }
        #admin-chat-messages .msg-row.admin-msg { align-items: flex-end; }
        #admin-chat-messages .msg-row.admin-msg > small { margin-right: 0.1rem; }
        #admin-chat-messages .msg-row.user-msg { align-items: flex-start; }
        #admin-chat-messages .msg-row.user-msg > small { margin-left: 0.1rem; }
        #admin-chat-messages .msg-bubble {
            max-width: 80%;
            padding: 0.55rem 0.8rem;
            border-radius: 1rem;
            font-size: 0.92rem;
            line-height: 1.35;
            word-break: break-word;
        }
        #admin-chat-messages .admin-msg .msg-bubble {
            background: #dc3545;
            color: #fff;
            border-bottom-right-radius: 0.45rem;
        }
        #admin-chat-messages .user-msg .msg-bubble {
            background: #ffffff;
            color: #721c24;
            border: 1px solid #f1ccd1;
            border-bottom-left-radius: 0.45rem;
        }
        #admin-chat-input {
            border-radius: 999px 0 0 999px;
            border-color: #f1ccd1;
            height: 40px;
        }
        #admin-chat-send-btn {
            border-radius: 0 999px 999px 0;
            background: #dc3545;
            border-color: #dc3545;
            font-weight: 600;
            padding: 0 1rem;
        }
        #admin-chat-send-btn:hover {
            background: #c82333;
            border-color: #bd2130;
        }
    </style>
</head>
<body>
    <div class="admin-wrapper">
        <!-- Navbar bên trái - cố định, không đổi khi chuyển trang -->
        <aside class="admin-sidebar">
            <div class="brand">
                <a href="{{ route('admin.dashboard') }}">NovaShop - Quản trị</a>
            </div>
            <nav class="nav">
                <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    Trang quản trị
                </a>
                <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                    Sản phẩm
                </a>
                <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                    Danh mục
                </a>
                <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                    Thương hiệu
                </a>
                <a class="nav-link {{ request()->routeIs('admin.attributes.*') ? 'active' : '' }}" href="{{ route('admin.attributes.index') }}">
                    Thuộc tính
                </a>
                <a class="nav-link {{ request()->routeIs('admin.flash-sales.*') ? 'active' : '' }}" href="{{ route('admin.flash-sales.index') }}">
                    Flash Sale
                </a>
                <a class="nav-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}" href="{{ route('admin.coupons.index') }}">
                    Mã giảm giá
                </a>
                <a class="nav-link {{ request()->routeIs('admin.search-synonyms.*') ? 'active' : '' }}" href="{{ route('admin.search-synonyms.index') }}">
                    Search synonyms
                </a>
                <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    Người dùng
                </a>
                <div class="nav-divider"></div>
                <a class="nav-link {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}" href="{{ route('admin.profile.edit') }}">
                    Thông tin tài khoản
                </a>
                <a class="nav-link" href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                    Đăng xuất
                </a>
                <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </aside>

        @php
            $successMessage = session()->pull('success');
            $errorMessage = session()->pull('error');
            $hasValidationErrors = $errors->any();
        @endphp
        @if ($successMessage || $errorMessage || $hasValidationErrors)
        <div class="alert-toast-container">
            @if ($successMessage)
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ $successMessage }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Đóng"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($errorMessage)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ $errorMessage }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Đóng"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
            @if ($hasValidationErrors)
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Đóng"><span aria-hidden="true">&times;</span></button>
                </div>
            @endif
        </div>
        @endif

        <!-- Nội dung chính - chỉ phần này đổi khi chuyển trang -->
        <main class="admin-main">
            @yield('content')
        </main>
    </div>

    <div class="modal fade" id="globalConfirmModal" tabindex="-1" aria-labelledby="globalConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="globalConfirmModalLabel">Xác nhận</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="globalConfirmModalBody" style="white-space: pre-line;">Bạn có chắc muốn thực hiện thao tác này?</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" id="globalConfirmModalOk">Đồng ý</button>
                </div>
            </div>
        </div>
    </div>

    <div id="admin-livechat">
        <button id="admin-chat-toggle" class="btn btn-danger rounded-pill" type="button">
            Chat khách hàng
            <span id="admin-chat-unread-badge" class="chat-unread-badge">0</span>
        </button>
        <div id="admin-chat-popup" class="card shadow-lg mt-2">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <strong>Hỗ trợ trực tuyến</strong>
                <button id="admin-chat-close" class="btn btn-sm btn-outline-light rounded-pill px-3" type="button">Đóng</button>
            </div>
            <div id="admin-chat-body">
                <div id="admin-chat-users">
                    <div class="p-2 text-center text-muted"><small>Đang tải danh sách...</small></div>
                </div>
                <div id="admin-chat-messages">
                    <div class="text-center mt-5 text-muted">Chọn một khách hàng để xem tin nhắn</div>
                </div>
            </div>
            <div class="card-footer bg-white">
                <div class="input-group">
                    <input type="text" id="admin-chat-input" class="form-control form-control-sm" placeholder="Nhập câu trả lời..." autocomplete="off">
                    <div class="input-group-append">
                        <button id="admin-chat-send-btn" class="btn btn-danger btn-sm" type="button">Gửi</button>
                    </div>
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
        $(function() { setTimeout(function() { $('.alert').alert('close'); }, 3000); });
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
            var toggle = document.getElementById('admin-chat-toggle');
            if (!toggle) return;
            var popup = document.getElementById('admin-chat-popup');
            var closeBtn = document.getElementById('admin-chat-close');
            var usersEl = document.getElementById('admin-chat-users');
            var messagesEl = document.getElementById('admin-chat-messages');
            var input = document.getElementById('admin-chat-input');
            var sendBtn = document.getElementById('admin-chat-send-btn');
            var unreadBadge = document.getElementById('admin-chat-unread-badge');
            var currentUserId = null;
            var usersCache = [];
            var adminId = {{ (int) auth()->id() }};
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

            function renderUsers(users) {
                usersCache = users || [];
                if (!users || users.length === 0) {
                    usersEl.innerHTML = '<div class="p-2 text-center text-muted"><small>Chưa có hội thoại</small></div>';
                    return;
                }
                var html = '';
                users.forEach(function(user) {
                    var activeClass = Number(currentUserId) === Number(user.id) ? 'active' : '';
                    var unreadCount = Number(user.unread_count || 0);
                    var unreadHtml = unreadCount > 0 ? '<span class="user-unread">' + (unreadCount > 99 ? '99+' : unreadCount) + '</span>' : '';
                    html += '<div class="user-item ' + activeClass + '" data-id="' + user.id + '">'
                        + '<span>' + escapeHtml(user.name) + '</span>'
                        + unreadHtml
                        + '</div>';
                });
                usersEl.innerHTML = html;
                Array.prototype.forEach.call(usersEl.querySelectorAll('.user-item'), function(item) {
                    item.addEventListener('click', function() {
                        currentUserId = Number(item.getAttribute('data-id'));
                        renderUsers(users);
                        loadMessages();
                    });
                });
            }

            function renderMessages(messages) {
                if (!messages || messages.length === 0) {
                    messagesEl.innerHTML = '<div class="text-center mt-5 text-muted">Chưa có tin nhắn</div>';
                    return;
                }
                var html = '';
                seenMessageIds = new Set();
                messages.forEach(function(msg) {
                    seenMessageIds.add(Number(msg.id));
                    var isAdmin = Number(msg.sender_id) === adminId;
                    html += '<div class="msg-row ' + (isAdmin ? 'admin-msg' : 'user-msg') + '">';
                    html += '<small class="text-muted">' + (isAdmin ? 'Bạn' : (msg.sender ? escapeHtml(msg.sender.name) : 'Khách hàng')) + '</small>';
                    html += '<div class="msg-bubble">' + escapeHtml(msg.content || '') + '</div>';
                    html += '</div>';
                });
                messagesEl.innerHTML = html;
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function appendMessage(msg) {
                if (!msg) return;
                var msgId = Number(msg.id);
                if (msgId > 0 && seenMessageIds.has(msgId)) {
                    return;
                }
                var isAdmin = Number(msg.sender_id) === adminId;
                var otherUserId = Number(msg.sender_id) === adminId ? Number(msg.receiver_id) : Number(msg.sender_id);
                if (!currentUserId) {
                    currentUserId = otherUserId;
                }
                if (otherUserId !== Number(currentUserId)) {
                    loadUsers();
                    loadUnreadCount();
                    return;
                }
                if (!isAdmin && isPopupOpen()) {
                    // Mark as read immediately when admin is viewing this conversation.
                    loadMessages();
                    return;
                }
                if (msgId > 0) {
                    seenMessageIds.add(msgId);
                }
                if (messagesEl.querySelector('.text-center')) {
                    messagesEl.innerHTML = '';
                }
                var wrapper = document.createElement('div');
                wrapper.className = 'msg-row ' + (isAdmin ? 'admin-msg' : 'user-msg');
                wrapper.innerHTML = '<small class="text-muted">'
                    + (isAdmin ? 'Bạn' : (msg.sender ? escapeHtml(msg.sender.name) : 'Khách hàng'))
                    + '</small><div class="msg-bubble">' + escapeHtml(msg.content || '') + '</div>';
                messagesEl.appendChild(wrapper);
                messagesEl.scrollTop = messagesEl.scrollHeight;
                loadUsers();
                loadUnreadCount();
            }

            function loadUsers() {
                fetch("{{ route('admin.chat.users') }}", { headers: { 'Accept': 'application/json' } })
                    .then(function(res) { return res.ok ? res.json() : []; })
                    .then(function(users) {
                        renderUsers(users);
                        if (!currentUserId && users.length) {
                            currentUserId = Number(users[0].id);
                            renderUsers(users);
                            loadMessages();
                        }
                    });
            }

            function loadUnreadCount() {
                fetch("{{ route('admin.chat.unread-count') }}", { headers: { 'Accept': 'application/json' } })
                    .then(function(res) { return res.ok ? res.json() : { unread: 0 }; })
                    .then(function(data) { setUnreadBadge(data.unread || 0); });
            }

            function loadMessages() {
                if (!currentUserId) return;
                fetch('/admin/chat/messages/' + currentUserId, { headers: { 'Accept': 'application/json' } })
                    .then(function(res) { return res.ok ? res.json() : []; })
                    .then(function(messages) {
                        renderMessages(messages);
                        loadUsers();
                        loadUnreadCount();
                    });
            }

            function sendMessage() {
                var message = (input.value || '').trim();
                if (!message || !currentUserId) return;
                input.disabled = true;
                sendBtn.disabled = true;
                fetch("{{ route('admin.chat.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message,
                        user_id: currentUserId
                    })
                })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data && data.error) throw new Error(data.error);
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
                channel = window.chatEchoInstance.private('chat.user.' + adminId);
                channel.listen('.message.sent', function(payload) {
                    appendMessage(payload);
                });
            }

            toggle.addEventListener('click', function() {
                popup.style.display = 'flex';
                loadUsers();
                if (currentUserId) {
                    loadMessages();
                }
            });
            closeBtn.addEventListener('click', function() {
                popup.style.display = 'none';
            });
            sendBtn.addEventListener('click', sendMessage);
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sendMessage();
                }
            });

            initEcho();
            loadUsers();
            loadUnreadCount();
        })();
    </script>
    @stack('scripts')
</body>
</html>
