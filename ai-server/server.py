import os
from pathlib import Path
from io import BytesIO

from fastapi import FastAPI, UploadFile, File, HTTPException
from fastapi.responses import FileResponse
from fastapi.staticfiles import StaticFiles
from PIL import Image
from ultralytics import YOLO

ROOT = Path(__file__).resolve().parent
WEIGHTS = ROOT / "models" / "best.pt"

app = FastAPI(title="Waste YOLO Detection API")
model = None

@app.on_event("startup")
def load_yolo():
    global model
    if not WEIGHTS.exists():
        raise RuntimeError(f"YOLO weights not found: {WEIGHTS}")
    model = YOLO(str(WEIGHTS))

@app.get("/api/health")
def health():
    return {
        "model_loaded": model is not None,
        "model_file": WEIGHTS.name,
        "class_names": model.names if model is not None else {}
    }

@app.post("/api/detect")
async def detect(file: UploadFile = File(...)):
    if model is None:
        raise HTTPException(status_code=503, detail="YOLO model is not loaded.")
    if not file.content_type or not file.content_type.startswith("image/"):
        raise HTTPException(status_code=400, detail="Upload must be an image.")
    try:
        image_bytes = await file.read()
        image = Image.open(BytesIO(image_bytes)).convert("RGB")
        width, height = image.size
        results = model.predict(
            source=image,
            conf=0.20,
            imgsz=384,
            verbose=False
        )
        detections = []
        for result in results:
            if result.boxes is None:
                continue
            for box in result.boxes:
                coords = box.xyxy[0].cpu().tolist()
                class_id = int(box.cls[0].cpu().item())
                confidence = float(box.conf[0].cpu().item())
                names = model.names
                class_name = names.get(class_id, str(class_id)) if isinstance(names, dict) else names[class_id]
                x1, y1, x2, y2 = coords
                detections.append({
                    "class_id": class_id,
                    "class_name": str(class_name),
                    "confidence": confidence,
                    "box": [
                        max(0.0, min(float(width), x1)),
                        max(0.0, min(float(height), y1)),
                        max(0.0, min(float(width), x2)),
                        max(0.0, min(float(height), y2))
                    ]
                })
        detections.sort(key=lambda item: item["confidence"], reverse=True)
        return {"image_width": width, "image_height": height, "detections": detections}
    except HTTPException:
        raise
    except Exception as exc:
        raise HTTPException(status_code=500, detail=str(exc))

@app.get("/")
def index():
    return FileResponse(ROOT / "index.html")

# Serve the Teachable Machine model files and any other frontend assets.
app.mount("/", StaticFiles(directory=ROOT, html=True), name="static")
