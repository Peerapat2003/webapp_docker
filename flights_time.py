import requests

# ใส่ API Key ที่ได้จาก aviationstack.com
API_KEY = '02c1b6c09fbd93ad0eca0e2fbf70e487'
BASE_URL = 'http://api.aviationstack.com/v1/flights'

# ตั้งค่าพารามิเตอร์ - กรองเที่ยวบินขาออกจากสนามบิน Bangkok (IATA: BKK)
params = {
    'access_key': API_KEY,
    'dep_iata': 'BKK',  # รหัสสนามบินสุวรรณภูมิ
    'limit': 3          # แสดงแค่ 3 เที่ยวบิน
}

# เรียก API
response = requests.get(BASE_URL, params=params)
response.encoding = 'utf-8'

# ตรวจสอบการตอบสนอง
if response.status_code == 200:
    flights = response.json()['data']

    for idx, flight in enumerate(flights, start=1):
        airline = flight['airline']['name']
        flight_number = flight['flight']['iata']
        destination = flight['arrival']['airport']
        departure_time = flight['departure']['scheduled']

        print(f"\n เที่ยวบินที่ {idx}")
        print(f"สายการบิน: {airline}")
        print(f"เที่ยวบิน: {flight_number}")
        print(f"ปลายทาง: {destination}")
        print(f"เวลาออกเดินทางตามกำหนด: {departure_time}")
else:
    print("ไม่สามารถเชื่อมต่อ API ได้:", response.status_code)
