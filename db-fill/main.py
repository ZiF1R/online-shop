import json

from vek21 import Vek21

if __name__ == '__main__':
    shop = Vek21()
    data = shop.get_shop_data("https://www.21vek.by/repairs/")

    with open('brands.json', 'w', encoding="utf-8") as f:
        brands = {
            "brands": data["brands"]
        }
        json.dump(brands, f, indent=2, ensure_ascii=False)

    with open('data.json', 'w', encoding="utf-8") as f:
        json.dump(data["sections"], f, indent=2, ensure_ascii=False)
