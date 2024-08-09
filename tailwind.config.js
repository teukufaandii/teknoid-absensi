/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './src/**/*.{php,html}',   
    './pages/**/*.{php,html}',  
  ],
  theme: {
    extend: {
      fontFamily: {
        customFont: ['"Poppins"', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
