<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Quản trị Fresh Garden</title>
  <script src="https://cdn.jsdelivr.net/npm/react@18.2.0/umd/react.development.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/react-dom@18.2.0/umd/react-dom.development.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@babel/standalone@7.20.15/babel.min.js"></script>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, #e6f3e9 0%, #d4e4f7 100%);
    }
    .sidebar {
      transition: all 0.3s ease;
    }
    .sidebar:hover {
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    .card {
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    @media (max-width: 768px) {
      .sidebar {
        transform: translateX(-100%);
        position: fixed;
        z-index: 50;
      }
      .sidebar.open {
        transform: translateX(0);
      }
    }
  </style>
</head>
<body>
  <div id="root"></div>
  <script type="text/babel">
    const { useState, useEffect } = React;

    function AdminDashboard() {
      const [products, setProducts] = useState([
        { id: 1, name: "Bánh mì tươi", price: 15000, stock: 50, sold: 100 },
        { id: 2, name: "Bánh ngọt nhân custard", price: 25000, stock: 30, sold: 60 },
        { id: 3, name: "Nước ép táo", price: 30000, stock: 20, sold: 40 },
      ]);
      const [newProduct, setNewProduct] = useState({ name: "", price: "", stock: "" });
      const [editingProduct, setEditingProduct] = useState(null);

      const [employees, setEmployees] = useState([
        { id: 1, name: "Nguyễn Văn A", role: "Nhân viên bán hàng", phone: "0901234567" },
        { id: 2, name: "Trần Thị B", role: "Quản lý", phone: "0912345678" },
      ]);
      const [newEmployee, setNewEmployee] = useState({ name: "", role: "", phone: "" });
      const [editingEmployee, setEditingEmployee] = useState(null);

      const [invoices, setInvoices] = useState([
        { id: 1, customer: "Khách lẻ", total: 45000, date: "2025-04-20", status: "Hoàn thành" },
        { id: 2, customer: "Nguyễn Văn C", total: 75000, date: "2025-04-21", status: "Đang xử lý" },
      ]);

      const [coupons, setCoupons] = useState([
        { id: 1, code: "FRESH10", discount: 10, expiry: "2025-12-31" },
        { id: 2, code: "SUMMER20", discount: 20, expiry: "2025-06-30" },
      ]);
      const [newCoupon, setNewCoupon] = useState({ code: "", discount: "", expiry: "" });
      const [editingCoupon, setEditingCoupon] = useState(null);

      const [activeTab, setActiveTab] = useState("products");
      const [isSidebarOpen, setIsSidebarOpen] = useState(false);
      const [searchQuery, setSearchQuery] = useState("");

      // Quản lý sản phẩm
      const handleAddProduct = () => {
        if (newProduct.name && newProduct.price && newProduct.stock) {
          setProducts([
            ...products,
            {
              id: products.length + 1,
              ...newProduct,
              price: parseInt(newProduct.price),
              stock: parseInt(newProduct.stock),
              sold: 0,
            },
          ]);
          setNewProduct({ name: "", price: "", stock: "" });
        }
      };

      const handleEditProduct = (product) => {
        setEditingProduct(product);
      };

      const handleUpdateProduct = () => {
        setProducts(
          products.map((p) => (p.id === editingProduct.id ? { ...editingProduct } : p))
        );
        setEditingProduct(null);
      };

      const handleDeleteProduct = (id) => {
        setProducts(products.filter((p) => p.id !== id));
      };

      // Quản lý nhân viên
      const handleAddEmployee = () => {
        if (newEmployee.name && newEmployee.role && newEmployee.phone) {
          setEmployees([
            ...employees,
            { id: employees.length + 1, ...newEmployee },
          ]);
          setNewEmployee({ name: "", role: "", phone: "" });
        }
      };

      const handleEditEmployee = (employee) => {
        setEditingEmployee(employee);
      };

      const handleUpdateEmployee = () => {
        setEmployees(
          employees.map((e) => (e.id === editingEmployee.id ? { ...editingEmployee } : e))
        );
        setEditingEmployee(null);
      };

      const handleDeleteEmployee = (id) => {
        setEmployees(employees.filter((e) => e.id !== id));
      };

      // Quản lý mã giảm giá
      const handleAddCoupon = () => {
        if (newCoupon.code && newCoupon.discount && newCoupon.expiry) {
          setCoupons([
            ...coupons,
            { id: coupons.length + 1, ...newCoupon, discount: parseInt(newCoupon.discount) },
          ]);
          setNewCoupon({ code: "", discount: "", expiry: "" });
        }
      };

      const handleEditCoupon = (coupon) => {
        setEditingCoupon(coupon);
      };

      const handleUpdateCoupon = () => {
        setCoupons(
          coupons.map((c) => (c.id === editingCoupon.id ? { ...editingCoupon } : c))
        );
        setEditingCoupon(null);
      };

      const handleDeleteCoupon = (id) => {
        setCoupons(coupons.filter((c) => c.id !== id));
      };

      // Tìm kiếm
      const filteredProducts = products.filter((p) =>
        p.name.toLowerCase().includes(searchQuery.toLowerCase())
      );
      const filteredEmployees = employees.filter((e) =>
        e.name.toLowerCase().includes(searchQuery.toLowerCase())
      );
      const filteredInvoices = invoices.filter((i) =>
        i.customer.toLowerCase().includes(searchQuery.toLowerCase())
      );
      const filteredCoupons = coupons.filter((c) =>
        c.code.toLowerCase().includes(searchQuery.toLowerCase())
      );

      // Báo cáo
      const totalRevenue = invoices.reduce((sum, inv) => sum + inv.total, 0);
      const lowStockProducts = products.filter((p) => p.stock < 10).length;
      const topSellingProduct = products.reduce(
        (max, p) => (p.sold > max.sold ? p : max),
        products[0]
      );

      // Biểu đồ doanh thu
      useEffect(() => {
        if (activeTab === "reports") {
          const ctx = document.getElementById("revenueChart").getContext("2d");
          new Chart(ctx, {
            type: "bar",
            data: {
              labels: products.map((p) => p.name),
              datasets: [
                {
                  label: "Doanh thu (VND)",
                  data: products.map((p) => p.price * p.sold),
                  backgroundColor: "rgba(34, 197, 94, 0.6)",
                  borderColor: "rgba(34, 197, 94, 1)",
                  borderWidth: 1,
                },
              ],
            },
            options: {
              animation: { duration: 1000 },
              scales: { y: { beginAtZero: true } },
            },
          });
        }
      }, [activeTab, products]);

      return (
        <div className="flex min-h-screen">
          {/* Sidebar */}
          <div
            className={`sidebar w-64 bg-white shadow-lg p-6 fixed h-full md:static md:translate-x-0 z-50 ${
              isSidebarOpen ? "open" : ""
            }`}
          >
            <div className="flex items-center justify-between mb-6">
              <h2 className="text-2xl font-bold text-green-600">Fresh Garden</h2>
              <button
                className="md:hidden text-gray-600"
                onClick={() => setIsSidebarOpen(false)}
              >
                ✕
              </button>
            </div>
            <nav>
              {["products", "employees", "invoices", "coupons", "reports"].map((tab) => (
                <button
                  key={tab}
                  className={`w-full text-left p-3 rounded-lg mb-2 flex items-center space-x-2 ${
                    activeTab === tab ? "bg-green-100 text-green-600" : "hover:bg-gray-100"
                  }`}
                  onClick={() => {
                    setActiveTab(tab);
                    setIsSidebarOpen(false);
                  }}
                >
                  <span>
                    {tab === "products" && "📦"}
                    {tab === "employees" && "👥"}
                    {tab === "invoices" && "📜"}
                    {tab === "coupons" && "🎟️"}
                    {tab === "reports" && "📊"}
                  </span>
                  <span>
                    {tab === "products" && "Sản phẩm"}
                    {tab === "employees" && "Nhân viên"}
                    {tab === "invoices" && "Hóa đơn"}
                    {tab === "coupons" && "Mã giảm giá"}
                    {tab === "reports" && "Báo cáo"}
                  </span>
                </button>
              ))}
            </nav>
          </div>

          {/* Nội dung chính */}
          <div className="flex-1 p-4 md:p-8">
            <div className="flex justify-between items-center mb-6">
              <h1 className="text-3xl font-bold text-gray-800">
                {activeTab === "products" && "Quản lý sản phẩm"}
                {activeTab === "employees" && "Quản lý nhân viên"}
                {activeTab === "invoices" && "Quản lý hóa đơn"}
                {activeTab === "coupons" && "Quản lý mã giảm giá"}
                {activeTab === "reports" && "Báo cáo cửa hàng"}
              </h1>
              <div className="flex items-center space-x-4">
                <input
                  type="text"
                  placeholder="Tìm kiếm..."
                  className="border p-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                  value={searchQuery}
                  onChange={(e) => setSearchQuery(e.target.value)}
                />
                <button
                  className="md:hidden text-gray-600"
                  onClick={() => setIsSidebarOpen(true)}
                >
                  ☰
                </button>
              </div>
            </div>

            {activeTab === "products" && (
              <div>
                {/* Form thêm sản phẩm */}
                <div className="card bg-white p-6 rounded-lg shadow-lg mb-6">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Thêm sản phẩm mới</h2>
                  <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <input
                      type="text"
                      placeholder="Tên sản phẩm"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newProduct.name}
                      onChange={(e) => setNewProduct({ ...newProduct, name: e.target.value })}
                    />
                    <input
                      type="number"
                      placeholder="Giá (VND)"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newProduct.price}
                      onChange={(e) => setNewProduct({ ...newProduct, price: e.target.value })}
                    />
                    <input
                      type="number"
                      placeholder="Tồn kho"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newProduct.stock}
                      onChange={(e) => setNewProduct({ ...newProduct, stock: e.target.value })}
                    />
                    <button
                      className="bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition"
                      onClick={handleAddProduct}
                    >
                      Thêm
                    </button>
                  </div>
                </div>

                {/* Form sửa sản phẩm */}
                {editingProduct && (
                  <div className="card bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 className="text-xl font-semibold text-gray-700 mb-4">Sửa sản phẩm</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                      <input
                        type="text"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingProduct.name}
                        onChange={(e) =>
                          setEditingProduct({ ...editingProduct, name: e.target.value })
                        }
                      />
                      <input
                        type="number"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingProduct.price}
                        onChange={(e) =>
                          setEditingProduct({ ...editingProduct, price: parseInt(e.target.value) })
                        }
                      />
                      <input
                        type="number"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingProduct.stock}
                        onChange={(e) =>
                          setEditingProduct({ ...editingProduct, stock: parseInt(e.target.value) })
                        }
                      />
                      <button
                        className="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition"
                        onClick={handleUpdateProduct}
                      >
                        Cập nhật
                      </button>
                      <button
                        className="bg-gray-600 text-white p-3 rounded-lg hover:bg-gray-700 transition"
                        onClick={() => setEditingProduct(null)}
                      >
                        Hủy
                      </button>
                    </div>
                  </div>
                )}

                {/* Danh sách sản phẩm */}
                <div className="card bg-white p-6 rounded-lg shadow-lg">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Danh sách sản phẩm</h2>
                  <table className="w-full table-auto">
                    <thead>
                      <tr className="bg-gray-100">
                        <th className="p-3 text-left">ID</th>
                        <th className="p-3 text-left">Tên sản phẩm</th>
                        <th className="p-3 text-left">Giá (VND)</th>
                        <th className="p-3 text-left">Tồn kho</th>
                        <th className="p-3 text-left">Đã bán</th>
                        <th className="p-3 text-left">Hành động</th>
                      </tr>
                    </thead>
                    <tbody>
                      {filteredProducts.map((product) => (
                        <tr key={product.id} className="border-t hover:bg-gray-50">
                          <td className="p-3">{product.id}</td>
                          <td className="p-3">{product.name}</td>
                          <td className="p-3">{product.price.toLocaleString()}</td>
                          <td className="p-3">{product.stock}</td>
                          <td className="p-3">{product.sold}</td>
                          <td className="p-3 flex space-x-2">
                            <button
                              className="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition"
                              onClick={() => handleEditProduct(product)}
                            >
                              Sửa
                            </button>
                            <button
                              className="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition"
                              onClick={() => handleDeleteProduct(product.id)}
                            >
                              Xóa
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {activeTab === "employees" && (
              <div>
                {/* Form thêm nhân viên */}
                <div className="card bg-white p-6 rounded-lg shadow-lg mb-6">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Thêm nhân viên mới</h2>
                  <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <input
                      type="text"
                      placeholder="Tên nhân viên"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newEmployee.name}
                      onChange={(e) => setNewEmployee({ ...newEmployee, name: e.target.value })}
                    />
                    <input
                      type="text"
                      placeholder="Vị trí"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newEmployee.role}
                      onChange={(e) => setNewEmployee({ ...newEmployee, role: e.target.value })}
                    />
                    <input
                      type="text"
                      placeholder="Số điện thoại"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newEmployee.phone}
                      onChange={(e) => setNewEmployee({ ...newEmployee, phone: e.target.value })}
                    />
                    <button
                      className="bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition"
                      onClick={handleAddEmployee}
                    >
                      Thêm
                    </button>
                  </div>
                </div>

                {/* Form sửa nhân viên */}
                {editingEmployee && (
                  <div className="card bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 className="text-xl font-semibold text-gray-700 mb-4">Sửa nhân viên</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                      <input
                        type="text"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingEmployee.name}
                        onChange={(e) =>
                          setEditingEmployee({ ...editingEmployee, name: e.target.value })
                        }
                      />
                      <input
                        type="text"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingEmployee.role}
                        onChange={(e) =>
                          setEditingEmployee({ ...editingEmployee, role: e.target.value })
                        }
                      />
                      <input
                        type="text"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingEmployee.phone}
                        onChange={(e) =>
                          setEditingEmployee({ ...editingEmployee, phone: e.target.value })
                        }
                      />
                      <button
                        className="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition"
                        onClick={handleUpdateEmployee}
                      >
                        Cập nhật
                      </button>
                      <button
                        className="bg-gray-600 text-white p-3 rounded-lg hover:bg-gray-700 transition"
                        onClick={() => setEditingEmployee(null)}
                      >
                        Hủy
                      </button>
                    </div>
                  </div>
                )}

                {/* Danh sách nhân viên */}
                <div className="card bg-white p-6 rounded-lg shadow-lg">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Danh sách nhân viên</h2>
                  <table className="w-full table-auto">
                    <thead>
                      <tr className="bg-gray-100">
                        <th className="p-3 text-left">ID</th>
                        <th className="p-3 text-left">Tên nhân viên</th>
                        <th className="p-3 text-left">Vị trí</th>
                        <th className="p-3 text-left">Số điện thoại</th>
                        <th className="p-3 text-left">Hành động</th>
                      </tr>
                    </thead>
                    <tbody>
                      {filteredEmployees.map((employee) => (
                        <tr key={employee.id} className="border-t hover:bg-gray-50">
                          <td className="p-3">{employee.id}</td>
                          <td className="p-3">{employee.name}</td>
                          <td className="p-3">{employee.role}</td>
                          <td className="p-3">{employee.phone}</td>
                          <td className="p-3 flex space-x-2">
                            <button
                              className="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition"
                              onClick={() => handleEditEmployee(employee)}
                            >
                              Sửa
                            </button>
                            <button
                              className="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition"
                              onClick={() => handleDeleteEmployee(employee.id)}
                            >
                              Xóa
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {activeTab === "invoices" && (
              <div>
                {/* Danh sách hóa đơn */}
                <div className="card bg-white p-6 rounded-lg shadow-lg">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Danh sách hóa đơn</h2>
                  <table className="w-full table-auto">
                    <thead>
                      <tr className="bg-gray-100">
                        <th className="p-3 text-left">ID</th>
                        <th className="p-3 text-left">Khách hàng</th>
                        <th className="p-3 text-left">Tổng tiền (VND)</th>
                        <th className="p-3 text-left">Ngày</th>
                        <th className="p-3 text-left">Trạng thái</th>
                      </tr>
                    </thead>
                    <tbody>
                      {filteredInvoices.map((invoice) => (
                        <tr key={invoice.id} className="border-t hover:bg-gray-50">
                          <td className="p-3">{invoice.id}</td>
                          <td className="p-3">{invoice.customer}</td>
                          <td className="p-3">{invoice.total.toLocaleString()}</td>
                          <td className="p-3">{invoice.date}</td>
                          <td className="p-3">
                            <span
                              className={`px-2 py-1 rounded ${
                                invoice.status === "Hoàn thành"
                                  ? "bg-green-100 text-green-600"
                                  : "bg-yellow-100 text-yellow-600"
                              }`}
                            >
                              {invoice.status}
                            </span>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {activeTab === "coupons" && (
              <div>
                {/* Form thêm mã giảm giá */}
                <div className="card bg-white p-6 rounded-lg shadow-lg mb-6">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Thêm mã giảm giá mới</h2>
                  <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <input
                      type="text"
                      placeholder="Mã giảm giá"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newCoupon.code}
                      onChange={(e) => setNewCoupon({ ...newCoupon, code: e.target.value })}
                    />
                    <input
                      type="number"
                      placeholder="Phần trăm giảm"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newCoupon.discount}
                      onChange={(e) => setNewCoupon({ ...newCoupon, discount: e.target.value })}
                    />
                    <input
                      type="date"
                      placeholder="Ngày hết hạn"
                      className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                      value={newCoupon.expiry}
                      onChange={(e) => setNewCoupon({ ...newCoupon, expiry: e.target.value })}
                    />
                    <button
                      className="bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition"
                      onClick={handleAddCoupon}
                    >
                      Thêm
                    </button>
                  </div>
                </div>

                {/* Form sửa mã giảm giá */}
                {editingCoupon && (
                  <div className="card bg-white p-6 rounded-lg shadow-lg mb-6">
                    <h2 className="text-xl font-semibold text-gray-700 mb-4">Sửa mã giảm giá</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                      <input
                        type="text"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingCoupon.code}
                        onChange={(e) =>
                          setEditingCoupon({ ...editingCoupon, code: e.target.value })
                        }
                      />
                      <input
                        type="number"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingCoupon.discount}
                        onChange={(e) =>
                          setEditingCoupon({ ...editingCoupon, discount: parseInt(e.target.value) })
                        }
                      />
                      <input
                        type="date"
                        className="border p-3 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value={editingCoupon.expiry}
                        onChange={(e) =>
                          setEditingCoupon({ ...editingCoupon, expiry: e.target.value })
                        }
                      />
                      <button
                        className="bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition"
                        onClick={handleUpdateCoupon}
                      >
                        Cập nhật
                      </button>
                      <button
                        className="bg-gray-600 text-white p-3 rounded-lg hover:bg-gray-700 transition"
                        onClick={() => setEditingCoupon(null)}
                      >
                        Hủy
                      </button>
                    </div>
                  </div>
                )}

                {/* Danh sách mã giảm giá */}
                <div className="card bg-white p-6 rounded-lg shadow-lg">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Danh sách mã giảm giá</h2>
                  <table className="w-full table-auto">
                    <thead>
                      <tr className="bg-gray-100">
                        <th className="p-3 text-left">ID</th>
                        <th className="p-3 text-left">Mã giảm giá</th>
                        <th className="p-3 text-left">Phần trăm giảm</th>
                        <th className="p-3 text-left">Ngày hết hạn</th>
                        <th className="p-3 text-left">Hành động</th>
                      </tr>
                    </thead>
                    <tbody>
                      {filteredCoupons.map((coupon) => (
                        <tr key={coupon.id} className="border-t hover:bg-gray-50">
                          <td className="p-3">{coupon.id}</td>
                          <td className="p-3">{coupon.code}</td>
                          <td className="p-3">{coupon.discount}%</td>
                          <td className="p-3">{coupon.expiry}</td>
                          <td className="p-3 flex space-x-2">
                            <button
                              className="bg-yellow-500 text-white px-3 py-1 rounded-lg hover:bg-yellow-600 transition"
                              onClick={() => handleEditCoupon(coupon)}
                            >
                              Sửa
                            </button>
                            <button
                              className="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600 transition"
                              onClick={() => handleDeleteCoupon(coupon.id)}
                            >
                              Xóa
                            </button>
                          </td>
                        </tr>
                      ))}
                    </tbody>
                  </table>
                </div>
              </div>
            )}

            {activeTab === "reports" && (
              <div>
                {/* Thẻ thống kê */}
                <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-6">
                  <div className="card bg-white p-6 rounded-lg shadow-lg">
                    <h3 className="text-lg font-semibold text-gray-700">Tổng doanh thu</h3>
                    <p className="text-2xl font-bold text-green-600">
                      {totalRevenue.toLocaleString()} VND
                    </p>
                  </div>
                  <div className="card bg-white p-6 rounded-lg shadow-lg">
                    <h3 className="text-lg font-semibold text-gray-700">Sản phẩm tồn thấp</h3>
                    <p className="text-2xl font-bold text-red-600">{lowStockProducts}</p>
                  </div>
                  <div className="card bg-white p-6 rounded-lg shadow-lg">
                    <h3 className="text-lg font-semibold text-gray-700">Sản phẩm bán chạy</h3>
                    <p className="text-2xl font-bold text-blue-600">{topSellingProduct.name}</p>
                  </div>
                </div>

                {/* Biểu đồ doanh thu */}
                <div className="card bg-white p-6 rounded-lg shadow-lg">
                  <h2 className="text-xl font-semibold text-gray-700 mb-4">Doanh thu theo sản phẩm</h2>
                  <canvas id="revenueChart" height="100"></canvas>
                </div>
              </div>
            )}
          </div>
        </div>
      );
    }

    ReactDOM.render(<AdminDashboard />, document.getElementById("root"));
  </script>
</body>
</html>