# -*- coding: utf-8 -*-
"""Generates all product mockup SVGs for the Medical Ozone Care website.
Run:  python tools/gen_svgs.py
Outputs to public/assets/img/ and public/assets/img/products/
"""
import os

ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
IMG = os.path.join(ROOT, "public", "assets", "img")
PROD = os.path.join(IMG, "products")
os.makedirs(PROD, exist_ok=True)

# ---------- brand palette ----------
TEAL   = "#0AA2B8"
TEAL_D = "#077E92"
TEAL_X = "#055468"
NAVY   = "#0B3A52"
CYAN   = "#22D3EE"
SKY    = "#7DD3FC"
ICE    = "#E0F7FB"
INK    = "#0F2A38"
WHITE  = "#FBFCFE"
PL_HI  = "#FFFFFF"
PL_MID = "#EDF1F6"
PL_SH  = "#D4DCE6"
PL_DK  = "#B7C2CF"
GREEN  = "#2EA24E"
GREEN_D= "#176E33"
BRASS  = "#CBA13A"
STEEL  = "#9AA7B4"
STEEL_D= "#6B7884"


def write(name, content):
    path = os.path.join(PROD, name)
    with open(path, "w", encoding="utf-8") as f:
        f.write(content)
    print("wrote", os.path.relpath(path, ROOT))


def write_img(name, content):
    path = os.path.join(IMG, name)
    with open(path, "w", encoding="utf-8") as f:
        f.write(content)
    print("wrote", os.path.relpath(path, ROOT))


def hx(c):
    c = c.lstrip("#")
    return tuple(int(c[i:i+2], 16) for i in (0, 2, 4))


def lerp(a, b, t):
    ca, cb = hx(a), hx(b)
    r = tuple(round(ca[i] + (cb[i]-ca[i])*t) for i in range(3))
    return "#%02X%02X%02X" % r


def chart_color(col, row, ncol, nrow):
    """Cool -> warm gradient across columns, lighter -> deeper down rows."""
    stops = ["#3E78C0", "#2E9CC0", "#2EB59A", "#67BE3B", "#C9B53A", "#D08A3A", "#CF5A4A"]
    t = col / max(1, ncol - 1)
    # pick between adjacent stops
    pos = t * (len(stops) - 1)
    i = int(pos)
    frac = pos - i
    base = lerp(stops[i], stops[min(i+1, len(stops)-1)], frac)
    # darken slightly by row
    return lerp("#FFFFFF", base, 0.45 + 0.5 * (row / max(1, nrow - 1)))


def SVG(w, h, defs, body, title=""):
    return (
        f'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" '
        f'role="img" aria-label="{title}" width="{w}" height="{h}">\n'
        f'<title>{title}</title>\n<defs>{defs}</defs>\n{body}\n</svg>\n'
    )


# ============================================================
# Shared: the AOT-MD-520 device drawn as a 3/4 isometric box
# ============================================================
def device_defs(uid):
    return f"""
  <linearGradient id="bg{uid}" x1="0" y1="0" x2="0" y2="1">
    <stop offset="0" stop-color="{ICE}"/><stop offset="1" stop-color="#F4FAFC"/>
  </linearGradient>
  <linearGradient id="top{uid}" x1="0" y1="0" x2="0.6" y2="1">
    <stop offset="0" stop-color="{PL_HI}"/><stop offset="1" stop-color="{PL_MID}"/>
  </linearGradient>
  <linearGradient id="front{uid}" x1="0" y1="0" x2="0" y2="1">
    <stop offset="0" stop-color="{PL_MID}"/><stop offset="1" stop-color="{PL_SH}"/>
  </linearGradient>
  <linearGradient id="side{uid}" x1="0" y1="0" x2="1" y2="0">
    <stop offset="0" stop-color="{PL_SH}"/><stop offset="1" stop-color="{PL_DK}"/>
  </linearGradient>
  <radialGradient id="knob{uid}" cx="0.35" cy="0.3" r="0.8">
    <stop offset="0" stop-color="#5B6B7A"/><stop offset="0.6" stop-color="#33414E"/><stop offset="1" stop-color="#1B252E"/>
  </radialGradient>
  <radialGradient id="blue{uid}" cx="0.35" cy="0.3" r="0.8">
    <stop offset="0" stop-color="#6FB7FF"/><stop offset="0.6" stop-color="#2E73C8"/><stop offset="1" stop-color="#173E78"/>
  </radialGradient>
  <radialGradient id="led{uid}" cx="0.4" cy="0.35" r="0.7">
    <stop offset="0" stop-color="#BFFFD0"/><stop offset="0.5" stop-color="#34D058"/><stop offset="1" stop-color="#137a2d"/>
  </radialGradient>
  <filter id="soft{uid}" x="-20%" y="-20%" width="140%" height="140%">
    <feDropShadow dx="0" dy="10" stdDeviation="14" flood-color="#0B3A52" flood-opacity="0.18"/>
  </filter>
"""


def device_group(uid):
    """Returns the device drawn with front-top-left at (170,372). 3/4 view."""
    # geometry
    FLb, FRb = (170, 500), (690, 500)
    FRt, FLt = (690, 372), (170, 372)
    D = (150, -100)
    BLt = (FLt[0]+D[0], FLt[1]+D[1])  # 320,272
    BRt = (FRt[0]+D[0], FRt[1]+D[1])  # 840,272
    BRb = (BRt[0], BRt[1]+128)        # 840,400

    p = []
    p.append(f'<g filter="url(#soft{uid})">')
    # ground shadow
    p.append(f'<ellipse cx="500" cy="520" rx="330" ry="34" fill="#0B3A52" opacity="0.12"/>')
    # right side face
    p.append(f'<path d="M{FRb[0]},{FRb[1]} L{FRt[0]},{FRt[1]} L{BRt[0]},{BRt[1]} L{BRb[0]},{BRb[1]} Z" fill="url(#side{uid})" stroke="{PL_DK}" stroke-width="1.5"/>')
    # vent slots on right side (sheared)
    for i in range(5):
        x0 = FRb[0]+18; y0 = FRb[1]-22 - i*16
        p.append(f'<path d="M{x0},{y0} l120,-80 l14,0 l-120,80 Z" fill="#0B3A52" opacity="0.08"/>')
    # front face
    p.append(f'<path d="M{FLb[0]},{FLb[1]} L{FRb[0]},{FRb[1]} L{FRt[0]},{FRt[1]} L{FLt[0]},{FLt[1]} Z" fill="url(#front{uid})" stroke="{PL_DK}" stroke-width="1.5"/>')
    # front vent slots (left + right groups)
    for gx in (210, 560):
        for i in range(6):
            p.append(f'<rect x="{gx}" y="{392+i*16}" width="86" height="6" rx="3" fill="#0B3A52" opacity="0.06"/>')
    # front nameplate
    p.append(f'<rect x="372" y="398" width="180" height="74" rx="9" fill="#0B3A52"/>')
    p.append(f'<rect x="372" y="398" width="180" height="74" rx="9" fill="url(#top{uid})" opacity="0.08"/>')
    p.append(f'<text x="462" y="424" text-anchor="middle" font-family="Arial,Segoe UI,sans-serif" font-size="13" letter-spacing="2" fill="{SKY}">MEDICAL OZONE CARE</text>')
    p.append(f'<text x="462" y="452" text-anchor="middle" font-family="Arial,Segoe UI,sans-serif" font-weight="700" font-size="22" letter-spacing="1" fill="#FFFFFF">AOT-MD-520</text>')
    # power led on front
    p.append(f'<circle cx="252" cy="486" r="6" fill="url(#led{uid})"/>')
    # CE mark on front (upright — front face is not sheared)
    p.append(f'<text x="572" y="448" font-family="Arial,Segoe UI,sans-serif" font-weight="700" font-size="22" fill="{STEEL_D}">CE</text>')

    # ---- top face (transformed local space 520 x 150) ----
    m = f'matrix(1,0,1,-0.6667,{FLt[0]},{FLt[1]})'
    t = [f'<g transform="{m}">']
    # top surface fill
    t.append(f'<rect x="0" y="0" width="520" height="150" fill="url(#top{uid})" stroke="{PL_DK}" stroke-width="1.5"/>')
    # NOTE: the top face is a sheared parallelogram (negative determinant), so real
    # text would render mirrored. We use color bars + faux-text lines here; the fully
    # labelled panel lives in device-top.svg.
    # chart panel
    t.append(f'<rect x="14" y="10" width="288" height="92" rx="5" fill="#FFFFFF" stroke="{PL_SH}" stroke-width="1"/>')
    t.append(f'<rect x="22" y="16" width="118" height="7" rx="3.5" fill="{TEAL}"/>')        # faux title
    t.append(f'<rect x="150" y="17" width="40" height="6" rx="3" fill="{PL_SH}"/>')          # faux subtitle
    # mode bars (M1 teal / M2 amber)
    t.append(f'<rect x="52" y="27" width="160" height="6" rx="2" fill="{TEAL}" opacity="0.9"/>')
    t.append(f'<rect x="212" y="27" width="80" height="6" rx="2" fill="{BRASS}"/>')
    # chart grid
    ncol, nrow = 6, 6
    gx, gy, cw, ch = 52, 38, 40, 10
    for r in range(nrow):
        t.append(f'<rect x="26" y="{gy+r*ch+1}" width="20" height="{ch-3}" rx="1.5" fill="{PL_MID}"/>')  # faux row label
        for c in range(ncol):
            col = chart_color(c, r, ncol, nrow)
            t.append(f'<rect x="{gx+c*cw}" y="{gy+r*ch}" width="{cw-2}" height="{ch-1.5}" rx="1.5" fill="{col}"/>')
    # operation panel (faux text lines)
    t.append(f'<rect x="314" y="10" width="190" height="92" rx="5" fill="#FFFFFF" stroke="{PL_SH}" stroke-width="1"/>')
    t.append(f'<rect x="322" y="18" width="72" height="7" rx="3.5" fill="{TEAL}"/>')
    for i, w in enumerate((156, 142, 160, 120, 148)):
        t.append(f'<rect x="322" y="{36+i*12}" width="{w}" height="5" rx="2.5" fill="{PL_SH}"/>')
    # ---- controls band (shapes only; no text — would mirror) ----
    # M1/M2 rocker
    t.append(f'<rect x="20" y="112" width="30" height="26" rx="4" fill="#222C34"/>')
    t.append(f'<rect x="24" y="116" width="22" height="9" rx="2" fill="#3a4751"/>')
    # L1-L4 buttons
    for lx in (78, 106, 134, 162):
        t.append(f'<circle cx="{lx}" cy="124" r="9" fill="#EFF3F7" stroke="{STEEL}" stroke-width="1.4"/>')
        t.append(f'<circle cx="{lx}" cy="124" r="4.5" fill="#D7DFE7"/>')
    # big M2 knob
    t.append(f'<circle cx="232" cy="124" r="24" fill="url(#knob{uid})"/>')
    t.append(f'<rect x="230" y="104" width="4" height="16" rx="2" fill="#cfd8df"/>')
    # L5 / L6 blue knobs
    for bx in (296, 330):
        t.append(f'<circle cx="{bx}" cy="120" r="12" fill="url(#blue{uid})"/>')
        t.append(f'<circle cx="{bx}" cy="120" r="4" fill="#cfe6ff" opacity="0.8"/>')
    # O3 outlets
    for ox in (452, 492):
        t.append(f'<circle cx="{ox}" cy="122" r="8" fill="#E7ECF1" stroke="{STEEL}" stroke-width="1.4"/>')
        t.append(f'<circle cx="{ox}" cy="122" r="3" fill="{STEEL_D}"/>')
    t.append('</g>')
    p.extend(t)
    p.append('</g>')
    return "\n".join(p)


def gen_device_hero():
    uid = "h"
    body = f'<rect x="0" y="0" width="1000" height="640" fill="url(#bg{uid})"/>\n' + device_group(uid)
    write("device-hero.svg", SVG(1000, 640, device_defs(uid), body, "AOT-MD-520 Medical Ozone Generator"))


def gen_device_card():
    uid = "c"
    defs = device_defs(uid) + f"""
  <linearGradient id="card{uid}" x1="0" y1="0" x2="1" y2="1">
    <stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{ICE}"/>
  </linearGradient>"""
    body = (
        f'<rect x="0" y="0" width="1000" height="640" rx="0" fill="url(#card{uid})"/>\n'
        f'<circle cx="820" cy="150" r="180" fill="{TEAL}" opacity="0.06"/>\n'
        f'<circle cx="150" cy="560" r="140" fill="{TEAL}" opacity="0.05"/>\n'
        + device_group(uid)
    )
    write("device-card.svg", SVG(1000, 640, defs, body, "AOT-MD-520 ozone generator"))


# ============================================================
# Flat top-down panel (readable schematic)
# ============================================================
def gen_device_top():
    defs = f"""
  <linearGradient id="bgt" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{ICE}"/><stop offset="1" stop-color="#F4FAFC"/></linearGradient>
  <linearGradient id="caset" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{PL_MID}"/></linearGradient>
  <radialGradient id="knobt" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#5B6B7A"/><stop offset="0.6" stop-color="#33414E"/><stop offset="1" stop-color="#1B252E"/></radialGradient>
  <radialGradient id="bluet" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#6FB7FF"/><stop offset="0.6" stop-color="#2E73C8"/><stop offset="1" stop-color="#173E78"/></radialGradient>
  <filter id="dst" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#0B3A52" flood-opacity="0.18"/></filter>
"""
    W, H = 760, 480
    b = [f'<rect width="{W}" height="{H}" fill="url(#bgt)"/>']
    b.append(f'<g filter="url(#dst)"><rect x="40" y="40" width="680" height="400" rx="22" fill="url(#caset)" stroke="{PL_SH}" stroke-width="2"/></g>')
    # chart
    b.append(f'<rect x="64" y="64" width="360" height="150" rx="8" fill="#FFFFFF" stroke="{PL_SH}"/>')
    b.append(f'<text x="78" y="84" font-family="Arial" font-size="13" font-weight="700" fill="{NAVY}">O3 Concentration Chart <tspan font-size="10" fill="{STEEL_D}">(µg/ml)</tspan></text>')
    ncol, nrow = 6, 6
    gx, cw, ch = 130, 46, 14
    cy0 = 122  # first cell row y
    labels = ["L1","L2","L3","L4","L5","L6"]; flows=["1","3/4","1/2","1/4","1/8","1/16"]
    # mode headers
    b.append(f'<rect x="{gx}" y="92" width="{cw*4}" height="13" rx="2" fill="{TEAL}" opacity="0.9"/><text x="{gx+cw*2}" y="102" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700" fill="#fff">M1</text>')
    b.append(f'<rect x="{gx+cw*4}" y="92" width="{cw*2}" height="13" rx="2" fill="{BRASS}"/><text x="{gx+cw*5}" y="102" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700" fill="#fff">M2</text>')
    b.append(f'<text x="{gx-8}" y="118" text-anchor="end" font-family="Arial" font-size="8" font-weight="700" fill="{STEEL_D}">L/min</text>')
    for c in range(ncol):
        b.append(f'<text x="{gx+c*cw+cw/2}" y="118" text-anchor="middle" font-family="Arial" font-size="9" font-weight="700" fill="{NAVY}">{labels[c]}</text>')
    for r in range(nrow):
        b.append(f'<text x="{gx-8}" y="{cy0+r*ch+10}" text-anchor="end" font-family="Arial" font-size="8" fill="{STEEL_D}">{flows[r]}</text>')
        for c in range(ncol):
            b.append(f'<rect x="{gx+c*cw}" y="{cy0+r*ch}" width="{cw-3}" height="{ch-2}" rx="2" fill="{chart_color(c,r,ncol,nrow)}"/>')
    # operation
    b.append(f'<rect x="440" y="64" width="256" height="150" rx="8" fill="#FFFFFF" stroke="{PL_SH}"/>')
    b.append(f'<text x="456" y="86" font-family="Arial" font-size="12" font-weight="700" fill="{NAVY}">Operation</text>')
    ops=["1. Press M1 or M2 to set mode.","2. Adjust oxygen flow on regulator.","3. Press L1–L6 for the level you need.","4. Wait ~30s for stable ozone output.","Unused ozone routes to destructor port."]
    for i,o in enumerate(ops):
        b.append(f'<text x="456" y="{108+i*18}" font-family="Arial" font-size="9.5" fill="{STEEL_D}">{o}</text>')
    # controls
    cy=320
    b.append(f'<rect x="70" y="{cy-18}" width="34" height="40" rx="5" fill="#222C34"/><rect x="75" y="{cy-12}" width="24" height="14" rx="3" fill="#3a4751"/><text x="87" y="{cy+38}" text-anchor="middle" font-family="Arial" font-size="10" fill="{NAVY}">M1/M2</text>')
    for i,lx in enumerate((150,196,242,288)):
        b.append(f'<circle cx="{lx}" cy="{cy}" r="15" fill="#EFF3F7" stroke="{STEEL}" stroke-width="2"/><circle cx="{lx}" cy="{cy}" r="7" fill="#D7DFE7"/><text x="{lx}" y="{cy+38}" text-anchor="middle" font-family="Arial" font-size="10" fill="{NAVY}">L{i+1}</text>')
    b.append(f'<circle cx="380" cy="{cy}" r="34" fill="url(#knobt)"/><rect x="377" y="{cy-30}" width="6" height="22" rx="3" fill="#cfd8df"/><text x="380" y="{cy+50}" text-anchor="middle" font-family="Arial" font-size="10" fill="{NAVY}">M2 flow</text>')
    for i,bx in enumerate((452,498)):
        b.append(f'<circle cx="{bx}" cy="{cy-4}" r="17" fill="url(#bluet)"/><circle cx="{bx}" cy="{cy-4}" r="6" fill="#cfe6ff" opacity="0.85"/><text x="{bx}" y="{cy+38}" text-anchor="middle" font-family="Arial" font-size="10" fill="{NAVY}">L{5+i}</text>')
    b.append(f'<text x="566" y="{cy+6}" font-family="Arial" font-weight="700" font-size="30" fill="{NAVY}" opacity="0.85">CE</text>')
    for i,ox in enumerate((636,684)):
        b.append(f'<circle cx="{ox}" cy="{cy}" r="13" fill="#E7ECF1" stroke="{STEEL}" stroke-width="2"/><circle cx="{ox}" cy="{cy}" r="5" fill="{STEEL_D}"/><text x="{ox}" y="{cy+38}" text-anchor="middle" font-family="Arial" font-size="9" fill="{NAVY}">O₃ {i+1}</text>')
    write("device-top.svg", SVG(W, H, defs, "\n".join(b), "AOT-MD-520 control panel (top view)"))


# ============================================================
# Back / ports panel
# ============================================================
def gen_device_ports():
    defs = f"""
  <linearGradient id="bgp" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{ICE}"/><stop offset="1" stop-color="#F4FAFC"/></linearGradient>
  <linearGradient id="casep" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{PL_MID}"/></linearGradient>
  <filter id="dsp" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#0B3A52" flood-opacity="0.18"/></filter>
"""
    W, H = 760, 420
    b=[f'<rect width="{W}" height="{H}" fill="url(#bgp)"/>']
    b.append(f'<g filter="url(#dsp)"><rect x="60" y="120" width="640" height="200" rx="18" fill="url(#casep)" stroke="{PL_SH}" stroke-width="2"/></g>')
    b.append(f'<text x="380" y="100" text-anchor="middle" font-family="Arial" font-size="15" font-weight="700" fill="{NAVY}">Rear ports</text>')
    def port(cx, label, inner):
        s = f'<circle cx="{cx}" cy="220" r="34" fill="#EFF3F7" stroke="{STEEL}" stroke-width="2.5"/>'
        s += inner
        s += f'<text x="{cx}" y="300" text-anchor="middle" font-family="Arial" font-size="12" fill="{NAVY}">{label}</text>'
        return s
    b.append(port(180, "O₂ inlet", f'<circle cx="180" cy="220" r="14" fill="{STEEL_D}"/><circle cx="180" cy="220" r="6" fill="#222"/>'))
    b.append(port(330, "O₃ destructor", f'<circle cx="330" cy="220" r="16" fill="#2b3640"/><path d="M330,206 v28 M316,220 h28" stroke="#7f8b96" stroke-width="3"/>'))
    b.append(port(480, "Power socket", f'<rect x="462" y="206" width="36" height="28" rx="4" fill="#2b3640"/><rect x="470" y="214" width="6" height="12" fill="#7f8b96"/><rect x="484" y="214" width="6" height="12" fill="#7f8b96"/>'))
    b.append(port(610, "Fuse / switch", f'<rect x="596" y="206" width="28" height="28" rx="4" fill="#222C34"/><rect x="601" y="214" width="18" height="12" rx="2" fill="#3a4751"/>'))
    write("device-ports.svg", SVG(W, H, defs, "\n".join(b), "AOT-MD-520 rear ports"))


# ============================================================
# Oxygen regulator (parametrised by inlet type)
# ============================================================
def regulator(name, model, inlet, title):
    uid = model.replace("-", "")
    defs = f"""
  <linearGradient id="bg{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{ICE}"/><stop offset="1" stop-color="#F4FAFC"/></linearGradient>
  <linearGradient id="body{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#56C46E"/><stop offset="0.5" stop-color="{GREEN}"/><stop offset="1" stop-color="{GREEN_D}"/></linearGradient>
  <radialGradient id="gauge{uid}" cx="0.4" cy="0.35" r="0.7"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="#E7ECEF"/></radialGradient>
  <linearGradient id="steel{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#D7DEE4"/><stop offset="0.5" stop-color="{STEEL}"/><stop offset="1" stop-color="{STEEL_D}"/></linearGradient>
  <filter id="ds{uid}" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="9" stdDeviation="12" flood-color="#0B3A52" flood-opacity="0.2"/></filter>
"""
    W, H = 620, 460
    b=[f'<rect width="{W}" height="{H}" fill="url(#bg{uid})"/>']
    b.append(f'<ellipse cx="310" cy="392" rx="180" ry="26" fill="#0B3A52" opacity="0.12"/>')
    b.append(f'<g filter="url(#ds{uid})">')
    # main green body (horizontal capsule)
    b.append(f'<rect x="150" y="210" width="240" height="96" rx="22" fill="url(#body{uid})" stroke="{GREEN_D}" stroke-width="2"/>')
    b.append(f'<rect x="162" y="220" width="216" height="14" rx="7" fill="#FFFFFF" opacity="0.25"/>')
    # gauge
    b.append(f'<circle cx="240" cy="170" r="62" fill="url(#steel{uid})"/>')
    b.append(f'<circle cx="240" cy="170" r="52" fill="url(#gauge{uid})" stroke="{STEEL_D}" stroke-width="2"/>')
    for a in range(-60, 241, 30):
        import math
        rad = math.radians(a)
        x1=240+42*math.cos(rad); y1=170-42*math.sin(rad)
        x2=240+50*math.cos(rad); y2=170-50*math.sin(rad)
        b.append(f'<line x1="{x1:.1f}" y1="{y1:.1f}" x2="{x2:.1f}" y2="{y2:.1f}" stroke="{NAVY}" stroke-width="2"/>')
    b.append(f'<line x1="240" y1="170" x2="276" y2="138" stroke="#D0342C" stroke-width="3.5" stroke-linecap="round"/>')
    b.append(f'<circle cx="240" cy="170" r="6" fill="{NAVY}"/>')
    b.append(f'<text x="240" y="210" text-anchor="middle" font-family="Arial" font-size="9" fill="{STEEL_D}">O₂  MPa</text>')
    # flow knob (right end)
    b.append(f'<circle cx="372" cy="258" r="30" fill="url(#steel{uid})" stroke="{STEEL_D}" stroke-width="2"/>')
    for i in range(12):
        import math
        a=math.radians(i*30);
        b.append(f'<line x1="{372+22*math.cos(a):.1f}" y1="{258+22*math.sin(a):.1f}" x2="{372+29*math.cos(a):.1f}" y2="{258+29*math.sin(a):.1f}" stroke="{STEEL_D}" stroke-width="2"/>')
    b.append(f'<circle cx="372" cy="258" r="10" fill="{NAVY}"/>')
    # barbed outlet (bottom)
    b.append(f'<rect x="250" y="306" width="20" height="40" fill="url(#steel{uid})"/>')
    b.append(f'<path d="M244,346 h32 l-4,10 h-24 Z" fill="{STEEL_D}"/>')
    b.append(f'<rect x="256" y="356" width="8" height="22" rx="3" fill="{STEEL}"/>')
    # inlet (left) varies
    b.append(inlet(uid))
    b.append('</g>')
    b.append(f'<text x="310" y="430" text-anchor="middle" font-family="Arial" font-size="20" font-weight="700" fill="{NAVY}">{model}</text>')
    write(name, SVG(W, H, defs, "\n".join(b), title))


def inlet_pin(uid):  # CGA-870 pin-index yoke
    return (f'<rect x="96" y="226" width="60" height="64" rx="10" fill="url(#steel{uid})" stroke="{STEEL_D}" stroke-width="2"/>'
            f'<rect x="120" y="200" width="16" height="40" rx="4" fill="url(#steel{uid})"/>'
            f'<circle cx="112" cy="276" r="4" fill="{NAVY}"/><circle cx="124" cy="276" r="4" fill="{NAVY}"/>'
            f'<circle cx="128" cy="220" r="9" fill="{BRASS}"/>')

def inlet_thread(uid):  # CGA-540 threaded nut
    s=f'<rect x="92" y="236" width="64" height="44" rx="6" fill="url(#steel{uid})" stroke="{STEEL_D}" stroke-width="2"/>'
    for i in range(7):
        s+=f'<line x1="{96+i*9}" y1="238" x2="{96+i*9}" y2="278" stroke="{STEEL_D}" stroke-width="1.4" opacity="0.7"/>'
    s+=f'<rect x="78" y="248" width="18" height="20" rx="3" fill="{BRASS}"/>'
    return s

def inlet_bull(uid):  # bull-nose with handwheel
    return (f'<rect x="104" y="232" width="52" height="52" rx="8" fill="url(#steel{uid})" stroke="{STEEL_D}" stroke-width="2"/>'
            f'<circle cx="96" cy="180" r="30" fill="none" stroke="url(#steel{uid})" stroke-width="11"/>'
            f'<rect x="92" y="182" width="8" height="70" fill="url(#steel{uid})"/>'
            f'<circle cx="128" cy="258" r="8" fill="{BRASS}"/>')


# ============================================================
# Accessories
# ============================================================
def acc_wrap(uid, W, H, body, title):
    defs = f"""
  <linearGradient id="bga{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{ICE}"/></linearGradient>
  <linearGradient id="clear{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#EAF6FA"/><stop offset="0.5" stop-color="#CDEAF2"/><stop offset="1" stop-color="#AFD9E6"/></linearGradient>
  <linearGradient id="steela{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#D7DEE4"/><stop offset="1" stop-color="{STEEL_D}"/></linearGradient>
  <filter id="dsa{uid}" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="7" stdDeviation="9" flood-color="#0B3A52" flood-opacity="0.18"/></filter>
"""
    return SVG(W, H, defs, f'<rect width="{W}" height="{H}" fill="url(#bga{uid})"/>\n'+body, title)


def gen_acc_hose():
    uid="hose"; W,H=460,360
    b=[f'<ellipse cx="230" cy="300" rx="150" ry="22" fill="#0B3A52" opacity="0.10"/>','<g filter="url(#dsahose)">']
    # coil of tubing
    for i in range(6):
        r=110-i*7
        b.append(f'<ellipse cx="230" cy="{180+i*8}" rx="{r}" ry="{r*0.42:.0f}" fill="none" stroke="url(#clearhose)" stroke-width="13" opacity="0.95"/>')
        b.append(f'<ellipse cx="230" cy="{180+i*8}" rx="{r}" ry="{r*0.42:.0f}" fill="none" stroke="#FFFFFF" stroke-width="3" opacity="0.5"/>')
    b.append('</g>')
    b.append(f'<text x="230" y="344" text-anchor="middle" font-family="Arial" font-size="18" font-weight="700" fill="{NAVY}">Silicone Hose · 2 m</text>')
    write("acc-hose.svg", acc_wrap(uid,W,H,"\n".join(b),"Ozone-resistant silicone hose"))


def gen_acc_diffuser():
    uid="diff"; W,H=460,360
    b=[f'<ellipse cx="230" cy="300" rx="120" ry="20" fill="#0B3A52" opacity="0.10"/>','<g filter="url(#dsadiff)">']
    for cx in (170, 290):
        b.append(f'<rect x="{cx-26}" y="120" width="52" height="120" rx="14" fill="#8A8F94"/>')
        b.append(f'<rect x="{cx-26}" y="120" width="52" height="120" rx="14" fill="url(#cleardiff)" opacity="0.10"/>')
        # speckle
        for sx,sy in [(-12,150),(8,170),(-4,200),(14,210),(-16,225),(2,135)]:
            b.append(f'<circle cx="{cx+sx}" cy="{sy}" r="3" fill="#6f757a" opacity="0.7"/>')
        b.append(f'<rect x="{cx-6}" y="96" width="12" height="30" rx="4" fill="url(#steeladiff)"/>')
    b.append('</g>')
    b.append(f'<text x="230" y="344" text-anchor="middle" font-family="Arial" font-size="18" font-weight="700" fill="{NAVY}">Stone Diffuser × 2</text>')
    write("acc-diffuser.svg", acc_wrap(uid,W,H,"\n".join(b),"Stone diffusers"))


def gen_acc_luer():
    uid="luer"; W,H=460,360
    b=[f'<ellipse cx="230" cy="300" rx="120" ry="20" fill="#0B3A52" opacity="0.10"/>','<g filter="url(#dsaluer)">']
    b.append(f'<path d="M150,150 h120 l44,28 v36 l-44,28 h-120 Z" fill="url(#clearluer)" stroke="#9FC8D6" stroke-width="2"/>')
    # threads
    for i in range(5):
        b.append(f'<rect x="{266+i*10}" y="158" width="6" height="68" rx="2" fill="#9FC8D6" opacity="0.6"/>')
    b.append(f'<rect x="120" y="168" width="34" height="36" rx="6" fill="url(#clearluer)" stroke="#9FC8D6" stroke-width="2"/>')
    b.append(f'<rect x="324" y="178" width="34" height="16" rx="6" fill="url(#steelaluer)"/>')
    b.append('</g>')
    b.append(f'<text x="230" y="344" text-anchor="middle" font-family="Arial" font-size="18" font-weight="700" fill="{NAVY}">Male Luer Lock</text>')
    write("acc-luer.svg", acc_wrap(uid,W,H,"\n".join(b),"Male luer lock"))


def gen_acc_valve():
    uid="valve"; W,H=460,360
    b=[f'<ellipse cx="230" cy="300" rx="120" ry="20" fill="#0B3A52" opacity="0.10"/>','<g filter="url(#dsavalve)">']
    # 3-way body
    b.append(f'<rect x="120" y="172" width="220" height="34" rx="17" fill="url(#clearvalve)" stroke="#9FC8D6" stroke-width="2"/>')
    b.append(f'<rect x="212" y="206" width="34" height="70" rx="14" fill="url(#clearvalve)" stroke="#9FC8D6" stroke-width="2"/>')
    # blue stopcock handle
    b.append(f'<circle cx="229" cy="168" r="26" fill="#2E73C8"/>')
    b.append(f'<rect x="221" y="120" width="16" height="60" rx="6" fill="#2E73C8"/>')
    b.append(f'<rect x="190" y="160" width="78" height="16" rx="8" fill="#1f5aa8"/>')
    # luer ends
    for ex in (108,352):
        b.append(f'<rect x="{ex-8 if ex<200 else ex-12}" y="180" width="20" height="18" rx="5" fill="url(#steelavalve)"/>')
    b.append('</g>')
    b.append(f'<text x="230" y="344" text-anchor="middle" font-family="Arial" font-size="18" font-weight="700" fill="{NAVY}">3-Valve Lock (Stopcock)</text>')
    write("acc-valve.svg", acc_wrap(uid,W,H,"\n".join(b),"3-way valve lock"))


def gen_acc_adapter():
    uid="adp"; W,H=460,360
    b=[f'<ellipse cx="230" cy="305" rx="130" ry="20" fill="#0B3A52" opacity="0.10"/>','<g filter="url(#dsaadp)">']
    b.append(f'<rect x="160" y="150" width="140" height="110" rx="16" fill="#2B333B"/>')
    b.append(f'<rect x="160" y="150" width="140" height="40" rx="16" fill="#3a444d"/>')
    b.append(f'<rect x="290" y="176" width="40" height="20" rx="4" fill="#3a444d"/><rect x="326" y="180" width="8" height="5" fill="#9aa7b4"/><rect x="326" y="187" width="8" height="5" fill="#9aa7b4"/>')
    b.append(f'<text x="230" y="212" text-anchor="middle" font-family="Arial" font-size="11" fill="#9aa7b4">100–240V</text>')
    # cable
    b.append(f'<path d="M180,260 q-60,40 -90,10 t-40,-30" fill="none" stroke="#2B333B" stroke-width="10" stroke-linecap="round"/>')
    b.append(f'<rect x="40" y="206" width="22" height="34" rx="5" fill="#2B333B"/>')
    b.append('</g>')
    b.append(f'<text x="230" y="344" text-anchor="middle" font-family="Arial" font-size="18" font-weight="700" fill="{NAVY}">Power Adaptor 100–240V</text>')
    write("acc-adapter.svg", acc_wrap(uid,W,H,"\n".join(b),"Power adaptor"))


def gen_accessories_combo():
    """A composite card showing the accessory kit."""
    uid="kit"; W,H=1000,640
    defs=f"""
  <linearGradient id="bgk" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{ICE}"/></linearGradient>
  <linearGradient id="cleark" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#EAF6FA"/><stop offset="1" stop-color="#AFD9E6"/></linearGradient>
  <linearGradient id="steelk" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#D7DEE4"/><stop offset="1" stop-color="{STEEL_D}"/></linearGradient>
  <filter id="dsk" x="-30%" y="-30%" width="160%" height="160%"><feDropShadow dx="0" dy="8" stdDeviation="12" flood-color="#0B3A52" flood-opacity="0.16"/></filter>
"""
    b=[f'<rect width="{W}" height="{H}" fill="url(#bgk)"/>']
    b.append(f'<circle cx="850" cy="120" r="160" fill="{TEAL}" opacity="0.06"/>')
    def tile(x,y,label,inner):
        s=f'<g filter="url(#dsk)"><rect x="{x}" y="{y}" width="270" height="220" rx="18" fill="#FFFFFF" stroke="{PL_SH}" stroke-width="1.5"/></g>'
        s+=inner
        s+=f'<text x="{x+135}" y="{y+200}" text-anchor="middle" font-family="Arial" font-size="15" font-weight="700" fill="{NAVY}">{label}</text>'
        return s
    # hose
    b.append(tile(60,60,"Silicone Hose 2m", ''.join([f'<ellipse cx="195" cy="{120+i*9}" rx="74" ry="26" fill="none" stroke="url(#cleark)" stroke-width="11"/>' for i in range(4)])))
    # diffuser (centred in its tile at x=365 -> centre 500)
    dif=''
    for cx in (482,518):
        dif+=f'<rect x="{cx-17}" y="108" width="34" height="84" rx="11" fill="#8A8F94"/>'
        dif+=f'<rect x="{cx-17}" y="108" width="34" height="84" rx="11" fill="url(#cleark)" opacity="0.10"/>'
        dif+=f'<rect x="{cx-4}" y="92" width="8" height="20" rx="3" fill="url(#steelk)"/>'
        for sx,sy in [(-9,130),(6,150),(-3,172),(9,182)]:
            dif+=f'<circle cx="{cx+sx}" cy="{sy}" r="2.4" fill="#6f757a" opacity="0.7"/>'
    b.append(tile(365,60,"Stone Diffuser ×2", dif))
    # luer
    b.append(tile(670,60,"Male Luer Lock", f'<path d="M735,120 h90 l30,20 v30 l-30,20 h-90 Z" fill="url(#cleark)" stroke="#9FC8D6" stroke-width="2"/><rect x="700" y="135" width="34" height="30" rx="6" fill="url(#cleark)" stroke="#9FC8D6" stroke-width="2"/>'))
    # valve
    b.append(tile(212,330,"3-Valve Lock", f'<rect x="262" y="410" width="170" height="28" rx="14" fill="url(#cleark)" stroke="#9FC8D6" stroke-width="2"/><rect x="333" y="438" width="28" height="44" rx="10" fill="url(#cleark)" stroke="#9FC8D6" stroke-width="2"/><circle cx="347" cy="404" r="20" fill="#2E73C8"/><rect x="339" y="372" width="16" height="40" rx="6" fill="#2E73C8"/>'))
    # adapter
    b.append(tile(517,330,"Power Adaptor", f'<rect x="600" y="396" width="104" height="80" rx="12" fill="#2B333B"/><text x="652" y="442" text-anchor="middle" font-family="Arial" font-size="10" fill="#9aa7b4">100–240V</text><path d="M600,470 q-40,30 -70,6" fill="none" stroke="#2B333B" stroke-width="8" stroke-linecap="round"/>'))
    b.append(f'<text x="500" y="40" text-anchor="middle" font-family="Arial" font-size="22" font-weight="800" fill="{NAVY}">Ozone Therapy Accessory Kit</text>')
    write("accessories.svg", SVG(W,H,defs,"\n".join(b),"Accessory kit"))


# ============================================================
# Connection diagram
# ============================================================
def gen_diagram():
    uid="dg"; W,H=1000,420
    defs=f"""
  <linearGradient id="bgd" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{ICE}"/></linearGradient>
  <linearGradient id="cyl" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#3DBF66"/><stop offset="0.5" stop-color="{GREEN}"/><stop offset="1" stop-color="{GREEN_D}"/></linearGradient>
  <linearGradient id="boxd" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{PL_MID}"/></linearGradient>
  <marker id="arrd" markerWidth="12" markerHeight="12" refX="8" refY="4" orient="auto"><path d="M0,0 L8,4 L0,8 Z" fill="{TEAL_D}"/></marker>
"""
    b=[f'<rect width="{W}" height="{H}" fill="url(#bgd)"/>']
    b.append(f'<text x="500" y="44" text-anchor="middle" font-family="Arial" font-size="22" font-weight="800" fill="{NAVY}">Connection Overview</text>')
    # 1. cylinder
    b.append(f'<rect x="60" y="120" width="80" height="180" rx="36" fill="url(#cyl)" stroke="{GREEN_D}" stroke-width="2"/>')
    b.append(f'<rect x="92" y="96" width="16" height="32" fill="{STEEL}"/><circle cx="100" cy="92" r="14" fill="{STEEL}"/>')
    b.append(f'<text x="100" y="330" text-anchor="middle" font-family="Arial" font-size="14" font-weight="700" fill="{NAVY}">O₂ Cylinder</text>')
    b.append(f'<text x="100" y="350" text-anchor="middle" font-family="Arial" font-size="11" fill="{STEEL_D}">or concentrator</text>')
    # arrow
    b.append(f'<line x1="150" y1="200" x2="250" y2="200" stroke="{TEAL_D}" stroke-width="4" marker-end="url(#arrd)"/>')
    # 2. regulator
    b.append(f'<rect x="262" y="168" width="120" height="64" rx="16" fill="url(#cyl)" stroke="{GREEN_D}" stroke-width="2"/>')
    b.append(f'<circle cx="300" cy="150" r="24" fill="#EEF2F4" stroke="{STEEL_D}" stroke-width="2"/><line x1="300" y1="150" x2="314" y2="138" stroke="#D0342C" stroke-width="2.5"/>')
    b.append(f'<text x="322" y="330" text-anchor="middle" font-family="Arial" font-size="14" font-weight="700" fill="{NAVY}">Regulator</text>')
    b.append(f'<text x="322" y="350" text-anchor="middle" font-family="Arial" font-size="11" fill="{STEEL_D}">sets O₂ flow</text>')
    b.append(f'<line x1="392" y1="200" x2="492" y2="200" stroke="{TEAL_D}" stroke-width="4" marker-end="url(#arrd)"/>')
    # 3. generator
    b.append(f'<rect x="506" y="150" width="170" height="100" rx="16" fill="url(#boxd)" stroke="{PL_SH}" stroke-width="2"/>')
    b.append(f'<rect x="522" y="166" width="80" height="34" rx="4" fill="#FFF" stroke="{PL_SH}"/>')
    for c in range(4):
        for r in range(3):
            b.append(f'<rect x="{524+c*19}" y="{168+r*10}" width="16" height="8" fill="{chart_color(c,r,4,3)}"/>')
    b.append(f'<circle cx="540" cy="222" r="8" fill="#EFF3F7" stroke="{STEEL}"/><circle cx="566" cy="222" r="8" fill="#EFF3F7" stroke="{STEEL}"/><circle cx="600" cy="220" r="12" fill="url(#cyl)" opacity="0"/><circle cx="600" cy="220" r="12" fill="#33414E"/>')
    b.append(f'<text x="591" y="330" text-anchor="middle" font-family="Arial" font-size="14" font-weight="700" fill="{NAVY}">Ozone Generator</text>')
    b.append(f'<text x="591" y="350" text-anchor="middle" font-family="Arial" font-size="11" fill="{STEEL_D}">AOT-MD-520</text>')
    b.append(f'<line x1="686" y1="200" x2="786" y2="200" stroke="{TEAL_D}" stroke-width="4" marker-end="url(#arrd)"/>')
    # 4. application
    b.append(f'<circle cx="860" cy="200" r="58" fill="#EAF6FA" stroke="{TEAL}" stroke-width="2"/>')
    # bubbles
    for bx,by,r in [(842,210,7),(858,225,5),(872,205,6),(852,190,4),(868,188,5)]:
        b.append(f'<circle cx="{bx}" cy="{by}" r="{r}" fill="{SKY}" opacity="0.8"/>')
    b.append(f'<path d="M828,170 q32,-26 64,0" fill="none" stroke="{TEAL_D}" stroke-width="3"/>')
    b.append(f'<text x="860" y="300" text-anchor="middle" font-family="Arial" font-size="14" font-weight="700" fill="{NAVY}">Application</text>')
    b.append(f'<text x="860" y="320" text-anchor="middle" font-family="Arial" font-size="11" fill="{STEEL_D}">diffuser / luer</text>')
    write("diagram-connection.svg", SVG(W,H,defs,"\n".join(b),"Connection overview diagram"))


# ============================================================
# Logo + favicon
# ============================================================
def o3_mark(cx, cy, s, dark=False):
    """Three bonded spheres (ozone molecule)."""
    c1 = TEAL if not dark else "#FFFFFF"
    g = []
    pts = [(cx, cy-0.0*s), (cx-0.9*s, cy+0.55*s), (cx+0.9*s, cy+0.55*s)]
    # bonds
    g.append(f'<line x1="{pts[0][0]}" y1="{pts[0][1]}" x2="{pts[1][0]}" y2="{pts[1][1]}" stroke="{c1}" stroke-width="{0.16*s}" opacity="0.6"/>')
    g.append(f'<line x1="{pts[0][0]}" y1="{pts[0][1]}" x2="{pts[2][0]}" y2="{pts[2][1]}" stroke="{c1}" stroke-width="{0.16*s}" opacity="0.6"/>')
    cols = [TEAL, TEAL_D, CYAN]
    for i,(px,py) in enumerate(pts):
        g.append(f'<circle cx="{px}" cy="{py}" r="{0.5*s}" fill="url(#sph{i})"/>')
    return "\n".join(g)


def gen_logo(dark=False):
    suffix = "-light" if dark else ""
    txt_main = "#FFFFFF" if dark else NAVY
    txt_sub = SKY if dark else TEAL_D
    defs = f"""
  <radialGradient id="sph0" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#7FE8F5"/><stop offset="0.6" stop-color="{TEAL}"/><stop offset="1" stop-color="{TEAL_X}"/></radialGradient>
  <radialGradient id="sph1" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#5FD0E0"/><stop offset="0.6" stop-color="{TEAL_D}"/><stop offset="1" stop-color="{TEAL_X}"/></radialGradient>
  <radialGradient id="sph2" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#AEF6FF"/><stop offset="0.6" stop-color="{CYAN}"/><stop offset="1" stop-color="{TEAL_D}"/></radialGradient>
"""
    W,H=460,120
    b=[f'<rect width="42" height="42" x="0" y="0" fill="none"/>']
    b.append(o3_mark(56,52,40))
    b.append(f'<text x="100" y="52" font-family="Segoe UI,Arial,sans-serif" font-size="34" font-weight="800" fill="{txt_main}" letter-spacing="-0.5">Medical Ozone</text>')
    b.append(f'<text x="100" y="86" font-family="Segoe UI,Arial,sans-serif" font-size="22" font-weight="600" fill="{txt_sub}" letter-spacing="6">C A R E</text>')
    write_img(f"logo{suffix}.svg", SVG(W,H,defs,"\n".join(b),"Medical Ozone Care"))


def gen_favicon():
    defs = f"""
  <radialGradient id="sph0" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#7FE8F5"/><stop offset="0.6" stop-color="{TEAL}"/><stop offset="1" stop-color="{TEAL_X}"/></radialGradient>
  <radialGradient id="sph1" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#5FD0E0"/><stop offset="0.6" stop-color="{TEAL_D}"/><stop offset="1" stop-color="{TEAL_X}"/></radialGradient>
  <radialGradient id="sph2" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#AEF6FF"/><stop offset="0.6" stop-color="{CYAN}"/><stop offset="1" stop-color="{TEAL_D}"/></radialGradient>
  <linearGradient id="fbg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{NAVY}"/><stop offset="1" stop-color="{TEAL_X}"/></linearGradient>
"""
    b=[f'<rect width="64" height="64" rx="14" fill="url(#fbg)"/>', o3_mark(32,28,22, dark=True)]
    b.append(f'<text x="32" y="58" text-anchor="middle" font-family="Arial" font-weight="800" font-size="13" fill="#fff">O₃</text>')
    write_img("favicon.svg", SVG(64,64,defs,"\n".join(b),"O3"))


# ============================================================
# Decorative: hero bubbles + wave divider
# ============================================================
def gen_bubbles():
    defs=f'<radialGradient id="bub" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#FFFFFF" stop-opacity="0.9"/><stop offset="0.5" stop-color="{SKY}" stop-opacity="0.5"/><stop offset="1" stop-color="{TEAL}" stop-opacity="0.15"/></radialGradient>'
    import math
    b=[]
    spots=[(80,520,46),(180,420,26),(300,560,34),(900,120,60),(820,260,30),(960,360,40),(680,80,22),(120,180,18),(520,60,16),(760,520,28)]
    for x,y,r in spots:
        b.append(f'<circle cx="{x}" cy="{y}" r="{r}" fill="url(#bub)"/>')
    write_img("bubbles.svg", SVG(1000,600,defs,"\n".join(b),""))


def gen_wave():
    b=[f'<path d="M0,40 C250,90 450,0 720,40 C1000,80 1200,20 1440,50 L1440,120 L0,120 Z" fill="#FFFFFF"/>']
    write_img("wave.svg", SVG(1440,120,"","\n".join(b),""))


BLUE = "#1F6FEB"
BLUE_L = "#4FB7FF"
MUTED = "#5C6F7A"
RED = "#E5343B"


# ============================================================
# Digital V/C-display generator (the 70K Medical Ozone Care unit)
# ============================================================
def gen_generator_digital():
    uid = "dg2"
    defs = f"""
  <linearGradient id="bg{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{ICE}"/><stop offset="1" stop-color="#F4FAFC"/></linearGradient>
  <linearGradient id="front{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{PL_MID}"/></linearGradient>
  <linearGradient id="top{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{PL_SH}"/></linearGradient>
  <linearGradient id="side{uid}" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="{PL_SH}"/><stop offset="1" stop-color="{PL_DK}"/></linearGradient>
  <radialGradient id="knob{uid}" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#5B6B7A"/><stop offset="0.6" stop-color="#2b3742"/><stop offset="1" stop-color="#141c24"/></radialGradient>
  <radialGradient id="led{uid}" cx="0.4" cy="0.35" r="0.7"><stop offset="0" stop-color="#cfe8ff"/><stop offset="0.5" stop-color="{BLUE_L}"/><stop offset="1" stop-color="#0c49b8"/></radialGradient>
  <radialGradient id="steel{uid}" cx="0.4" cy="0.3" r="0.8"><stop offset="0" stop-color="#eef2f5"/><stop offset="0.6" stop-color="#aeb9c4"/><stop offset="1" stop-color="#7c8893"/></radialGradient>
  <filter id="soft{uid}" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="12" stdDeviation="16" flood-color="#0B3A52" flood-opacity="0.18"/></filter>
"""
    W, H = 1000, 700
    FLb, FRb = (180, 600), (740, 600)
    FRt, FLt = (740, 232), (180, 232)
    D = (150, -95)
    BLt = (FLt[0]+D[0], FLt[1]+D[1]); BRt = (FRt[0]+D[0], FRt[1]+D[1]); BRb = (BRt[0], BRt[1]+368)
    p = [f'<rect width="{W}" height="{H}" fill="url(#bg{uid})"/>']
    p.append(f'<g filter="url(#soft{uid})">')
    p.append(f'<ellipse cx="500" cy="616" rx="330" ry="34" fill="#0B3A52" opacity="0.12"/>')
    p.append(f'<path d="M{FRb[0]},{FRb[1]} L{FRt[0]},{FRt[1]} L{BRt[0]},{BRt[1]} L{BRb[0]},{BRb[1]} Z" fill="url(#side{uid})" stroke="{PL_DK}" stroke-width="1.5"/>')
    p.append(f'<path d="M{FLt[0]},{FLt[1]} L{FRt[0]},{FRt[1]} L{BRt[0]},{BRt[1]} L{BLt[0]},{BLt[1]} Z" fill="url(#top{uid})" stroke="{PL_DK}" stroke-width="1.5"/>')
    # power-setting table on top (sheared, no text -> avoids mirror)
    m = f'matrix(1,0,1,-0.6333,{FLt[0]},{FLt[1]})'
    t = [f'<g transform="{m}">']
    t.append(f'<rect x="24" y="14" width="420" height="118" rx="6" fill="#FFFFFF" stroke="{PL_SH}"/>')
    t.append(f'<rect x="34" y="22" width="150" height="9" rx="4" fill="{BLUE}"/>')
    seg_cols = ["#E8A23A", "#3FA9C9", "#EBD9C2", "#C9536A", "#EBD9C2", "#9B6FB0", "#6FB07A"]
    for ry in (42, 70, 98):
        t.append(f'<rect x="34" y="{ry}" width="58" height="20" rx="3" fill="{PL_MID}"/>')
        x = 98
        for c in seg_cols:
            t.append(f'<rect x="{x}" y="{ry}" width="44" height="20" rx="2" fill="{c}" opacity="0.85"/>')
            x += 46
    t.append('</g>')
    p.extend(t)
    # top ports (upright)
    for px, py in ((610, 150), (720, 128)):
        p.append(f'<rect x="{px-10}" y="{py-4}" width="20" height="46" rx="5" fill="url(#steel{uid})" stroke="{STEEL_D}" stroke-width="1"/>')
        p.append(f'<rect x="{px-13}" y="{py-18}" width="26" height="16" rx="7" fill="#bcd8ec" stroke="#8fb4cf" stroke-width="1"/>')
    # front face
    p.append(f'<path d="M{FLb[0]},{FLb[1]} L{FRb[0]},{FRb[1]} L{FRt[0]},{FRt[1]} L{FLt[0]},{FLt[1]} Z" fill="url(#front{uid})" stroke="{PL_DK}" stroke-width="1.5"/>')
    p.append('</g>')
    # port labels
    p.append(f'<text x="610" y="214" text-anchor="middle" font-family="Arial" font-size="13" fill="{NAVY}">O₃ Port</text>')
    p.append(f'<text x="720" y="196" text-anchor="middle" font-family="Arial" font-size="13" fill="{NAVY}">Vacuum</text>')
    # red corner + brand
    p.append(f'<path d="M180,232 L246,232 L180,290 Z" fill="{RED}"/>')
    p.append(f'<text x="214" y="270" font-family="Arial" font-weight="800" font-size="21" letter-spacing="0.5" fill="{BLUE}">MEDICAL <tspan fill="{TEAL_D}">OZONE</tspan> CARE</text>')
    p.append(f'<text x="214" y="289" font-family="Arial" font-size="10.5" fill="{MUTED}">B-87, Madhu Vihar, Uttam Nagar, New Delhi-110059</text>')
    p.append(f'<text x="214" y="305" font-family="Arial" font-size="10.5" fill="{MUTED}">medicalozonecare@gmail.com · www.medicalozonecare.co.in</text>')
    p.append(f'<text x="648" y="292" font-family="Arial" font-weight="700" font-size="30" fill="{STEEL_D}">CE</text>')
    # On/Off rocker
    p.append(f'<rect x="214" y="470" width="40" height="56" rx="6" fill="#1c252d"/><rect x="221" y="478" width="26" height="22" rx="3" fill="#36444f"/>')
    p.append(f'<text x="234" y="548" text-anchor="middle" font-family="Arial" font-size="12" fill="{NAVY}">On/Off</text>')
    # V/C display meter
    p.append(f'<rect x="298" y="452" width="168" height="80" rx="8" fill="#0b0f12" stroke="#2a333b" stroke-width="2"/>')
    p.append(f'<text x="312" y="486" font-family="Consolas,monospace" font-weight="700" font-size="13" fill="#9aa7b4">DC</text>')
    p.append(f'<text x="436" y="488" text-anchor="end" font-family="Consolas,monospace" font-weight="700" font-size="26" fill="#ff3b30">12.3</text>')
    p.append(f'<text x="452" y="486" text-anchor="end" font-family="Arial" font-size="12" fill="#9aa7b4">V</text>')
    p.append(f'<text x="312" y="518" font-family="Consolas,monospace" font-weight="700" font-size="13" fill="#9aa7b4">DC</text>')
    p.append(f'<text x="436" y="520" text-anchor="end" font-family="Consolas,monospace" font-weight="700" font-size="26" fill="#34d058">0.07</text>')
    p.append(f'<text x="452" y="518" text-anchor="end" font-family="Arial" font-size="12" fill="#9aa7b4">A</text>')
    p.append(f'<text x="382" y="552" text-anchor="middle" font-family="Arial" font-size="12" fill="{NAVY}">V/C Display Meter</text>')
    # power LED (blue, glowing)
    p.append(f'<circle cx="548" cy="486" r="20" fill="#dfe9f2" stroke="{STEEL_D}" stroke-width="2"/>')
    p.append(f'<circle cx="548" cy="486" r="13" fill="url(#led{uid})"/>')
    p.append(f'<circle cx="548" cy="486" r="24" fill="none" stroke="{BLUE_L}" stroke-width="2" opacity="0.5"/>')
    # current knob with dotted ticks
    import math
    for i in range(11):
        a = math.radians(140 + i*26)
        p.append(f'<circle cx="{622+34*math.cos(a):.1f}" cy="{492+34*math.sin(a):.1f}" r="1.8" fill="{STEEL_D}"/>')
    p.append(f'<circle cx="622" cy="492" r="26" fill="url(#knob{uid})"/>')
    p.append(f'<rect x="619" y="470" width="6" height="20" rx="3" fill="#cfd8df"/>')
    p.append(f'<text x="588" y="500" text-anchor="end" font-family="Arial" font-size="10" fill="{MUTED}">Min</text>')
    p.append(f'<text x="656" y="500" font-family="Arial" font-size="10" fill="{MUTED}">Max</text>')
    p.append(f'<text x="622" y="548" text-anchor="middle" font-family="Arial" font-size="12" fill="{NAVY}">Current Knob</text>')
    # vacuum switch (metal button)
    p.append(f'<circle cx="704" cy="488" r="17" fill="url(#steel{uid})" stroke="{STEEL_D}" stroke-width="2"/><circle cx="704" cy="488" r="8" fill="#c7d0d8"/>')
    p.append(f'<text x="704" y="548" text-anchor="middle" font-family="Arial" font-size="12" fill="{NAVY}">Vacuum Switch</text>')
    # catalyst port (side bottom)
    p.append(f'<rect x="742" y="556" width="40" height="10" rx="3" fill="url(#steel{uid})"/><rect x="780" y="558" width="14" height="6" rx="3" fill="{STEEL}"/>')
    write("generator-digital.svg", SVG(W, H, defs, "\n".join(p), "Medical Ozone Care digital ozone generator"))


# ============================================================
# Ozone water & oil system (touchscreen unit + flask, the 90K unit)
# ============================================================
def gen_water_oil():
    uid = "wo"
    defs = f"""
  <linearGradient id="bg{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="{ICE}"/><stop offset="1" stop-color="#F4FAFC"/></linearGradient>
  <linearGradient id="case{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#FFFFFF"/><stop offset="1" stop-color="{PL_MID}"/></linearGradient>
  <linearGradient id="screen{uid}" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#16202b"/><stop offset="1" stop-color="#0a1118"/></linearGradient>
  <linearGradient id="glass{uid}" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#EAF6FA"/><stop offset="0.5" stop-color="#CFEAF2" stop-opacity="0.55"/><stop offset="1" stop-color="#AFD9E6"/></linearGradient>
  <linearGradient id="liq{uid}" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#bfeefb" stop-opacity="0.7"/><stop offset="1" stop-color="#7fd6ec"/></linearGradient>
  <filter id="soft{uid}" x="-20%" y="-20%" width="140%" height="140%"><feDropShadow dx="0" dy="12" stdDeviation="16" flood-color="#0B3A52" flood-opacity="0.16"/></filter>
"""
    W, H = 1000, 640
    p = [f'<rect width="{W}" height="{H}" fill="url(#bg{uid})"/>']
    p.append(f'<ellipse cx="320" cy="520" rx="230" ry="30" fill="#0B3A52" opacity="0.12"/>')
    p.append(f'<ellipse cx="720" cy="520" rx="120" ry="22" fill="#0B3A52" opacity="0.10"/>')
    # ---- unit ----
    p.append(f'<g filter="url(#soft{uid})">')
    p.append(f'<rect x="90" y="150" width="400" height="360" rx="22" fill="url(#case{uid})" stroke="{PL_SH}" stroke-width="2"/>')
    p.append(f'<rect x="124" y="206" width="262" height="244" rx="12" fill="url(#screen{uid})" stroke="#2a333b" stroke-width="3"/>')
    # touchscreen UI hint
    p.append(f'<rect x="146" y="232" width="120" height="14" rx="7" fill="{TEAL}" opacity="0.7"/>')
    p.append(f'<circle cx="255" cy="330" r="48" fill="none" stroke="{BLUE_L}" stroke-width="6" opacity="0.65"/>')
    p.append(f'<text x="255" y="338" text-anchor="middle" font-family="Arial" font-weight="700" font-size="22" fill="#cfe8ff">O₃</text>')
    p.append(f'<rect x="150" y="404" width="210" height="10" rx="5" fill="#2c3a45"/>')
    # top knob + outlet
    p.append(f'<circle cx="430" cy="150" r="0" fill="none"/>')
    p.append(f'<rect x="300" y="132" width="16" height="22" rx="4" fill="#9aa7b4"/>')  # outlet stub
    p.append(f'<circle cx="360" cy="138" r="14" fill="#33414e"/>')  # knob
    p.append(f'<text x="690" y="190" font-family="Arial" font-weight="700" font-size="26" fill="{STEEL_D}">CE</text>')
    p.append('</g>')
    # ---- silicone tube from unit outlet to diffuser ----
    p.append(f'<path d="M308,128 C320,70 560,70 640,150 C690,200 700,250 700,300" fill="none" stroke="#d7e6ee" stroke-width="9" stroke-linecap="round" opacity="0.95"/>')
    p.append(f'<path d="M308,128 C320,70 560,70 640,150 C690,200 700,250 700,300" fill="none" stroke="#ffffff" stroke-width="3" stroke-linecap="round" opacity="0.6"/>')
    # ---- Erlenmeyer flask ----
    fx = 720
    p.append(f'<g filter="url(#soft{uid})">')
    # flask body (conical) path
    p.append(f'<path d="M{fx-12},300 L{fx+12},300 L{fx+12},318 L{fx+70},470 Q{fx+74},490 {fx+52},490 L{fx-52},490 Q{fx-74},490 {fx-70},470 L{fx-12},318 Z" fill="url(#glass{uid})" stroke="#9FC8D6" stroke-width="2.5"/>')
    # liquid
    p.append(f'<path d="M{fx-44},430 L{fx+44},430 L{fx+58},470 Q{fx+62},486 {fx+44},486 L{fx-44},486 Q{fx-62},486 {fx-58},470 Z" fill="url(#liq{uid})"/>')
    # neck rim
    p.append(f'<rect x="{fx-18}" y="292" width="36" height="12" rx="4" fill="#cfe7f0" stroke="#9FC8D6" stroke-width="2"/>')
    p.append('</g>')
    # bubbles in liquid
    for bx, by, r in [(fx-20, 470, 5), (fx+6, 458, 4), (fx+24, 472, 6), (fx-6, 480, 3), (fx+40, 468, 4)]:
        p.append(f'<circle cx="{bx}" cy="{by}" r="{r}" fill="#ffffff" opacity="0.75"/>')
    # ---- glass diffuser pipe into flask ----
    p.append(f'<path d="M700,300 q4,-6 14,2 l6,150" fill="none" stroke="#cfe7f0" stroke-width="7" stroke-linecap="round"/>')
    p.append(f'<rect x="716" y="446" width="10" height="26" rx="4" fill="#b9d6e2" stroke="#9FC8D6" stroke-width="1.5"/>')  # stone tip
    for bx, by in [(721, 440), (726, 428), (718, 432), (730, 444)]:
        p.append(f'<circle cx="{bx}" cy="{by}" r="2.6" fill="#ffffff" opacity="0.8"/>')
    # labels
    p.append(f'<text x="290" y="556" text-anchor="middle" font-family="Arial" font-weight="700" font-size="18" fill="{NAVY}">Ozone Water &amp; Oil System</text>')
    p.append(f'<text x="730" y="556" text-anchor="middle" font-family="Arial" font-size="13" fill="{MUTED}">Glass diffuser into bottle</text>')
    write("ozone-water-oil.svg", SVG(W, H, defs, "\n".join(p), "Ozone water and oil system"))


# ============================================================
# Applications graphic (informational — neutral, no cure claims)
# ============================================================
def gen_applications():
    uid = "app"
    defs = f"""
  <radialGradient id="c0{uid}" cx="0.4" cy="0.3" r="0.8"><stop offset="0" stop-color="#7FE8F5"/><stop offset="1" stop-color="{TEAL_D}"/></radialGradient>
  <radialGradient id="c1{uid}" cx="0.4" cy="0.3" r="0.8"><stop offset="0" stop-color="{BLUE_L}"/><stop offset="1" stop-color="{BLUE}"/></radialGradient>
"""
    W, H = 1100, 420
    p = [f'<rect width="{W}" height="{H}" fill="none"/>']
    items = [
        ("Blood Therapy", "droplet"),
        ("Joint O₃", "joint"),
        ("Dental", "tooth"),
        ("Rectal Insufflation", "organ"),
        ("Gynaecological", "flask"),
    ]
    import math
    # title with molecule
    p.append(o3_mark(470, 60, 22))
    p.append(f'<text x="510" y="68" font-family="Inter,Arial" font-weight="800" font-size="24" fill="{NAVY}">Medical Ozone — Applications</text>')
    n = len(items)
    xs = [150 + i*(800/(n-1)) for i in range(n)]
    ys = [250 - 26*math.sin(i/(n-1)*math.pi) for i in range(n)]
    # dotted connector through centres
    pts = " ".join(f"{xs[i]:.0f},{ys[i]:.0f}" for i in range(n))
    p.append(f'<polyline points="{pts}" fill="none" stroke="{TEAL}" stroke-width="2" stroke-dasharray="3 7" opacity="0.55"/>')
    for i, (label, gl) in enumerate(items):
        x, y = xs[i], ys[i]
        grad = "c1"+uid if i % 2 else "c0"+uid
        p.append(f'<circle cx="{x:.0f}" cy="{y:.0f}" r="58" fill="url(#{grad})" opacity="0.15"/>')
        p.append(f'<circle cx="{x:.0f}" cy="{y:.0f}" r="46" fill="url(#{grad})"/>')
        gx, gy = x, y
        if gl == "droplet":
            g = f'<path d="M{gx},{gy-16} q15,17 0,29 q-15,-12 0,-29 Z" fill="#fff"/>'
        elif gl == "joint":
            g = f'<circle cx="{gx-8}" cy="{gy-7}" r="8" fill="#fff"/><circle cx="{gx+8}" cy="{gy+8}" r="9" fill="#fff"/><rect x="{gx-4}" y="{gy-5}" width="8" height="14" rx="4" fill="#fff" transform="rotate(42 {gx} {gy})"/>'
        elif gl == "tooth":
            g = f'<path d="M{gx-14},{gy-12} q14,-11 28,0 q5,23 -6,31 q-4,-11 -8,-11 q-4,0 -8,11 q-11,-8 -6,-31 Z" fill="#fff"/>'
        elif gl == "organ":
            g = f'<path d="M{gx-12},{gy+15} q-7,-28 8,-28 q15,0 8,19 q15,-2 10,13" fill="none" stroke="#fff" stroke-width="5" stroke-linecap="round"/>'
        else:
            g = f'<path d="M{gx-7},{gy-16} h14 M{gx-5},{gy-16} v9 l-8,19 q-2,9 7,9 h12 q9,0 7,-9 l-8,-19 v-9" fill="none" stroke="#fff" stroke-width="4" stroke-linejoin="round"/>'
        p.append(g)
        p.append(f'<text x="{x:.0f}" y="{y+80:.0f}" text-anchor="middle" font-family="Inter,Arial" font-weight="700" font-size="15" fill="{NAVY}">{label}</text>')
    # reuse sphere defs from o3_mark
    sph = f"""
  <radialGradient id="sph0" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#7FE8F5"/><stop offset="0.6" stop-color="{TEAL}"/><stop offset="1" stop-color="{TEAL_X}"/></radialGradient>
  <radialGradient id="sph1" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#5FD0E0"/><stop offset="0.6" stop-color="{TEAL_D}"/><stop offset="1" stop-color="{TEAL_X}"/></radialGradient>
  <radialGradient id="sph2" cx="0.35" cy="0.3" r="0.8"><stop offset="0" stop-color="#AEF6FF"/><stop offset="0.6" stop-color="{CYAN}"/><stop offset="1" stop-color="{TEAL_D}"/></radialGradient>"""
    write("applications.svg", SVG(W, H, defs + sph, "\n".join(p), "Medical ozone applications"))


if __name__ == "__main__":
    gen_device_hero()
    gen_device_card()
    gen_device_top()
    gen_device_ports()
    gen_generator_digital()
    gen_water_oil()
    gen_applications()
    regulator("regulator-870.svg", "AOT-OR-870", inlet_pin, "Oxygen regulator AOT-OR-870")
    regulator("regulator-540.svg", "AOT-OR-540", inlet_thread, "Oxygen regulator AOT-OR-540")
    regulator("regulator-bn.svg", "AOT-OR-BN1", inlet_bull, "Oxygen regulator AOT-OR-BN")
    gen_acc_hose(); gen_acc_diffuser(); gen_acc_luer(); gen_acc_valve(); gen_acc_adapter()
    gen_accessories_combo()
    gen_diagram()
    gen_logo(False); gen_logo(True); gen_favicon()
    gen_bubbles(); gen_wave()
    print("\nAll SVGs generated.")
