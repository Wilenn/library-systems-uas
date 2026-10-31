// JavaScript minimal: menu mobile, notifikasi otomatis hilang, dan tombol submit
// yang dinonaktifkan saat form diproses (mencegah klik ganda).

// Menu sidebar di layar kecil
const menuToggle = document.getElementById('menu-toggle');
const sidebar = document.getElementById('sidebar');

if (menuToggle && sidebar) {
    const setOpen = (open) => {
        sidebar.classList.toggle('max-lg:hidden', !open);
        menuToggle.setAttribute('aria-expanded', String(open));
    };

    menuToggle.addEventListener('click', () => {
        setOpen(menuToggle.getAttribute('aria-expanded') !== 'true');
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            setOpen(false);
        }
    });
}

// Notifikasi sukses hilang sendiri setelah 5 detik
document.querySelectorAll('[data-autodismiss]').forEach((element) => {
    setTimeout(() => element.remove(), 5000);
});

// Loading state: nonaktifkan tombol submit setelah form lolos validasi browser
const submitButtons = (form) => form.querySelectorAll('button[type="submit"]');

document.addEventListener('submit', (event) => {
    submitButtons(event.target).forEach((button) => {
        button.dataset.label = button.textContent;
        button.disabled = true;
        button.textContent = 'Memproses…';
    });
});

// Saat halaman dipulihkan lewat tombol Back, kembalikan tombol seperti semula
window.addEventListener('pageshow', (event) => {
    if (!event.persisted) {
        return;
    }

    document.querySelectorAll('button[data-label]').forEach((button) => {
        button.disabled = false;
        button.textContent = button.dataset.label;
    });
});
