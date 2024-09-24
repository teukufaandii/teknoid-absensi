/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',          // Matches PHP files in the root directory
    './src/**/*.php',   // Matches PHP files within the src directory and its subdirectories
  ],
  theme: {
    extend: {
      fontFamily: {
        Poppins: ['Poppins', 'sans-serif'],
      },
      colors: {
        purpleNavbar: '#8C85FF',
        purpleNavbarHover: '#675EFF', 
        dashboardBoxBlue: '#6A8DE5',
        dashboardBoxPurple: '#A062E0',
        mainBgColor: '#EFEFEF',
      },
    },
  },
  plugins: [],
}
