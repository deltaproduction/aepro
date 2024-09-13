import os
import sys

import json

import time
import fitz

import numpy as np
import cv2

from pprint import pprint
from PIL import Image as PILImage, ImageDraw, ImageFont


def draw_background(horizontal, number, output):
    mes = 10

    if horizontal:
        canvas_width, canvas_height = 310 * mes, 210 * mes
    else:
        canvas_width, canvas_height = 210 * mes, 310 * mes

    image = np.ones((canvas_height, canvas_width, 3), dtype=np.uint8) * 255

    font_path = "storage/app/public/papers/arial.ttf"
    font_size = 35
    font = ImageFont.truetype(font_path, font_size)

    block_width, block_height = 210, 100

    block_image = PILImage.new('RGB', (block_width, block_height), color='white')
    draw = ImageDraw.Draw(block_image)

    text = number

    bbox = draw.textbbox((0, 0), text, font=font)
    text_width = bbox[2] - bbox[0]
    text_height = bbox[3] - bbox[1]
    text_x = (block_width - text_width) // 2
    text_y = (block_height + text_height) // 2
    draw.text((text_x, text_y), text, fill="#f4f4f4", font=font)

    block_image = np.array(block_image)

    for y in range(0, canvas_height, block_height):
        for x in range(0, canvas_width, block_width):
            if y + block_height <= canvas_height and x + block_width <= canvas_width:
                image[y:y+block_height, x:x+block_width] = block_image

    cv2.imwrite(output, image)


def assemble(auditorium_id, pdfs):
    background_scale_factor = 0.9
    overlay_scale_factor = 2.7
    pdf = fitz.open()

    fn = f"{time.time()}.png"
    pages_count = 0
    for variant in pdfs:
        pdf_path = pdfs[variant]

        if os.path.exists(pdf_path):
            overlay_pdf = fitz.open(pdf_path)

            k = str(variant)

            draw_background(False, k, fn)

            for i in range(overlay_pdf.page_count):
                pages_count += 1
                background_image_path = fn

                background_image = fitz.open(background_image_path)
                background_rect = background_image[0].rect

                overlay_page = overlay_pdf[i]
                overlay_rect = overlay_page.rect
                page = pdf.new_page(width=background_rect.width, height=background_rect.height)

                scaled_background_width = background_rect.width * background_scale_factor
                scaled_background_height = background_rect.height * background_scale_factor

                background_x_offset = (background_rect.width - scaled_background_width) / 2
                background_y_offset = (background_rect.height - scaled_background_height) / 2
                background_position = fitz.Rect(background_x_offset, background_y_offset,
                                                background_x_offset + scaled_background_width,
                                                background_y_offset + scaled_background_height)

                page.insert_image(background_position, filename=background_image_path)

                scaled_overlay_width = overlay_rect.width * overlay_scale_factor
                scaled_overlay_height = overlay_rect.height * overlay_scale_factor

                overlay_x_offset = (background_rect.width - scaled_overlay_width) / 2
                overlay_y_offset = (background_rect.height - scaled_overlay_height) / 2
                overlay_position = fitz.Rect(overlay_x_offset, overlay_y_offset,
                                             overlay_x_offset + scaled_overlay_width,
                                             overlay_y_offset + scaled_overlay_height)

                page.show_pdf_page(overlay_position, overlay_pdf, i)

    if pages_count:
        pdf.save(f"storage/app/public/papers/pdfs/{auditorium_id}.pdf")
    pdf.close()

    if os.path.exists(fn):
        os.remove(fn)


if __name__ == "__main__":
    args = sys.argv
    tmp_file_path = args[-1]

    with open(tmp_file_path, "r", encoding="utf8") as tmp_file:
        tmp_file_raw = json.loads(tmp_file.read())
        auditorium_id = tmp_file_raw[0]
        data = tmp_file_raw[1]
        options = {}
        for reg_number in data:
            variant_number = data[reg_number]
            options[reg_number] = f"storage/app/public/options/{variant_number}.pdf"

        assemble(auditorium_id, options)
