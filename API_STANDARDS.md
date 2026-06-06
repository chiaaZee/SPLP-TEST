# SPLPD API Response Standard

Dokumen ini berisi standar format response JSON yang WAJIB digunakan di seluruh endpoint API SPLPD-APPS.

## 1. Response Sukses (2xx)

Gunakan `ApiResponse::success($data, $message, $code, $metadata)`.

### Struktur JSON
```json
{
  "status": "success",
  "code": 200,
  "message": "Data berhasil diambil.", // Opsional
  "data": {
    // Data inti (Object/Array)
    "id": 1,
    "name": "Contoh"
  },
  "metadata": {
    // Opsional: Pagination, Source, Timestamp
    "page": 1,
    "total": 100
  }
}
```

## 2. Response Gagal / Error (4xx - 5xx)

Gunakan `ApiResponse::error($title, $detail, $code, $type)`.
Format ini mengadopsi standar RFC 7807 yang disederhanakan.

### Struktur JSON
```json
{
  "status": "error",
  "code": 404,
  "error": {
    "type": "RESOURCE_NOT_FOUND", // Konstanta Unik (Snake Case Upper)
    "title": "Data Tidak Ditemukan", // User Friendly Title
    "detail": "Dataset dengan ID tersebut tidak ditemukan." // Technical Detail
  }
}
```

## 3. Daftar Referensi Kode Error (Type)

| HTTP Code | Error Type | Deskripsi |
| :--- | :--- | :--- |
| **400** | `BAD_REQUEST` | Format input salah atau JSON invalid. |
| **401** | `AUTH_INVALID_TOKEN` | Token tidak ada, salah, atau expired. |
| **401** | `AUTH_SUSPENDED` | User dibanned/suspended. |
| **403** | `FORBIDDEN_ACCESS` | Tidak punya hak akses ke resource ini. |
| **404** | `RESOURCE_NOT_FOUND` | Data atau Endpoint tidak ditemukan. |
| **422** | `VALIDATION_ERROR` | Gagal validasi input form (wajib isi, format email, dll). |
| **429** | `RATE_LIMIT_EXCEEDED` | Terlalu banyak request (Spamming). |
| **500** | `SERVER_ERROR` | Internal Server Error (Bug/Exception). |
| **502** | `GATEWAY_ERROR` | Error saat meneruskan request ke layanan target. |
| **503** | `SERVICE_UNAVAILABLE` | Sedang maintenance. |

## 4. Contoh Penggunaan di Controller

```php
use App\Helpers\ApiResponse;

// 1. Sukses Basic
return ApiResponse::success($user);

// 2. Sukses dengan Metadata
return ApiResponse::success($users, "List User", 200, [
    'total' => 50,
    'page' => 1
]);

// 3. Error 404
return ApiResponse::error(
    "Data Hilang",
    "ID 99 tidak ditemukan",
    404,
    "RESOURCE_NOT_FOUND"
);

// 4. Error Validasi
return ApiResponse::error(
    "Input Tidak Valid",
    "Email wajib diisi",
    422,
    "VALIDATION_ERROR"
);
```
