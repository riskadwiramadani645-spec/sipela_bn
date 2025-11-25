# Storage Setup untuk Bukti Pelaksanaan

Untuk menampilkan file bukti pelaksanaan, jalankan command berikut di terminal:

```bash
php artisan storage:link
```

Command ini akan membuat symbolic link dari `storage/app/public` ke `public/storage` sehingga file yang diupload bisa diakses melalui browser.

Setelah menjalankan command tersebut, file bukti pelaksanaan akan bisa diakses melalui URL seperti:
`http://localhost:8000/storage/bukti_pelaksanaan/filename.jpg`