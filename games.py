import requests

# URL ของ RAWG API
# API_KEY = "f7123d1c28d944d3b6a7182c3509fcf7"
url = "https://api.rawg.io/api/games"

# พารามิเตอร์ (ขอข้อมูล 3 เกม)
params = {
    "page_size": 3,
    "key": "f7123d1c28d944d3b6a7182c3509fcf7"  # ถ้าไม่มี API Key ให้ลบออก
}

# ดึงข้อมูลจาก API
response = requests.get(url, params=params)

# แปลงข้อมูล JSON
data = response.json()

# แสดงข้อมูลเกม
print("ข้อมูลเกมจาก RAWG API:\n")
for i, game in enumerate(data['results'], start=1):
    name = game.get('name', 'ไม่ทราบชื่อ')
    released = game.get('released', 'ไม่ทราบวันที่')
    platforms = [p['platform']['name'] for p in game.get('platforms', [])]
    print(f"{i}. ชื่อเกม: {name}")
    print(f"   วันที่วางจำหน่าย: {released}")
    print(f"   แพลตฟอร์ม: {', '.join(platforms)}\n")
