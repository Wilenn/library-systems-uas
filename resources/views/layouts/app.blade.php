<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Library System</title>
</head>
<body>

    <header>
        <h1>Library System</h1>
    </header>

    <nav>
        <a href="{{ route('dashboard') }}">Dashboard</a> |
        <a href="{{ route('books.index') }}">Books</a> |
        <a href="{{ route('categories.index') }}">Categories</a> |
        <a href="{{ route('members.index') }}">Members</a>
        @auth
            @if (auth()->user()->isAdmin())
                | <a href="{{ route('users.index') }}">Users</a>
            @endif
            | {{ auth()->user()->name }} ({{ auth()->user()->role }}) |
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit">Logout</button>
            </form>
        @endauth
    </nav>

    <hr>

    <main>
        @if (session('success'))
            <div id="notif" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; padding:10px 14px; margin-bottom:14px; border-radius:4px;">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div style="background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:10px 14px; margin-bottom:14px; border-radius:4px;">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <hr>

    <footer>
        <p>Library System</p>
    </footer>

    <script>
        // Notifikasi hilang otomatis setelah 4 detik
        const notif = document.getElementById('notif');
        if (notif) {
            setTimeout(() => notif.style.display = 'none', 4000);
        }
    </script>

</body>
</html>