<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PiGLy Admin')</title>

    <link rel="stylesheet" href="{{ asset('css/admin/main.css') }}">
    
    @yield('styles')
</head>
<body>
    <header>
        <nav class="admin-nav">
            <div class="header-logo">PiGLy</div>
            <div class="header-actions">
                <button type="button" class="btn-goal-setting" onclick="window.location='{{ route('goal_setting') }}'">目標体重設定</button>
                
                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    @csrf
                    <button type="submit" class="logout-button">ログアウト</button>
                </form>
            </div>
        </nav>
    </header>
    <main class="admin-content">
        @yield('content')
    </main>
    
    <script>
        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.remove('hidden');
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
            }
        }
    </script>
</body>
</html>