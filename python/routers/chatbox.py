import aiomysql
from fastapi import APIRouter, Form, Request
from fastapi.responses import JSONResponse
import os
import google.generativeai as genai
from dotenv import load_dotenv
import re
import logging
from datetime import datetime, timedelta
from collections import defaultdict
import httpx
import json
from typing import Dict

logging.basicConfig(level=logging.DEBUG)


load_dotenv()
router = APIRouter()

MYSQL_HOST = os.getenv("MYSQL_HOST", "localhost")
MYSQL_PORT = int(os.getenv("MYSQL_PORT", 3306))
MYSQL_USER = os.getenv("MYSQL_USER", "root")
MYSQL_PASSWORD = os.getenv("MYSQL_PASSWORD", "")
MYSQL_DB = os.getenv("MYSQL_DB", "data_new")

GEMINI_API_KEY = os.getenv("GEMINI_API_KEY")
if not GEMINI_API_KEY:
    raise ValueError("Missing GEMINI_API_KEY in .env")
genai.configure(api_key=GEMINI_API_KEY)

OLLAMA_ENDPOINT = os.getenv("OLLAMA_ENDPOINT")
OLLAMA_MODEL = os.getenv("OLLAMA_MODEL")

# anti sờ bam
SPAM_LIMIT = 5
SPAM_WINDOW = 60
request_log = defaultdict(list)

# cache cho các câu hỏi thường gặp
FAQ_CACHE = {
    r"ship\s+bao\s+lâu": "Thời gian giao hàng từ 2-5 ngày làm việc tùy khu vực. Bạn cho mình xin địa chỉ nhận hàng nhé!",
    r"đổi\s+trả\s+hàng": "Bạn có thể đổi trả hàng trong vòng 7 ngày nếu sản phẩm còn nguyên tag, chưa qua sử dụng. Mình cần biết mã đơn hàng để hỗ trợ nhé!",
    r"size\s+M\s+phù\s+hợp\s+cho\s+bao\s+nhiêu\s+cân": "Size M phù hợp với người có cân nặng từ 55kg-65kg. Bạn có thể cho mình biết chiều cao và cân nặng để tư vấn chính xác hơn không?",
    r"thanh\s+toán": "Chúng tôi hỗ trợ các hình thức: COD, chuyển khoản ngân hàng và ví điện tử Momo. Bạn muốn dùng hình thức nào ạ?",
    r"khuyến\s+mãi": "Hiện đang có chương trình giảm 10% cho đơn hàng đầu tiên. Nhập mã WELCOME10 khi thanh toán nhé!",
}


# anti spam check
def check_spam(user_ip: str) -> bool:
    """check  nếu user gửi quá nhiều request trong khoảng thời gian ngắn"""
    now = datetime.now()
    # xóa request cũ
    request_log[user_ip] = [
        t for t in request_log[user_ip] if now - t < timedelta(seconds=SPAM_WINDOW)
    ]

    if len(request_log[user_ip]) >= SPAM_LIMIT:
        logging.warning(f"User {user_ip} is spamming requests")
        return True
    request_log[user_ip].append(now)
    return False


# FAQ matching function
def match_faq(question: str) -> str:
    """Check if question matches any FAQ pattern"""
    question = question.lower().strip()
    for pattern, answer in FAQ_CACHE.items():
        if re.search(pattern, question):
            return answer
    return ""


# lưu lịch sử câu hỏi
async def save_question_history(user_ip: str, question: str, answer: str, source: str):
    """Lưu lịch sử câu hỏi và trả lời"""
    try:
        conn = await aiomysql.connect(
            host=MYSQL_HOST,
            port=MYSQL_PORT,
            user=MYSQL_USER,
            password=MYSQL_PASSWORD,
            db=MYSQL_DB,
            autocommit=True,
        )
        async with conn.cursor() as cur:
            await cur.execute(
                "INSERT INTO chat_history (user_ip, question, response, source) VALUES (%s, %s, %s, %s)",
                (user_ip, question, answer, source),
            )
            await conn.commit()
    except Exception as e:
        logging.error(f"Không thể lưu lịch sử: {e}")
    finally:
        if conn:
            conn.close()


# gọi ollama API
async def call_ollama_api(question: str) -> str:
    """Gọi API của Ollama để lấy câu trả lời"""
    prompt = f"""Bạn là trợ lý thời trang M A G SHOP.
    Trả lời ngắn gọn bằng tiếng Việt (dưới 150 ký tự).
    Câu hỏi: {question}"""

    payload = {
        "model": OLLAMA_MODEL,
        "prompt": prompt,
        "stream": False,
        "options": {"temperature": 0.3, "num_predict": 150},
    }
    try:
        async with httpx.AsyncClient(timeout=60.0) as client:
            response = await client.post(
                f"{OLLAMA_ENDPOINT}/api/generate", json=payload
            )
        response.raise_for_status()
        data = response.json()
        return data.get("response", "").strip()
    except Exception as e:
        logging.error(f"Ollama error: {e}")
        return ""


# gọi gemini API
async def call_gemini(question: str) -> str:
    """Call Gemini API for complex questions"""
    try:
        model = genai.GenerativeModel("gemini-2.5-flash")
        response = await model.generate_content_async(
            f"Bạn là trợ lý thời trang M A G SHOP. Trả lời ngắn gọn dưới 200 ký tự bằng tiếng Việt: {question}"
        )
        return response.text.strip()
    except Exception as e:
        logging.error(f"Gemini error: {str(e)}")
        return "Xin lỗi, hiện mình chưa trả lời được câu hỏi này. Bạn vui lòng liên hệ hotline 0962615032 nhé!"


# check xem câu trả lời có chất lượng không
def is_quality_response(response: str) -> bool:
    """Check câu trả lời có chất lượng"""
    if not response:
        return False
    if len(response) < 10:
        return False
    if "không biết" in response or "không rõ" in response:
        return False
    return True


async def find_products(query: str) -> list[Dict]:
    # 1. Normalize query
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

    # 2) Find matching terms
    matched_terms = [name for name in all_names if name in query_lower]
    if not matched_terms:
        stop_words = {"và", "của", "cho", "tôi", "bạn", "có"}
        words = [
            word
            for word in query_lower.split()
            if word not in stop_words and len(word) > 1
        ]

        # 3) If no exact matches, find similar terms
        phrases = []
        for i in range(len(words) - 1):
            phrase = words[i] + " " + words[i + 1]
            phrases.append(phrase)

        # Prioritize phrases first
        for phrase in phrases:
            for name in all_names:
                if phrase in name and name not in matched_terms:
                    matched_terms.append(name)
        # Then single words
        for word in words:
            for name in all_names:
                if word in name and name not in matched_terms:
                    matched_terms.append(name)
                    break
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
        SELECT p.id, p.name, p.description, p.price, c.name AS color, s.name AS size, img.path AS image_url
        FROM products p
        JOIN product_variants pv ON pv.product_id = p.id
        JOIN colors c ON c.id = pv.color_id
        JOIN sizes s ON s.id = pv.size_id
        LEFT JOIN product_images img ON img.product_id = p.id AND img.`order` = 1
        {where_clause}
        LIMIT 10
    """

    async with conn.cursor() as cur:
        await cur.execute(sql, params)
        rows = await cur.fetchall()

    logging.debug(f"All product names: {all_names}")
    logging.debug(f"Matched terms: {matched_terms}")
    logging.debug(f"[FIND_PRODUCTS] Tìm với câu hỏi: {query}")
    logging.debug(f"[FIND_PRODUCTS] Các từ khoá khớp: {matched_terms}")
    logging.debug(f"[FIND_PRODUCTS] Sản phẩm tìm được: {[r[1] for r in rows]}")

    product_map: Dict[int, Dict] = {}
    for pid, name, desc, price, color, size, image_url in rows:
        if pid not in product_map:
            product_map[pid] = {
                "id": pid,
                "name": name,
                "description": desc,
                "price": price,
                "sizes": set(),
                "colors": set(),
                "image_url": image_url
            }
        else:
            # Nếu sp đã có image_url, giữ nguyên ảnh đầu tiên (order=1)
            if not product_map[pid]["image_url"] and image_url:
                product_map[pid]["image_url"] = image_url
        product_map[pid]["sizes"].add(size)
        product_map[pid]["colors"].add(color)

    await conn.ensure_closed()
    return [
        {**p, "sizes": list(p["sizes"]), "colors": list(p["colors"])}
        for p in product_map.values()
    ]


def format_product_html(product: Dict) -> str:
    """Generate HTML for a product"""
    image_url = product.get("image_url") or "/default.jpg"
    price = f"{product['price']:,.0f}".replace(",", ".")
    return f"""
    <a href="/detail/{product['id']}" class="suggestion-card product-link">
<<<<<<< HEAD
        <img
            src="{image_url}"
            alt="{product['name']}"
=======
        <img 
            src="http://127.0.0.1:8080/{image_url}" 
            alt="{product['name']}" 
>>>>>>> 610089186eb5925a6a59910035ad1d73ff49a39e
            class="product-image"
        />
        <p class="product-name">{product['name']}</p>
        <p class="product-price">{price}₫</p>
    </a>
    """


@router.post("/chatbox")
async def chatbox(request: Request, my_instructions: str = Form("")):
    """
    API endpoint để gửi yêu cầu tư vấn đến Chat.

    Args:
        my_instructions (str): Hướng dẫn hoặc câu hỏi từ người dùng.
    Returns:
        dict: Phản hồi từ Chat hoặc thông báo lỗi.
    """
    user_ip = request.client.host
    if check_spam(user_ip):
        return JSONResponse(
            content={
                "message": "Bạn gửi quá nhiều yêu cầu, vui lòng thử lại sau.",
                "status": "error",
            },
            status_code=429,
        )

    if not my_instructions.strip():
        return JSONResponse(
            content={"message": "Vui lòng nhập câu hỏi của bạn", "status": "error"},
            status_code=400,
        )
    special_keywords = {
        "liên hệ": "📞 Bạn có thể liên hệ qua email: MAG@info.com hoặc hotline: 0962615032",
        "contact": "📞 Contact us at MAG@info.com or call 0962615032",
        "sđt": "📞 Hotline hỗ trợ: 0962615032 (8h-22h hàng ngày)",
        "email": "✉️ Email hỗ trợ: MAG@info.com",
    }
    for keyword, response in special_keywords.items():
        if keyword in my_instructions.lower():
            await save_question_history(user_ip, my_instructions, response, "keyword")
            return JSONResponse(
                content={"message": response, "status": "success"}, status_code=200
            )
    # tìm sản phẩm liên quan
    related_products = await find_products(my_instructions)
    logging.debug(f"Sản phẩm gợi ý: {[p['name'] for p in related_products]}")

    suggestions_html = ""

    if related_products:
        suggestions_html = (
            '<div class="suggestion-section">'
            "<h2>Gợi ý sản phẩm phù hợp</h2>"
            + "".join([format_product_html(p) for p in related_products[:3]])
            + "</div>"
        )
    # Kiểm tra FAQ trước
    faq_response = match_faq(my_instructions)
    if faq_response:
        await save_question_history(user_ip, my_instructions, faq_response, "faq")
        return JSONResponse(
            content={
                "message": faq_response,
                "status": "success",
                "suggestions": suggestions_html,
            },
            status_code=200,
        )
    # Tier 2: Simple question handling
    simple_questions = {
        r"có\s+ship\s+không": "🚚 Chúng tôi ship toàn quốc. Phí ship từ 20k-50k tùy khu vực!",
        r"giờ\s+mở\s+cửa": "⏰ Giờ mở cửa: 8h30 - 21h30 hàng ngày",
        r"chính\s+sách\s+bảo\s+mật": "🔒 Thông tin khách hàng được bảo mật tuyệt đối theo luật PDPA",
    }

    for pattern, response in simple_questions.items():
        if re.search(pattern, my_instructions.lower()):
            await save_question_history(user_ip, my_instructions, response, "simple")
            return JSONResponse(
                content={"message": response, "status": "success"}, status_code=200
            )

    # Tier 3: Local Ollama (LLaMA 3 8B)
    ollama_response = await call_ollama_api(my_instructions)
    if is_quality_response(ollama_response):
        await save_question_history(user_ip, my_instructions, ollama_response, "ollama")
        return JSONResponse(
            content={
                "message": ollama_response,
                "status": "success",
                "suggestions": suggestions_html,
            },
            status_code=200,
        )

    # Tier 4: Gemini API for complex questions
    gemini_response = await call_gemini(my_instructions)
    await save_question_history(user_ip, my_instructions, gemini_response, "gemini")

    return JSONResponse(
        content={
            "message": gemini_response,
            "status": "success",
            "suggestions": suggestions_html,
        },
        status_code=200,
    )
