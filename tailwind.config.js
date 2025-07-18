/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./*.php", 
    "./src/**/*.php", 
  ],
  theme: {
    extend: {
      fontFamily: {
        Poppins: ["Poppins", "sans-serif"],
      },
      colors: {
        purpleNavbar: "#8C85FF",
        purpleNavbarHover: "#675EFF",
        purpleNavbarHover2: "#3f38b5",
        dashboardBoxBlue: "#6A8DE5",
        dashboardBoxPurple: "#A062E0",
        mainBgColor: "#EFEFEF",
        greenButton: "#A3D9A5",
        greenButtonHover: "#74c38b",
      },
      boxShadow: {
        customTable: "0 0 20px 0 rgba(0, 0, 0, 0.3)", 
        dashboardTag: "0 7px 20px -1px rgba(0, 0, 0, 0.5)",
      },
    },
  },
  plugins: [],
};
