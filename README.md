# 🌟 Radagile - A New Era of Team Growth

## 🏆 Product Vision
To empower teams with the insights, tools, and guidance to master Agile, break through plateaus, and achieve lasting transformation.

---

## ✨ Mission Statement
Our mission is to empower teams to unlock their full potential by providing:

- **Tailored assessments**
- **Actionable insights**
- **Continuous learning opportunities**

We are committed to guiding organizations through their Agile transformation journey, enabling them to build **high-performing teams** that deliver **exceptional value** and drive **sustainable change**.

---

## 🚨 Why This Matters

Agile teams today are stuck in a cycle of **_doing Agile_** rather than **_being Agile_**.

They follow the motions:
- 👥 Stand-ups
- 🕰️ Retrospectives
- 📈 Velocity tracking

❌ Without truly evolving. Leaders struggle to measure progress, teams hit invisible barriers, and organizations **invest in Agile** without understanding if it’s working.

---

## ✅ The Solution: Data-Driven Growth with Personalized Insights
We believe Agile is more than just frameworks—it’s a **culture, a mindset, and a pursuit of excellence**.

Our platform provides:
🔍 **Dynamic Agile Maturity Assessments**  
💡 **AI-powered recommendations** tailored to team challenges  
📊 **Measurable paths for fostering true Agile culture**

---

## 🔧 Features Built So Far

### 🛡️ **Authentication and Access Control**
- 🚪 **Login & Logout:** Users can securely log in and out of the platform.
- 📝 **User Registration:** Allow users to sign up through guided workflows.
- 🔒 **Role-Based Access Control (RBAC):** Restrict access based on user roles.
- 🪙 **Subscription System:** Manage user subscriptions with variable-level access.

### 📨 **Registration and Invitation Workflow**
- 📌 **Invitation-Only Registration:** Enroll new users through invitation links.
- 🔔 **Notify Me Feature:** Capture visitor interest for admin follow-up.

### 📊 **User and Admin Dashboards**
- 👤 **User Dashboard:** Personalized hub for activities, updates, and insights.
- 👥 **Admin Dashboard:** Powerful tools for managing users, roles, and settings.

### 👥 **Profile Management**
- 🧑 **User Profile Pages:** View and update personal information.
- 🛠️ **Admin Profile Pages:** Advanced management for admins with enhanced controls.

### ⚙️ **Administration Tools**
- 🛠️ **Settings Page:** Configure platform-wide settings.
- 📋 **User Management:** Manage user accounts, roles, and permissions.

---

## 💻 Technologies Used

| Backend      | Database | Frontend          | PHP Version | Dependency Management |
|--------------|----------|-------------------|-------------|------------------------|
| Laravel 11   | MySQL    | Bootstrap (AdminLTE) | >= 8.2     | Composer, npm          |

---

## 🛠️ How to Install and Run this Project Locally

📝 **Prerequisites**  
Before you start, ensure you have the following installed:
- **PHP** >= 8.2
- **Composer**
- **MySQL**
- **Node.js and npm**
- (optional but preferred) **Git**

---

📂 **Installation Steps**

### Step 1️⃣: Clone the Repository
```bash
git clone https://github.com/your-repo/radagile.git
cd radagile
```

### Step 2️⃣: Install Backend Dependencies
```bash
composer install
```

### Step 3️⃣: Configure Environment Variables
- Duplicate the `.env.example` file:
  ```bash
  cp .env.example .env
  ```
- Update the `.env` file with your database credentials:
  ```plaintext
  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=radagile_db
  DB_USERNAME=root
  DB_PASSWORD=your_password
  ```

### Step 4️⃣: Generate the Application Key
```bash
php artisan key:generate
```

### Step 5️⃣: Run Database Migrations
```bash
php artisan migrate
```

### Step 6️⃣: Optional - Seed the Database
Seed the database with essential default data:
```bash
php artisan db:seed
```

---

📦 **Frontend Setup**  
If the project includes frontend assets:
```bash
npm install
npm run build
```

### Step 7️⃣: Serve the Application
Start your development environment:
```bash
php artisan serve
```
Access your application at **`http://127.0.0.1:8000`** 🚀.

---

## 🔐 Example User Roles and Permissions

| Role   | Access Permissions                                                                 |
|--------|------------------------------------------------------------------------------------|
| **Admin** | Manage platform settings, user accounts, and advanced controls via the admin panel. |
| **User**  | Access personalized dashboards, update profiles, and participate in activities. |

⏩ For more detailed role descriptions, check the admin settings page.

---

## ⚠️ Troubleshooting

1. **Environment Issues:** Double-check `.env` for correct database and app configurations.
2. **Cache Issues:** Clear all caches if issues persist:
    ```bash
    php artisan config:clear
    php artisan cache:clear
    php artisan route:clear
    ```
3. **Dependency Errors:** Ensure Composer and npm are installed and updated.

---

## 🎉 Get Started

With these steps, you're ready to explore Radagile locally. For feature suggestions or contributions:

1. Submit a pull request 🛠️
2. Open a GitHub issue 🐛

---

### 🚀 Ready to unlock your team's Agile potential?
💙 **Radagile** – Empowering teams to break through and achieve lasting growth.
