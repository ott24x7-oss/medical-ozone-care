# Medical Ozone Care — PHP + MySQL website (Hostinger)

A complete, server-rendered **PHP 8 + MySQL** website for Medical Ozone Care, built for
**Hostinger shared hosting**. Includes a one-click installer, an admin panel, and a
**Content editor where every text on the site is editable** — no coding required.

> **Medical disclaimer:** This website provides medical equipment information only. Use of
> medical ozone equipment should be under qualified medical supervision and applicable local
> regulations. No medical-cure claims are made anywhere on the site.

---

## What you upload

Upload the **contents of this `php/` folder** to your Hostinger `public_html`
(so `index.php` sits in `public_html/index.php`). The other folders in the project
(`server/`, `public/`) are the Node.js version and are **not** used on Hostinger.

---

## Install on Hostinger (10 minutes)

1. **Create a MySQL database** — hPanel → **Databases → MySQL Databases**. Create a
   database and a user (give the user all privileges). Note the **database name, user,
   and password**.

2. **Upload the files** — hPanel → **File Manager** → open `public_html` → upload all files
   from this `php/` folder (or upload a ZIP and Extract). Easiest: ZIP the contents of
   `php/`, upload the ZIP into `public_html`, then **Extract**.

3. **Edit `config.php`** — in File Manager, right-click `config.php` → Edit. Set:
   ```php
   'db' => [ 'driver'=>'mysql', 'host'=>'localhost',
             'name'=>'YOUR_DB_NAME', 'user'=>'YOUR_DB_USER', 'pass'=>'YOUR_DB_PASSWORD' ],
   ```
   Also set a long random `security.secret`, and change the `admin.password`.

4. **Run the installer** — visit **`https://your-domain/install.php`** in a browser.
   You'll see "Installation complete" with 9 products and all content seeded.

5. **Delete `install.php`** (File Manager → delete) for security.

6. **Log in** at **`https://your-domain/admin/`** with the email/password from `config.php`.

Done — your site is live. Edit any text from **Admin → Site Content**.

---

## Admin panel (`/admin/`)

- **Dashboard** — enquiry stats, recent enquiries.
- **Products** — add / edit / delete, upload images, mark active/inactive, set price.
- **Enquiries** — view all submissions, set status (New → Contacted → Quoted → Closed),
  add admin notes, delete spam.
- **Brochures** — upload a PDF per product (download links are tracked).
- **Site Content** — **edit every text on the website**, grouped by page (Global, Home,
  Products, About, Contact, FAQ, Footer, Instructions). Includes:
  - **Contact Details** (phone, WhatsApp, emails, address, hours)
  - **SEO Settings** (title, description, keywords, base URL)
  - **Legal Pages** (Terms, Privacy, Disclaimer, Warranty, Usage — full HTML)

### Editing text
Most fields are plain text or paragraphs. A few control **repeating blocks** and are stored
as **JSON** (clearly labelled) — e.g. home features, steps, testimonials, FAQ. Keep the same
shape (e.g. `[{"icon":"shield","title":"…","text":"…"}]`) and click Save. Icons available:
shield, sliders, atom, globe, plug, badge, gauge, droplet, wind, settings, flask, users,
truck, headset, award, check, phone, mail.

---

## How enquiry emails work

Every form submission is **saved to the database** (visible in Admin → Enquiries) and, if
email is enabled in `config.php` (`mail.enabled = true`), a notification is sent via PHP
`mail()` to the addresses in `mail.notify_to` (default: both business emails). The subject is
`New Medical Ozone Care Enquiry - <type>`. If your host's `mail()` is unreliable, ask
Hostinger for SMTP details and switch to an SMTP library (e.g. PHPMailer) in
`includes/mailer.php`.

---

## Pages

`index.php` (home) · `products.php` · `product.php?slug=…` · `instructions.php` ·
`faq.php` · `about.php` · `contact.php` · `legal.php?doc=terms|privacy|disclaimer|warranty|usage`
· `sitemap.php` · `robots.txt` · `admin/`

(With `mod_rewrite` on, `/products`, `/about`, etc. also work without `.php`.)

---

## Folder structure

```
config.php            ← your DB + admin + email settings (edit this)
install.php           ← run once, then delete
index.php products.php product.php instructions.php faq.php about.php contact.php legal.php
brochure.php sitemap.php robots.txt .htaccess
api/enquiry.php       ← form submissions
includes/             ← db, functions, auth, mailer, header/footer, seed data (protected)
admin/                ← admin panel
assets/               ← css, js, images (SVG product mockups)
uploads/              ← admin-uploaded images & brochures (PHP execution blocked)
data/                 ← only used if you switch to SQLite for local testing
```

---

## Security

- Admin passwords hashed with PHP `password_hash` (bcrypt).
- Admin actions protected by session auth + CSRF tokens.
- Inputs validated and escaped; SQL via prepared statements (PDO).
- Public enquiry form has a honeypot anti-spam field.
- `.htaccess` blocks PHP execution in `uploads/`, denies `includes/`, hides `.sqlite`/config.
- After install, **delete `install.php`** and **change the admin password**.

---

## Local testing (optional, not needed for Hostinger)

You can run it locally with SQLite (no MySQL needed):
```bash
# set an env var so it uses SQLite, then run PHP's built-in server from this folder
MOC_SQLITE_PATH="$PWD/data/local.sqlite" php install.php   # seeds the DB (CLI prints HTML)
MOC_SQLITE_PATH="$PWD/data/local.sqlite" php -S localhost:8000
# open http://localhost:8000/index.php and /admin/
```

---

## Replace mockups with real photos

Product images are scalable **SVG mockups** in `assets/img/products/`. To use real photos:
Admin → Products → Edit → **Upload image(s)**, or drop files into `uploads/` and set the path.

---

## Contact

**Medical Ozone Care** · Shekhar Pathak · +91 99588 03980
shekharaiims@gmail.com · medicalozonecare@gmail.com · www.medicalozonecare.co.in
