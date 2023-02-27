# coding=utf-8
import io

import requests as requests
from PIL import Image
from lxml import html


class Shop:
    errors = []

    def __init__(self, shop_name):
        self.shop_name = shop_name

    def add_error(self, message, print_error=True, ret=None):
        if print_error:
            print(message)
        self.errors.append(message)
        return ret

    def has_errors(self):
        return len(self.errors) > 0

    def first_error(self):
        return self.errors[0] if self.has_errors() else None

    def query(self, url):
        r = requests.get(url)

        if r.status_code != requests.codes.ok:
            raise Exception(f"http code == {r.status_code}")

        if not r.content or len(r.content) < 7:
            raise Exception(f"no content at {url}")

        return html.fromstring(r.content.decode(r.encoding))

    def xp(self, dom, xpath, throw_exc=False, attr_name='text', index=0, ret_none=None):
        try:
            el = dom.xpath(xpath)[index]
            if not attr_name:
                return el
            v = el.text_content() if attr_name == 'text' else el.get(attr_name)
            return v
        except Exception as e:
            if throw_exc:
                raise e
            return self.add_error(str(e), ret_none)

    def download_image(self, url, filename):
        r = requests.get(url)
        if r.status_code != requests.codes.ok:
            raise Exception(f"image downloading error: http code == {r.status_code}")

        path = f"data/{filename}.jpg"
        with Image.open(io.BytesIO(r.content)) as im:
            im.save(path)
