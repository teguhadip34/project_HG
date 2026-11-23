# Hierarchical Waste Recognition (YOLOv8 + TensorFlow.js)

## Project structure

```text
waste_ai_hierarchical/
├── index.html
├── server.py
├── requirements.txt
├── models/
│   └── best.pt
└── web_model/
    ├── model.json
    └── weights.bin
```

## How the pipeline works

1. The browser camera captures a frame.
2. The Python API runs YOLOv8 object detection and returns bounding boxes.
3. The browser draws the boxes and crops the highest-confidence detected object.
4. The crop is passed to your existing TensorFlow.js / Teachable Machine model.
5. The seven-class material prediction is mapped to a broad waste category.

## Material-to-category mapping in this first version

| Material classifier output | Broad category |
|---|---|
| e-waste | Hazardous waste |
| medical | Hazardous waste |
| plastic | Recyclable waste |
| metal | Recyclable waste |
| cardboard | Recyclable waste |
| paper | Recyclable waste |
| glass | Recyclable waste |

General waste and organic waste are not inferred from missing labels. They require training examples and additional output classes in a retrained material classifier. Low-confidence predictions display `Unclassified`.

## Setup (Windows)

Install Python 3.10 or 3.11, then open Command Prompt in this project folder:

```bat
py -m venv .venv
.venv\Scripts\activate
python -m pip install --upgrade pip
pip install -r requirements.txt
```

If PyTorch installation fails on your machine, install the appropriate PyTorch build from the official PyTorch installation selector, then run `pip install -r requirements.txt` again.

Start the app:

```bat
uvicorn server:app --host 127.0.0.1 --port  
```

Open this address in Chrome:

```text
http://127.0.0.1:8000
```

Use the page served by FastAPI rather than double-clicking `index.html`. The camera needs localhost or HTTPS, and the YOLO API is served from the same origin.

## Important notes

- `models/best.pt` is the YOLOv8n checkpoint from `waste_classifier-3` in the uploaded training-results ZIP. That run's final recorded validation metrics were precision 0.67829, recall 0.62943, mAP50 0.64525, and mAP50-95 0.47545 after 10 epochs. These are training-run metrics, not a guarantee of real-world performance.
- The original ZIP did not include `data.yaml`, so the YOLO class names cannot be independently checked here. The API reads class names directly from the checkpoint.
- This first implementation classifies only the highest-confidence YOLO detection per button press. It draws all detections, but does not yet classify every detected object.
- The material model expects 224x224 RGB inputs normalized by 255, matching the preprocessing already used in the uploaded HTML. Verify this against the original training settings.
- The `MATERIAL_CLASSES` order must remain identical to the model's training-label order.
- Hazardous-waste classification is a visual screening aid only. Do not use it as the sole basis for handling or disposal decisions.
