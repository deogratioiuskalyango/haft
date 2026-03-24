/**
 * Pesapal OAuth 1.0a signing — mirrors Pesapal PHP samples (OAuth.php + pesapal-iframe.php).
 * @see https://developer.pesapal.com
 */

import crypto from "node:crypto";

/** Match PHP htmlentities() on the XML blob before it is sent as pesapal_request_data */
export function htmlEntitiesPhp(str) {
  return String(str)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function oauthEncode(input) {
  if (Array.isArray(input)) return input.map(oauthEncode);
  return encodeURIComponent(input)
    .replace(/!/g, "%21")
    .replace(/\*/g, "%2A")
    .replace(/'/g, "%27")
    .replace(/\(/g, "%28")
    .replace(/\)/g, "%29")
    .replace(/%7E/g, "~");
}

function buildHttpQueryFromRaw(rawParams) {
  const keys = Object.keys(rawParams);
  if (keys.length === 0) return "";
  const encKeys = keys.map((k) => oauthEncode(k));
  const encVals = keys.map((k) => oauthEncode(rawParams[k]));
  const combined = {};
  for (let i = 0; i < keys.length; i++) {
    combined[encKeys[i]] = encVals[i];
  }
  const sorted = Object.keys(combined).sort((a, b) => (a < b ? -1 : a > b ? 1 : 0));
  return sorted.map((k) => `${k}=${combined[k]}`).join("&");
}

export function getNormalizedHttpUrl(url) {
  const u = new URL(url);
  const scheme = u.protocol.replace(":", "");
  const defaultPort = scheme === "https" ? "443" : "80";
  const port = u.port || defaultPort;
  let host = u.hostname;
  if ((scheme === "https" && port !== "443") || (scheme === "http" && port !== "80")) {
    host = `${host}:${port}`;
  }
  return `${scheme}://${host}${u.pathname}`;
}

function signHmacSha1Base64(baseString, consumerSecret, tokenSecret = "") {
  const key = `${oauthEncode(consumerSecret)}&${oauthEncode(tokenSecret)}`;
  return crypto.createHmac("sha1", key).update(baseString).digest("base64");
}

function generateNonce() {
  return crypto.randomBytes(16).toString("hex");
}

function signGetUrl(apiUrl, consumerKey, consumerSecret, rawParams) {
  const params = {
    oauth_version: "1.0",
    oauth_nonce: generateNonce(),
    oauth_timestamp: Math.floor(Date.now() / 1000).toString(),
    oauth_consumer_key: consumerKey,
    ...rawParams,
  };
  params.oauth_signature_method = "HMAC-SHA1";

  const normalizedUrl = getNormalizedHttpUrl(apiUrl);
  const paramString = buildHttpQueryFromRaw(params);
  const baseString = ["GET", oauthEncode(normalizedUrl), oauthEncode(paramString)].join("&");
  const signature = signHmacSha1Base64(baseString, consumerSecret, "");
  params.oauth_signature = signature;

  return `${normalizedUrl}?${buildHttpQueryFromRaw(params)}`;
}

/** Remove characters that break XML attributes (PHP sample does not entity-encode inner values). */
function stripForXmlAttr(s) {
  return String(s).replace(/[<>&"]/g, " ").replace(/\s+/g, " ").trim();
}

/**
 * @param {object} o
 * @param {string} o.amount - numeric (no thousands separators)
 * @param {string} o.description
 * @param {string} o.reference - unique merchant reference
 * @param {string} o.firstName
 * @param {string} o.lastName
 * @param {string} o.email
 * @param {string} [o.phoneNumber]
 */
export function buildPesapalDirectOrderIframeUrl({
  consumerKey,
  consumerSecret,
  callbackUrl,
  orderApiUrl,
  amount,
  description,
  reference,
  firstName,
  lastName,
  email,
  phoneNumber = "",
}) {
  const amt = Number(amount);
  const amountStr = amt.toFixed(2);
  const rawXml =
    `<?xml version="1.0" encoding="utf-8"?>` +
    `<PesapalDirectOrderInfo xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ` +
    `xmlns:xsd="http://www.w3.org/2001/XMLSchema" Amount="${stripForXmlAttr(amountStr)}" ` +
    `Description="${stripForXmlAttr(description)}" Type="MERCHANT" Reference="${stripForXmlAttr(reference)}" ` +
    `FirstName="${stripForXmlAttr(firstName)}" LastName="${stripForXmlAttr(lastName)}" Email="${stripForXmlAttr(email)}" ` +
    `PhoneNumber="${stripForXmlAttr(phoneNumber)}" xmlns="http://www.pesapal.com" />`;
  const postXml = htmlEntitiesPhp(rawXml);

  return signGetUrl(orderApiUrl, consumerKey, consumerSecret, {
    oauth_callback: callbackUrl,
    pesapal_request_data: postXml,
  });
}

export function buildPesapalQueryPaymentStatusUrl({
  consumerKey,
  consumerSecret,
  queryApiUrl,
  merchantReference,
  transactionTrackingId,
}) {
  return signGetUrl(queryApiUrl, consumerKey, consumerSecret, {
    pesapal_merchant_reference: merchantReference,
    pesapal_transaction_tracking_id: transactionTrackingId,
  });
}
