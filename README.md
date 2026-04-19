# UTP Teknologi Integrasi Sistem - Ecommerce-like Backend API

Nama: Ahmad Thoriq Hafidzurrohman<br>
NIM: 245150701111026

---

## Deskripsi

Backend API sederhana berbasis Laravel yang mensimulasikan sistem *e-commerce*. Data disimpan menggunakan mock data JSON sebagai pengganti database, tanpa koneksi ke database apapun. 

---

## Daftar Endpoint

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/items` | Menampilkan semua item |
| GET | `/api/items/{id}` | Menampilkan item berdasarkan ID |
| POST | `/api/items` | Menambahkan item baru |
| PUT | `/api/items/{id}` | Meng-update seluruh data item |
| PATCH | `/api/items/{id}` | Meng-update sebagian data item |
| DELETE | `/api/items/{id}` | Menghapus item |

---

## Dokumentasi API

Dokumentasi interaktif tersedia melalui Swagger UI di:

```
http://localhost:8000/api/documentation
```
