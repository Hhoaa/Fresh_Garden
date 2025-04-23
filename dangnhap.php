<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Đăng nhập & Đăng ký - Fresh Garden</title>
  <script src="https://cdn.jsdelivr.net/npm/react@18.2.0/umd/react.development.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/react-dom@18.2.0/umd/react-dom.development.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@babel/standalone@7.20.15/babel.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(135deg, #d1fae5 0%, #bfdbfe 100%);
      overflow: hidden;
    }
    .auth-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      animation: fadeIn 0.8s ease-out;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .auth-card:hover {
      transform: scale(1.02);
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .tab {
      position: relative;
      transition: color 0.3s ease;
    }
    .tab-active::after {
      content: '';
      position: absolute;
      bottom: -2px;
      left: 0;
      width: 100%;
      height: 2px;
      background: #10B981;
      animation: slideIn 0.3s ease;
    }
    @keyframes slideIn {
      from { width: 0; }
      to { width: 100%; }
    }
    .input-group {
      position: relative;
      animation: stagger 0.5s ease-out forwards;
    }
    .input-group:nth-child(1) { animation-delay: 0.1s; }
    .input-group:nth-child(2) { animation-delay: 0.2s; }
    .input-group:nth-child(3) { animation-delay: 0.3s; }
    @keyframes stagger {
      from { opacity: 0; transform: translateX(-20px); }
      to { opacity: 1; transform: translateX(0); }
    }
    .input-icon {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #6b7280;
    }
    input:focus + .input-icon {
      color: #10B981;
    }
    .toast {
      animation: slideUp 0.5s ease-out;
    }
    @keyframes slideUp {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .particles {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: -1;
    }
    .particle {
      position: absolute;
      background: rgba(16, 185, 129, 0.3);
      border-radius: 50%;
      animation: float 15s infinite linear;
    }
    @keyframes float {
      0% { transform: translateY(100vh) scale(1); opacity: 0.8; }
      100% { transform: translateY(-100vh) scale(0.5); opacity: 0; }
    }
  </style>
</head>
<body>
  <div id="root"></div>
  <script type="text/babel">
    const { useState, useEffect } = React;

    function AuthPage() {
      const [activeTab, setActiveTab] = useState("login");
      const [users, setUsers] = useState([
        { email: "admin@freshgarden.com", password: "admin123" },
      ]);
      const [loginData, setLoginData] = useState({ email: "", password: "" });
      const [registerData, setRegisterData] = useState({
        email: "",
        password: "",
        confirmPassword: "",
      });
      const [message, setMessage] = useState({ text: "", type: "" });

      // Xử lý thông báo toast
      useEffect(() => {
        if (message.text) {
          const timer = setTimeout(() => setMessage({ text: "", type: "" }), 3000);
          return () => clearTimeout(timer);
        }
      }, [message]);

      // Xử lý đăng nhập
      const handleLogin = () => {
        if (!loginData.email || !loginData.password) {
          setMessage({ text: "Vui lòng nhập đầy đủ email và mật khẩu", type: "error" });
          return;
        }
        const user = users.find(
          (u) => u.email === loginData.email && u.password === loginData.password
        );
        if (user) {
          setMessage({ text: "Đăng nhập thành công!", type: "success" });
          setLoginData({ email: "", password: "" });
        } else {
          setMessage({ text: "Email hoặc mật khẩu không đúng", type: "error" });
        }
      };

      // Xử lý đăng ký
      const handleRegister = () => {
        if (!registerData.email || !registerData.password || !registerData.confirmPassword) {
          setMessage({ text: "Vui lòng nhập đầy đủ thông tin", type: "error" });
          return;
        }
        if (registerData.password !== registerData.confirmPassword) {
          setMessage({ text: "Mật khẩu xác nhận không khớp", type: "error" });
          return;
        }
        if (users.find((u) => u.email === registerData.email)) {
          setMessage({ text: "Email đã tồn tại", type: "error" });
          return;
        }
        setUsers([...users, { email: registerData.email, password: registerData.password }]);
        setMessage({ text: "Đăng ký thành công! Vui lòng đăng nhập.", type: "success" });
        setRegisterData({ email: "", password: "", confirmPassword: "" });
        setActiveTab("login");
      };

      // Tạo particles
      const particles = Array.from({ length: 20 }).map((_, i) => (
        <div
          key={i}
          className="particle"
          style={{
            left: `${Math.random() * 100}%`,
            width: `${Math.random() * 20 + 10}px`,
            height: `${Math.random() * 20 + 10}px`,
            animationDelay: `${Math.random() * 10}s`,
          }}
        />
      ));

      return (
        <div className="min-h-screen flex items-center justify-center p-4 relative">
          <div className="particles">{particles}</div>
          <div className="auth-card p-8 rounded-2xl shadow-2xl w-full max-w-md">
            <h1 className="text-4xl font-bold text-center mb-8 bg-clip-text text-transparent bg-gradient-to-r from-green-600 to-green-400">
              Fresh Garden
            </h1>
            <div className="flex justify-center mb-8">
              <button
                className={`px-6 py-2 text-lg font-semibold tab ${
                  activeTab === "login" ? "tab-active" : "text-gray-600"
                }`}
                onClick={() => {
                  setActiveTab("login");
                  setMessage({ text: "", type: "" });
                }}
              >
                Đăng nhập
              </button>
              <button
                className={`px-6 py-2 text-lg font-semibold tab ${
                  activeTab === "register" ? "tab-active" : "text-gray-600"
                }`}
                onClick={() => {
                  setActiveTab("register");
                  setMessage({ text: "", type: "" });
                }}
              >
                Đăng ký
              </button>
            </div>

            {activeTab === "login" && (
              <div>
                <div className="input-group mb-5">
                  <input
                    type="email"
                    placeholder="Email"
                    className="w-full border border-gray-300 p-4 pl-12 rounded-lg focus:border-green-500 transition-colors"
                    value={loginData.email}
                    onChange={(e) => setLoginData({ ...loginData, email: e.target.value })}
                  />
                  <span className="input-icon">📧</span>
                </div>
                <div className="input-group mb-5">
                  <input
                    type="password"
                    placeholder="Mật khẩu"
                    className="w-full border border-gray-300 p-4 pl-12 rounded-lg focus:border-green-500 transition-colors"
                    value={loginData.password}
                    onChange={(e) => setLoginData({ ...loginData, password: e.target.value })}
                  />
                  <span className="input-icon">🔒</span>
                </div>
                <button
                  className="w-full bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition font-semibold"
                  onClick={handleLogin}
                >
                  Đăng nhập
                </button>
              </div>
            )}

            {activeTab === "register" && (
              <div>
                <div className="input-group mb-5">
                  <input
                    type="email"
                    placeholder="Email"
                    className="w-full border border-gray-300 p-4 pl-12 rounded-lg focus:border-green-500 transition-colors"
                    value={registerData.email}
                    onChange={(e) => setRegisterData({ ...registerData, email: e.target.value })}
                  />
                  <span className="input-icon">📧</span>
                </div>
                <div className="input-group mb-5">
                  <input
                    type="password"
                    placeholder="Mật khẩu"
                    className="w-full border border-gray-300 p-4 pl-12 rounded-lg focus:border-green-500 transition-colors"
                    value={registerData.password}
                    onChange={(e) =>
                      setRegisterData({ ...registerData, password: e.target.value })
                    }
                  />
                  <span className="input-icon">🔒</span>
                </div>
                <div className="input-group mb-5">
                  <input
                    type="password"
                    placeholder="Xác nhận mật khẩu"
                    className="w-full border border-gray-300 p-4 pl-12 rounded-lg focus:border-green-500 transition-colors"
                    value={registerData.confirmPassword}
                    onChange={(e) =>
                      setRegisterData({ ...registerData, confirmPassword: e.target.value })
                    }
                  />
                  <span className="input-icon">🔒</span>
                </div>
                <button
                  className="w-full bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 transition font-semibold"
                  onClick={handleRegister}
                >
                  Đăng ký
                </button>
              </div>
            )}

            {message.text && (
              <div
                className={`toast fixed bottom-4 left-1/2 transform -translate-x-1/2 px-6 py-3 rounded-lg shadow-lg text-white ${
                  message.type === "success" ? "bg-green-600" : "bg-red-600"
                }`}
              >
                {message.text}
              </div>
            )}
          </div>
        </div>
      );
    }

    ReactDOM.render(<AuthPage />, document.getElementById("root"));
  </script>
</body>
</html>