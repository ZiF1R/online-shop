import random
import re
from datetime import datetime

from shop import Shop


def try_cast(value, to_type):
    try:
        return to_type(value)
    except (ValueError, TypeError):
        return value


class Vek21(Shop):

    def __init__(self):
        Shop.__init__(self, "21vek.by")

    def get_shop_brands(self, sections):
        brands = []
        for section in sections:
            for category in sections[section]:
                for product in sections[section][category]:
                    if product:
                        brands.append(product["brand"])
        return list(set(brands))

    def get_shop_data(self, url):
        try:
            dom = self.query(url)

            print("Start", datetime.now())
            sections = self.get_shop_sections(dom, 6)
            brands = self.get_shop_brands(sections)
            print("End", datetime.now())

            return {
                "brands": brands,
                "sections": sections
            }

        except Exception as e:
            print("err:", e)
            return self.add_error(e, True)

    def get_shop_sections(self, dom, sections_limit):
        try:
            sections_elements = dom.xpath('//div[@class="b-category-set"]')
            sections = {}

            for i, section in enumerate(sections_elements):
                if i + 1 > sections_limit:
                    break
                print("section", i + 1)
                section_header = section.xpath('//h2')[i].text_content()
                sections[section_header] = self.get_section_categories(dom, i, 6)
                i += 1

            return sections

        except Exception as e:
            print("err:", e)
            return self.add_error(e, True)

    def get_section_categories(self, dom, i, categories_limit):
        try:
            path = '//div[@class="b-category-set"][' + str(i + 1) + ']//a[@class="cloud-sub__header"]'
            section_categories = dom.xpath(path)

            categories = {}
            i = 1
            for category in section_categories:
                if i > categories_limit:
                    break
                print("\tcategory", i)
                category_header = category.text_content()

                category_link = category.get("href")
                category_dom = self.query(category_link)
                categories[category_header] = self.get_category_products(category_dom, 10)
                i += 1

            return categories

        except Exception as e:
            print("err:", e)
            return self.add_error(e, True)

    def get_category_products(self, dom, products_limit):
        try:
            products = []
            products_links = dom.xpath('//a[contains(@class, "result__link")]')
            i = 1
            for product_link in products_links:
                if i > products_limit:
                    break
                print("\t\tproduct", i)
                link = product_link.get("href")
                product_dom = self.query(link)
                product = self.get_product_data(product_dom)
                products.append(product)
                i += 1

            return products

        except Exception as e:
            print("err:", e)
            return self.add_error(e, True)

    def get_product_data(self, dom):
        try:
            data = {}

            item_data = dom.xpath(
                '//span[contains(@class, "item__price")]//span[contains(@class, "g-item-data")]')[0]
            data["name"] = item_data.get("data-name")
            data["code"] = int(item_data.get("data-code"))
            data["price"] = float(format(item_data.get("data-price"), '.22'))
            data["brand"] = item_data.get("data-producer_name")
            data["count"] = random.randrange(100)
            data["discount"] = None
            data["description"] = None
            data["photo_link"] = None

            link = dom.xpath('//img[@class=""]')
            desc = dom.xpath('//div[contains(@class, "cr-info-descr")]')
            if len(desc) != 0:
                desc = desc[0].text_content()
                desc = re.sub(r"[\t\r]", "", desc)
                desc = re.sub(r"\n", " ", desc)
                data["description"] = desc
            if len(link) != 0:
                data["photo_link"] = link[0].get("src")

            data["properties"] = {}
            attr_names = dom.xpath('//span[@class="attr__name"]')
            attr_values = dom.xpath('//span[@class="attr__value"]')
            for i, attr_name in enumerate(attr_names):
                name = re.sub(r"[\n\t]", "", attr_name.text_content())
                value = re.sub(r"\xa0", " ", attr_values[i].text_content())

                default_designation = re.findall(r"^([\/\d\.]+)\s([^,]+)$", value)
                # range_designation = re.findall(r"^от ([\/\d\.]+) до ([\/\d\.]+) ([^,]+)$", value)

                if len(default_designation) != 0:
                    data["properties"][name] = {
                        "value": default_designation[0][0],
                        "designation": default_designation[0][1],
                    }
                else:
                    data["properties"][name] = {
                        "value": value,
                    }

            return data

        except Exception as e:
            print("err:", e)
            return self.add_error(e, True)
