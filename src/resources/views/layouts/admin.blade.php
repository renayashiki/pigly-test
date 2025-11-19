<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PiGLy Admin')</title>

    <link rel="stylesheet" href="{{ asset('css/admin/main.css') }}">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:ital,wght@0,400..800;1,400..800&family=Lobster+Two:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

    @yield('styles')
</head>
<body>
    <header>
        <nav class="admin-nav">
            <div class="header-logo">PiGLy</div>
            <div class="header-actions">
                
                {{-- 目標体重設定ボタン (ご要望の線画形状を塗りつぶしに変更) --}}
                <button type="button" class="btn-goal-setting" onclick="window.location='{{ route('goal_setting') }}'">
                    <span class="icon-button-content">
                        {{-- 設定アイコン (ねじマーク) --}}
                        <svg class="icon-header icon-settings" viewBox="0 0 24 24" fill="currentColor" stroke="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            {{-- 中央の穴を空けるため、パスを連結し fill-rule="evenodd" を適用 --}}
                            <path fill-rule="evenodd" d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1.82.33 1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.82-.33zM12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                        </svg>
                        目標体重設定
                    </span>
                </button>
                
                <form method="POST" action="{{ route('logout') }}" class="inline-form">
                    {{-- ログアウトボタン (取っ手付きの扉と矢印のアイコン) --}}
                    @csrf
                    <button type="submit" class="logout-button">
                        <span class="icon-button-content">
                            {{-- ログアウトアイコン --}}
                            <svg class="icon-header icon-log-out" viewBox="0 0 24 24" fill="currentColor" stroke="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                {{-- 扉のフレームと取っ手（白抜き）--}}
                                <path d="M16 9v-4l8 8-8 8v-4h-8v-8h8zM14 2h-12v20h12v-2h-10v-16h10v-2z" fill-rule="evenodd"/>
                            </svg>
                            ログアウト
                        </span>
                    </button>
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