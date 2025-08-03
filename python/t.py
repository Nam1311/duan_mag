import os
import requests

OLLAMA_MODEL    = os.getenv("OLLAMA_MODEL", "llama3:8b")
OLLAMA_ENDPOINT = os.getenv("OLLAMA_ENDPOINT", "http://localhost:11434")

payload = {
    "model":   OLLAMA_MODEL,
    "prompt":  "Xin chào, bạn là ai?",   # ← phải dùng "prompt"
    "stream":  False,
    "options": {
        "temperature": 0.3,
        "num_predict": 150
    }
}

res = requests.post(f"{OLLAMA_ENDPOINT}/api/generate", json=payload, timeout=60)
res.raise_for_status()
data = res.json()

print("Full response:", data)
print("Answer:", data.get("response", "<empty>"))
