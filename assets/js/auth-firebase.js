import { initializeApp } from "https://www.gstatic.com/firebasejs/10.12.0/firebase-app.js";
import {
    getAuth,
    GoogleAuthProvider,
    signInWithPopup
} from "https://www.gstatic.com/firebasejs/10.12.0/firebase-auth.js";

const firebaseConfig = {
    apiKey: "AIzaSyCkrJw-TlDvOGVdPTy5_BgAejzSAqemGwA",
    authDomain: "fir-31a17.firebaseapp.com",
    projectId: "fir-31a17",
    storageBucket: "fir-31a17.firebasestorage.app",
    messagingSenderId: "386798394570",
    appId: "1:386798394570:web:c1f02c391a8d8465ff2833",
    measurementId: "G-5Q9L9QSMLX"
};

const app = initializeApp(firebaseConfig);
const auth = getAuth(app);
const provider = new GoogleAuthProvider();

let loginGoogleEnCurso = false;

/* LOGIN CON GOOGLE */
async function loginGoogle() {
    if (loginGoogleEnCurso) return; // evita doble clic / doble popup
    loginGoogleEnCurso = true;

    try {
        const result = await signInWithPopup(auth, provider);
        const user = result.user;

        const respuesta = await fetch(BASE_URL + "/controllers/AuthController.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
                accion: "login_google",
                correo: user.email,
                nombre: user.displayName || "Usuario",
                uid: user.uid
            })
        });

        const data = await respuesta.json();

        if (data.ok) {
            if (data.nuevo) {
                // Usuario nuevo o con DNI incompleto -> pedir datos
                if (typeof cerrarModales === "function") cerrarModales();
                if (typeof abrirCompletarGoogle === "function") {
                    abrirCompletarGoogle();
                } else {
                    console.error("abrirCompletarGoogle no está definida");
                }
            } else {
                // Usuario ya completo -> entra directo
                window.location.href = BASE_URL + "/views/inicio.php";
            }
        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.mensaje || "Error al iniciar sesión con Google", confirmButtonColor: '#e30613'});
        }

    } catch (error) {
        console.error("Google Login Error:", error);
        if (error.code !== "auth/cancelled-popup-request") {
            Swal.fire({icon: 'error', title: 'Error', text: "No se pudo iniciar sesión con Google", confirmButtonColor: '#e30613'});
        }
    } finally {
        loginGoogleEnCurso = false;
    }
}

window.loginGoogle = loginGoogle;