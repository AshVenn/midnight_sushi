import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    "./vendor/robsontenorio/mary/src/View/Components/**/*.php"
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  // We’re using class-based dark mode
  darkMode: 'class', 

  daisyui: {
    darkTheme: "dark", // name of the theme for dark mode
    themes: [
      // LIGHT THEME
      {
        light: {
          // A near-white background
          "base-100": "#fdfdfd",  
          // Sushi orange for primary brand color
          primary: "#ff4001",     
          // “Rice” color for a secondary highlight
          secondary: "#f5e1be",   
          // A black/gray accent
          accent: "#1f1f1f",      
          // Typically a neutral used for, e.g., backgrounds or text 
          // that isn't the primary brand color.
          // You might also use #1f1f1f for text if you need strong contrast.
          neutral: "#1f1f1f",     

          // Optionally, override foreground (content) colors for better contrast:
          // "primary-content": "#ffffff",    // text on primary
          // "secondary-content": "#1f1f1f",  // text on secondary
          // "accent-content": "#fdfdfd",     // text on accent
          // "neutral-content": "#ffffff",    // text on neutral
        },
      },

      // DARK THEME
      {
        dark: {
          // Dark background
          "base-100": "#1f1f1f",  
          // Sushi orange still as the main highlight in dark mode
          primary: "#ff4001",     
          // Rice color still used for secondary in dark mode
          secondary: "#f5e1be",   
          // Clean white for accent elements
          accent: "#fdfdfd",      
          // Use white for “neutral” if you want a lot of bright text
          // or choose a mid-gray to soften things.
          neutral: "#fdfdfd",     

          // Optionally override content (text) colors for better contrast:
          // "primary-content": "#1f1f1f",
          // "secondary-content": "#1f1f1f",
          // "accent-content": "#1f1f1f",
          // "neutral-content": "#1f1f1f",
        },
      },
    ],
  },

  plugins: [
    require("daisyui"),
  ],
};
