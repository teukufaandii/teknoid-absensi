/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',            
    './**/*.php',         
  ],
  theme: {
    extend: {
      colors: {
        customColor: '#123456',
      },
      fontFamily: {
        customFont: ['"Poppins"', 'sans-serif'],
      },
    },
  },
  plugins: [],
}
