console.log("buscador cargado");

function irAProductoEnPagina(idProducto) {
    const card = document.getElementById(`producto-${idProducto}`);

    if (!card) {
        window.location.reload();
        return;
    }

    card.scrollIntoView({
        behavior: "smooth",
        block: "start"
    });

    document.querySelectorAll(".producto-highlight").forEach(el => {
        el.classList.remove("producto-highlight");
    });

    card.classList.add("producto-highlight");

    setTimeout(() => {
        if (typeof window.abrirModal === "function") {
            window.abrirModal(card);
        }
    }, 500);
}

document.addEventListener("DOMContentLoaded", () => {
    const buscador = document.getElementById("buscadorInput");
    const resultados = document.getElementById("search-results");
    let currentSearchIndex = -1;

    if (!buscador || !resultados) return;

    buscador.addEventListener("input", async function () {
        const termino = this.value.trim();

        if (termino.length < 2) {
            resultados.innerHTML = "";
            resultados.style.display = "none";
            currentSearchIndex = -1;
            return;
        }

        try {
            const response = await fetch(
                `${BASE_URL}/controllers/BuscarController.php?q=${encodeURIComponent(termino)}`
            );

            const productos = await response.json();

            resultados.innerHTML = "";

            // filtro precio actual
            const sliderPrecio = document.getElementById("sliderPrecio");
            const precioMaximo = sliderPrecio
                ? parseFloat(sliderPrecio.value)
                : Infinity;

            // categoría actual
            const idCategoriaActual = new URLSearchParams(
                window.location.search
            ).get("id");

            // filtrar:
            // misma categoría = aplicar filtro precio
            // otra categoría = mostrar siempre
            const productosFiltrados = productos.filter(producto => {
                const mismaCategoria =
                    String(producto.id_categoria) === String(idCategoriaActual);

                if (!mismaCategoria) return true;

                return parseFloat(producto.precio) <= precioMaximo;
            });

            if (!productosFiltrados.length) {
                resultados.innerHTML =
                    `<div class="sin-resultados">No hay productos en ese rango</div>`;
                resultados.style.display = "block";
                return;
            }

            productosFiltrados.forEach(producto => {
                const item = document.createElement("div");
                item.classList.add("search-item");

                item.innerHTML = `
                    <img src="${producto.imagen}" alt="${producto.nombre}">
                    <div class="search-info">
                        <span>${producto.nombre}</span>
                    </div>
                    <strong>S/ ${parseFloat(producto.precio).toFixed(2)}</strong>
                `;

                item.onclick = () => {
                    buscador.value = "";
                    resultados.innerHTML = "";
                    resultados.style.display = "none";

                    const idCategoriaActual = new URLSearchParams(
                        window.location.search
                    ).get("id");

                    const mismaCategoria =
                        window.location.pathname.includes("categoria.php") &&
                        String(idCategoriaActual) === String(producto.id_categoria);

                    if (mismaCategoria) {
                        irAProductoEnPagina(producto.id_producto);
                    } else {
                        const url =
                            `${BASE_URL}/views/categoria.php?id=${producto.id_categoria}#producto-${producto.id_producto}`;

                        window.location.href = url;
                    }
                };

                resultados.appendChild(item);
            });

            resultados.style.display = "block";
            currentSearchIndex = -1;

        } catch (error) {
            console.error("Error buscando:", error);
        }
    });

    // cerrar resultados al click fuera
    document.addEventListener("click", function (e) {
        if (!e.target.closest(".search-container")) {
            resultados.style.display = "none";
        }
    });

    // navegación con teclado
    buscador.addEventListener("keydown", function (e) {
        const items = resultados.querySelectorAll(".search-item");
        if (!items.length) return;

        if (e.key === "ArrowDown") {
            e.preventDefault();
            currentSearchIndex++;
            if (currentSearchIndex >= items.length) currentSearchIndex = 0;
            actualizarSeleccionBuscador(items);
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            currentSearchIndex--;
            if (currentSearchIndex < 0) currentSearchIndex = items.length - 1;
            actualizarSeleccionBuscador(items);
        } else if (e.key === "Enter") {
            e.preventDefault();
            if (currentSearchIndex >= 0 && currentSearchIndex < items.length) {
                items[currentSearchIndex].click();
            } else {
                items[0].click();
            }
        }
    });

    function actualizarSeleccionBuscador(items) {
        items.forEach((item, index) => {
            if (index === currentSearchIndex) {
                item.style.backgroundColor = "#ffecec"; // Color de selección
                item.scrollIntoView({ block: "nearest" });
            } else {
                item.style.backgroundColor = "";
            }
        });
    }
});