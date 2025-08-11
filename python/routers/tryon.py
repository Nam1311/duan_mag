from fastapi import APIRouter, UploadFile, File, Form, HTTPException
from fastapi.responses import JSONResponse
from utils.base64_helpers import array_buffer_to_base64
from dotenv import load_dotenv
import os
from google import genai
from google.genai import types
import traceback
import base64
import mediapipe as mp
import cv2
import numpy as np

load_dotenv()

router = APIRouter()

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
if not GEMINI_API_KEY:
    raise ValueError("Missing GEMINI_API_KEY in .env")

client = genai.Client(api_key=GEMINI_API_KEY)

# Khởi tạo Face Mesh
mp_face_mesh = mp.solutions.face_mesh
face_mesh = mp_face_mesh.FaceMesh(static_image_mode=True, max_num_faces=1)

@router.post("/try-on")
async def try_on(
    person_image: UploadFile = File(...),
    cloth_image: UploadFile = File(...),
    instructions: str = Form(""),
    model_type: str = Form(""),
    gender: str = Form(""),
    garment_type: str = Form(""),
    style: str = Form(""),
):
    try:
        
        MAX_IMAGE_SIZE_MB = 10
        ALLOWED_MIME_TYPES = {
            "image/jpeg",
            "image/png",
            "image/webp",
            "image/heic",
            "image/heif",
        }

        if person_image.content_type not in ALLOWED_MIME_TYPES:
            raise HTTPException(
                status_code=400, detail=f"Unsupported file type for person_image: {person_image.content_type}"
            )

        user_bytes = await person_image.read()

        size_in_mb_for_person_image = len(user_bytes) / (1024 * 1024)
        if size_in_mb_for_person_image > MAX_IMAGE_SIZE_MB:
            raise HTTPException(status_code=400, detail="Image exceeds 10MB size limit for person_image")
        
        if cloth_image.content_type not in ALLOWED_MIME_TYPES:
            raise HTTPException(
                status_code=400, detail=f"Unsupported file type for cloth_image: {cloth_image.content_type}"
            )

        cloth_bytes = await cloth_image.read()

        size_in_mb_for_cloth_image = len(cloth_bytes) / (1024 * 1024)
        if size_in_mb_for_cloth_image > MAX_IMAGE_SIZE_MB:
            raise HTTPException(status_code=400, detail="Image exceeds 10MB size limit for cloth_image")


        user_b64 = array_buffer_to_base64(user_bytes)
        cloth_b64 = array_buffer_to_base64(cloth_bytes)

        prompt = f"""
{{
    "objective": "Virtual try-on with PERFECT FACE AND HAIR PRESERVATION as absolute priority",
    "task": "High-Fidelity Virtual Try-On with STRICT Face Preservation, Garment Integration, and Background Replacement", 

    "inputs": {{
        "person_image": {{
            "description": "Source image containing the target person. The FACE MUST BE PRESERVED EXACTLY AS IS - NO CHANGES ALLOWED TO ANY FACIAL FEATURES. Use for identity, face, skin tone, pose, body shape, and hair.",
            "id": "input_1"
        }},
        "garment_image": {{
            "description": "Source image containing the target clothing item. Used strictly for the clothing's visual properties.",
            "id": "input_2"
        }}
    }},

    "processing_steps": [
        "STEP 1: ISOLATE AND PRESERVE THE EXACT FACE from 'person_image' (input_1) with 100% fidelity. DO NOT MODIFY, REGENERATE OR ENHANCE ANY FACIAL FEATURES.",
        "STEP 2: Extract the clothing item from 'garment_image' (input_2).",
        "STEP 3: Apply the clothing to the person while KEEPING THE FACE COMPLETELY INTACT.",
        "STEP 4: Generate a suitable background that complements the outfit."
    ],

    "output_requirements": {{
        "description": "Generate an image where the person wears the new clothing while PRESERVING THEIR EXACT FACIAL FEATURES, EXPRESSION, AND IDENTITY with 100% accuracy.",
        "quality": "Photorealistic, with perfect face preservation being the absolute highest priority."
    }},

    "core_constraints": {{
        "identity_lock": {{
            "priority": "MAXIMUM CRITICAL OVERRIDE",
            "instruction": "FACE AND HAIR MUST BE PRESERVED PIXEL-FOR-PIXEL FROM THE ORIGINAL IMAGE. NO CHANGES, NO ENHANCEMENTS, NO RECREATION. The face and hair regions must be treated as UNTOUCHABLE MASKS that are copied directly from the source image."
        }},
        "face_and_hair_definition": {{
            "priority": "ABSOLUTE CRITICAL",
            "instruction": "The face includes ALL features from hairline to chin and ear to ear. The HAIR includes the ENTIRE HAIRSTYLE, LENGTH, COLOR, AND TEXTURE. PRESERVE EXACTLY AS IN THE ORIGINAL IMAGE."
        }},
        "garment_fidelity": {{
            "priority": "HIGH (but secondary to face preservation)",
            "instruction": "Preserve the exact color, pattern, texture, and design details of the clothing item."
        }},
        "background_replacement": {{
            "priority": "MEDIUM (tertiary priority)",
            "instruction": "Generate a new and different background that complements the outfit."
        }}
    }},

    "prohibitions": [
        "ABSOLUTELY DO NOT alter, enhance, beautify, or recreate ANY part of the face",
        "DO NOT modify facial features, expression, skin tone, or makeup",
        "DO NOT 'improve' or 'fix' the face in any way",
        "DO NOT generate new facial details not present in the original image",
        "DO NOT apply filters or effects that would change facial appearance"
    ],
    
    "face_preservation_methodology": {{
        "technique": "DIRECT FACE TRANSFER",
        "steps": [
            "1. Precisely identify face region in source image",
            "2. Copy this region EXACTLY without modifications",
            "3. Position the face at exact same angle and position",
            "4. Blend ONLY at the very edges where face meets rest of the image",
            "5. Double-check that NO facial features have been modified"
        ]
    }}
}}

You are a virtual fashion stylist specialized in PERFECT FACE PRESERVATION.
Create a realistic try-on visualization of the uploaded clothing onto the person image.
The FACE MUST BE PRESERVED EXACTLY AS IN THE ORIGINAL IMAGE - this is the HIGHEST PRIORITY.

Match the following context:
- Model Type: {model_type}
- Gender: {gender}
- Garment Type: {garment_type}
- Style: {style}
- Special Instructions: {instructions}

Return image of try on and a short caption in Vietnamese.
"""
               
        print(model_type)
        print(gender)
        print(garment_type)
        print(style)
        print(instructions)
        
        print(prompt)

        contents=[
            prompt,
            types.Part.from_bytes(
                data=user_b64,
                mime_type= person_image.content_type,
            ),
            types.Part.from_bytes(
                data=cloth_b64,
                mime_type= cloth_image.content_type,
            ),
        ]        
        
        # Thêm các cài đặt cho API call để giảm sáng tạo
        response = client.models.generate_content(
            model="gemini-2.0-flash-exp-image-generation",
            contents=contents,
            config=types.GenerateContentConfig(
                response_modalities=['TEXT', 'IMAGE'],
                temperature=0.1,  # Thêm nhiệt độ thấp để giảm sáng tạo
                top_p=0.1,        # Giảm đa dạng
                top_k=1           # Giới hạn lựa chọn
            )
        )


        print(response)
        
        image_data = None
        text_response = "No Description available."
        if response.candidates and len(response.candidates) > 0:
            parts = response.candidates[0].content.parts

            if parts:
                print("Number of parts in response:", len(parts))

                for part in parts:
                    if hasattr(part, "inline_data") and part.inline_data:
                        image_data = part.inline_data.data
                        image_mime_type = getattr(part.inline_data, "mime_type", "image/png")
                        print("Image data received, length:", len(image_data))
                        print("MIME type:", image_mime_type)

                    elif hasattr(part, "text") and part.text:
                        text_response = part.text
                        preview = (text_response[:100] + "...") if len(text_response) > 100 else text_response
                        print("Text response received:", preview)
            else:
                print("No parts found in the response candidate.")
        else:
            print("No candidates found in the API response.")

        image_url = None
        if image_data:
            image_base64 = base64.b64encode(image_data).decode("utf-8")
            image_url = f"data:{image_mime_type};base64,{image_base64}"
        else:
            image_url = None

        # Phát hiện khuôn mặt với MediaPipe
        user_img_cv = cv2.imdecode(np.frombuffer(user_bytes, np.uint8), cv2.IMREAD_COLOR)
        rgb_image = cv2.cvtColor(user_img_cv, cv2.COLOR_BGR2RGB)
        results = face_mesh.process(rgb_image)

        face_mask = np.zeros(user_img_cv.shape[:2], dtype=np.uint8)
        if results.multi_face_landmarks:
            landmarks = results.multi_face_landmarks[0]
            # Tìm các điểm biên của khuôn mặt và tóc
            points = []
            for landmark in landmarks.landmark:
                x = int(landmark.x * user_img_cv.shape[1])
                y = int(landmark.y * user_img_cv.shape[0])
                points.append([x, y])
            
            # Mở rộng vùng phát hiện để đảm bảo bao gồm tóc
            points = np.array(points)
            x, y, w, h = cv2.boundingRect(points)
            expanded_y = max(0, y - int(h * 0.7))
            
            # Vẽ mặt nạ cho khuôn mặt và tóc
            cv2.rectangle(face_mask, (x-20, expanded_y), (x+w+20, y+h+15), 255, -1)

        # Áp dụng mặt nạ lên hình ảnh người dùng để tách khuôn mặt và tóc
        user_face_hair_b64 = array_buffer_to_base64(cv2.imencode('.png', cv2.bitwise_and(user_img_cv, user_img_cv, mask=face_mask))[1].tobytes())

        contents=[
            prompt,
            types.Part.from_bytes(
                data=user_b64,
                mime_type= person_image.content_type,
            ),
            types.Part.from_bytes(
                data=cloth_b64,
                mime_type= cloth_image.content_type,
            ),
            types.Part.from_bytes(
                data=user_face_hair_b64,
                mime_type= person_image.content_type,
            ),
        ]        

        # Gọi lại API với hình ảnh đã tách khuôn mặt và tóc
        response = client.models.generate_content(
            model="gemini-2.0-flash-exp-image-generation",
            contents=contents,
            config=types.GenerateContentConfig(
                response_modalities=['TEXT', 'IMAGE'],
                temperature=0.1,
                top_p=0.1,
                top_k=1
            )
        )

        print(response)
        
        image_data = None
        text_response = "No Description available."
        if response.candidates and len(response.candidates) > 0:
            parts = response.candidates[0].content.parts

            if parts:
                print("Number of parts in response:", len(parts))

                for part in parts:
                    if hasattr(part, "inline_data") and part.inline_data:
                        image_data = part.inline_data.data
                        image_mime_type = getattr(part.inline_data, "mime_type", "image/png")
                        print("Image data received, length:", len(image_data))
                        print("MIME type:", image_mime_type)

                    elif hasattr(part, "text") and part.text:
                        text_response = part.text
                        preview = (text_response[:100] + "...") if len(text_response) > 100 else text_response
                        print("Text response received:", preview)
            else:
                print("No parts found in the response candidate.")
        else:
            print("No candidates found in the API response.")

        image_url = None
        if image_data:
            image_base64 = base64.b64encode(image_data).decode("utf-8")
            image_url = f"data:{image_mime_type};base64,{image_base64}"
        else:
            image_url = None
    
        return JSONResponse(
        content={
            "image": image_url,
            "text": text_response,
        }
        )

    except Exception as e:
        print(f"Error in /api/try-on endpoint: {e}")
        traceback.print_exc()
        raise HTTPException(status_code=500, detail="Internal Server Error")
