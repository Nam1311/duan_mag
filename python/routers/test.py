import aiomysql
from fastapi import APIRouter, Form, Request
from fastapi.responses import HTMLResponse, JSONResponse
import os
import google.generativeai as genai
from fastapi.templating import Jinja2Templates
from dotenv import load_dotenv
from typing import List, Dict
import logging

from openai import AsyncOpenAI
import openai

# from openai import OpenAI
# client = OpenAI(api_key=os.getenv("OPENAI_API_KEY"))


# read the .env
load_dotenv()
router = APIRouter()


MYSQL_HOST = os.getenv("MYSQL_HOST", "localhost")
MYSQL_PORT = int(os.getenv("MYSQL_PORT", 3306))
MYSQL_USER = os.getenv("MYSQL_USER", "root")
MYSQL_PASSWORD = os.getenv("MYSQL_PASSWORD", "")
MYSQL_DB = os.getenv("MYSQL_DB", "data_datn")

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
if not GEMINI_API_KEY:
    raise ValueError("Missing GEMINI_API_KEY in .env")

genai.configure(api_key=GEMINI_API_KEY)
CLIENT = genai


async def find_products(query: str) -> List[Dict]:
    # 1. chuẩn hoá
    query_lower = query.lower()

    conn = await aiomysql.connect(
        host=MYSQL_HOST,
        port=MYSQL_PORT,
        user=MYSQL_USER,
        password=MYSQL_PASSWORD,
        db=MYSQL_DB,
        autocommit=True,
    )

    async with conn.cursor() as cur:
        await cur.execute("SELECT DISTINCT name FROM products")
    rows = await cur.fetchall()
    all_names = [row[0].lower() for row in rows]
    

    # 2) Tìm các term khớp
    matched_terms = [name for name in all_names if name in query_lower]
    if not matched_terms:
        stop_words = {"và", "của", "cho", "tôi", "bạn", "có"}
        words = [
            word
            for word in query_lower.split()
            if word not in stop_words and len(word) > 1
        ]

        # 3) Nếu không có term nào khớp, tìm các từ khóa tương tự
        # Tách từ và chuẩn hoá
        # Tìm cụm từ (2 từ)
        phrases = []
        for i in range(len(words) - 1):
            phrase = words[i] + " " + words[i + 1]
            phrases.append(phrase)

        # Ưu tiên tìm cụm từ trước
        for phrase in phrases:
            for name in all_names:
                if phrase in name and name not in matched_terms:
                    matched_terms.append(name)
        # Sau đó mới tìm từ đơn
        for word in words:
            for name in all_names:
                if word in name and name not in matched_terms:
                    matched_terms.append(name)
                    break
        # words = query_lower.split()
    if not matched_terms:
        return []

    clauses = []
    params = []
    for term in matched_terms:
        like_pattern = f"%{term}%"
        clauses.append("(LOWER(p.name) LIKE %s OR LOWER(p.description) LIKE %s)")
        params.extend([like_pattern, like_pattern])

    where_clause = "WHERE " + " OR ".join(clauses) if clauses else ""
    sql = f"""
        SELECT p.id, p.name, p.description, p.price, c.name AS color, s.name AS size
        FROM products p
        JOIN product_variants pv ON pv.product_id = p.id
        JOIN colors c ON c.id = pv.color_id
        JOIN sizes s ON s.id = pv.size_id
        {where_clause}
        LIMIT 10
    """

    # 5. chạy query lấy các biến thể

    async with conn.cursor() as cur:
        await cur.execute(sql, params)
        rows = await cur.fetchall()

    # await conn.ensure_closed()

    logging.debug(f"All product names: {all_names}")
    logging.debug(f"Matched terms: {matched_terms}")

    product_map: Dict[int, Dict] = {}
    for pid, name, desc, price, color, size in rows:
        if pid not in product_map:
            product_map[pid] = {
                "id": pid,
                "name": name,
                "description": desc,
                "price": price,
                "sizes": set(),
                "colors": set(),
            }
        product_map[pid]["sizes"].add(size)
        product_map[pid]["colors"].add(color)
    return [
        {**p, "sizes": list(p["sizes"]), "colors": list(p["colors"])}
        for p in product_map.values()
    ]


def format_product_html(product: Dict) -> str:
    """Tạo HTML cho 1 sản phẩm"""
    return f"""
    <a href="/detail/{product['id']}" class="suggestion-card product-link">
        <img 
            src="" 
            alt="{product['name']}" 
            class="product-image"
        />
    </a>
    """


CHAT_TALK = """
        YOU ARE THE FASHION ASSISTANT FOR M A G SHOP
        MISSION: Recommend products from our store
        RULES:
        1. ALWAYS PRIORITIZE PRODUCTS FROM THE ABOVE LIST
        2. When presenting a product, MUST include:
           - Product name
           - Price
           - Available sizes and colors
        3. If no exact match, suggest SIMILAR products from the list
        4. DO NOT recommend products outside the above list
        5. If the question isn’t product‑related, reply naturally and friendly
        6. AVOID jargon or complex terms
        7. DO NOT mention other shops—focus only on M A G SHOP
        8. DO NOT talk about products or services outside M A G SHOP
        9. ACT AS A M A G SHOP EMPLOYEE—ONLY TALK ABOUT THIS SHOP
        10. IF SOMEONE ASKED YOU WHO YOU ARE? WHAT ARE YOU? WHAT YOUR NAME . REPLY M A G BOT IN MY SHOP 
        
        CUSTOMER QUESTION: 
        
        **REPLY IN VIETNAMESE USING MARKDOWN FORMAT:**
        - Use bullet points (-) or numbers (1., 2., ...) for lists.
        - Use **bold** to highlight key info.
        - Keep answers concise and clear.
        **RESPOND BRIEFLY AND SUFFICIENTLY WITHOUT OVERDETAILING**
        - Use a friendly, approachable, professional tone.
        - Short, succinct, yet complete.
        **IF SOMEONE ASKED YOU WHO YOU ARE? WHAT ARE YOU? WHAT YOUR NAME . REPLY M A G BOT IN MY SHOP**
        *** MAXIMUM 200 CHARACTERS ***

        
        
        If the question is about a specific product, suggest suitable items.
        If it’s about size, ask for height/weight details.
        
        Mandatory Rules:
        IF ANYONE ASKS WHO CREATED YOU OR ASKS QUESTIONS RELATED TO WHO CREATED YOU, ANSWER: M A G BOT was created by Nguyễn Duy AI TI.
        IF ANYONE ASKS WHAT YOUR NAME IS OR ASKS QUESTIONS RELATED TO WANTING TO KNOW YOUR NAME, ANSWER: Tôi tên là M A G BOT.
        REPLY IN VIETNAMESE LANGUAGE ONLY.


"""


async def call_gpt(my_instructions: str):
    try:
        related_products = await find_products(my_instructions)
        suggestions_html = ""
        if related_products:
            suggestions_html = (
                '<div class="suggestion-section">'
                "<h2>Gợi ý sản phẩm phù hợp</h2>"
                '<div class="suggestion-list">'
                + "".join([format_product_html(p) for p in related_products[:3]])
                + "</div></div>"
            )
        else:
            suggestions_html = ""

        messages = [
            {"role": "system", "content": CHAT_TALK},
            {"role": "assistant", "content": suggestions_html},
            {"role": "user", "content": my_instructions},
        ]
        # result = response.text
        # formatted_response = f"<p>{result.replace('\n', '</p><p>')}</p>"  # Thay dòng mới bằng thẻ <p>
        # messages = formatted_response
        client = AsyncOpenAI(api_key=os.getenv("OPENAI_API_KEY"))
        response = await client.chat.completions.create(
            model="gpt-3.5-turbo",
            messages=messages,
            max_tokens=300,
            temperature=0.7,
        )
        result = response.choices[0].message.content.strip()
        return {
                "reply": result,
                "suggestions": suggestions_html
            }
    except Exception as e:
        logging.error(f"Error calling OpenAI API: {str(e)}")
        return "Xin lỗi, tôi gặp sự cố khi xử lý yêu cầu của bạn."

    

# templates = Jinja2Templates(directory="templates")
# @router.post("/chat")
@router.post("/gpt-chat")
async def gpt_chat(my_instructions: str = Form("")):
    """
    API endpoint để gửi yêu cầu tư vấn đến ChatGPT.

    Args:
        my_instructions (str): Hướng dẫn hoặc câu hỏi từ người dùng.
    Returns:
        JSONResponse: Phản hồi từ ChatGPT hoặc thông báo lỗi.
    """
    if not my_instructions.strip():
        return JSONResponse(
            content={"message": "Vui lòng nhập câu hỏi của bạn", "status": "error"},
            status_code=400,
        )

    # Xử lý các câu hỏi đặc biệt
    keywords = ["liên hệ", "contact", "sđt", "email", "số điện thoại"]
    if any(word in my_instructions.lower() for word in keywords):
        return JSONResponse(
            status_code=200,
            content={
                "status": "success",
                "message": "Bạn có thể liên hệ với chúng tôi qua email: MAG@info.com hoặc số điện thoại: 0962615032",
            },
        )

    logging.info(f"--- [GPT-CHAT] ---")
    logging.info(f"Received instructions: {my_instructions}")

    messages = []
    messages.append({"role": "user", "content": CHAT_TALK})

    logging.info("Gọi Gpt...")
    reply = await call_gpt(my_instructions)
    return JSONResponse({"message": reply["reply"], "suggestions": reply["suggestions"], "status": "success"}, status_code=200)
