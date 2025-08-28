import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import { resolve } from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/js/app.jsx", // Utilisez .jsx si vous avez des fichiers JSX
                "resources/sass/app.scss",
            ],
            refresh: true,
        }),
        react({
            jsxRuntime: "classic", // Optionnel, selon votre configuration React
        }),
    ], 
    define: {
        "process.env": JSON.stringify(process.env),
        "process.env.NODE_ENV": JSON.stringify(
            process.env.NODE_ENV || "development"
        ),
    },
    server: {
        host: "localhost",
        port: 5173,
        strictPort: true,
        hmr: {
            host: "localhost",
            protocol: "ws",
        },
        watch: {
            usePolling: true,
        },
    },
    
});
