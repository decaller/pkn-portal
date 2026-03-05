# PKN Portal - Rencana Uji Manual Perjalanan Pengguna

Dokumen ini menguraikan langkah-langkah pengujian manual untuk memverifikasi alur perjalanan pengguna utama (user journeys) pada aplikasi PKN Portal. Dokumen ini mencakup skenario biasa (happy paths), kasus ekstrem (edge cases), pengujian multitenancy, dan batasan (hal-hal yang _tidak_ boleh dilakukan oleh pengguna) di panel Publik, User, dan Admin.

---

## 1. Perjalanan Publik (Tanpa Autentikasi)

**Tujuan:** Memastikan pengunjung umum dapat mengakses informasi publik dengan benar.  
**Alasan & Tujuan Fitur:** Memberikan transparansi informasi acara dan berita kepada publik secara luas tanpa mewajibkan akun di awal. Hal ini bertujuan untuk menarik minat calon peserta (marketing) dan memberikan informasi dasar yang dibutuhkan sebelum mereka memutuskan untuk mendaftar.

### 1.1 Mengakses Halaman Utama (Landing Page)

- **Tautan:** [/](/)
- **Biasa:**
    - [ ] Buka URL utama.
    - [ ] Pastikan halaman dimuat dengan benar beserta semua elemen yang diharapkan (header, footer, bagian hero).
- **Kasus Ekstrem:**
    - [ ] Akses menggunakan perangkat seluler untuk memastikan tata letak (layout) responsif.

### 1.2 Menjelajahi Acara (Events) & Berita

- **Tautan:** [/events](/events) atau bagian acara di Halaman Utama
- **Biasa:**
    - [ ] Lihat daftar acara yang tersedia.
    - [ ] Lihat daftar artikel berita yang tersedia.
    - [ ] Klik pada suatu acara untuk membaca detail lengkapnya.
    - [ ] Klik pada item berita untuk membaca detail lengkapnya.
- **Kasus Ekstrem:**
    - [ ] Picu penomoran halaman (pagination) atau filter (jika ada) dan pastikan item yang benar ditampilkan.
    - [ ] Verifikasi tampilan output yang tepat ketika tidak ada acara atau berita yang aktif.

### 1.3 Mencoba Akses yang Dibatasi

- **Biasa:**
    - [ ] Klik tombol "Daftar" (Register) pada suatu acara dari halaman publik.
- **Hasil yang Diharapkan:**
    - [ ] Pengguna harus dialihkan (redirect) dengan aman ke halaman Login atau Registrasi.

### 🚫 Batasan (Hal yang tidak boleh bisa Anda lakukan)

- [ ] Mengakses Dashboard User (`/user`) tanpa login.
- [ ] Mengakses Dashboard Admin (`/admin`) tanpa login.
- [ ] Mendaftar untuk sebuah acara secara langsung tanpa membuat akun / login terlebih dahulu.
- [ ] Mengirim atau melihat sumber daya internal, daftar peserta, atau detail acara yang bersifat rahasia.

---

## 2. Perjalanan User (Pendaftar / Peserta)

**Tujuan:** Memverifikasi bahwa pengguna dapat mendaftar, mengelola organisasi mereka, dan berhasil mendaftar untuk acara.  
**Alasan & Tujuan Fitur:** Mengadopsi sistem _multitenancy_ untuk memungkinkan pendaftaran berbasis organisasi (bukan hanya individu). Tujuannya adalah mempermudah pengelolaan peserta dalam jumlah besar oleh delegasi organisasi, memastikan isolasi data antar organisasi yang ketat, dan memberikan pengalaman mandiri (self-service) dalam proses pendaftaran hingga pembayaran.

### 2.1 Registrasi & Autentikasi

- **Tautan:** [/user/register](/user/register) dan [/user/login](/user/login)
- **Biasa:**
    - [ ] Buka halaman registrasi.
    - [ ] Isi detail yang valid (Nama, Email, Password, dll).
    - [ ] Kirim formulir.
    - [ ] Pastikan pengguna berhasil terdaftar dan masuk (atau menerima email verifikasi jika berlaku).
- **Kasus Ekstrem:**
    - [ ] **Email Duplikat:** Coba mendaftar menggunakan email yang sudah terdaftar. Sistem harus menampilkan pesan error validasi yang jelas.
    - [ ] **Password Lemah:** Masukkan password yang tidak memenuhi persyaratan. Sistem harus menampilkan pesan error.
    - [ ] **Kolom Kosong:** Kirim formulir dengan field wajib (required) dibiarkan kosong. Sistem harus menunjukkan pesan error validasi.

### 2.2 Manajemen Organisasi (Tenancy)

- **Tautan:** [/user/new](/user/new) (Halaman Pendaftaran Organisasi Filament) / Dashboard User
- **Biasa:**
    - [ ] Masuk ke Dashboard User.
    - [ ] Ikuti alur "Buat organisasi baru" (Create a new organization).
    - [ ] Berikan detail yang valid (Nama, Slug, Logo) dan kirim.
    - [ ] Sebagai alternatif, pilih "Bergabung dengan organisasi yang ada" (Join an existing organization).
    - [ ] Cari dan pilih nama/kode organisasi untuk bergabung.
- **Kasus Ekstrem:**
    - [ ] **Organisasi Duplikat:** Coba buat organisasi dengan nama/slug yang sudah digunakan orang lain.
    - [ ] **Bergabung Tidak Valid:** Coba bergabung dengan organisasi yang tidak ada.

### 2.3 Isolasi Tenancy (Hak Akses Antar Organisasi)

- **Tautan:** [/user](/user)
- **Biasa:**
    - [ ] Buat Pengguna A di Organisasi X.
    - [ ] Buat Pengguna B di Organisasi Y.
    - [ ] Pastikan Pengguna A hanya dapat melihat data (peserta/pendaftaran event) milik Organisasi X.
    - [ ] Pastikan Pengguna B hanya dapat melihat data milik Organisasi Y.
- **Kasus Ekstrem:**
    - [ ] **Pengguna Multi-Tenant:** Jika pengguna terdaftar di lebih dari satu organisasi, pastikan mereka dapat beralih ke organisasi lain menggunakan menu pilihan tenant (jika diaktifkan) dan konteks data berubah sesuai organisasi aktif.

### 2.4 Alur Pendaftaran Acara

- **Tautan:** Dashboard User -> Pilih Acara
- **Biasa:**
    - [ ] Pilih acara aktif dari dashboard atau halaman publik.
    - [ ] Klik untuk mendaftar.
    - [ ] Baca instruksi pembayaran (pastikan ditampilkan dengan benar melalui rich text).
    - [ ] Lengkapi formulir pendaftaran utama.
    - [ ] Masuk ke bagian peserta dan tambahkan jumlah peserta yang disyaratkan.
    - [ ] Unggah bukti pembayaran yang valid (gambar/PDF).
    - [ ] Kirim pendaftaran.
    - [ ] Verifikasi bahwa pendaftaran muncul di dashboard dengan status "Menunggu" (Pending) atau "Menunggu Persetujuan" (Awaiting Approval).
- **Kasus Ekstrem:**
    - [ ] **Acara Penuh:** Coba mendaftar pada acara di mana `participants_count` (jumlah peserta) telah mencapai batas maksimal. Harus dicegah atau ada pesan "Penuh".
    - [ ] **Acara Lampau:** Coba mendaftar pada acara yang batas waktu pendaftarannya telah lewat.
    - [ ] **Pendaftaran Ganda:** Coba mendaftar pada acara yang sama dua kali menggunakan akun/organisasi yang sama. Sistem seharusnya mencegah duplikasi pendaftaran utama.
    - [ ] **Melebihi Batas Peserta:** Coba tambahkan peserta individu lebih banyak dibanding limit yang ditentukan per tiket pendaftaran.
    - [ ] **File Pembayaran Tidak Valid:** Unggah format file yang dilarang (mis. `.exe` atau ukuran file melebihi batas).

### 2.5 Widget & Dashboard User

- **Tautan:** [/user](/user)
- **Biasa:**
    - [ ] Login dan masuk ke dashboard.
    - [ ] Pastikan "Welcome Widget" menampilkan status pendaftaran acara terakhir dengan benar.
    - [ ] Gunakan tombol shortcut pada widget untuk dengan cepat menambahkan peserta atau mengunggah pembayaran jika pendaftaran masih belum lengkap.
    - [ ] Pastikan tombol berubah secara dinamis (mis., berubah menjadi tautan "Halaman Acara" setelah pembayaran diverifikasi dan peserta sudah lengkap ditambahkan).
    - [ ] Setelah admin menyetujui pembayaran di sistem, pastikan status langsung berubah menjadi "Disetujui" (Approved) di tampilan pengguna pengguna.

### 🚫 Batasan (Hal yang tidak boleh bisa Anda lakukan)

- [ ] Mengakses Dashboard Admin (`/admin`).
- [ ] Mengubah, menyetujui, atau menghapus verifikasi pembayaran milik user/organisasi lain, atau menyetujui punya Anda sendiri (hanya boleh melihat/mengunggah).
- [ ] Melihat pendaftaran acara milik organisasi atau user lain yang sama sekali berbeda.
- [ ] Memodifikasi detail acara apa pun atau artikel berita.
- [ ] Memanipulasi proses pembayaran untuk mengubah status pendaftaran Anda sendiri jadi 'disetujui'.

---

## 3. Perjalanan Admin

**Tujuan:** Memastikan administrator dapat mengelola konten portal dan menyetujui pendaftaran.  
**Alasan & Tujuan Fitur:** Memberikan kendali pusat bagi penyelenggara untuk melakukan validasi manual terhadap bukti pembayaran (menghindari penipuan), mengelola inventori peserta acara, serta mempublikasikan informasi terkini (berita). Tujuannya adalah menjamin kualitas data pendaftaran dan ketertiban administrasi acara secara keseluruhan.

### 3.1 Autentikasi Admin

- **Tautan:** [/admin/login](/admin/login)
- **Biasa:**
    - [ ] Navigasi ke akses login `/admin`.
    - [ ] Login menggunakan kredensial tingkat administrator yang valid.
    - [ ] Pastikan akses diberikan dan widget dashboard admin terlihat.
- **Kasus Ekstrem:**
    - [ ] **Akses Tidak Sah:** Minta "user" biasa untuk mencoba mengakses `/admin`. Pastikan mereka diblokir (error 403 atau diarahkan ulang/redirect dengan aman).

### 3.2 Manajemen Acara (Events)

- **Tautan:** [/admin/events](/admin/events)
- **Biasa:**
    - [ ] Arahkan ke menu Data Acara (Events).
    - [ ] Buat acara baru, mengisi semua bidang termasuk judul, tanggal, jumlah maksimal peserta, dan rich text instruksi pembayaran.
    - [ ] Edit acara yang sudah ada dan simpan perubahan.
    - [ ] Hapus atau arsipkan acara.
- **Kasus Ekstrem:**
    - [ ] **Tanggal Tidak Valid:** Tetapkan tanggal selesai acara _sebelum_ tanggal mulai. Formulir harus menangkap error ini dan menggagalkan simpan data.
    - [ ] **Modifikasi Acara Berjalan:** Edit acara yang sudah memiliki user mendaftar di dalamnya. Pastikan registrasi yang sudah masuk tetap terhubung dengan baik.

### 3.3 Persetujuan Pembayaran & Pendaftaran

- **Tautan:** [/admin/event-registrations](/admin/event-registrations)
- **Biasa:**
    - [ ] Arahkan ke Registrasi Acara (Event Registrations).
    - [ ] Lihat pendaftaran menunggu (pending) yang baru-baru ini dikirim pengguna.
    - [ ] Periksa file bukti pembayaran yang diunggah.
    - [ ] Jalankan tindakan pendaftaran: Setujui (Approve).
    - [ ] Jalankan tindakan pendaftaran: Tolak (Reject) (mis. karena gambar pembayaran buram/tidak valid).
    - [ ] Pastikan status pendaftaran user diperbarui secara otomatis di basis data dan dasbor mereka.
- **Kasus Ekstrem:**
    - [ ] **Pemesanan Berlebih (Overbooking):** Setujui pendaftaran yang secara resmi melampaui batas global maksimum acara (admin sebaiknya mendapat pesan peringatan/warning).

### 3.4 Manajemen User & Organisasi

- **Tautan:** [/admin/users](/admin/users) dan [/admin/organizations](/admin/organizations)
- **Biasa:**
    - [ ] Arahkan ke data Pengguna (Users).
    - [ ] Lihat daftar pengguna terdaftar.
    - [ ] Edit detail profil pengguna atau peran profil dengan berhasil.
    - [ ] Arahkan ke menu Organisasi (Organizations).
    - [ ] Lihat dan edit profil organisasi.

### 3.5 Manajemen Berita / Konten

- **Tautan:** [/admin/news](/admin/news)
- **Biasa:**
    - [ ] Arahkan ke Data Berita (News).
    - [ ] Buat artikel berita baru.
    - [ ] Edit artikel berita yang sudah ada.
    - [ ] Hapus artikel.
    - [ ] Pastikan semua konten diperbarui secara akurat di halaman Utama/Publik.

### 🚫 Batasan (Hal yang tidak diperbolehkan bagi admin)

- Meskipun memiliki status administrator super:
    - [ ] Admin **tidak boleh** memeriksa isi rentang sandi bersih pengguna (plaintext password) tanpa hash di database.
    - [ ] Admin menghindari proses penghapusan permanen (hard/physical-delete) dari sistem rekam pendaftaran acara (harus melalui langkah soft-deletes / audit trail log).
    - [ ] Melewatkan atau mengabaikan bypass tingkat database krusial (mis. menyimpan event tanpa ada judul sama sekali).
