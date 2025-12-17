# Dokumentasi API Web Service (M2M)

## I. Dokumentasi Flow

Berikut adalah diagram alur (Flowchart) proses autentikasi M2M dan penggunaan End-Point.

```mermaid
graph TD
    Start([Mulai]) --> ReqToken[Client Request Token\n(POST /oauth/token)]
    ReqToken --> ValCreds{Validasi\nCredentials?}
    
    ValCreds -- Tidak --> ErrToken[Return 401 Unauthorized]
    ErrToken --> End([Selesai])
    
    ValCreds -- Ya --> GenToken[Generate Access Token]
    GenToken --> RetToken[Return Bearer Token]
    
    RetToken --> ReqRes[Client Request Resource\n(e.g. GET /api/products)]
    ReqRes --> CheckMiddleware{Middleware:\nCek Token Valid?}
    
    CheckMiddleware -- Tidak --> ErrAuth[Return 401 Unauthorized]
    ErrAuth --> End
    
    CheckMiddleware -- Ya --> Process{Proses Controller\n(M2M Flow)}
    
    Process --> DB[Query Database]
    DB --> ResData[Return Data JSON]
    ResData --> End
```

## II. Dokumentasi End-Point (Swagger)

End-point API telah didokumentasikan menggunakan anotasi **OpenAPI 3.0 / Swagger** langsung di dalam controller (`app/Http/Controllers/Api/ProductController.php`).

Berikut adalah ringkasan spesifikasi End-Point yang tersedia:

### Authentication
*   **Method**: `POST`
*   **URL**: `/oauth/token`
*   **Body**:
    *   `grant_type`: `client_credentials`
    *   `client_id`: `[CLIENT_ID]`
    *   `client_secret`: `[CLIENT_SECRET]`

### Product Resources
Semua request ke endpoint ini **WAJIB** menyertakan Header:
`Authorization: Bearer [ACCESS_TOKEN]`

#### 1. Get All Products
*   **Method**: `GET`
*   **URL**: `/api/products`
*   **Description**: Mengambil semua data produk.
*   **Response**: `200 OK` (Array of objects).

#### 2. Create Product
*   **Method**: `POST`
*   **URL**: `/api/products`
*   **Body (JSON)**:
    ```json
    {
        "name": "Nama Produk",
        "description": "Deskripsi",
        "price": 10000,
        "stock": 10
    }
    ```
*   **Response**: `201 Created`

#### 3. Show Product
*   **Method**: `GET`
*   **URL**: `/api/products/{id}`
*   **Response**: `200 OK` atau `404 Not Found`.

#### 4. Update Product
*   **Method**: `PUT`
*   **URL**: `/api/products/{id}`
*   **Body (JSON)**:
    ```json
    {
        "name": "Nama Baru",
        "price": 20000
    }
    ```
*   **Response**: `200 OK`

#### 5. Delete Product
*   **Method**: `DELETE`
*   **URL**: `/api/products/{id}`
*   **Response**: `200 OK`.

---
**Catatan:** Kode anotasi Swagger lengkap sudah tertanam di `app/Http/Controllers/Api/ProductController.php`.
