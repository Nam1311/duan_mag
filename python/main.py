from fastapi import FastAPI
from routers import tryon
from routers.test import router as ui_router
from routers.article_generator import router as ui_article_generator
from routers.chatbox import router as chatbox_router

from fastapi.templating import Jinja2Templates
from fastapi import Request


import logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)


from fastapi.middleware.cors import CORSMiddleware
import google.generativeai as genai
import os
from dotenv import load_dotenv
load_dotenv()

genai.configure(api_key=os.getenv("GEMINI_API_KEY"))
model = genai.GenerativeModel("models/gemini-2.5-flash")
app = FastAPI()
templates = Jinja2Templates(directory="frontend")


# Allow frontend to connect
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # or ["http://localhost:3000"] for React
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

active_connections = []
app.include_router(tryon.router, prefix="/api")
app.include_router(ui_router, prefix="/api")
app.include_router(ui_article_generator, prefix="/api")
app.include_router(chatbox_router, prefix="/api")



# @app.get("/chat")
# async def chat_interface(request: Request):
#     logger.info("Đang xử lý yêu cầu /chat")
#     return templates.TemplateResponse("index.html", {"request": request})
