

## 🏗️ Arquitectura del Nuevo Flujo

El sistema ahora utiliza un flujo de dos pasos para garantizar que el usuario pueda revisar el documento antes de crearlo definitivamente.

### Paso 1: Previsualización (`/api/procesos/previsualizar`)
1.  **Recepción de Datos**: El backend recibe todos los datos del formulario (tesis, integrantes, formato, etc.).
2.  **Generación en Memoria**: Se crean instancias de los modelos (Tesis, Persona, etc.) en memoria, **sin guardar en la base de datos**.
3.  **Generación de PDF**: Se genera un PDF temporal en la carpeta `storage/app/public/temp`.
4.  **Respuesta**: Se devuelve la URL de este PDF temporal para que el frontend lo muestre.

### Paso 2: Registro (`/api/procesos/registrar`)
1.  **Confirmación**: El usuario confirma que el documento es correcto.
2.  **Firma**:
    *   **Manual**: El usuario sube el PDF firmado manualmente.
    *   **Digital**: El backend toma el PDF temporal y (simuladamente) lo firma digitalmente.
3.  **Persistencia**: Recién en este punto se guardan todos los datos en la base de datos (Proceso, Tesis, Integrantes, Historial, etc.) dentro de una transacción segura.

---

## 🛠️ Prerrequisitos para Pruebas

Antes de probar en Postman, asegúrate de haber ejecutado estos comandos en tu terminal:

1.  **Configurar Base de Datos y Datos de Prueba**:
    ```powershell
    php artisan migrate:fresh
    php artisan db:seed --class=ProcesoTestSeeder
    ```

2.  **Vincular Storage (Importante para ver los PDFs)**:
    ```powershell
    php artisan storage:link
    ```

3.  **Iniciar Servidor**:
    ```powershell
    php artisan serve
    ```

---

## 🧪 Pruebas en Postman

### 1. Previsualizar (Generar PDF Temporal)

Este endpoint simula la creación y devuelve un PDF para revisar.

*   **Método**: `POST`
*   **URL**: `http://127.0.0.1:8000/api/procesos/previsualizar`
*   **Headers**:
    *   `Content-Type`: `application/json`
    *   `Accept`: `application/json`

**Body (JSON):**
```json
{
    "general": {
        "titulo": "Implementación de IA en la Gestión Pública",
        "nivel": 1,
        "mencion": 1,
        "tesistas": [2]
    },
    "documento": {
        "formato": 1,
        "destinatario": [3],
        "sumilla": "Solicito aprobación de Plan de Tesis",
        "fundamento": "Cumpliendo con los requisitos establecidos en el reglamento..."
    }
}
```

**Respuesta Esperada:**
Deberías recibir un JSON con `success: true` y una `pdf_url`. Copia el valor de `pdf_path` de la respuesta, lo necesitarás para el paso 2 (si usas firma digital).

---

### 2. Opción A: Registrar con Firma Digital

Simula que el sistema firma el documento automáticamente.

*   **Método**: `POST`
*   **URL**: `http://127.0.0.1:8000/api/procesos/registrar`
*   **Headers**:
    *   `Content-Type`: `application/json`
    *   `Accept`: `application/json`

**Body (JSON):**
*Reemplaza `temp/doc_xxxx.pdf` con el path que obtuviste en el paso 1.*

```json
{
    "tramite_id": 1,
    "tipo_firma": "digital",
    "pdf_path_temp": "temp/doc_674ccebd1234.pdf", 
    "general": {
        "titulo": "Implementación de IA en la Gestión Pública",
        "nivel": 1,
        "mencion": 1,
        "tesistas": [2]
    },
    "documento": {
        "formato": 1,
        "destinatario": [3],
        "sumilla": "Solicito aprobación de Plan de Tesis",
        "fundamento": "Cumpliendo con los requisitos establecidos en el reglamento..."
    }
}
```

---

### 3. Opción B: Registrar con Firma Manual

Simula que el usuario descargó el PDF, lo firmó a mano y lo escaneó.

*   **Método**: `POST`
*   **URL**: `http://127.0.0.1:8000/api/procesos/registrar`
*   **Body**: Selecciona `form-data`.

| Key | Type | Value |
|-----|------|-------|
| `tramite_id` | Text | `1` |
| `tipo_firma` | Text | `manual` |
| `archivo_firmado` | File | *(Sube cualquier PDF aquí)* |
| `general[titulo]` | Text | `Implementación de IA en la Gestión Pública` |
| `general[nivel]` | Text | `1` |
| `general[mencion]` | Text | `1` |
| `general[tesistas][]` | Text | `2` |
| `documento[formato]` | Text | `1` |
| `documento[destinatario][]` | Text | `3` |
| `documento[sumilla]` | Text | `Solicito aprobación` |
| `documento[fundamento]` | Text | `Fundamento de prueba` |
| `requisitos[0][requisito]` | Text | `1` |
| `requisitos[0][documento]` | File | *(Sube archivo requisito 1)* |
| `requisitos[0][tipoFirma]` | Text | `manual` |
| `requisitos[1][requisito]` | Text | `2` |
| `requisitos[1][documento]` | File | *(Sube archivo requisito 2)* |
| `requisitos[1][tipoFirma]` | Text | `digital` |

> **Nota Importante para Frontend (Vue/React):**
> Como estás enviando archivos (`File`), **NO** puedes enviar un JSON simple. Debes usar `FormData`.
>
> ```javascript
> const formData = new FormData();
> // General
> formData.append('general[titulo]', general.value.titulo);
> formData.append('general[nivel]', general.value.nivel);
> // ... resto de general
>
> // Documento
> formData.append('documento[formato]', documento.value.formato);
> // ... resto de documento
>
> // Requisitos (Array)
> requisitos.value.forEach((req, index) => {
>     formData.append(`requisitos[${index}][requisito]`, req.requisito);
>     formData.append(`requisitos[${index}][tipoFirma]`, req.tipoFirma);
>     if (req.documento) {
>         formData.append(`requisitos[${index}][documento]`, req.documento);
>     }
> });
>
> // Enviar con axios
> axios.post('/api/procesos/registrar', formData, {
>     headers: { 'Content-Type': 'multipart/form-data' }
> });
> ```

