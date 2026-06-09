<?php require_once 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CT Bone Viewer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <script src="https://unpkg.com/@niivue/niivue@0.44.1/dist/niivue.umd.js"></script>
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .medical-header { background: linear-gradient(135deg, #1a3c6e 0%, #3c6eb4 100%); color: white; padding: 2rem 0; margin-bottom: 2rem; border-radius: 0 0 25px 25px; }
        .preview-container { position: relative; width: 100%; height: 500px; background-color: #1a1a1a; border-radius: 10px; overflow: hidden; }
        #gl { border-radius: 10px; width: 100%; height: 500px; outline: none; opacity: 0; transition: opacity 0.5s ease-in-out; }
        .viewer-label { position: absolute; top: 10px; left: 10px; background: rgba(0,0,0,0.6); color: white; padding: 5px 12px; border-radius: 5px; z-index: 10; font-size: 0.85rem; }
        .viewer-controls { position: absolute; bottom: 10px; right: 10px; z-index: 10; display: flex; gap: 8px; flex-wrap: wrap; justify-content: flex-end; }
    </style>
</head>
<body>

<div class="medical-header text-center">
    <h1><i class="bi bi-hospital"></i> CT Bone Viewer</h1>
    <p>Interaktywna przeglądarka struktur kostnych z tomografii komputerowej</p>
</div>

<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <div class="card p-4 h-100">
                <h4 class="mb-4">Nowe Badanie</h4>

                <form id="mainForm" action="upload_handler.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="snapshot" id="snapshotData">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">ID Pacjenta</label>
                        <input type="text" name="patient_id" class="form-control" placeholder="np. 0004" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Typ Anatomii</label>
                        <select name="anatomy" id="anatomySelect" class="form-select">
                            <optgroup label="Całe badanie">
                                <option value="ct_whole">Całe badanie CT (Whole CT)</option>
                            </optgroup>
                            <optgroup label="Kości i stawy">
                                <option value="pelvis">Miednica (Pelvis)</option>
                                <option value="hip_left">Biodro lewe (Hip Left)</option>
                                <option value="hip_right">Biodro prawe (Hip Right)</option>
                                <option value="sternum">Mostek (Sternum)</option>
                                <option value="spine_cervical">Kręgosłup szyjny (Spine Cervical)</option>
                                <option value="spine_thoracic">Kręgosłup piersiowy (Spine Thoracic)</option>
                                <option value="spine_lumbar">Kręgosłup lędźwiowy (Spine Lumbar)</option>
                                <option value="vertebrae_C1">Kręg C1 (Vertebrae C1)</option>
                                <option value="vertebrae_C2">Kręg C2 (Vertebrae C2)</option>
                                <option value="vertebrae_C3">Kręg C3 (Vertebrae C3)</option>
                                <option value="vertebrae_C4">Kręg C4 (Vertebrae C4)</option>
                                <option value="vertebrae_C5">Kręg C5 (Vertebrae C5)</option>
                                <option value="vertebrae_C6">Kręg C6 (Vertebrae C6)</option>
                                <option value="vertebrae_C7">Kręg C7 (Vertebrae C7)</option>
                                <option value="vertebrae_T1">Kręg T1 (Vertebrae T1)</option>
                                <option value="vertebrae_T2">Kręg T2 (Vertebrae T2)</option>
                                <option value="vertebrae_T3">Kręg T3 (Vertebrae T3)</option>
                                <option value="vertebrae_T4">Kręg T4 (Vertebrae T4)</option>
                                <option value="vertebrae_T5">Kręg T5 (Vertebrae T5)</option>
                                <option value="vertebrae_T6">Kręg T6 (Vertebrae T6)</option>
                                <option value="vertebrae_T7">Kręg T7 (Vertebrae T7)</option>
                                <option value="vertebrae_T8">Kręg T8 (Vertebrae T8)</option>
                                <option value="vertebrae_T9">Kręg T9 (Vertebrae T9)</option>
                                <option value="vertebrae_T10">Kręg T10 (Vertebrae T10)</option>
                                <option value="vertebrae_T11">Kręg T11 (Vertebrae T11)</option>
                                <option value="vertebrae_T12">Kręg T12 (Vertebrae T12)</option>
                                <option value="vertebrae_L1">Kręg L1 (Vertebrae L1)</option>
                                <option value="vertebrae_L2">Kręg L2 (Vertebrae L2)</option>
                                <option value="vertebrae_L3">Kręg L3 (Vertebrae L3)</option>
                                <option value="vertebrae_L4">Kręg L4 (Vertebrae L4)</option>
                                <option value="vertebrae_L5">Kręg L5 (Vertebrae L5)</option>
                                <option value="sacrum">Kość krzyżowa (Sacrum)</option>
                                <option value="skull">Czaszka (Skull)</option>
                                <option value="clavicula_left">Obojczyk lewy (Clavicula Left)</option>
                                <option value="clavicula_right">Obojczyk prawy (Clavicula Right)</option>
                                <option value="scapula_left">Łopatka lewa (Scapula Left)</option>
                                <option value="scapula_right">Łopatka prawa (Scapula Right)</option>
                                <option value="humerus_left">Kość ramienna lewa (Humerus Left)</option>
                                <option value="humerus_right">Kość ramienna prawa (Humerus Right)</option>
                                <option value="femur_left">Kość udowa lewa (Femur Left)</option>
                                <option value="femur_right">Kość udowa prawa (Femur Right)</option>
                                <option value="tibia_left">Piszczel lewa (Tibia Left)</option>
                                <option value="tibia_right">Piszczel prawa (Tibia Right)</option>
                                <option value="fibula_left">Strzałka lewa (Fibula Left)</option>
                                <option value="fibula_right">Strzałka prawa (Fibula Right)</option>
                                <option value="rib_left_1">Żebro lewe 1 (Rib Left 1)</option>
                                <option value="rib_left_2">Żebro lewe 2 (Rib Left 2)</option>
                                <option value="rib_left_3">Żebro lewe 3 (Rib Left 3)</option>
                                <option value="rib_left_4">Żebro lewe 4 (Rib Left 4)</option>
                                <option value="rib_left_5">Żebro lewe 5 (Rib Left 5)</option>
                                <option value="rib_left_6">Żebro lewe 6 (Rib Left 6)</option>
                                <option value="rib_left_7">Żebro lewe 7 (Rib Left 7)</option>
                                <option value="rib_left_8">Żebro lewe 8 (Rib Left 8)</option>
                                <option value="rib_left_9">Żebro lewe 9 (Rib Left 9)</option>
                                <option value="rib_left_10">Żebro lewe 10 (Rib Left 10)</option>
                                <option value="rib_left_11">Żebro lewe 11 (Rib Left 11)</option>
                                <option value="rib_left_12">Żebro lewe 12 (Rib Left 12)</option>
                                <option value="rib_right_1">Żebro prawe 1 (Rib Right 1)</option>
                                <option value="rib_right_2">Żebro prawe 2 (Rib Right 2)</option>
                                <option value="rib_right_3">Żebro prawe 3 (Rib Right 3)</option>
                                <option value="rib_right_4">Żebro prawe 4 (Rib Right 4)</option>
                                <option value="rib_right_5">Żebro prawe 5 (Rib Right 5)</option>
                                <option value="rib_right_6">Żebro prawe 6 (Rib Right 6)</option>
                                <option value="rib_right_7">Żebro prawe 7 (Rib Right 7)</option>
                                <option value="rib_right_8">Żebro prawe 8 (Rib Right 8)</option>
                                <option value="rib_right_9">Żebro prawe 9 (Rib Right 9)</option>
                                <option value="rib_right_10">Żebro prawe 10 (Rib Right 10)</option>
                                <option value="rib_right_11">Żebro prawe 11 (Rib Right 11)</option>
                                <option value="rib_right_12">Żebro prawe 12 (Rib Right 12)</option>
                            </optgroup>
                            <optgroup label="Narządy wewnętrzne">
                                <option value="heart">Serce (Heart)</option>
                                <option value="aorta">Aorta (Aorta)</option>
                                <option value="lung_left">Płuco lewe (Lung Left)</option>
                                <option value="lung_right">Płuco prawe (Lung Right)</option>
                                <option value="trachea">Tchawica (Trachea)</option>
                                <option value="liver">Wątroba (Liver)</option>
                                <option value="spleen">Śledziona (Spleen)</option>
                                <option value="pancreas">Trzustka (Pancreas)</option>
                                <option value="kidney_left">Nerka lewa (Kidney Left)</option>
                                <option value="kidney_right">Nerka prawa (Kidney Right)</option>
                                <option value="stomach">Żołądek (Stomach)</option>
                                <option value="gallbladder">Pęcherzyk żółciowy (Gallbladder)</option>
                                <option value="esophagus">Przełyk (Esophagus)</option>
                                <option value="small_bowel">Jelito cienkie (Small Bowel)</option>
                                <option value="duodenum">Dwunastnica (Duodenum)</option>
                                <option value="colon">Okrężnica (Colon)</option>
                                <option value="urinary_bladder">Pęcherz moczowy (Urinary Bladder)</option>
                                <option value="prostate">Prostata (Prostate)</option>
                                <option value="adrenal_gland_left">Nadnercze lewe (Adrenal Gland Left)</option>
                                <option value="adrenal_gland_right">Nadnercze prawe (Adrenal Gland Right)</option>
                                <option value="thyroid_gland">Tarczyca (Thyroid Gland)</option>
                            </optgroup>
                            <optgroup label="Naczynia krwionośne">
                                <option value="pulmonary_artery">Tętnica płucna (Pulmonary Artery)</option>
                                <option value="brachiocephalic_trunk">Pień ramienno-głowowy (Brachiocephalic Trunk)</option>
                                <option value="brachiocephalic_vein_left">Żyła ramienno-głowowa lewa (Brachiocephalic Vein Left)</option>
                                <option value="brachiocephalic_vein_right">Żyła ramienno-głowowa prawa (Brachiocephalic Vein Right)</option>
                                <option value="iliac_artery_left">Tętnica biodrowa lewa (Iliac Artery Left)</option>
                                <option value="iliac_artery_right">Tętnica biodrowa prawa (Iliac Artery Right)</option>
                                <option value="iliac_vein_left">Żyła biodrowa lewa (Iliac Vein Left)</option>
                                <option value="iliac_vein_right">Żyła biodrowa prawa (Iliac Vein Right)</option>
                                <option value="inferior_vena_cava">Żyła główna dolna (Inferior Vena Cava)</option>
                                <option value="portal_vein">Żyła wrotna (Portal Vein)</option>
                            </optgroup>
                            <optgroup label="Mięśnie">
                                <option value="autochthon_left">Mięsień autochtoniczny lewy (Autochthon Left)</option>
                                <option value="autochthon_right">Mięsień autochtoniczny prawy (Autochthon Right)</option>
                                <option value="iliopsoas_left">Mięsień biodrowo-lędźwiowy lewy (Iliopsoas Left)</option>
                                <option value="iliopsoas_right">Mięsień biodrowo-lędźwiowy prawy (Iliopsoas Right)</option>
                                <option value="gluteus_maximus_left">Mięsień pośladkowy wielki lewy (Gluteus Maximus Left)</option>
                                <option value="gluteus_maximus_right">Mięsień pośladkowy wielki prawy (Gluteus Maximus Right)</option>
                                <option value="gluteus_medius_left">Mięsień pośladkowy średni lewy (Gluteus Medius Left)</option>
                                <option value="gluteus_medius_right">Mięsień pośladkowy średni prawy (Gluteus Medius Right)</option>
                                <option value="gluteus_minimus_left">Mięsień pośladkowy mały lewy (Gluteus Minimus Left)</option>
                                <option value="gluteus_minimus_right">Mięsień pośladkowy mały prawy (Gluteus Minimus Right)</option>
                            </optgroup>
                            <optgroup label="Inne">
                                <option value="other">Inna struktura (Other)</option>
                            </optgroup>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Notatki</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Opcjonalne uwagi..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted text-uppercase">Wybierz pliki NIfTI</label>
                        <input type="file" id="fileInput" name="nifti_files[]" class="form-control" accept=".nii,.gz" multiple required>
                    </div>

                    <button type="submit" id="saveBtn" class="btn btn-primary w-100 mt-4" disabled>
                        <i class="bi bi-save"></i> Zapisz Badanie
                    </button>
                </form>

                <div class="mt-3 text-center">
                    <a href="results.php" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-table"></i> Historia Badań
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="m-0">Podgląd Struktury 3D</h5>
                    <span id="statusLabel" class="badge bg-dark">Gotowy do wczytania</span>
                </div>

                <div class="preview-container">
                    <div class="viewer-label">Zintegrowana przeglądarka medyczna</div>
                    <canvas id="gl"></canvas>
                    <div class="viewer-controls">
                        <button type="button" class="btn btn-sm btn-primary" onclick="window.nv.setSliceType(window.nv.sliceTypeRender)">Widok 3D</button>
                        <button type="button" class="btn btn-sm btn-light" onclick="resetCamera()"><i class="bi bi-arrow-counterclockwise"></i> Reset</button>
                    </div>
                </div>

                <div class="mt-3 row text-center small text-muted g-2">
                    <div class="col-4"><i class="bi bi-mouse"></i> <strong>Lewy klik:</strong> Obrót</div>
                    <div class="col-4"><i class="bi bi-brightness-high"></i> <strong>Prawy klik:</strong> Kontrast</div>
                    <div class="col-4"><i class="bi bi-zoom-in"></i> <strong>Scroll:</strong> Przybliżanie</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let isHighContrast = false;

    function resetCamera() {
        if (window.nv && window.nv.scene) {
            window.nv.scene.renderAzimuth = 110;
            window.nv.scene.renderElevation = 15;
            if (window.nv.volumes.length > 0) {
                isHighContrast = false;
                window.nv.volumes.forEach((vol) => {
                    vol.cal_min = vol.cal_max <= 5 ? 0 : 100;
                    vol.cal_max = vol.cal_max <= 5 ? 1 : 800;
                });
            }
            if (typeof window.nv.drawScene === 'function') window.nv.drawScene();
        }
    }

    document.addEventListener("DOMContentLoaded", function () {
        window.nv = new niivue.Niivue({ backColor: [0.1, 0.1, 0.1, 1], show3Dcrosshair: false });
        window.nv.attachTo('gl');
        window.nv.setSliceType(window.nv.sliceTypeRender);

        const canvasGL = document.getElementById('gl');
        canvasGL.addEventListener('contextmenu', e => e.preventDefault());

        canvasGL.addEventListener('mouseup', function (e) {
            if (e.button === 2 && window.nv && window.nv.volumes.length > 0) {
                isHighContrast = !isHighContrast;
                window.nv.volumes.forEach((vol) => {
                    if (vol.cal_max <= 5) {
                        vol.cal_min = isHighContrast ? 0.2 : 0;
                    } else {
                        vol.cal_min = isHighContrast ? 200 : 100;
                        vol.cal_max = isHighContrast ? 1000 : 800;
                    }
                });
                if (typeof window.nv.drawScene === 'function') window.nv.drawScene();
            }
        });

        const fileInput = document.getElementById('fileInput');
        const statusLabel = document.getElementById('statusLabel');
        const saveBtn = document.getElementById('saveBtn');

        fileInput.addEventListener('change', async function (e) {
            const files = e.target.files;
            if (files.length === 0) return;

            canvasGL.style.opacity = '0';
            statusLabel.innerText = "Wczytywanie " + files.length + " pliku/ów...";
            statusLabel.className = "badge bg-warning text-dark";

            try {
                const volumeList = [];
                for (let file of files) {
                    volumeList.push({ url: URL.createObjectURL(file), name: file.name });
                }
                await window.nv.loadVolumes(volumeList);

                window.nv.volumes.forEach((vol) => {
                    vol.cal_min = vol.cal_max <= 5 ? 0 : 100;
                    vol.cal_max = vol.cal_max <= 5 ? 1 : 800;
                });
                window.nv.setSliceType(window.nv.sliceTypeRender);
                if (typeof window.nv.drawScene === 'function') window.nv.drawScene();

                canvasGL.style.opacity = '1';
                statusLabel.innerText = "Gotowe do zapisu";
                statusLabel.className = "badge bg-success";
                saveBtn.disabled = false;

            } catch (err) {
                console.error(err);
                statusLabel.innerText = "Błąd wczytywania";
                statusLabel.className = "badge bg-danger";
                canvasGL.style.opacity = '1';
            }
        });

        const form = document.getElementById('mainForm');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Zapisywanie...';
            saveBtn.disabled = true;
            resetCamera();
            setTimeout(() => {
                document.getElementById('snapshotData').value = canvasGL.toDataURL('image/png');
                form.submit();
            }, 150);
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#anatomySelect').select2({
            theme: 'bootstrap-5',
            placeholder: 'Wyszukaj strukturę...',
            allowClear: true
        });
    });
</script>
</body>
</html>