// Admin authentication: scrypt password hashing + HMAC-signed bearer tokens.
// Tokens are sent in the Authorization header (not cookies) → inherently CSRF-safe.
import crypto from "node:crypto";

const SECRET = process.env.SESSION_SECRET || "moc-dev-secret-change-me-in-production";
const TTL_MS = 1000 * 60 * 60 * 12; // 12 hours

export function hashPassword(password) {
  const salt = crypto.randomBytes(16).toString("hex");
  const hash = crypto.scryptSync(String(password), salt, 64).toString("hex");
  return `scrypt$${salt}$${hash}`;
}

export function verifyPassword(password, stored) {
  try {
    const [algo, salt, hash] = String(stored).split("$");
    if (algo !== "scrypt" || !salt || !hash) return false;
    const test = crypto.scryptSync(String(password), salt, 64).toString("hex");
    const a = Buffer.from(hash, "hex");
    const b = Buffer.from(test, "hex");
    return a.length === b.length && crypto.timingSafeEqual(a, b);
  } catch {
    return false;
  }
}

const b64 = (obj) => Buffer.from(JSON.stringify(obj)).toString("base64url");
const sign = (body) => crypto.createHmac("sha256", SECRET).update(body).digest("base64url");

export function signToken(payload, ttlMs = TTL_MS) {
  const body = b64({ ...payload, exp: Date.now() + ttlMs });
  return `${body}.${sign(body)}`;
}

export function verifyToken(token) {
  if (!token || typeof token !== "string" || !token.includes(".")) return null;
  const [body, sig] = token.split(".");
  const expected = sign(body);
  // constant-time compare
  const a = Buffer.from(sig);
  const b = Buffer.from(expected);
  if (a.length !== b.length || !crypto.timingSafeEqual(a, b)) return null;
  try {
    const payload = JSON.parse(Buffer.from(body, "base64url").toString("utf8"));
    if (!payload.exp || Date.now() > payload.exp) return null;
    return payload;
  } catch {
    return null;
  }
}
