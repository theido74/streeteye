import requests

TOKEN = "8668820683:AAGEwyk9Y366USFrXdQg6eZ3rWgjsejbCjs"
url = f"https://api.telegram.org/bot{TOKEN}/getUpdates"
updates = requests.get(url, timeout=10).json()

for u in updates["result"]:
    msg = u.get("message") or u.get("edited_message")
    if msg:
        print("chat_id =", msg["chat"]["id"])
        break
