# 🦴 CT Bone Viewer

Webowy system interaktywnego przeglądania struktur kostnych z tomografii komputerowej (CT) w formacie NIfTI, wdrożony w chmurze Microsoft Azure.

🌐 **Live demo:** https://ct-bone-viewer.azurewebsites.net

---

## 📋 Opis projektu

Aplikacja umożliwia lekarzom:
- Bezpieczne logowanie i rejestrację konta
- Wczytanie plików segmentacji w formacie NIfTI (`.nii`, `.nii.gz`) i wizualizację struktury kostnej 3D bezpośrednio w przeglądarce (WebGL)
- Wybór spośród ponad 100 struktur anatomicznych z bazy TotalSegmentator
- Zapis metadanych badania do bazy danych
- Przeglądanie i usuwanie historii badań
- Podgląd statystyk systemu pobieranych z REST API (mikrousługa ct-bone-api)

Panel administratora umożliwia zarządzanie kontami lekarzy (reset haseł, usuwanie kont).

---

## 🏗️ Architektura

Projekt wdrożony w architekturze mikrousług na Microsoft Azure:

```
┌─────────────────────────────────┐     HTTP/JSON      ┌─────────────────────────────┐
│   ct-bone-viewer (App Service)  │ ────────────────→  │  ct-bone-api (App Service)  │
│  https://ct-bone-viewer.        │                    │  https://ct-bone-api.        │
│         azurewebsites.net       │ ←────────────────  │       azurewebsites.net     │
│  PHP 8.2 | SQLite | Bootstrap   │    JSON statystyki  │  PHP 8.2 | REST API         │
└─────────────────────────────────┘                    └─────────────────────────────┘
                    │                                               │
                    └───────────────────┬───────────────────────────┘
                                        │
                              ┌─────────▼─────────┐
                              │   Microsoft Azure  │
                              │  Switzerland North │
                              │   Terraform IaC    │
                              └───────────────────┘
```

---

## 🛠️ Technologie

| Warstwa | Technologia |
|---|---|
| Frontend | PHP 8.2, HTML5, Bootstrap 5, JavaScript |
| Wizualizacja 3D | [NiiVue](https://github.com/niivue/niivue) (WebGL) |
| Wyszukiwarka struktur | [Select2](https://select2.org/) |
| Baza danych | SQLite (PDO) |
| Infrastruktura | Microsoft Azure App Service (Linux, PHP 8.2) |
| IaC | Terraform v1.15.5 |
| CI/CD | GitHub Actions (test → build → deploy) |
| Testy | PHP Unit Tests (19 testów) |

---

## 📁 Struktura projektu

```
CT_Bone_Viewer/
├── index.php                          # Strona główna + panel admina
├── login.php                          # Logowanie
├── register.php                       # Rejestracja lekarza
├── logout.php                         # Wylogowanie
├── results.php                        # Historia badań + widget API
├── upload_handler.php                 # Backend: zapis plików i bazy
├── delete.php                         # Usuwanie rekordów
├── db_connect.php                     # Połączenie SQLite + tworzenie tabel
├── tests/
│   └── AppTest.php                    # 19 testów jednostkowych PHP
├── terraform/
│   ├── main.tf                        # Infrastruktura Azure jako kod
│   └── .gitignore                     # Ignorowanie plików stanu Terraform
├── .github/
│   └── workflows/
│       └── main_ct-bone-viewer.yml    # Pipeline CI/CD: test→build→deploy
├── .htaccess                          # Konfiguracja Apache
└── README.md
```

---

## ⚙️ Instalacja lokalna

### Wymagania
- [XAMPP](https://www.apachefriends.org/) z Apache i PHP 8.2
- Nowoczesna przeglądarka z WebGL (Chrome, Firefox, Edge)

### Kroki

**1. Sklonuj repozytorium**
```bash
git clone https://github.com/martynaswitula/CT_Bone_Viewer.git C:/xampp/htdocs/projekt_ct_viewer
```

**2. Uruchom XAMPP**
- Otwórz XAMPP Control Panel
- Kliknij Start przy Apache

**3. Utwórz wymagane foldery**
```bash
mkdir C:/xampp/htdocs/projekt_ct_viewer/uploads
mkdir C:/xampp/htdocs/projekt_ct_viewer/previews
mkdir C:/xampp/htdocs/projekt_ct_viewer/database
```

**4. Otwórz aplikację**
```
http://localhost/projekt_ct_viewer/login.php
```

---

## ☁️ Wdrożenie w chmurze (Terraform)

### Wymagania
- [Azure CLI](https://docs.microsoft.com/cli/azure/install-azure-cli)
- [Terraform](https://developer.hashicorp.com/terraform/install)
- Konto Microsoft Azure

### Kroki

```bash
# Zaloguj się do Azure
az login

# Przejdź do folderu Terraform
cd terraform/

# Zainicjuj Terraform
terraform init

# Sprawdź plan
terraform plan

# Wdróż infrastrukturę
terraform apply
```

---

## 🚀 CI/CD Pipeline

Pipeline GitHub Actions uruchamia się automatycznie po każdym `push` na branch `main`:

```
push → test (19 testów PHP) → build → deploy → Azure App Service
```

Wdrożenie następuje **tylko po pomyślnym przejściu wszystkich testów**.

---

## 🔐 Bezpieczeństwo

- **HTTPS** — wymuszony na poziomie Azure App Service
- **Szyfrowanie haseł** — bcrypt (`password_hash()` / `password_verify()`)
- **SQL Injection** — PDO Prepared Statements
- **XSS** — `htmlspecialchars()` na wszystkich wyjściach
- **Autoryzacja** — weryfikacja sesji na każdej stronie
- **Walidacja plików** — sprawdzanie rozszerzeń (.nii, .gz)

---

## 👤 Dane testowe

| Rola | Email | Hasło |
|---|---|---|
| Administrator | admin@ctviewer.pl | Admin2026! |
| Lekarz | zarejestruj przez formularz | min. 8 znaków |

---

## 🔗 Powiązane repozytoria

- **REST API:** [CT_Bone_API](https://github.com/martynaswitula/CT_Bone_API)

---

## 👤 Autor

**Martyna Śwituła**
Politechnika Śląska
[github.com/martynaswitula](https://github.com/martynaswitula)
