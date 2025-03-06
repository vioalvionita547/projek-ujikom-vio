# projek-ujikom-vio
1. login.php
Fungsi:
Digunakan sebagai halaman login untuk autentikasi pengguna sebelum mengakses sistem.

Fitur:

Form login dengan input username dan password.
Sistem autentikasi menggunakan database (dengan mysqli).
Hashing password menggunakan password_verify() untuk keamanan.
Menyimpan sesi pengguna setelah berhasil login.
Menampilkan pesan error jika username atau password salah.
2. register.php
Fungsi:
Digunakan sebagai halaman registrasi untuk pengguna baru.

Fitur:

Form pendaftaran dengan input username, email, dan password.
Hashing password sebelum disimpan ke database (password_hash()).
Memastikan tidak ada duplikasi username.
Menampilkan pesan sukses atau error setelah registrasi.
3. dashboard.php
Fungsi:
Halaman utama setelah pengguna berhasil login.

Fitur:

Menampilkan daftar tugas yang sudah dibuat oleh pengguna.
Memungkinkan pengguna menambah, mengedit, menyelesaikan, atau menghapus tugas.
Menampilkan status tugas (belum selesai atau selesai).
Menggunakan sesi untuk memastikan hanya pengguna yang login yang dapat mengaksesnya.
4. koneksi.php
Fungsi:
File konfigurasi koneksi database.

Fitur:

Berisi kode PHP untuk menghubungkan sistem ke database menggunakan mysqli_connect().
Digunakan di semua halaman yang memerlukan akses database.
5. logic.js
Fungsi:
Mengelola interaksi pengguna dan pengolahan data tugas dengan JavaScript.

Fitur:

Menangani event ketika pengguna mengisi dan mengirim form tambah tugas.
Mengirim data tugas ke add_task.php menggunakan fetch API.
Melakukan validasi input sebelum dikirim.
Menampilkan notifikasi jika tugas berhasil atau gagal ditambahkan.
Me-refresh halaman setelah tugas berhasil ditambahkan​
.
6. edit_task.php
Fungsi:
Memungkinkan pengguna mengedit tugas yang sudah dibuat.

Fitur:

Mengambil data tugas yang ingin diedit dari database.
Form edit tugas dengan input judul, deskripsi, deadline, dan prioritas.
Memperbarui tugas di database setelah perubahan dilakukan.
7. hapus_task.php
Fungsi:
Digunakan untuk menghapus tugas dari sistem.

Fitur:

Menghapus tugas dari database berdasarkan ID.
Menampilkan konfirmasi sebelum menghapus tugas.
Redirect ke dashboard setelah tugas berhasil dihapus.
8. selesaikan_task.php
Fungsi:
Menandai tugas sebagai selesai.

Fitur:

Mengubah status tugas di database dari "belum selesai" menjadi "selesai".
Memungkinkan pengguna melihat daftar tugas yang sudah diselesaikan.
9. logout.php
Fungsi:
Menghapus sesi pengguna dan mengembalikan mereka ke halaman login.

Fitur:

Menggunakan session_destroy() untuk menghapus semua sesi.
Redirect ke halaman login setelah logout.
Kesimpulan
Sistem ini adalah aplikasi manajemen tugas sederhana berbasis web yang memiliki fitur utama: ✅ Autentikasi pengguna (Login & Register).
✅ Manajemen tugas (Tambah, Edit, Hapus, Selesaikan).
✅ Interaksi real-time menggunakan JavaScript (logic.js).
✅ Keamanan dengan hashing password dan sesi.
