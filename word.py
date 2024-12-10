import requests
from bs4 import BeautifulSoup

def scan_wordpress(url):
    try:
        # Mengirim permintaan GET ke URL
        response = requests.get(url)
        response.raise_for_status()  # Memastikan permintaan berhasil

        # Memeriksa versi WordPress
        if 'generator' in response.text:
            soup = BeautifulSoup(response.text, 'html.parser')
            generator = soup.find('meta', attrs={'name': 'generator'})
            if generator:
                print(f"WordPress Version: {generator['content']}")
            else:
                print("WordPress version not found.")
        else:
            print("This site does not appear to be a WordPress site.")

        # Mencari tema yang digunakan
        theme_url = url + '/wp-content/themes/'
        themes_response = requests.get(theme_url)
        if themes_response.status_code == 200:
            print("Themes Directory is accessible.")
            themes = themes_response.text.splitlines()
            for line in themes:
                if 'href' in line:
                    print(f"Theme found: {line.split('href=')[1].split('>')[0]}")
        else:
            print("Themes Directory is not accessible.")

        # Mencari plugin yang digunakan
        plugins_url = url + '/wp-content/plugins/'
        plugins_response = requests.get(plugins_url)
        if plugins_response.status_code == 200:
            print("Plugins Directory is accessible.")
            plugins = plugins_response.text.splitlines()
            for line in plugins:
                if 'href' in line:
                    print(f"Plugin found: {line.split('href=')[1].split('>')[0]}")
        else:
            print("Plugins Directory is not accessible.")

    except requests.exceptions.RequestException as e:
        print(f"An error occurred: {e}")

# Ganti dengan URL situs WordPress yang ingin Anda scan
url = 'http://example.com'
scan_wordpress(url)