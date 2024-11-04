import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    "./storage/framework/views/*.php",
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./vendor/robsontenorio/mary/src/View/Components/**/*.php",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["Figtree", ...defaultTheme.fontFamily.sans],
      },
      animation: {
        marquee: "marquee var(--marquee-duration) linear infinite",
        "marquee-vertical":
          "marquee-vertical var(--marquee-duration) linear infinite",
      },
      keyframes: {
        marquee: {
          "100%": { transform: "translateX(-50%)" },
        },
        "marquee-vertical": {
          "100%": { transform: "translateY(-50%)" },
        },
      },
    },
  },
  plugins: [require("daisyui")],
};
