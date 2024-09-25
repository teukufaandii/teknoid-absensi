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
      boxShadow: {
        customTable: '0 0 20px 0 rgba(0, 0, 0, 0.3)', // Define your custom box-shadow
        dashboardTag: '0 7px 20px -1px rgba(0, 0, 0, 0.5)',
      },
    },
  },
  plugins: [],
}
