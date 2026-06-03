# Package php/ contents at the zip root for Hostinger upload.
import zipfile, os

base = os.path.join(os.path.dirname(os.path.dirname(os.path.abspath(__file__))), "php")
out = os.path.join(os.path.dirname(base), "medical-ozone-care-php.zip")

n = 0
with zipfile.ZipFile(out, "w", zipfile.ZIP_DEFLATED) as z:
    for root, dirs, files in os.walk(base):
        for f in files:
            full = os.path.join(root, f)
            rel = os.path.relpath(full, base).replace(os.sep, "/")
            if rel.endswith((".sqlite", ".sqlite3", ".db")):
                continue
            z.write(full, rel)
            n += 1

print("files zipped:", n)
print("size KB:", round(os.path.getsize(out) / 1024))
names = zipfile.ZipFile(out).namelist()
checks = [
    "index.php", "install.php", "config.php", ".htaccess",
    "admin/login.php", "admin/content.php", "admin/product-edit.php",
    "includes/seed_content.php", "includes/functions.php",
    "uploads/.htaccess", "data/.htaccess",
    "assets/css/styles.css", "assets/img/products/generator-digital.svg",
]
for k in checks:
    print(("OK   " if k in names else "MISS ") + k)
print("htaccess entries:", [x for x in names if x.endswith(".htaccess")])
