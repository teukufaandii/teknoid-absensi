/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',          // Matches PHP files in the root directory
    './src/**/*.php',   // Matches PHP files within the src directory and its subdirectories
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
