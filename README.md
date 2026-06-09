# 🦴 CT Bone Viewer

Interaktywna webowa przeglądarka struktur kostnych z tomografii komputerowej (CT) w formacie NIfTI. Umożliwia wczytanie plików segmentacji, wizualizację 3D bezpośrednio w przeglądarce oraz zarządzanie historią badań w bazie danych.

---

## 📋 Opis projektu

Aplikacja umożliwia:
- Wczytanie plików segmentacji w formacie NIfTI (`.nii`, `.nii.gz`) i interaktywne przeglądanie struktury kostnej 3D w przeglądarce (WebGL)
- Wybór spośród ponad 100 struktur anatomicznych z bazy TotalSegmentator (kości, narządy, naczynia, mięśnie)
- Zapis metadanych badania (ID pacjenta, typ struktury, notatki) do bazy danych MySQL
- Przeglądanie i usuwanie historii badań

---

## 🛠️ Technologie

| Warstwa | Technologia |
|---|---|
| Frontend | PHP, HTML5, Bootstrap 5, JavaScript |
| Wizualizacja 3D | [NiiVue](https://github.com/niivue/niivue) (WebGL) |
| Wyszukiwarka struktur | [Select2](https://select2.org/) |
| Backend | PHP 8.x |
| Baza danych | MySQL / MariaDB |
| Serwer lokalny | XAMPP (Apache) |

---

## 📁 Struktura projektu

```
projekt_ct_viewer/
├── index.php            # Strona główna – formularz i przeglądarka 3D
├── upload_handler.php   # Backend – obsługa przesyłania plików i zapis do bazy
├── results.php          # Panel historii badań
├── delete.php           # Usuwanie rekordu z bazy
├── db_connect.php       # Konfiguracja połączenia z bazą danych (PDO)
├── uploads/             # (gitignore) Wgrane pliki NIfTI
└── previews/            # (gitignore) Zrzuty ekranu podglądu 3D
```

---

## ⚙️ Instalacja i uruchomienie (lokalnie)

### Wymagania

- [XAMPP](https://www.apachefriends.org/) z uruchomionym Apache i MySQL
- Nowoczesna przeglądarka z obsługą WebGL (Chrome, Firefox, Edge)

### Kroki instalacji

**1. Sklonuj repozytorium do folderu XAMPP**

```bash
git clone https://github.com/martyna190802/CT_Bone_Viewer.git C:/xampp/htdocs/projekt_ct_viewer
```

**2. Utwórz bazę danych**

W phpMyAdmin (`http://localhost/phpmyadmin`) utwórz bazę i tabelę:

```sql
CREATE DATABASE ct_viewer;
USE ct_viewer;

CREATE TABLE badania_ct (
    id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id VARCHAR(50) NOT NULL,
    anatomy_type VARCHAR(20) NOT NULL,
    file_name VARCHAR(255),
    notes TEXT,
    preview_path VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**3. Skonfiguruj połączenie z bazą danych**

Edytuj plik `db_connect.php`:

```php
$host = 'localhost';
$dbname = 'ct_viewer';
$username = 'root';
$password = '';
```

**4. Utwórz wymagane foldery**

```bash
mkdir C:/xampp/htdocs/projekt_ct_viewer/uploads
mkdir C:/xampp/htdocs/projekt_ct_viewer/previews
```

**5. Uruchom aplikację**

Otwórz w przeglądarce:
```
http://localhost/projekt_ct_viewer/
```

---

## 🚀 Sposób użycia

1. Otwórz stronę główną aplikacji
2. Wprowadź **ID Pacjenta**
3. Wyszukaj i wybierz **typ struktury anatomicznej** (np. wpisz "miednica" lub "pelvis")
4. Opcjonalnie dodaj **notatki**
5. Zaznacz pliki NIfTI (`.nii` lub `.nii.gz`)
6. Poczekaj aż przeglądarka 3D wyrenderuje strukturę
7. Kliknij **Zapisz Badanie**
8. Wynik pojawi się w panelu **Historia Badań**

### Sterowanie przeglądarką 3D

| Akcja | Efekt |
|---|---|
| Lewy przycisk myszy | Obrót struktury |
| Prawy przycisk myszy | Zmiana kontrastu |
| Scroll myszy | Przybliżanie / oddalanie |
| Przycisk Reset | Przywrócenie domyślnego widoku |

---

## 🫀 Obsługiwane struktury anatomiczne

Aplikacja obsługuje ponad 100 struktur z bazy [TotalSegmentator](https://github.com/wasserth/TotalSegmentator), pogrupowanych w kategorie:

- **Kości i stawy** — miednica, mostek, kręgosłup (C1–L5), czaszka, żebra, kości kończyn
- **Narządy wewnętrzne** — serce, płuca, wątroba, nerki, trzustka i inne
- **Naczynia krwionośne** — aorta, tętnice, żyły
- **Mięśnie** — mięśnie pośladkowe, biodrowo-lędźwiowe i inne

---

## ⚠️ Gitignore

Plik `.gitignore` wyklucza z repozytorium:
```
uploads/
previews/
*.nii
*.nii.gz
```

---

## 👤 Autor

**Martyna Śwituła**
Politechnika Śląska
