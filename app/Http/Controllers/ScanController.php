<?php

namespace App\Http\Controllers;

use App\Models\Scan;
use App\Models\WasteType;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScanController extends Controller
{
    private const MAX_SIDE = 600;

    private const LABEL_ALIASES = [
        'organic waste' => 'organic',
        'food waste' => 'organic',
        'food' => 'organic',
        'biodegradable' => 'organic',
        'compostable' => 'organic',
        'inorganic' => 'recyclable',
        'inorganic waste' => 'recyclable',
        'residu' => 'residual',
        'residue' => 'residual',
        'general waste' => 'residual',
        'non-recyclable' => 'residual',
        'non recyclable' => 'residual',
        'hazardous' => 'hazardous waste',
        'toxic waste' => 'hazardous waste',
        'battery' => 'hazardous waste',
        'chemical' => 'hazardous waste',
        'b3' => 'hazardous waste',
        'e-waste' => 'hazardous waste',
        'medical' => 'hazardous waste',
    ];

    public function index()
    {
        return view('scan');
    }

   public function store(Request $request, GamificationService $gamification)
{
    $request->validate([
        'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
    ]);

    $file = $request->file('image');

    // Kirim foto ke API YOLO
    try {
        $response = Http::timeout(60)
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->post(rtrim(env('YOLO_API_URL', 'http://192.168.1.7:8001'), '/') . '/api/detect');

        if (! $response->successful()) {
            return back()->withErrors([
                'image' => 'YOLO gagal memproses gambar. Coba lagi.',
            ])->withInput();
        }

        $detections = $response->json('detections', []);
        $annotatedImage = $response->json('annotated_image');

    } catch (\Throwable $e) {
        return back()->withErrors([
            'image' => 'Tidak dapat terhubung ke server YOLO. Pastikan FastAPI sedang berjalan.',
        ])->withInput();
    }

    // Ambil hasil deteksi dengan confidence tertinggi
    if (empty($detections)) {
        return back()->withErrors([
            'image' => 'YOLO tidak menemukan objek sampah pada gambar.',
        ])->withInput();
    }

    $bestDetection = $detections[0];

    $label = $bestDetection['class_name'] ?? null;
    $confidence = isset($bestDetection['confidence'])
        ? round($bestDetection['confidence'] * 100, 2)
        : 0;
    $detectionSummary = $this->buildDetectionSummary($detections);

    // Cari jenis sampah di database
    $normalizedLabel = strtolower(trim($label ?? ''));
    $canonicalLabel = self::LABEL_ALIASES[$normalizedLabel] ?? $normalizedLabel;
    $wasteType = null;

    if ($label) {
        $wasteType = WasteType::where('label', $canonicalLabel)->first()
            ?? WasteType::where('label', $normalizedLabel)->first();
    }

    $hash = sha1_file($file->getRealPath());
    $userId = $request->user()->id;

    $duplicate = Scan::where('user_id', $userId)
        ->where('image_hash', $hash)
        ->exists();

    $imageData = $this->toDataUri($file->getRealPath());

    $scan = Scan::create([
        'user_id'       => $userId,
        'waste_type_id' => $wasteType?->id,
        'detected_label' => $label,
        'detection_summary' => $detectionSummary,
        'image_path'    => null,
        'image_data'    => $imageData,
        'annotated_image_data' => is_string($annotatedImage) && str_starts_with($annotatedImage, 'data:image/jpeg;base64,')
            ? $annotatedImage
            : null,
        'confidence'    => $confidence,
        'image_hash'    => $hash,
    ]);

    $reward = $gamification->awardScan($scan, $duplicate);

    return redirect()
        ->route('scan.show', $scan)
        ->with('gamification', $reward);
}

    public function show(Scan $scan)
    {
        abort_unless($scan->user_id === auth()->id(), 403);

        $scan->load('wasteType.category');

        if (! $scan->wasteType && $scan->detected_label) {
            $normalizedLabel = strtolower(trim($scan->detected_label));
            $canonicalLabel = self::LABEL_ALIASES[$normalizedLabel] ?? $normalizedLabel;
            $matchedWasteType = WasteType::with('category')
                ->where('label', $canonicalLabel)
                ->first()
                ?? WasteType::with('category')->where('label', $normalizedLabel)->first();

            if ($matchedWasteType) {
                $scan->setRelation('wasteType', $matchedWasteType);
            }
        }

        $categoryNames = ['Organik', 'Anorganik', 'Residu', 'B3'];
        $categoryScores = array_fill_keys($categoryNames, 0.0);
        $storedCategories = data_get($scan->detection_summary, 'categories', []);
        $legacyCategory = data_get($scan->detection_summary, 'category');

        if ($legacyCategory) {
            $storedCategories[] = $legacyCategory;
        }

        foreach ($storedCategories as $category) {
            if (isset($categoryScores[$category['name']])) {
                $categoryScores[$category['name']] = (float) $category['confidence'];
            }
        }

        if (max($categoryScores) === 0.0 && $scan->detected_label) {
            $detectedCategory = $this->categoryForLabel($scan->detected_label);

            if ($detectedCategory !== null) {
                $categoryScores[$detectedCategory] = (float) ($scan->confidence ?? 0);
            }
        }

        $topCategories = [];

        foreach ($categoryScores as $name => $confidence) {
            $topCategories[] = [
                'name' => $name,
                'confidence' => $confidence,
            ];
        }

        usort($topCategories, fn (array $first, array $second): int => $second['confidence'] <=> $first['confidence']);

        return view('hasil', compact('scan', 'topCategories'));
    }

    /**
     * @param array<int, array<string, mixed>> $detections
     * @return array{categories: array<int, array{name: string, confidence: float}>}
     */
    private function buildDetectionSummary(array $detections): array
    {
        $categoryScores = [];

        foreach (['Organik', 'Anorganik', 'Residu', 'B3'] as $category) {
            $categoryScores[$category] = [
                'name' => $category,
                'confidence' => 0.0,
                'raw_confidence' => 0.0,
            ];
        }

        foreach ($detections as $detection) {
            $label = trim((string) ($detection['class_name'] ?? ''));
            $confidence = $detection['confidence'] ?? null;

            if ($label === '' || ! is_numeric($confidence)) {
                continue;
            }

            $confidence = (float) $confidence;
            $percentage = round($confidence * 100, 2);

            $category = $this->categoryForLabel($label);

            if ($category !== null && (! isset($categoryScores[$category]) || $confidence > $categoryScores[$category]['raw_confidence'])) {
                $categoryScores[$category] = [
                    'name' => $category,
                    'confidence' => $percentage,
                    'raw_confidence' => $confidence,
                ];
            }
        }

        usort($categoryScores, fn (array $first, array $second): int => $second['raw_confidence'] <=> $first['raw_confidence']);
        $topCategories = array_slice(array_values($categoryScores), 0, 4);
        $topCategories = array_map(function (array $category): array {
            unset($category['raw_confidence']);

            return $category;
        }, $topCategories);

        return [
            'categories' => $topCategories,
        ];
    }

    private function categoryForLabel(string $label): ?string
    {
        $normalizedLabel = strtolower(trim($label));
        $canonicalLabel = self::LABEL_ALIASES[$normalizedLabel] ?? $normalizedLabel;

        return match ($canonicalLabel) {
            'organic' => 'Organik',
            'recyclable', 'plastic', 'metal', 'glass', 'paper', 'cardboard' => 'Anorganik',
            'residual' => 'Residu',
            'hazardous waste' => 'B3',
            default => null,
        };
    }

    /** Kecilkan foto lalu ubah jadi data URI JPEG supaya bisa disimpan di database. */
    private function toDataUri(string $path): ?string
    {
        $info = @getimagesize($path);
        if (! $info) {
            return null;
        }

        $src = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
            IMAGETYPE_PNG  => @imagecreatefrompng($path),
            default        => false,
        };
        if (! $src) {
            return null;
        }

        // Putar sesuai orientasi kamera HP (kalau ada)
        if ($info[2] === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $orientation = @exif_read_data($path)['Orientation'] ?? 1;
            $angle = match ($orientation) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
            if ($angle !== 0 && ($rotated = imagerotate($src, $angle, 0))) {
                $src = $rotated;
            }
        }

        $w = imagesx($src);
        $h = imagesy($src);
        $scale = min(1, self::MAX_SIDE / max($w, $h));
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));

        $canvas = imagecreatetruecolor($nw, $nh);
        imagefill($canvas, 0, 0, imagecolorallocate($canvas, 255, 255, 255));
        imagecopyresampled($canvas, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

        ob_start();
        imagejpeg($canvas, null, 75);
        $jpeg = ob_get_clean();

        return 'data:image/jpeg;base64,' . base64_encode($jpeg);
    }

    public function detectPreview(Request $request)
{
    $request->validate([
        'image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
    ]);

    try {
        $file = $request->file('image');

        $response = Http::timeout(60)
            ->attach(
                'file',
                file_get_contents($file->getRealPath()),
                $file->getClientOriginalName()
            )
            ->post(
                rtrim(env('YOLO_API_URL', 'http://192.168.1.7:8001'), '/')
                . '/api/detect'
            );

        if (!$response->successful()) {
            return response()->json([
                'message' => 'YOLO gagal mendeteksi gambar.',
            ], 502);
        }

        return response()->json($response->json());

    } catch (\Throwable $e) {
        return response()->json([
            'message' => 'Tidak dapat terhubung ke server YOLO.',
        ], 500);
    }
}
}
