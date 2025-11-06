import { initializeApp, cert } from "firebase-admin/app";
import { getAuth } from "firebase-admin/auth";
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

// Fix __dirname for ESM
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Load service account JSON
const serviceAccountPath = path.join(__dirname, "firebaseServiceAccount.json");
const serviceAccount = JSON.parse(fs.readFileSync(serviceAccountPath, "utf8"));

// Initialize Firebase Admin SDK
initializeApp({
    credential: cert(serviceAccount),
});

const uid = "test-user"; // Replace with a UID from your DB

async function generateToken() {
    try {
        const customToken = await getAuth().createCustomToken(uid);
        console.log("Firebase Custom Token (use this in Authorization header):");
        console.log(customToken);
    } catch (error) {
        console.error("Error creating custom token:", error);
    }
}

generateToken();
