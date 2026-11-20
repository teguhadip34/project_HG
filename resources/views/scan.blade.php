
@extends('layouts.app')

@section('title', 'Scan Sampah - EcoScan AI')

@section('header')
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">
                <span class="h-2 w-2 animate-pulse rounded-full bg-green-500"></span>
                EcoScan AI
            </div>

            <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                Kenali Sampahmu dengan
                <span class="text-emerald-300">AI</span>
            </h1>

            <p class="mt-2 max-w-2xl text-sm text-green-50 sm:text-base">
                Unggah atau ambil foto sampah untuk mengetahui jenis dan kategorinya secara otomatis.
            </p>
        </div>
    </div>
@endsection

@section('content')
<div class="grid gap-6 lg:grid-cols-3">

    {{-- AREA SCAN --}}
    <form
        id="scanForm"
        action="{{ route('scan.store') }}"
        method="POST"
        enctype="multipart/form-data"
        class="relative overflow-hidden rounded-3xl border border-green-100 bg-white p-5 shadow-sm sm:p-7 lg:col-span-2"
    >
        @csrf

        {{-- Dekorasi --}}
        <div class="pointer-events-none absolute -right-20 -top-20 h-48 w-48 rounded-full bg-green-100 opacity-60 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-20 -left-20 h-48 w-48 rounded-full bg-emerald-100 opacity-50 blur-3xl"></div>

        {{-- Error Laravel --}}
        @if ($errors->any())
            <div class="relative mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <p class="font-semibold">⚠️ Terjadi kesalahan</p>
                <p class="mt-1">{{ $errors->first() }}</p>
            </div>
        @endif

        {{-- Judul --}}
        <div class="relative mb-6 text-center">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-green-100 text-2xl">
                ♻️
            </div>

            <h2 class="text-xl font-bold text-gray-900 sm:text-2xl">
                Upload Gambar Sampah
            </h2>

            <p class="mt-1 text-sm text-gray-500">
                Pilih gambar atau gunakan kamera untuk memulai pemindaian.
            </p>
        </div>

        {{-- Input gambar --}}
        <input
            type="file"
            name="image"
            id="image"
            accept="image/png,image/jpeg"
            class="hidden"
            required
        >

        {{-- Hasil deteksi --}}
        <input type="hidden" name="label" id="label">
        <input type="hidden" name="confidence" id="confidence">

        {{-- MENU UPLOAD --}}
        <div id="menu" class="relative grid gap-4 sm:grid-cols-2">

            <button
                type="button"
                id="btnPick"
                class="group rounded-2xl border-2 border-dashed border-green-200 bg-green-50/50 p-6 text-center transition hover:border-green-500 hover:bg-green-50"
            >
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white text-2xl shadow-sm transition group-hover:scale-105">
                    🖼️
                </div>

                <span class="block font-bold text-gray-800">
                    Pilih Gambar
                </span>

                <span class="mt-1 block text-xs text-gray-500">
                    JPG, JPEG, atau PNG
                </span>
            </button>

            <button
                type="button"
                id="btnCamera"
                class="group rounded-2xl bg-gradient-to-br from-green-700 to-emerald-600 p-6 text-center text-white shadow-lg shadow-green-100 transition hover:-translate-y-0.5 hover:from-green-800 hover:to-emerald-700"
            >
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-2xl backdrop-blur">
                    📷
                </div>

                <span class="block font-bold">
                    Buka Kamera
                </span>

                <span class="mt-1 block text-xs text-green-100">
                    Ambil foto secara langsung
                </span>
            </button>
        </div>

        {{-- CAMERA --}}
        <div id="cameraBox" class="relative hidden">

            <div class="overflow-hidden rounded-2xl bg-black shadow-lg">
                <video
                    id="video"
                    autoplay
                    playsinline
                    muted
                    class="mx-auto max-h-[60vh] w-full object-contain"
                ></video>
            </div>

            <div class="mt-4 grid grid-cols-2 gap-3">
                <button
                    type="button"
                    id="btnSnap"
                    class="rounded-xl bg-green-700 py-3.5 text-sm font-bold text-white shadow-sm transition hover:bg-green-800"
                >
                    📸 Ambil Foto
                </button>

                <button
                    type="button"
                    id="btnCancel"
                    class="rounded-xl border border-gray-200 bg-white py-3.5 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                >
                    Batal
                </button>
            </div>
        </div>

        <canvas id="canvas" class="hidden"></canvas>

        {{-- PREVIEW --}}
        <div id="previewBox" class="relative mt-6 hidden">

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-gray-50 p-3 shadow-inner">
                <div id="imageWrapper" class="relative mx-auto overflow-hidden rounded-xl">

                    <img
                        id="preview"
                        class="mx-auto max-h-72 w-full object-contain sm:max-h-96"
                        alt="Preview gambar sampah"
                    >

                    {{-- Bounding box YOLO --}}
                    <canvas
                        id="boxOverlay"
                        class="pointer-events-none absolute inset-0 h-full w-full"
                    ></canvas>

                    {{-- Animasi scan --}}
                    <div
                        id="scanLine"
                        class="pointer-events-none absolute left-0 right-0 top-0 hidden h-1 bg-green-400 shadow-[0_0_15px_rgba(74,222,128,0.9)]"
                    ></div>
                </div>
            </div>

            {{-- STATUS AI --}}
            <div
                id="aiStatusBox"
                class="mt-4 rounded-2xl border border-green-100 bg-green-50 p-4"
            >
                <div class="flex items-center gap-3">

                    <div
                        id="aiIcon"
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-green-100"
                    >
                        🤖
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium uppercase tracking-wide text-green-600">
                            Status AI
                        </p>

                        <p
                            id="aiStatus"
                            class="mt-0.5 text-sm font-semibold text-green-900"
                        >
                            Menunggu analisis...
                        </p>
                    </div>

                    <div id="loadingDots" class="hidden">
                        <div class="flex gap-1">
                            <span class="h-2 w-2 animate-bounce rounded-full bg-green-500"></span>
                            <span class="h-2 w-2 animate-bounce rounded-full bg-green-500 [animation-delay:150ms]"></span>
                            <span class="h-2 w-2 animate-bounce rounded-full bg-green-500 [animation-delay:300ms]"></span>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- TOMBOL SUBMIT --}}
        <button
            type="submit"
            id="submitBtn"
            disabled
            class="relative mt-6 hidden w-full overflow-hidden rounded-2xl bg-gradient-to-r from-green-700 to-emerald-600 py-4 font-bold text-white shadow-lg shadow-green-100 transition hover:-translate-y-0.5 hover:from-green-800 hover:to-emerald-700 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0"
        >
            <span class="relative flex items-center justify-center gap-2">
                🔍
                <span>Lihat Hasil Scan</span>
            </span>
        </button>

        <p class="relative mt-3 text-center text-xs text-gray-400">
            AI akan menganalisis gambar secara otomatis sebelum hasil disimpan.
        </p>
    </form>

    {{-- SIDEBAR TIPS --}}
    <aside class="h-fit space-y-4">

        {{-- Tips --}}
        <div class="rounded-3xl border border-green-100 bg-gradient-to-br from-green-50 to-emerald-50 p-6">

            <div class="mb-5 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-600 text-xl text-white shadow-sm">
                    💡
                </div>

                <div>
                    <p class="font-bold text-green-900">
                        Tips Scanning
                    </p>

                    <p class="text-xs text-green-700">
                        Agar hasil AI lebih akurat
                    </p>
                </div>
            </div>

            <div class="space-y-4">

                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sm shadow-sm">
                        ☀️
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-green-900">
                            Pencahayaan cukup
                        </p>

                        <p class="mt-0.5 text-xs leading-relaxed text-green-700">
                            Hindari foto yang terlalu gelap atau terlalu terang.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sm shadow-sm">
                        🗑️
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-green-900">
                            Satu jenis sampah
                        </p>

                        <p class="mt-0.5 text-xs leading-relaxed text-green-700">
                            Sebaiknya hanya satu objek sampah dalam satu foto.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sm shadow-sm">
                        🔎
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-green-900">
                            Objek terlihat jelas
                        </p>

                        <p class="mt-0.5 text-xs leading-relaxed text-green-700">
                            Dekatkan kamera dan pastikan objek tidak tertutup.
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- INFO AI --}}
        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm">

            <div class="mb-3 flex items-center gap-2">
                <span class="text-lg">🤖</span>

                <p class="font-bold text-gray-900">
                    Cara Kerja AI
                </p>
            </div>

            <div class="space-y-3 text-xs text-gray-500">

                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        1
                    </span>

                    <span>Upload atau ambil foto sampah</span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        2
                    </span>

                    <span>AI YOLO mendeteksi objek dari foto</span>
                </div>

                <div class="flex items-center gap-3">
                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-green-100 font-bold text-green-700">
                        3
                    </span>

                    <span>Hasil deteksi ditampilkan</span>
                </div>

            </div>
        </div>
    </aside>
</div>

{{-- JAVASCRIPT YOLO --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ==========================================
    // ELEMENT HTML
    // ==========================================

    const input = document.getElementById('image');
    const menu = document.getElementById('menu');

    const cameraBox = document.getElementById('cameraBox');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');

    const preview = document.getElementById('preview');
    const previewBox = document.getElementById('previewBox');
    const boxOverlay = document.getElementById('boxOverlay');

    const aiStatus = document.getElementById('aiStatus');
    const aiIcon = document.getElementById('aiIcon');
    const loadingDots = document.getElementById('loadingDots');
    const scanLine = document.getElementById('scanLine');

    const submitBtn = document.getElementById('submitBtn');

    const labelInput = document.getElementById('label');
    const confidenceInput = document.getElementById('confidence');

    const btnPick = document.getElementById('btnPick');
    const btnCamera = document.getElementById('btnCamera');
    const btnSnap = document.getElementById('btnSnap');
    const btnCancel = document.getElementById('btnCancel');

    const scanForm = document.getElementById('scanForm');

    // Route Laravel untuk deteksi YOLO
    const DETECT_URL = @json(route('scan.detect'));

    let stream = null;
    let previewObjectUrl = null;


    // ==========================================
    // STATUS AI
    // ==========================================

    function setStatus(message, icon = '🤖', isError = false) {
        aiStatus.textContent = message;
        aiIcon.textContent = icon;

        if (isError) {
            aiStatusBox.classList.remove(
                'border-green-100',
                'bg-green-50'
            );

            aiStatusBox.classList.add(
                'border-red-200',
                'bg-red-50'
            );

            aiStatus.classList.remove('text-green-900');
            aiStatus.classList.add('text-red-700');
        } else {
            aiStatusBox.classList.remove(
                'border-red-200',
                'bg-red-50'
            );

            aiStatusBox.classList.add(
                'border-green-100',
                'bg-green-50'
            );

            aiStatus.classList.remove('text-red-700');
            aiStatus.classList.add('text-green-900');
        }
    }

    function categoryForLabel(label) {
        const normalized = String(label || '').trim().toLowerCase();
        const categories = {
            organic: 'Organik',
            'organic waste': 'Organik',
            'food waste': 'Organik',
            food: 'Organik',
            biodegradable: 'Organik',
            compostable: 'Organik',
            inorganic: 'Anorganik',
            'inorganic waste': 'Anorganik',
            recyclable: 'Anorganik',
            plastic: 'Anorganik',
            metal: 'Anorganik',
            glass: 'Anorganik',
            paper: 'Anorganik',
            cardboard: 'Anorganik',
            residual: 'Residu',
            residu: 'Residu',
            residue: 'Residu',
            'general waste': 'Residu',
            'non-recyclable': 'Residu',
            'non recyclable': 'Residu',
            'hazardous waste': 'B3',
            hazardous: 'B3',
            'toxic waste': 'B3',
            battery: 'B3',
            chemical: 'B3',
            b3: 'B3',
            'e-waste': 'B3',
            medical: 'B3'
        };

        return categories[normalized] || null;
    }


    // ==========================================
    // RESET HASIL DETEKSI
    // ==========================================

    function resetDetection() {
        labelInput.value = '';
        confidenceInput.value = '';

        submitBtn.disabled = true;

        const ctx = boxOverlay.getContext('2d');

        ctx.clearRect(
            0,
            0,
            boxOverlay.width,
            boxOverlay.height
        );
    }


    // ==========================================
    // TAMPILKAN PREVIEW
    // ==========================================

    function showPreview(file) {

        if (!file) return;

        const allowedTypes = [
            'image/jpeg',
            'image/png'
        ];

        if (!allowedTypes.includes(file.type)) {
            alert('Format gambar harus JPG, JPEG, atau PNG.');
            return;
        }

        // Batas ukuran 5 MB
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran gambar maksimal 5 MB.');
            return;
        }

        resetDetection();

        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
        }

        previewBox.classList.remove('hidden');
        submitBtn.classList.remove('hidden');

        setStatus('Menyiapkan gambar...', '⏳');

        previewObjectUrl = URL.createObjectURL(file);

        preview.onload = async function () {
            await classify(file);
        };

        preview.onerror = function () {
            setStatus('Gambar gagal dimuat.', '⚠️', true);
        };

        preview.src = previewObjectUrl;
    }


    // ==========================================
    // DETEKSI YOLO MELALUI LARAVEL
    // ==========================================

    async function classify(file) {

        submitBtn.disabled = true;

        labelInput.value = '';
        confidenceInput.value = '';

        setStatus('YOLO sedang mendeteksi objek...', '🤖');

        loadingDots.classList.remove('hidden');
        scanLine.classList.remove('hidden');

        try {

            const formData = new FormData();

            // Nama field harus "image" untuk Laravel
            formData.append('image', file);

            const csrfToken = document.querySelector(
                'input[name="_token"]'
            ).value;

            const response = await fetch(DETECT_URL, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });

            let data;

            try {
                data = await response.json();
            } catch {
                throw new Error('Server tidak mengirim respons JSON.');
            }

            if (!response.ok) {
                throw new Error(
                    data.message || 'Deteksi gagal. Periksa server YOLO.'
                );
            }

            const detections = data.detections || [];

            // Gambar bounding box
            drawBoxes(
                detections,
                data.image_width,
                data.image_height
            );

            if (detections.length === 0) {
                resetDetection();

                setStatus(
                    'Tidak ada objek sampah yang terdeteksi. Coba foto lain.',
                    '⚠️',
                    true
                );

                return;
            }

            // Ambil objek dengan confidence tertinggi
            const best = detections.reduce((previous, current) => {
                return current.confidence > previous.confidence
                    ? current
                    : previous;
            });

            const confidence = Number(best.confidence);
            const percentage = (confidence * 100).toFixed(2);

            // Simpan hasil ke hidden input
            labelInput.value = best.class_name;
            confidenceInput.value = percentage;

            const category = categoryForLabel(best.class_name);
            const status = category
                ? `${confidence >= 0.8 ? 'Terdeteksi' : 'Prediksi'}: ${category} — ${best.class_name} (${percentage}%)`
                : `Jenis belum dikenali: ${best.class_name} (${percentage}%)`;

            setStatus(status, category && confidence >= 0.8 ? '✅' : '🔎');

            submitBtn.disabled = false;

        } catch (error) {

            console.error('YOLO Detection Error:', error);

            resetDetection();

            setStatus(
                error.message || 'Terjadi kesalahan saat mendeteksi gambar.',
                '⚠️',
                true
            );

        } finally {

            loadingDots.classList.add('hidden');
            scanLine.classList.add('hidden');
        }
    }


    // ==========================================
    // GAMBAR BOUNDING BOX YOLO
    // ==========================================

    function drawBoxes(detections, imageWidth, imageHeight) {

        const ctx = boxOverlay.getContext('2d');

        const width = preview.clientWidth;
        const height = preview.clientHeight;

        const dpr = window.devicePixelRatio || 1;

        boxOverlay.width = Math.round(width * dpr);
        boxOverlay.height = Math.round(height * dpr);

        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

        ctx.clearRect(0, 0, width, height);

        if (!imageWidth || !imageHeight) {
            return;
        }

        // Menghitung skala gambar
        const scale = Math.min(
            width / imageWidth,
            height / imageHeight
        );

        // Posisi gambar di dalam area preview
        const offsetX = (width - imageWidth * scale) / 2;
        const offsetY = (height - imageHeight * scale) / 2;

        detections.forEach(function (item) {

            if (!item.box || item.box.length !== 4) {
                return;
            }

            const [x1, y1, x2, y2] = item.box;

            const x = offsetX + x1 * scale;
            const y = offsetY + y1 * scale;

            const boxWidth = (x2 - x1) * scale;
            const boxHeight = (y2 - y1) * scale;

            const confidence = (
                Number(item.confidence) * 100
            ).toFixed(1);

            const text = `${item.class_name} ${confidence}%`;

            // Kotak deteksi
            ctx.strokeStyle = '#22c55e';
            ctx.lineWidth = 3;

            ctx.strokeRect(
                x,
                y,
                boxWidth,
                boxHeight
            );

            // Label deteksi
            ctx.font = 'bold 13px Arial';

            const textWidth = ctx.measureText(text).width;
            const textHeight = 22;

            const textY = Math.max(0, y - textHeight);

            ctx.fillStyle = '#16a34a';

            ctx.fillRect(
                x,
                textY,
                textWidth + 12,
                textHeight
            );

            ctx.fillStyle = '#ffffff';

            ctx.fillText(
                text,
                x + 6,
                textY + 15
            );
        });
    }


    // ==========================================
    // PILIH GAMBAR
    // ==========================================

    btnPick.addEventListener('click', function () {

        input.removeAttribute('capture');
        input.click();

    });


    input.addEventListener('change', function () {

        const file = input.files[0];

        if (file) {
            showPreview(file);
        }

    });


    // ==========================================
    // BUKA KAMERA
    // ==========================================

    btnCamera.addEventListener('click', async function () {

        if (
            !navigator.mediaDevices ||
            !navigator.mediaDevices.getUserMedia
        ) {
            input.setAttribute('capture', 'environment');
            input.click();

            return;
        }

        try {

            stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: {
                        ideal: 'environment'
                    }
                },
                audio: false
            });

            video.srcObject = stream;

            await video.play();

            menu.classList.add('hidden');
            cameraBox.classList.remove('hidden');

        } catch (error) {

            console.error('Camera Error:', error);

            alert(
                'Kamera tidak bisa dibuka. Pastikan izin kamera sudah diizinkan di browser.'
            );

        }
    });


    // ==========================================
    // MATIKAN KAMERA
    // ==========================================

    function stopCamera() {

        if (stream) {

            stream.getTracks().forEach(function (track) {
                track.stop();
            });

            stream = null;
        }

        video.srcObject = null;

        cameraBox.classList.add('hidden');
        menu.classList.remove('hidden');
    }


    // ==========================================
    // AMBIL FOTO DARI KAMERA
    // ==========================================

    btnSnap.addEventListener('click', function () {

        if (!video.videoWidth || !video.videoHeight) {
            alert('Kamera belum siap. Coba beberapa saat lagi.');
            return;
        }

        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;

        const ctx = canvas.getContext('2d');

        ctx.drawImage(
            video,
            0,
            0,
            canvas.width,
            canvas.height
        );

        canvas.toBlob(function (blob) {

            if (!blob) {
                alert('Gagal mengambil foto dari kamera.');
                return;
            }

            const file = new File(
                [blob],
                'kamera.jpg',
                {
                    type: 'image/jpeg'
                }
            );

            // Masukkan foto kamera ke input file
            const dataTransfer = new DataTransfer();

            dataTransfer.items.add(file);

            input.files = dataTransfer.files;

            // Matikan kamera
            stopCamera();

            // Tampilkan preview dan deteksi
            showPreview(file);

        }, 'image/jpeg', 0.9);

    });


    // ==========================================
    // BATAL KAMERA
    // ==========================================

    btnCancel.addEventListener('click', function () {
        stopCamera();
    });


    // ==========================================
    // VALIDASI SEBELUM SIMPAN
    // ==========================================

    scanForm.addEventListener('submit', function (event) {

        if (!input.files.length) {
            event.preventDefault();

            alert('Pilih atau ambil foto sampah terlebih dahulu.');
            return;
        }

        if (!labelInput.value || !confidenceInput.value) {
            event.preventDefault();

            alert('Tunggu sampai AI selesai mendeteksi gambar.');
            return;
        }

        submitBtn.disabled = true;

        submitBtn.querySelector('span span').textContent =
            'Menyimpan hasil scan...';
    });


    // ==========================================
    // BERSIHKAN KAMERA DAN PREVIEW
    // ==========================================

    window.addEventListener('beforeunload', function () {

        if (stream) {
            stream.getTracks().forEach(function (track) {
                track.stop();
            });
        }

        if (previewObjectUrl) {
            URL.revokeObjectURL(previewObjectUrl);
        }
    });

});
</script>
@endsection
