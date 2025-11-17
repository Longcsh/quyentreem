// ==========================================
// HÀM DÙNG CHUNG
// ==========================================

// Load HTML vào 1 phần tử
function loadHTML(id, file) {
  fetch(file)
    .then(res => {
      if (!res.ok) throw new Error("Không tìm thấy file " + file);
      return res.text();
    })
    .then(data => document.getElementById(id).innerHTML = data)
    .catch(err => console.error(err));
}

// Khi load index.html -> load header + sidebar
window.addEventListener("DOMContentLoaded", () => {
  loadHTML("header", "partials/header.html");
  loadHTML("sidebar", "partials/sidebar.html");

  setTimeout(() => {
    const sidebar = document.getElementById("sidebar");
    if (!sidebar) return;
    sidebar.addEventListener("click", e => {
      if (e.target.tagName === "A") {
        e.preventDefault();
        const page = e.target.getAttribute("data-page");
        loadHTML("content", "pages/" + page);
      }
    });
  }, 300);
});

// ==========================================
// API CONFIG
// ==========================================
const API_POSTS = "../BE/";   // cho bài đăng
const API_USERS = "../BE/";   // cho người dùng & chuyên gia

// ==========================================
// POSTS
// ==========================================
function statusBadge(status) {
  const map = {
    approved: {text: "Đã duyệt", cls: "green"},
    pending:  {text: "Chờ duyệt", cls: "yellow"},
    rejected: {text: "Bị từ chối", cls: "red"},
    unread:   {text: "Chưa đọc", cls: "gray"}
  };
  const {text, cls} = map[status] || {text: status, cls: ""};
  return `<span class="badge ${cls}">${text}</span>`;
}
function btn(text, type="primary") {
  return `<button class="btn ${type}">${text}</button>`;
}

async function loadStats() {
  const res = await fetch(API_POSTS + "posts_get_stats.php");
  const s = await res.json();
  if (!document.getElementById("stats")) return;
  document.getElementById("stats").innerHTML = `
    <div class="stat-card">Tổng số bài đăng <h2>${s.total || 0}</h2></div>
    <div class="stat-card">Bài đang chờ duyệt <h2>${s.pending || 0}</h2></div>
    <div class="stat-card">Bài đã duyệt <h2>${s.approved || 0}</h2></div>
    <div class="stat-card highlight">Bài bị từ chối <h2>${s.rejected || 0}</h2></div>
  `;
}

async function loadLatest() {
  const res = await fetch(API_POSTS + "posts_get_latest.php?limit=5");
  const data = await res.json();
  const tbody = document.querySelector("#latestTable tbody");
  if (!tbody) return;
  tbody.innerHTML = data.map(p =>
    `<tr><td>${p.title}</td><td>${p.author}</td><td>${p.created_at}</td></tr>`
  ).join("");
}

async function loadPosts(page=1) {
  const url = API_POSTS + `posts_list.php?page=${page}&pageSize=10`;
  const res = await fetch(url);
  const data = await res.json();
  const tbody = document.getElementById("postTbody");
  if (!tbody) return;
  tbody.innerHTML = data.items.map(p => `
    <tr data-id="${p.id}">
      <td>${p.id}</td>
      <td>${p.title}</td>
      <td>${p.author}</td>
      <td>${p.created_at}</td>
      <td>${statusBadge(p.status)}</td>
      <td class="actions">
        <button class="btn primary btn-edit">Sửa</button>
        <button class="btn danger btn-del">Xóa</button>
      </td>
    </tr>
  `).join("");
}

// Cập nhật trạng thái bài đăng
async function updatePostStatus(e, newStatus){
  const tr = e.target.closest("tr");
  const id = tr.dataset.id;
  const form = new FormData();
  form.append("id", id);
  form.append("status", newStatus);

  const res = await fetch(API_POSTS+"posts_status.php", { method:"POST", body:form });
  const data = await res.json();
  if(data.success){
    loadPosts();
  } else {
    alert("Cập nhật thất bại!");
  }
}

// ==========================================
// USERS
// ==========================================
let U_STATE = { q:"", role:"", status:"", page:1, pageSize:10 };

function userBadge(status){
  return status==='active'
    ? `<span class="badge green">Hoạt động</span>`
    : `<span class="badge red">Bị khóa</span>`;
}

async function loadUsers(){
  const p = new URLSearchParams(U_STATE);
  const res = await fetch(API_USERS + "users_list.php?"+p.toString());
  const data = await res.json();
  const tbody = document.getElementById("userTbody");
  if (!tbody) return;

  tbody.innerHTML = data.items.map(u => `
    <tr data-id="${u.id}">
      <td>#${u.id}</td>
      <td>${u.name}</td>
      <td>${u.email}</td>
      <td>${u.phone||''}</td>
      <td>${u.role}</td>
      <td>${u.created_at}</td>
      <td>${userBadge(u.status)}</td>
      <td>
        <button class="btn primary btn-edit">Sửa</button>
        ${u.status==='active'
          ? '<button class="btn warn btn-block">Khóa</button>'
          : '<button class="btn success btn-unblock">Mở khóa</button>'}
        <button class="btn danger btn-del">Xóa</button>
      </td>
    </tr>
  `).join("");

  tbody.querySelectorAll(".btn-edit").forEach(b=>b.addEventListener('click', e=>editUser(e)));
  tbody.querySelectorAll(".btn-block").forEach(b=>b.addEventListener('click', e=>updateUserStatus(e,'blocked')));
  tbody.querySelectorAll(".btn-unblock").forEach(b=>b.addEventListener('click', e=>updateUserStatus(e,'active')));
  tbody.querySelectorAll(".btn-del").forEach(b=>b.addEventListener('click', e=>deleteUser(e)));
}

function editUser(e){
  const tr = e.target.closest("tr");
  alert("Sửa user ID = " + tr.dataset.id);
}

async function updateUserStatus(e,newStatus){
  const tr = e.target.closest("tr");
  const id = tr.dataset.id;
  const form = new FormData();
  form.append("id", id);
  form.append("status", newStatus);
  const res = await fetch(API_USERS+"users_status.php",{method:"POST",body:form});
  const d = await res.json();
  if (d.success) loadUsers();
}

async function deleteUser(e){
  if(!confirm("Bạn chắc muốn xóa user này?")) return;
  const tr = e.target.closest("tr");
  const id = tr.dataset.id;
  const form = new FormData();
  form.append("id", id);
  const res = await fetch(API_USERS+"users_delete.php",{method:"POST",body:form});
  const d = await res.json();
  if (d.success) loadUsers();
}

// ==========================================
// EXPERTS
// ==========================================
async function loadExperts(){
  const res = await fetch(API_USERS+"experts_list.php?page=1&pageSize=20");
  const data = await res.json();
  const tbody = document.getElementById("expertTbody");
  if (!tbody) return;

  tbody.innerHTML = data.items.map(e=>`
    <tr data-id="${e.id}">
      <td>${e.id}</td>
      <td>${e.name}</td>
      <td>${e.email||''}</td>
      <td>${e.phone||''}</td>
      <td>${e.degree||''}</td>
      <td>${e.field||''}</td>
      <td>${"★".repeat(e.rating)}${"☆".repeat(5-e.rating)}</td>
      <td>${e.created_at}</td>
      <td>
        <span class="badge ${e.status === 'active' ? 'green' : 'red'}">
          ${e.status === 'active' ? 'Hoạt động' : 'Bị khóa'}
        </span>
      </td>
      <td>
        <button class="btn success btn-appointments">📅 Lịch hẹn</button>
        <button class="btn primary btn-edit">Sửa</button>
        <button class="btn danger btn-del">Xóa</button>
      </td>
    </tr>
  `).join("");

  tbody.querySelectorAll(".btn-appointments").forEach(b=>{
    b.addEventListener("click", e=>{
      const expertId = e.target.closest("tr").dataset.id;
      openAppointmentsModal(expertId);
    });
  });

  tbody.querySelectorAll(".btn-edit").forEach(b=>{
    b.addEventListener("click", e=>{
      const id = e.target.closest("tr").dataset.id;
      editExpert(id);
    });
  });

  tbody.querySelectorAll(".btn-del").forEach(b=>{
    b.addEventListener("click", async e=>{
      if (!confirm("Bạn có chắc muốn xóa chuyên gia này?")) return;
      const tr = e.target.closest("tr");
      const id = tr.dataset.id;
      const form = new FormData();
      form.append("id", id);
      const res = await fetch(API_USERS+"experts_delete.php",{method:"POST",body:form});
      const d = await res.json();
      if (d.success) tr.remove();
      else alert("Xóa thất bại!");
    });
  });
}

// Sửa chuyên gia
async function editExpert(id) {
  const res = await fetch(API_USERS + "experts_get.php?id=" + id);
  const d = await res.json();

  if (!d.success) {
    alert(d.message || "Lỗi tải dữ liệu!");
    return;
  }
  const data = d.data;

  document.getElementById("expertModalTitle").textContent = "Sửa chuyên gia";
  document.getElementById("ex_id").value = data.id;
  document.getElementById("ex_name").value = data.name || "";
  document.getElementById("ex_email").value = data.email || "";
  document.getElementById("ex_phone").value = data.phone || "";
  document.getElementById("ex_password").value = "";
  document.getElementById("ex_degree").value = data.degree || "";
  document.getElementById("ex_field").value = data.field || "";
  document.getElementById("ex_rating").value = data.rating || "3";
  document.getElementById("ex_status").value = data.status || "active";

  document.getElementById("expertModal").classList.remove("hidden");
}



// ================== APPOINTMENTS ==================
async function loadAppointments(expertId){
  const res = await fetch(API_USERS+"appointments_list.php?expert_id="+expertId);
  const data = await res.json();
  const tbody = document.getElementById("appointmentTbody");
  if (!tbody) return;

  tbody.innerHTML = data.map(a=>`
    <tr data-id="${a.id}">
      <td>${a.user_name}</td>
      <td>${a.username}</td>
      <td>${a.email}</td>
      <td>${a.phone}</td>
      <td>${a.expert_name}</td>
      <td>${a.scheduled_at}</td>
      <td>${a.status}</td>
      <td>${a.note||''}</td>
      <td>
        ${a.status!=='cancelled'
          ? `<button class="btn danger btn-cancel-appt">Hủy</button>`:''}
      </td>
    </tr>
  `).join("");

  tbody.querySelectorAll(".btn-cancel-appt").forEach(b=>{
    b.addEventListener("click", e=>cancelAppointment(e));
  });
}

async function cancelAppointment(e){
  const tr = e.target.closest("tr");
  const id = tr.dataset.id;
  if(!confirm("Hủy lịch hẹn này?")) return;
  const form = new FormData();
  form.append("id", id);
  const res = await fetch(API_USERS+"appointments_cancel.php",{method:"POST",body:form});
  const d = await res.json();
  if(d.success) tr.querySelector("td:nth-child(7)").innerText="cancelled";
}

function showAppointments(expertId){
  document.getElementById("appointmentModal").classList.remove("hidden");
  loadAppointments(expertId);
}


async function loadAppointments(expertId) {
  const res = await fetch(API_USERS + "appointments_list.php?expert_id=" + expertId);
  const data = await res.json();

  const tbody = document.getElementById("appointmentTbody");
  if (!tbody) return;

  tbody.innerHTML = data.items.map(appt => `
    <tr data-id="${appt.id}">
      <td>${appt.user_name || "Ẩn danh"}</td>
      <td>${appt.user_account || "-"}</td>
      <td>${appt.email || "-"}</td>
      <td>${appt.phone || "-"}</td>
      <td>${appt.expert_name}</td>
      <td>${appt.scheduled_at}</td>
      <td><span class="badge ${appt.status === 'confirmed' ? 'green' : 'yellow'}">
        ${appt.status === 'confirmed' ? 'Đã xác nhận' : 'Chờ xử lý'}
      </span></td>
      <td>${appt.note || ""}</td>
      <td>
        <button class="btn danger btn-cancel">Hủy</button>
      </td>
    </tr>
  `).join("");

  // gắn sự kiện hủy lịch
  tbody.querySelectorAll(".btn-cancel").forEach(btn => {
    btn.addEventListener("click", e => cancelAppointment(e));
  });
}

// mở modal xem lịch hẹn
function openAppointmentsModal(expertId) {
  const modal = document.getElementById("appointmentModal");
  modal.classList.remove("hidden");
  document.getElementById("appt_expert_id").value = expertId;
  loadAppointments(expertId);
}

// đóng modal
document.getElementById("btnCloseAppointment").addEventListener("click", () => {
  document.getElementById("appointmentModal").classList.add("hidden");
});

// hủy lịch hẹn
async function cancelAppointment(e) {
  if (!confirm("Bạn có chắc muốn hủy lịch hẹn này?")) return;
  const tr = e.target.closest("tr");
  const id = tr.dataset.id;
  const form = new FormData();
  form.append("id", id);
  const res = await fetch(API_USERS + "appointments_cancel.php", { method: "POST", body: form });
  const data = await res.json();
  if (data.success) {
    tr.remove();
  } else {
    alert("Hủy thất bại!");
  }
}

// ================== ADD APPOINTMENT ==================
document.addEventListener("DOMContentLoaded", ()=>{
  const closeBtn=document.getElementById("btnCloseAppointment");
  if(closeBtn) closeBtn.addEventListener("click",()=>document.getElementById("appointmentModal").classList.add("hidden"));

  const closeAdd=document.getElementById("btnCloseAddAppointment");
  if(closeAdd) closeAdd.addEventListener("click",()=>document.getElementById("appointmentAddModal").classList.add("hidden"));

  const btnAdd=document.getElementById("btnAddAppointment");
  if(btnAdd) btnAdd.addEventListener("click",()=>{
    document.getElementById("appointmentAddModal").classList.remove("hidden");
    loadUsersDropdown();
  });

  const form=document.getElementById("appointmentForm");
  if(form){
    form.addEventListener("submit",async e=>{
      e.preventDefault();
      const formData=new FormData(form);
      const res=await fetch(API_USERS+"appointments_add.php",{method:"POST",body:formData});
      const d=await res.json();
      if(d.success){
        alert("Thêm lịch hẹn thành công");
        document.getElementById("appointmentAddModal").classList.add("hidden");
        loadAppointments(formData.get("expert_id"));
      }else{
        alert("Thêm thất bại");
      }
    });
  }
});

async function loadUsersDropdown(){
  const res=await fetch(API_USERS+"users_list.php?page=1&pageSize=100");
  const data=await res.json();
  const sel=document.getElementById("appt_user_id");
  sel.innerHTML=data.items.map(u=>`<option value="${u.id}">${u.name} (${u.username})</option>`).join("");
}

// ==========================================
// MODALS
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  // User modal
  const addBtn = document.getElementById("btnAddUser");
  const modal = document.getElementById("userModal");
  const closeBtn = document.getElementById("btnCloseModal");
  if (addBtn && modal && closeBtn) {
    addBtn.addEventListener("click", () => {
      document.getElementById("userModalTitle").textContent = "Thêm người dùng";
      document.getElementById("userForm").reset();
      modal.classList.remove("hidden");
    });
    closeBtn.addEventListener("click", () => modal.classList.add("hidden"));
  }

  // Expert modal
  const addExBtn = document.getElementById("btnAddExpert");
  const exModal = document.getElementById("expertModal");
  const closeExBtn = document.getElementById("btnCloseExpertModal");
  if (addExBtn && exModal && closeExBtn) {
    addExBtn.addEventListener("click", () => {
      document.getElementById("expertModalTitle").textContent = "Thêm chuyên gia";
      document.getElementById("expertForm").reset();
      exModal.classList.remove("hidden");
    });
    closeExBtn.addEventListener("click", () => exModal.classList.add("hidden"));
  }
});

// ==========================================
// FINANCE
// ==========================================
async function loadDonations(){
  const res = await fetch(API_USERS+"finance_donations.php");
  const data = await res.json();
  const tbody = document.getElementById("donationTbody");
  if (!tbody) return;
  tbody.innerHTML = data.map(d=>`
    <tr>
      <td>${d.donor_name}</td>
      <td>${Number(d.amount).toLocaleString()} đ</td>
      <td>${d.method}</td>
      <td>${d.phone||''}</td>
      <td>${d.email||''}</td>
      <td>${d.created_at}</td>
    </tr>
  `).join("");
}

async function loadExpenses(){
  const res = await fetch(API_USERS+"finance_expenses.php");
  const data = await res.json();
  const tbody = document.getElementById("expenseTbody");
  if (!tbody) return;
  tbody.innerHTML = data.map(e=>`
    <tr data-id="${e.id}">
      <td>${e.purpose}</td>
      <td>${Number(e.amount).toLocaleString()} đ</td>
      <td>${e.description||''}</td>
      <td>${e.created_at}</td>
      <td><button class="btn primary btn-edit-exp">Sửa</button></td>
    </tr>
  `).join("");

  tbody.querySelectorAll(".btn-edit-exp").forEach(b=>b.addEventListener("click", e=>editExpense(e)));
}

// Sửa khoản chi (mở modal)
function editExpense(e){
  const tr = e.target.closest("tr");
  const id = tr.dataset.id;
  alert("Sửa khoản chi ID = " + id);
  // TODO: mở modal và load dữ liệu từ API (finance_expense_get.php?id=...)
}

async function loadFundTotal(){
  const res1 = await fetch(API_USERS+"finance_donations.php");
  const donations = await res1.json();
  const res2 = await fetch(API_USERS+"finance_expenses.php");
  const expenses = await res2.json();

  const totalDonations = donations.reduce((s,d)=>s+Number(d.amount),0);
  const totalExpenses = expenses.reduce((s,e)=>s+Number(e.amount),0);
  const fund = totalDonations - totalExpenses;

  const div = document.getElementById("fundTotal");
  if (div) div.innerHTML = `<h2>${fund.toLocaleString()} đ</h2>`;

  // Chart
  const ctx = document.getElementById("fundChart");
  if (ctx){
    if (window._fundChart) window._fundChart.destroy();
    window._fundChart = new Chart(ctx, {
      type: "bar",
      data: {
        labels: ["Tổng Thu","Tổng Chi","Còn lại"],
        datasets: [{
          data: [totalDonations, totalExpenses, fund],
          backgroundColor: ["#22c55e","#ef4444","#3b82f6"]
        }]
      },
      options: {
        plugins: { legend: { display:false }},
        scales: {
          y: { beginAtZero:true, ticks:{ callback:v=>v.toLocaleString()+" đ" } }
        }
      }
    });
  }
}



// ==========================================
// KHỞI CHẠY
// ==========================================
document.addEventListener("DOMContentLoaded", () => {
  if (document.getElementById("stats")) {
    loadStats();
    loadLatest();
    loadPosts();
  }
  if (document.getElementById("userTbody")) {
    loadUsers();
  }
  if (document.getElementById("postTbody")) {
    loadPosts();
  }
  if (document.getElementById("expertTbody")) {
    loadExperts();
  }
});
