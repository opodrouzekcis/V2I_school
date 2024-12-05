import requests
import json

# Zabbix - API URL
url = "https://czabbix/api_jsonrpc.php"
headers = {"Content-Type": "application/json"}

# Zabbix API klíč
api_key = "e9ac1efefe24bf815c6c0486cf364a2e7fe3e80c65a4394c870916f1465fc892"

# JSON pro item.get
item_payload = {
    "jsonrpc": "2.0",
    "method": "item.get",
    "params": {
        "output": "extend",  # Zjisti všechny údaje o ITEMu
        "itemids": "60882",  # ID Itemu dle Zabbixu
        "hostids": "10633"   # ID hostitele v Zabbixu
    },
    "auth": api_key,
    "id": 2
}

# Poslání ITEM.GET pro zjištění údajů
item_response = requests.post(url, headers=headers, data=json.dumps(item_payload), verify=False)
item_result = item_response.json()

# Print zjištěných údajů pomocí IP
if "result" in item_result and len(item_result["result"]) > 0:
    print("Fetched item details:")
    for item in item_result["result"]:
        print(f"Item ID: {item['itemid']}")
        print(f"Name: {item['name']}")
        print(f"Last Value: {item.get('lastvalue', 'N/A')}")
else:
    print(f"Error: {item_result.get('error', 'No items found')}")
