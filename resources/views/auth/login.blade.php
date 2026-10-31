<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Perpustakaan</title>
</head>

<body>
    <h1>Login Sistem Perpustakaan</h1>
    @if (session('success'))
        <div id="notif" style="background:#d4edda; color:#155724; border:1px solid #c3e6cb; padding:10px 14px; margin-bottom:14px; border-radius:4px;">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div>
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    <form action="{{ route('login.submit') }}" method="POST">
        @csrf
        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <script>
        // Notifikasi hilang otomatis setelah 4 detik
        const notif = document.getElementById('notif');
        if (notif) {
            setTimeout(() => notif.style.display = 'none', 4000);
        }
    </script>
</body>

</html>

