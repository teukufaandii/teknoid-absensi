# Teknoid Absensi

## Project Overview

Teknoid Absensi is a PHP-based web application designed to manage and track attendance. It provides a user-friendly interface for both administrators and users, facilitating efficient attendance management.

## Features

- **User Authentication**: Secure login system with password reset functionality.
- **Attendance Management**: Easy tracking and management of attendance records.
- **Notifications**: Email notifications using PHPMailer.
- **Responsive Design**: Built with Tailwind CSS for a mobile-friendly experience.
- **Icon Support**: Utilizes Font Awesome for icons.

## Technologies Used

- **Backend**: PHP, MySQLi
- **Frontend**: Tailwind CSS, Font Awesome, Poppins Font
- **JavaScript**: Vanilla JS, Node.js (for building assets)
- **Version Control**: Git
- **Package Management**: npm, Composer
- **Email**: PHPMailer
- **IDE**: Visual Studio Code (VSCode)

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:

- [XAMPP](https://www.apachefriends.org/index.html) (for local server)
- [Node.js](https://nodejs.org/) (for npm)
- [Composer](https://getcomposer.org/) (for PHP dependencies)
- [Git](https://git-scm.com/) (for version control)

## Installation Guide

Follow these steps to set up the project on your local machine:

1. **Clone the Repository**: Start by cloning the repository into your XAMPP `htdocs` folder:
    ```bash
    git clone https://github.com/teukufaandii/teknoid-absensi.git
    ```

2. **Open the Project in VSCode or Another IDE**: Navigate to the project folder and open it in Visual Studio Code (VSCode) or your preferred IDE:
    ```bash
    cd htdocs/teknoid-absensi
    code .
    ```

3. **Install Dependencies**: Open the terminal in your IDE and run the following commands to install the necessary dependencies:
    - Install Node.js dependencies:
        ```bash
        npm install
        ```
    - Install PHP dependencies:
        ```bash
        composer install
        ```

4. **Build the Project**: Run the following command to compile and bundle the necessary assets:
    ```bash
    npm run build
    ```

5. **Access the Project**: Finally, start your XAMPP server and access the project in your browser through the local host:
    ```plaintext
    http://localhost/teknoid-absensi
    ```

